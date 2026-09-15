<?php

namespace App\Livewire\Teacher;

use App\Services\TeacherReportService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Báo cáo lớp học')]
class Reports extends Component
{
    public string $subjectId = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $failThreshold = 50;

    public function updatedFailThreshold(): void
    {
        $this->failThreshold = min(100, max(0, $this->failThreshold));
    }

    public function render(TeacherReportService $reports)
    {
        $filters = [
            'subject_id' => $this->subjectId,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'fail_threshold' => $this->failThreshold,
        ];

        return view('livewire.teacher.reports', [
            'report' => $reports->build(Auth::user(), $filters),
            'subjects' => $reports->subjectsFor(Auth::user()),
            'exportPdfUrl' => route('teacher.reports.export', array_filter([
                'format' => 'pdf',
                ...$filters,
            ], fn ($value) => $value !== '' && $value !== null)),
            'exportExcelUrl' => route('teacher.reports.export', array_filter([
                'format' => 'excel',
                ...$filters,
            ], fn ($value) => $value !== '' && $value !== null)),
        ]);
    }
}
