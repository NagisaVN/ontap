<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly int $dueCount) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cardLabel = $this->dueCount === 1 ? 'thẻ' : 'thẻ';

        return (new MailMessage)
            ->subject('Bạn có thẻ ôn tập đến hạn')
            ->greeting("Chào {$notifiable->name}!")
            ->line("Hôm nay bạn có {$this->dueCount} {$cardLabel} cần ôn lại.")
            ->line('Một phiên ngắn bây giờ sẽ giúp bạn nhớ lâu hơn.')
            ->action('Ôn tập ngay', route('student.on-tap'));
    }
}
