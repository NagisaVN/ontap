<?php

namespace App\Services;

use App\Enums\AttemptStatus;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Subject;
use App\Models\User;
use App\Models\UserQuestionStat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeacherReportService
{
    public function subjectsFor(User $user): Collection
    {
        return Subject::query()
            ->whereIn('id', $this->examScope($user)->select('bai_thi.mon_hoc_id'))
            ->orderBy('ten')
            ->get(['id', 'ten', 'ma_mon']);
    }

    /**
     * Build all report data from completed exam attempts visible to a teacher.
     * A cohort is the students who completed an exam created by that teacher.
     * Super administrators can view the system-wide cohort.
     */
    public function build(User $user, array $filters = []): array
    {
        $subjectId = filled($filters['subject_id'] ?? null) ? (int) $filters['subject_id'] : null;
        $dateFrom = filled($filters['date_from'] ?? null) ? $filters['date_from'] : null;
        $dateTo = filled($filters['date_to'] ?? null) ? $filters['date_to'] : null;
        $failThreshold = min(100, max(0, (int) ($filters['fail_threshold'] ?? 50)));

        $attempts = $this->attemptScope($user, $subjectId, $dateFrom, $dateTo);
        $scores = (clone $attempts)
            ->pluck('luot_thi.diem_so')
            ->map(fn ($score) => round((float) $score * 10, 1));

        $roster = (clone $attempts)
            ->join('users', 'users.id', '=', 'luot_thi.nguoi_dung_id')
            ->select([
                'users.id as user_id',
                'users.name',
                'users.is_active',
                DB::raw('COUNT(luot_thi.id) as exam_count'),
                DB::raw('AVG(luot_thi.diem_so) * 10 as average_score'),
            ])
            ->groupBy('users.id', 'users.name', 'users.is_active')
            ->orderByDesc('average_score')
            ->orderByDesc('exam_count')
            ->get()
            ->values()
            ->map(function (object $student, int $index) {
                $score = round((float) $student->average_score, 1);

                return [
                    'rank' => $index + 1,
                    'name' => $student->name,
                    'initials' => $this->initials($student->name),
                    'score' => $score,
                    'exams' => (int) $student->exam_count,
                    'status' => (bool) $student->is_active ? 'active' : 'inactive',
                ];
            })
            ->all();

        // Retrieve the cohort IDs separately because the public roster payload
        // deliberately does not expose IDs to the Blade view.
        $participantIds = (clone $attempts)->distinct()->pluck('luot_thi.nguoi_dung_id');
        $items = $this->itemAnalysis($attempts, $participantIds);

        return [
            'title' => 'Báo cáo lớp & kết quả học tập',
            'subjectName' => $subjectId
                ? Subject::find($subjectId)?->ten ?? 'Môn học đã chọn'
                : 'Tất cả môn học',
            'periodLabel' => $this->periodLabel($dateFrom, $dateTo),
            'cohortLabel' => $user->hasRole('super_admin')
                ? 'Toàn bộ học viên có lượt thi hoàn thành'
                : 'Học viên đã làm đề do bạn tạo',
            'filters' => [
                'subject_id' => $subjectId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'fail_threshold' => $failThreshold,
            ],
            'summary' => [
                'students' => count($roster),
                'attempts' => $scores->count(),
                'averageScore' => round((float) $scores->avg(), 1),
                'passRate' => $scores->isNotEmpty()
                    ? round(($scores->filter(fn (float $score) => $score >= 50)->count() / $scores->count()) * 100, 1)
                    : 0,
            ],
            'roster' => $roster,
            'scoreDistribution' => $this->scoreDistribution($scores),
            'difficultyAnalysis' => $items,
        ];
    }

    private function examScope(User $user): Builder
    {
        return Exam::query()
            ->when(
                ! $user->hasRole('super_admin'),
                fn (Builder $query) => $query->where('bai_thi.nguoi_dung_id', $user->id),
            );
    }

    private function attemptScope(User $user, ?int $subjectId, ?string $dateFrom, ?string $dateTo): Builder
    {
        return ExamAttempt::query()
            ->join('bai_thi', 'bai_thi.id', '=', 'luot_thi.bai_thi_id')
            ->where('luot_thi.trang_thai', AttemptStatus::HoanThanh->value)
            ->when(
                ! $user->hasRole('super_admin'),
                fn (Builder $query) => $query->where('bai_thi.nguoi_dung_id', $user->id),
            )
            ->when($subjectId, fn (Builder $query) => $query->where('bai_thi.mon_hoc_id', $subjectId))
            ->when($dateFrom, fn (Builder $query) => $query->whereDate('luot_thi.ket_thuc_luc', '>=', $dateFrom))
            ->when($dateTo, fn (Builder $query) => $query->whereDate('luot_thi.ket_thuc_luc', '<=', $dateTo));
    }

    private function itemAnalysis(Builder $attempts, Collection $participantIds): array
    {
        $attemptIds = (clone $attempts)->select('luot_thi.id');

        $items = ExamAttemptAnswer::query()
            ->joinSub($attemptIds, 'bao_cao_luot_thi', function ($join) {
                $join->on('bao_cao_luot_thi.id', '=', 'ket_qua.luot_thi_id');
            })
            ->join('cau_hoi', 'cau_hoi.id', '=', 'ket_qua.cau_hoi_id')
            ->join('chuong', 'chuong.id', '=', 'cau_hoi.chuong_id')
            ->join('mon_hoc', 'mon_hoc.id', '=', 'chuong.mon_hoc_id')
            ->whereNull('cau_hoi.deleted_at')
            ->select([
                'ket_qua.cau_hoi_id',
                'cau_hoi.noi_dung as question',
                'cau_hoi.do_kho as difficulty',
                'chuong.ten as topic',
                'mon_hoc.ten as subject',
                DB::raw('COUNT(ket_qua.id) as response_count'),
                DB::raw('SUM(CASE WHEN ket_qua.dung_sai IS FALSE THEN 1 ELSE 0 END) as wrong_count'),
            ])
            ->groupBy('ket_qua.cau_hoi_id', 'cau_hoi.noi_dung', 'cau_hoi.do_kho', 'chuong.ten', 'mon_hoc.ten')
            ->get();

        $practiceStats = $this->practiceStats($items->pluck('cau_hoi_id'), $participantIds);

        return $items
            ->map(function (object $item) use ($practiceStats) {
                $responses = (int) $item->response_count;
                $wrong = (int) $item->wrong_count;
                $practice = $practiceStats->get($item->cau_hoi_id);
                $practiceTotal = (int) ($practice->practice_total ?? 0);
                $practiceWrong = (int) ($practice->practice_wrong ?? 0);

                return [
                    'questionId' => (int) $item->cau_hoi_id,
                    'question' => $item->question,
                    'topic' => $item->topic,
                    'subject' => $item->subject,
                    'difficulty' => $this->difficultyLabel($item->difficulty),
                    'responses' => $responses,
                    'wrongCount' => $wrong,
                    'failRate' => $responses > 0 ? round(($wrong / $responses) * 100, 1) : 0,
                    'practiceAttempts' => $practiceTotal,
                    'practiceFailRate' => $practiceTotal > 0 ? round(($practiceWrong / $practiceTotal) * 100, 1) : null,
                ];
            })
            ->sort(function (array $left, array $right) {
                return [$right['failRate'], $right['wrongCount']]
                    <=> [$left['failRate'], $left['wrongCount']];
            })
            ->values()
            ->take(50)
            ->all();
    }

    private function practiceStats(Collection $questionIds, Collection $participantIds): Collection
    {
        if ($questionIds->isEmpty() || $participantIds->isEmpty()) {
            return collect();
        }

        return UserQuestionStat::query()
            ->select([
                'cau_hoi_id',
                DB::raw('SUM(so_lan_sai + so_lan_dung) as practice_total'),
                DB::raw('SUM(so_lan_sai) as practice_wrong'),
            ])
            ->whereIn('cau_hoi_id', $questionIds)
            ->whereIn('nguoi_dung_id', $participantIds)
            ->groupBy('cau_hoi_id')
            ->get()
            ->keyBy('cau_hoi_id');
    }

    private function scoreDistribution(Collection $scores): array
    {
        $buckets = [
            ['label' => '0–49', 'min' => 0, 'max' => 49.999],
            ['label' => '50–64', 'min' => 50, 'max' => 64.999],
            ['label' => '65–79', 'min' => 65, 'max' => 79.999],
            ['label' => '80–100', 'min' => 80, 'max' => 100],
        ];

        return collect($buckets)->map(function (array $bucket) use ($scores) {
            $count = $scores
                ->filter(fn (float $score) => $score >= $bucket['min'] && $score <= $bucket['max'])
                ->count();

            return [
                'label' => $bucket['label'],
                'count' => $count,
                'percentage' => $scores->isNotEmpty() ? round(($count / $scores->count()) * 100, 1) : 0,
            ];
        })->all();
    }

    private function initials(string $name): string
    {
        $words = preg_split('/\s+/u', trim($name)) ?: [];
        $first = $words[0] ?? '?';
        $last = $words[count($words) - 1] ?? $first;

        return mb_strtoupper(mb_substr($first, 0, 1).mb_substr($last, 0, 1));
    }

    private function difficultyLabel(string $difficulty): string
    {
        return match ($difficulty) {
            'kho' => 'Khó',
            'trung_binh' => 'Trung bình',
            default => 'Dễ',
        };
    }

    private function periodLabel(?string $dateFrom, ?string $dateTo): string
    {
        return match (true) {
            $dateFrom && $dateTo => "Từ {$dateFrom} đến {$dateTo}",
            $dateFrom !== null => "Từ {$dateFrom}",
            $dateTo !== null => "Đến {$dateTo}",
            default => 'Tất cả thời gian',
        };
    }
}
