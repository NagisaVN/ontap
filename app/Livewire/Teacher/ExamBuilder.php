<?php

namespace App\Livewire\Teacher;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ExamBuilder extends Component
{
    public string $search = '';
    public string $subject = '';
    public string $difficulty = '';
    public string $title = 'New Exam Paper';
    public int    $timeLimit = 60;
    public int    $maxAttempts = 1;
    public string $examCode = '';
    public array  $selected = [];

    public function mount(): void
    {
        $this->generateExamCode();
    }

    public function updatedSearch(): void    {}
    public function updatedSubject(): void   {}
    public function updatedDifficulty(): void {}

    public function toggleQuestion(int $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_filter($this->selected, fn($s) => $s !== $id));
        } else {
            $this->selected[] = $id;
        }
    }

    public function generateExamCode(): void
    {
        $this->examCode = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
    }

    public function incrementTimeLimit(): void  { $this->timeLimit  = min(180, $this->timeLimit  + 5); }
    public function decrementTimeLimit(): void  { $this->timeLimit  = max(5,   $this->timeLimit  - 5); }
    public function incrementAttempts(): void   { $this->maxAttempts = min(10, $this->maxAttempts + 1); }
    public function decrementAttempts(): void   { $this->maxAttempts = max(1,  $this->maxAttempts - 1); }

    protected function getBank(): array
    {
        // TODO: replace with real Question model query
        return [
            ['id' => 1,  'preview' => 'What is Faraday\'s Law of Electromagnetic Induction?', 'subject' => 'Physics',     'difficulty' => 'Medium'],
            ['id' => 2,  'preview' => 'Solve: ∫ x² dx from 0 to 3',                           'subject' => 'Mathematics', 'difficulty' => 'Hard'],
            ['id' => 3,  'preview' => 'What is an exothermic reaction?',                       'subject' => 'Chemistry',   'difficulty' => 'Easy'],
            ['id' => 4,  'preview' => 'State Newton\'s Second Law of Motion.',                 'subject' => 'Physics',     'difficulty' => 'Easy'],
        ];
    }

    public function render()
    {
        $bank = collect($this->getBank())
            ->when($this->search,     fn($c) => $c->filter(fn($q) => str_contains(strtolower($q['preview']), strtolower($this->search))))
            ->when($this->subject,    fn($c) => $c->where('subject', $this->subject))
            ->when($this->difficulty, fn($c) => $c->where('difficulty', $this->difficulty));

        $selectedQuestions = collect($this->getBank())->whereIn('id', $this->selected)->values()->toArray();

        return view('livewire.teacher.exam-builder', [
            'filteredBank'      => $bank->values()->toArray(),
            'selectedQuestions' => $selectedQuestions,
            'selected'          => $this->selected,
            'timeLimit'         => $this->timeLimit,
            'maxAttempts'       => $this->maxAttempts,
            'examCode'          => $this->examCode,
            'title'             => $this->title,
        ]);
    }
}
