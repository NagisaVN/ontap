<?php

namespace App\Console\Commands;

use App\Models\SpacedRepetitionSchedule;
use App\Notifications\ReviewReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders {--force : Resend reminders already sent today}';

    protected $description = 'Email students with flashcards due for review';

    public function handle(): int
    {
        $query = SpacedRepetitionSchedule::query()
            ->due()
            ->with('nguoiDung');

        if (! $this->option('force')) {
            $query->where(function ($query) {
                $query->whereNull('reminder_sent_at')
                    ->orWhere('reminder_sent_at', '<', now()->startOfDay());
            });
        }

        $dueSchedules = $query->get()->groupBy('nguoi_dung_id');

        foreach ($dueSchedules as $schedules) {
            $user = $schedules->first()->nguoiDung;

            if (! $user?->email) {
                continue;
            }

            Notification::send($user, new ReviewReminderNotification($schedules->count()));

            $schedules->each->update(['reminder_sent_at' => now()]);
        }

        $this->info("Sent {$dueSchedules->count()} review reminder(s).");

        return self::SUCCESS;
    }
}
