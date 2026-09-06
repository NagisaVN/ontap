<?php

namespace App\Livewire\Student;

use App\Enums\AttemptStatus;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tổng quan')]
class Dashboard extends Component
{
    public function render()
    {
        $userId = Auth::id();

        // ── Bài thi đang dở ────────────────────────────────
        $continueItems = ExamAttempt::with(['baiThi.monHoc'])
            ->where('nguoi_dung_id', $userId)
            ->where('trang_thai', AttemptStatus::DangLam)
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(function (ExamAttempt $lt) {
                $baiThi = $lt->baiThi;
                $total  = $baiThi->so_cau_hoi ?? 0;

                // Đếm câu đã trả lời từ cache auto-save
                $cached        = cache("luot_thi_{$lt->id}_tu_dong_luu") ?? [];
                $answeredCount = count($cached);
                $progress      = $total > 0 ? round($answeredCount / $total * 100) : 0;

                // Thời gian đã dùng
                $elapsed = $lt->bat_dau_luc
                    ? now()->diffInMinutes($lt->bat_dau_luc)
                    : 0;

                return [
                    'luot_thi_id' => $lt->id,
                    'bai_thi_id'  => $baiThi->id,
                    'topic'       => $baiThi->ten_bai_thi,
                    'subject'     => $baiThi->monHoc?->ten ?? 'Môn học',
                    'progress'    => $progress,
                    'questions'   => $answeredCount,
                    'total'       => $total,
                    'elapsed_min' => $elapsed,
                    'started_at'  => $lt->bat_dau_luc?->diffForHumans(),
                ];
            });

        // ── Streak heatmap (8 tuần × 7 ngày) ──────────────
        // TODO: thay bằng dữ liệu thực từ luot_thi khi có thêm tracking
        $streakData = array_map(
            fn() => array_map(fn() => rand(0, 4), range(0, 6)),
            range(0, 7)
        );

        return view('livewire.student.dashboard', compact('streakData', 'continueItems'));
    }
}
