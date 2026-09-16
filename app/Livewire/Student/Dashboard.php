<?php

namespace App\Livewire\Student;

use App\Enums\AttemptStatus;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use Carbon\Carbon;
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
        $today = now()->startOfDay();
        $weekStart = $today->copy()->startOfWeek(Carbon::MONDAY);
        $previousWeekStart = $weekStart->copy()->subWeek();

        $continueItems = ExamAttempt::with(['baiThi.monHoc'])
            ->where('nguoi_dung_id', $userId)
            ->where('trang_thai', AttemptStatus::DangLam)
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(function (ExamAttempt $attempt) {
                $exam = $attempt->baiThi;
                $total = $exam->so_cau_hoi ?? 0;
                $answers = cache("luot_thi_{$attempt->id}_tu_dong_luu") ?? [];
                $answeredCount = count($answers);

                return [
                    'luot_thi_id' => $attempt->id,
                    'bai_thi_id' => $exam->id,
                    'topic' => $exam->ten_bai_thi,
                    'subject' => $exam->monHoc?->ten ?? 'Môn học',
                    'progress' => $total > 0 ? round($answeredCount / $total * 100) : 0,
                    'questions' => $answeredCount,
                    'total' => $total,
                    'started_at' => $attempt->bat_dau_luc?->diffForHumans(),
                ];
            });

        $completedAttempts = ExamAttempt::query()
            ->where('nguoi_dung_id', $userId)
            ->where('trang_thai', AttemptStatus::HoanThanh);

        $completedCount = (clone $completedAttempts)->count();
        $thisWeekCount = (clone $completedAttempts)
            ->whereBetween('ket_thuc_luc', [$weekStart, $weekStart->copy()->endOfWeek()])
            ->count();
        $lastWeekCount = (clone $completedAttempts)
            ->whereBetween('ket_thuc_luc', [$previousWeekStart, $weekStart->copy()->subSecond()])
            ->count();
        $currentWeekScores = (clone $completedAttempts)
            ->whereBetween('ket_thuc_luc', [$weekStart, $weekStart->copy()->endOfWeek()])
            ->pluck('diem_so');
        $lastWeekScores = (clone $completedAttempts)
            ->whereBetween('ket_thuc_luc', [$previousWeekStart, $weekStart->copy()->subSecond()])
            ->pluck('diem_so');

        $answers = ExamAttemptAnswer::query()
            ->join('luot_thi', 'ket_qua.luot_thi_id', '=', 'luot_thi.id')
            ->where('luot_thi.nguoi_dung_id', $userId)
            ->where('luot_thi.trang_thai', AttemptStatus::HoanThanh->value);
        $answerCount = (clone $answers)->count();
        $correctAnswers = (clone $answers)->where('ket_qua.dung_sai', true)->count();

        $stats = [
            'completedCount' => $completedCount,
            'thisWeekCount' => $thisWeekCount,
            'lastWeekCount' => $lastWeekCount,
            'averageScore' => $completedCount > 0
                ? round((float) (clone $completedAttempts)->avg('diem_so') * 10, 1)
                : null,
            'accuracy' => $answerCount > 0 ? round($correctAnswers / $answerCount * 100, 1) : null,
            'scoreDelta' => $this->percentageDelta($currentWeekScores, $lastWeekScores),
            'accuracyDelta' => $this->ratioDelta(
                (clone $answers)->whereBetween('luot_thi.ket_thuc_luc', [$weekStart, $weekStart->copy()->endOfWeek()]),
                (clone $answers)->whereBetween('luot_thi.ket_thuc_luc', [$previousWeekStart, $weekStart->copy()->subSecond()]),
            ),
        ];

        // Activity is any attempt that was started, including unfinished practice.
        $gridStart = $weekStart->copy()->subWeeks(7);
        $activityByDate = ExamAttempt::query()
            ->selectRaw('DATE(COALESCE(bat_dau_luc, created_at)) as activity_date, COALESCE(SUM(thoi_gian_lam), 0) as seconds')
            ->where('nguoi_dung_id', $userId)
            ->whereBetween('bat_dau_luc', [$gridStart, $weekStart->copy()->endOfWeek()])
            ->groupByRaw('DATE(COALESCE(bat_dau_luc, created_at))')
            ->pluck('seconds', 'activity_date')
            ->all();

        $streakData = [];
        for ($week = 0; $week < 8; $week++) {
            $days = [];
            for ($day = 0; $day < 7; $day++) {
                $date = $gridStart->copy()->addWeeks($week)->addDays($day)->toDateString();
                $days[] = $this->heatmapIntensity((int) ($activityByDate[$date] ?? 0));
            }
            $streakData[] = $days;
        }

        // A streak remains visible until the student misses a full calendar day.
        $streakAnchor = array_key_exists($today->toDateString(), $activityByDate)
            ? $today->copy()
            : $today->copy()->subDay();
        $streakDays = 0;
        for ($date = $streakAnchor; $date->greaterThanOrEqualTo($gridStart); $date->subDay()) {
            if (! array_key_exists($date->toDateString(), $activityByDate)) {
                break;
            }
            $streakDays++;
        }

        return view('livewire.student.dashboard', compact('streakData', 'continueItems', 'stats', 'streakDays'));
    }

    private function percentageDelta($current, $previous): ?array
    {
        if ($current->isEmpty() || $previous->isEmpty()) {
            return null;
        }

        $delta = round(((float) $current->avg() - (float) $previous->avg()) * 10, 1);

        return ['value' => $delta, 'positive' => $delta >= 0];
    }

    private function ratioDelta($current, $previous): ?array
    {
        $currentTotal = (clone $current)->count();
        $previousTotal = (clone $previous)->count();

        if ($currentTotal === 0 || $previousTotal === 0) {
            return null;
        }

        $currentRate = (clone $current)->where('ket_qua.dung_sai', true)->count() / $currentTotal * 100;
        $previousRate = (clone $previous)->where('ket_qua.dung_sai', true)->count() / $previousTotal * 100;
        $delta = round($currentRate - $previousRate, 1);

        return ['value' => $delta, 'positive' => $delta >= 0];
    }

    private function heatmapIntensity(int $seconds): int
    {
        return match (true) {
            $seconds <= 0 => 0,
            $seconds < 10 * 60 => 1,
            $seconds < 30 * 60 => 2,
            $seconds < 60 * 60 => 3,
            default => 4,
        };
    }
}
