<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use App\Models\UserQuestionStat;
use App\Models\UserSubSubjectProgress;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgressTrackingService
{
    public function __construct(
        private readonly QuestionRepositoryInterface $cauHoiRepo,
    ) {}

    // ===================================================================
    // CẬP NHẬT DỮ LIỆU
    // ===================================================================

    /**
     * Cập nhật thống kê đúng/sai của một câu hỏi cho user.
     * Dùng upsert để tránh race condition.
     *
     * @return UserQuestionStat  — dùng để kiểm tra so_lan_sai
     */
    public function capNhatThongKeCauHoi(int $nguoiDungId, int $cauHoiId, bool $dungSai): UserQuestionStat
    {
        $thongKe = UserQuestionStat::firstOrCreate(
            ['nguoi_dung_id' => $nguoiDungId, 'cau_hoi_id' => $cauHoiId],
            ['so_lan_sai' => 0, 'so_lan_dung' => 0]
        );

        if ($dungSai) {
            $thongKe->increment('so_lan_dung');
        } else {
            $thongKe->increment('so_lan_sai');
        }

        $thongKe->update(['lan_cuoi_lam' => now()]);

        return $thongKe->fresh();
    }

    /**
     * Tính lại phần trăm thành thạo của user trong một chương.
     * Công thức: phan_tram = so_lan_dung / (so_lan_dung + so_lan_sai) * 100
     */
    public function capNhatTienDo(int $nguoiDungId, int $chuongId): UserSubSubjectProgress
    {
        // Lấy tổng hợp từ thong_ke_cau_hoi cho chương này
        $tongHop = DB::table('thong_ke_cau_hoi as tkch')
            ->join('cau_hoi as ch', 'ch.id', '=', 'tkch.cau_hoi_id')
            ->where('tkch.nguoi_dung_id', $nguoiDungId)
            ->where('ch.chuong_id', $chuongId)
            ->selectRaw('SUM(so_lan_dung) as tong_dung, SUM(so_lan_sai) as tong_sai, COUNT(*) as tong_cau')
            ->first();

        $tongDung = $tongHop->tong_dung ?? 0;
        $tongSai  = $tongHop->tong_sai  ?? 0;
        $tongLam  = $tongDung + $tongSai;

        $phanTram = $tongLam > 0
            ? round(($tongDung / $tongLam) * 100, 2)
            : 0.00;

        return UserSubSubjectProgress::updateOrCreate(
            ['nguoi_dung_id' => $nguoiDungId, 'chuong_id' => $chuongId],
            [
                'tong_da_lam'          => (int) $tongHop->tong_cau,
                'phan_tram_thanh_thao' => $phanTram,
            ]
        );
    }

    // ===================================================================
    // LẤY DỮ LIỆU CHO DASHBOARD
    // ===================================================================

    /**
     * Trả dữ liệu Radar Chart theo môn học.
     * Format: [{chuong: 'Chương 1', phan_tram: 80.5}, ...]
     */
    public function layDuLieuRadar(User $user, Subject $monHoc): array
    {
        $chuongs = $monHoc->chuong()->orderBy('thu_tu')->get();

        return $chuongs->map(function ($chuong) use ($user) {
            $tienDo = UserSubSubjectProgress::where('nguoi_dung_id', $user->id)
                ->where('chuong_id', $chuong->id)
                ->first();

            return [
                'chuong_id'  => $chuong->id,
                'ten_chuong' => $chuong->ten,
                'phan_tram'  => $tienDo?->phan_tram_thanh_thao ?? 0,
                'tong_da_lam'=> $tienDo?->tong_da_lam ?? 0,
            ];
        })->toArray();
    }

    /**
     * Trả dữ liệu Mistake Heatmap.
     * Format: [{cau_hoi_id, noi_dung, so_lan_sai, mau_sac: 'do'|'cam'}]
     * - Đỏ: >= 3 lần sai
     * - Cam: 1-2 lần sai
     */
    public function layDanhSachCauSai(User $user): Collection
    {
        return $this->cauHoiRepo->layCauHoiSai($user->id)
            ->map(function ($cauHoi) {
                $soLanSai = $cauHoi->thongKe->first()?->so_lan_sai ?? 0;
                return [
                    'cau_hoi_id'  => $cauHoi->id,
                    'noi_dung'    => mb_substr($cauHoi->noi_dung, 0, 120) . '...',
                    'chuong'      => $cauHoi->chuong?->ten,
                    'mon_hoc'     => $cauHoi->chuong?->monHoc?->ten,
                    'so_lan_sai'  => $soLanSai,
                    'mau_sac'     => $soLanSai >= 3 ? 'do' : 'cam', // đỏ / cam
                ];
            });
    }

    /**
     * Trả hàng đợi câu hỏi cho Revenge Mode (Spaced Repetition).
     * Lấy câu sai >= 1 lần, ưu tiên sai nhiều nhất & lâu nhất chưa ôn.
     */
    public function layHangDoiOnTap(User $user, ?int $monHocId = null): Collection
    {
        return $this->cauHoiRepo->layChoONTap($user->id, $monHocId)
            ->map(function ($cauHoi) {
                $thongKe = $cauHoi->thongKe->first();
                return [
                    'cau_hoi'      => $cauHoi,
                    'so_lan_sai'   => $thongKe?->so_lan_sai ?? 0,
                    'so_lan_dung'  => $thongKe?->so_lan_dung ?? 0,
                    'lan_cuoi_lam' => $thongKe?->lan_cuoi_lam,
                    'la_diem_yeu'  => ($thongKe?->so_lan_sai ?? 0) >= 3,
                ];
            });
    }

    /**
     * Lấy tổng quan tiến độ của user theo môn học (cho teacher dashboard).
     */
    public function layTongQuanUser(User $user): array
    {
        $tongLuotThi   = $user->luotThi()->count();
        $diemTrungBinh = $user->luotThi()
            ->where('trang_thai', 'hoan_thanh')
            ->avg('diem_so');

        $soMonDaHoc = UserSubSubjectProgress::where('nguoi_dung_id', $user->id)
            ->where('tong_da_lam', '>', 0)
            ->distinct('chuong_id')
            ->count();

        $cauHoiDiemYeu = UserQuestionStat::where('nguoi_dung_id', $user->id)
            ->where('so_lan_sai', '>=', 3)
            ->count();

        return [
            'tong_luot_thi'   => $tongLuotThi,
            'diem_trung_binh' => round($diemTrungBinh ?? 0, 2),
            'so_chuong_da_hoc'=> $soMonDaHoc,
            'so_cau_diem_yeu' => $cauHoiDiemYeu,
        ];
    }
}
