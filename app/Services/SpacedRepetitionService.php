<?php

namespace App\Services;

use App\Models\Question;
use App\Models\SpacedRepetitionSchedule;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SpacedRepetitionService
{
    public const DEFAULT_EASE_FACTOR = 2.50;

    public const MIN_EASE_FACTOR = 1.30;

    /**
     * A wrong answer creates a card that is due now. Existing cards keep their
     * SM-2 history, but are brought forward so a newly exposed weakness is not
     * hidden behind a future review date.
     */
    public function scheduleWrongAnswer(int $userId, int $questionId): SpacedRepetitionSchedule
    {
        return DB::transaction(function () use ($userId, $questionId) {
            $schedule = SpacedRepetitionSchedule::firstOrCreate(
                ['nguoi_dung_id' => $userId, 'cau_hoi_id' => $questionId],
                [
                    'interval_days' => 0,
                    'repetitions' => 0,
                    'ease_factor' => self::DEFAULT_EASE_FACTOR,
                    'next_review_at' => now(),
                ],
            );

            if ($schedule->next_review_at->isFuture()) {
                $schedule->update([
                    'next_review_at' => now(),
                    'reminder_sent_at' => null,
                ]);
            }

            return $schedule->fresh();
        });
    }

    /**
     * Creates due schedules for historical wrong answers. This makes the
     * feature safe to deploy against existing progress data.
     *
     * @param  Collection<int, Question>  $questions
     */
    public function synchronizeWrongAnswerSchedules(User $user, Collection $questions): void
    {
        $missingQuestionIds = $questions
            ->pluck('id')
            ->diff(
                SpacedRepetitionSchedule::where('nguoi_dung_id', $user->id)
                    ->whereIn('cau_hoi_id', $questions->pluck('id'))
                    ->pluck('cau_hoi_id'),
            );

        if ($missingQuestionIds->isEmpty()) {
            return;
        }

        $now = now();
        SpacedRepetitionSchedule::insertOrIgnore(
            $missingQuestionIds->map(fn (int $questionId) => [
                'nguoi_dung_id' => $user->id,
                'cau_hoi_id' => $questionId,
                'interval_days' => 0,
                'repetitions' => 0,
                'ease_factor' => self::DEFAULT_EASE_FACTOR,
                'next_review_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all(),
        );
    }

    /**
     * Apply the SM-2 formula after a user self-rates a flashcard.
     * hard=1, medium=3, easy=5 on the SuperMemo 0..5 quality scale.
     */
    public function review(SpacedRepetitionSchedule $schedule, string $rating): SpacedRepetitionSchedule
    {
        $quality = match ($rating) {
            'hard' => 1,
            'medium' => 3,
            'easy' => 5,
            default => throw new InvalidArgumentException('Invalid spaced-repetition rating.'),
        };

        return DB::transaction(function () use ($schedule, $quality) {
            /** @var SpacedRepetitionSchedule $lockedSchedule */
            $lockedSchedule = SpacedRepetitionSchedule::query()
                ->lockForUpdate()
                ->findOrFail($schedule->id);

            $easeFactor = max(
                self::MIN_EASE_FACTOR,
                $lockedSchedule->ease_factor + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02)),
            );

            if ($quality < 3) {
                $repetitions = 0;
                $intervalDays = 1;
            } else {
                $repetitions = $lockedSchedule->repetitions + 1;
                $intervalDays = match ($repetitions) {
                    1 => 1,
                    2 => 6,
                    default => max(1, (int) round($lockedSchedule->interval_days * $easeFactor)),
                };
            }

            $reviewedAt = now();
            $lockedSchedule->update([
                'interval_days' => $intervalDays,
                'repetitions' => $repetitions,
                'ease_factor' => round($easeFactor, 2),
                'last_reviewed_at' => $reviewedAt,
                'next_review_at' => $reviewedAt->copy()->addDays($intervalDays),
                'reminder_sent_at' => null,
            ]);

            return $lockedSchedule->fresh();
        });
    }
}
