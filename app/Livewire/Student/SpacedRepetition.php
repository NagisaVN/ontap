<?php

namespace App\Livewire\Student;

use App\Models\Subject;
use App\Services\ProgressTrackingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Ôn điểm yếu')]
class SpacedRepetition extends Component
{
    public int $selectedMonHocId = 0;

    public function render(ProgressTrackingService $progressService)
    {
        $user    = Auth::user();
        $monHocs = Subject::orderBy('ten')->get();
        $hangDoi = $progressService->layHangDoiOnTap($user, $this->selectedMonHocId ?: null);

        return view('livewire.student.spaced-repetition', compact('monHocs', 'hangDoi'));
    }
}
