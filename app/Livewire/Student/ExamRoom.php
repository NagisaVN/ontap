<?php

namespace App\Livewire\Student;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Repositories\Contracts\ExamAttemptRepositoryInterface;
use App\Services\ExamGradingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.exam')]
#[Title('Phòng thi')]
class ExamRoom extends Component
{
    // ── Exam state ──────────────────────────────────────
    #[Locked] public int $baiThiId;
    #[Locked] public int $luotThiId;

    public array  $cauHoiList    = [];      // [{id, noi_dung, lua_chon:[...]}]
    public int    $cauHienTai    = 0;       // Index câu đang xem (0-based)
    public array  $dapAnDaChon  = [];       // [cauHoiId => luaChonId]
    public int    $thoiGianConLai = 0;      // Giây còn lại
    public int    $soLanRoiTab  = 0;
    public bool   $daHoanThanh  = false;

    // ── Mount ───────────────────────────────────────────
    public function mount(Exam $baiThi, ExamAttemptRepositoryInterface $attemptRepo): void
    {
        $this->baiThiId = $baiThi->id;

        // Tìm lượt thi đang làm dở hoặc request param
        $luotThi = $attemptRepo->timDangLam(Auth::id(), $baiThi->id);

        if (!$luotThi) {
            $luotThi = $attemptRepo->taoLuotThi([
                'nguoi_dung_id' => Auth::id(),
                'bai_thi_id'    => $baiThi->id,
            ]);
        }

        $this->luotThiId      = $luotThi->id;
        $this->thoiGianConLai = $baiThi->thoi_gian_phut * 60;

        // Tính thời gian còn lại nếu đang làm dở
        if ($luotThi->bat_dau_luc) {
            $daDung = now()->diffInSeconds($luotThi->bat_dau_luc);
            $tongPhep = $baiThi->thoi_gian_phut * 60;
            $this->thoiGianConLai = max(0, $tongPhep - (int) $daDung);
        }

        // Load câu hỏi với lựa chọn
        $baiThi->load(['cauHoi' => fn($q) => $q->orderByPivot('thu_tu'), 'cauHoi.luaChon']);

        $this->cauHoiList = $baiThi->cauHoi->map(fn($cq) => [
            'id'       => $cq->id,
            'noi_dung' => $cq->noi_dung,
            'hinh_anh' => $cq->hinh_anh,
            'lua_chon' => $cq->luaChon->map(fn($lc) => [
                'id'      => $lc->id,
                'noi_dung'=> $lc->noi_dung,
                'thu_tu'  => $lc->thu_tu,
            ])->values()->toArray(),
        ])->values()->toArray();

        // Khôi phục đáp án từ cache (auto-save)
        $cached = cache("luot_thi_{$this->luotThiId}_tu_dong_luu");
        if ($cached) $this->dapAnDaChon = $cached;
    }

    // ── Chọn đáp án ─────────────────────────────────────
    public function chonDapAn(int $cauHoiId, int $luaChonId): void
    {
        $this->dapAnDaChon[$cauHoiId] = $luaChonId;
    }

    // ── Chuyển câu ──────────────────────────────────────
    public function denCau(int $index): void
    {
        $this->cauHienTai = $index;
    }

    public function cauTiep(): void
    {
        $this->cauHienTai = min($this->cauHienTai + 1, count($this->cauHoiList) - 1);
    }

    public function cauTruoc(): void
    {
        $this->cauHienTai = max($this->cauHienTai - 1, 0);
    }

    // ── Auto-save (poll 30s) ─────────────────────────────
    public function tuDongLuu(ExamAttemptRepositoryInterface $attemptRepo): void
    {
        $attemptRepo->tuDongLuu($this->luotThiId, $this->dapAnDaChon);
    }

    // ── Anti-cheat: ghi nhận rời tab ───────────────────
    public function ghiNhanRoiTab(): void
    {
        $this->soLanRoiTab++;
        ExamAttempt::where('id', $this->luotThiId)
            ->update(['so_lan_roi_tab' => $this->soLanRoiTab]);
    }

    // ── Nộp bài ─────────────────────────────────────────
    public function nopBai(ExamGradingService $grading, ExamAttemptRepositoryInterface $attemptRepo): void
    {
        if ($this->daHoanThanh) return;

        $luotThi = ExamAttempt::findOrFail($this->luotThiId);

        if ($luotThi->trang_thai->value === 'hoan_thanh') {
            $this->redirect(route('exam.result', $luotThi));
            return;
        }

        // Build đáp án theo format ExamGradingService::cham()
        $dapAn = collect($this->cauHoiList)->map(fn($cq) => [
            'cau_hoi_id' => $cq['id'],
            'lua_chon_id'=> $this->dapAnDaChon[$cq['id']] ?? null,
        ])->values()->toArray();

        $luotThiHoanThanh = $grading->cham($luotThi, $dapAn);
        $this->daHoanThanh = true;

        // Xóa cache auto-save
        cache()->forget("luot_thi_{$this->luotThiId}_tu_dong_luu");

        $this->redirect(route('exam.result', $luotThiHoanThanh));
    }

    public function render()
    {
        return view('livewire.student.exam-room');
    }
}
