<?php

namespace App\Livewire\Student;

use App\Models\SpacedRepetitionSchedule;
use App\Models\User;
use App\Services\ProgressTrackingService;
use App\Services\SpacedRepetitionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Ôn tập lặp lại')]
class SpacedRepetition extends Component
{
    public ?int $currentScheduleId = null;

    public int $sessionCount = 0;

    public int $sessionTotal = 0;

    public bool $done = false;

    public array $ratingCounts = [
        'hard' => 0,
        'medium' => 0,
        'easy' => 0,
    ];

    public function mount(ProgressTrackingService $progressTracking): void
    {
        $this->loadDueQueue($progressTracking, true);
    }

    public function rateCard(
        string $rating,
        SpacedRepetitionService $spacedRepetition,
        ProgressTrackingService $progressTracking,
    ): void {
        if (! in_array($rating, ['hard', 'medium', 'easy'], true) || ! $this->currentScheduleId) {
            return;
        }

        $schedule = SpacedRepetitionSchedule::query()
            ->where('id', $this->currentScheduleId)
            ->where('nguoi_dung_id', Auth::id())
            ->firstOrFail();

        $spacedRepetition->review($schedule, $rating);
        $this->sessionCount++;
        $this->ratingCounts[$rating]++;
        $this->loadDueQueue($progressTracking);
    }

    private function loadDueQueue(ProgressTrackingService $progressTracking, bool $newSession = false): void
    {
        /** @var User $user */
        $user = Auth::user();
        $queue = $progressTracking->layHangDoiOnTap($user);

        if ($newSession) {
            $this->sessionTotal = $queue->count();
        }

        $this->currentScheduleId = $queue->first()['schedule']->id ?? null;
        $this->done = $this->currentScheduleId === null;
    }

    public function render()
    {
        $currentCard = null;

        if ($this->currentScheduleId) {
            $schedule = SpacedRepetitionSchedule::query()
                ->where('id', $this->currentScheduleId)
                ->where('nguoi_dung_id', Auth::id())
                ->with(['cauHoi.luaChon', 'cauHoi.chuong.monHoc'])
                ->first();

            if ($schedule?->cauHoi) {
                $cauHoi = $schedule->cauHoi;
                $currentCard = (object) [
                    'id' => $schedule->id,
                    'subject' => $cauHoi->chuong?->monHoc?->ten ?? 'Môn học',
                    'question' => $cauHoi->noi_dung,
                    'answer' => $cauHoi->luaChon
                        ->where('la_dap_an', true)
                        ->pluck('noi_dung')
                        ->implode(' · ') ?: 'Chưa có đáp án được cấu hình.',
                    'explanation' => $cauHoi->giai_thich,
                ];
            }
        }

        return view('livewire.student.spaced-repetition', [
            'currentCard' => $currentCard,
            'currentPosition' => $currentCard ? $this->sessionCount + 1 : $this->sessionCount,
            'totalCards' => $this->sessionTotal,
        ]);
    }
}
