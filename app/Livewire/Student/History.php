<?php

namespace App\Livewire\Student;

use App\Enums\AttemptStatus;
use App\Models\ExamAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Lịch sử & Phân tích')]
class History extends Component
{
    use WithPagination;

    public int $perPage = 5;

    public function render()
    {
        $userId = Auth::id();

        // ── Bài thi đã hoàn thành (paginated) ─────────────
        $examLog = ExamAttempt::with(['baiThi.monHoc'])
            ->where('nguoi_dung_id', $userId)
            ->where('trang_thai', AttemptStatus::HoanThanh)
            ->latest('ket_thuc_luc')
            ->paginate($this->perPage);

        // ── Thống kê điểm theo môn (radar chart) ──────────
        $allCompleted = ExamAttempt::with('baiThi.monHoc')
            ->where('nguoi_dung_id', $userId)
            ->where('trang_thai', AttemptStatus::HoanThanh)
            ->get();

        // Tổng hợp điểm user đã thi theo môn_hoc_id
        $userScoreBySubject = $allCompleted
            ->filter(fn($lt) => $lt->baiThi?->monHoc !== null)
            ->groupBy(fn($lt) => $lt->baiThi->monHoc->id)
            ->map(fn($group) => [
                'score' => round($group->avg(fn($lt) => ($lt->diem_so ?? 0) * 10)),
                'count' => $group->count(),
            ]);

        // Lấy TẤT CẢ môn học — môn chưa thi hiển thị 0%
        $subjectStats = \App\Models\Subject::orderBy('ten')->get()
            ->map(fn($subject) => [
                'subject' => $subject->ten,
                'score'   => $userScoreBySubject[$subject->id]['score'] ?? 0,
                'count'   => $userScoreBySubject[$subject->id]['count'] ?? 0,
            ])
            ->toArray();

        return view('livewire.student.history', compact('examLog', 'subjectStats'));
    }
}
