<?php

namespace App\Livewire\Student;

use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Question;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kết quả bài thi')]
class ExamResult extends Component
{
    public ExamAttempt $luotThi;

    public function mount(ExamAttempt $luotThi): void
    {
        // Bảo mật: chỉ xem kết quả của chính mình
        abort_unless($luotThi->nguoi_dung_id === Auth::id(), 403);

        $this->luotThi = $luotThi;
    }

    public function render()
    {
        // Refresh để khi Livewire poll, dữ liệu ketQua (chứa giai_thich_ai) sẽ được cập nhật
        $this->luotThi->refresh()->load([
            'baiThi.monHoc',
            'ketQua.cauHoi.luaChon',
            'ketQua.luaChonDaChon',
        ]);
        $baiThi   = $this->luotThi->baiThi;
        $tongCau  = $this->luotThi->ketQua->count();
        $soDung   = $this->luotThi->so_cau_dung;
        $soSai    = $tongCau - $soDung;
        $soBo     = $this->luotThi->ketQua->whereNull('lua_chon_id')->count();

        $rank = match(true) {
            $this->luotThi->diem_so >= 9  => ['label' => 'Xuất sắc', 'color' => 'green', 'emoji' => '🏆'],
            $this->luotThi->diem_so >= 7  => ['label' => 'Giỏi',     'color' => 'indigo','emoji' => '🌟'],
            $this->luotThi->diem_so >= 5  => ['label' => 'Đạt',      'color' => 'yellow','emoji' => '👍'],
            default                        => ['label' => 'Cần cố gắng', 'color' => 'red', 'emoji' => '💪'],
        };

        return view('livewire.student.exam-result', compact(
            'baiThi', 'tongCau', 'soDung', 'soSai', 'soBo', 'rank'
        ));
    }

    public function generateExplanation(int $ketQuaId, GeminiAIService $gemini)
    {
        $ketQua = ExamAttemptAnswer::with('cauHoi.luaChon')
            ->where('luot_thi_id', $this->luotThi->id)
            ->find($ketQuaId);

        if (!$ketQua || $ketQua->giai_thich_ai) {
            return;
        }

        try {
            // Chuẩn bị dữ liệu cho AI
            $cauHoi = $ketQua->cauHoi;
            $luaChonDaChon = $ketQua->luaChonDaChon;
            $luaChonDungDan = $cauHoi->luaChon->firstWhere('la_dap_an', true);
            
            $noiDungCauHoi = $cauHoi->noi_dung;
            $noiDungDaSai = $luaChonDaChon ? $luaChonDaChon->noi_dung : 'Không chọn đáp án nào (bỏ trống)';
            $noiDungDungDan = $luaChonDungDan ? $luaChonDungDan->noi_dung : 'Không có đáp án đúng';
            $monHoc = $this->luotThi->baiThi->monHoc?->ten ?? '';

            // Gọi AI giải thích
            $giaiThich = $gemini->giaiThichCauHoi(
                $noiDungCauHoi, 
                $noiDungDaSai, 
                $noiDungDungDan, 
                $monHoc
            );
            
            // Lưu và cập nhật UI
            $ketQua->update(['giai_thich_ai' => $giaiThich]);

            // Flash message success using simple UI state
            $this->dispatch('explanation-generated', ketQuaId: $ketQuaId);
            
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
            if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'Too Many Requests') || str_contains($errorMsg, 'quota')) {
                $delay = 60;
                if (preg_match('/Please retry in ([\d\.]+)s/', $errorMsg, $matches)) {
                    $delay = (int) ceil((float) $matches[1]);
                }
                // Flash error cho user biết thời gian chờ chính xác
                session()->flash('error_' . $ketQuaId, "Hệ thống AI đang tạm hết lượt (Quota). Vui lòng đợi đúng {$delay} giây rồi bấm lại!");
            } else {
                session()->flash('error_' . $ketQuaId, 'Có lỗi xảy ra khi gọi AI: ' . $errorMsg);
            }
        }
    }
}
