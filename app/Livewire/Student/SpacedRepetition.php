<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SpacedRepetition extends Component
{
    public int $index = 0;
    public bool $done = false;
    public array $history = [];

    /** Placeholder deck — replace with real FlashCard model */
    protected array $deck = [
        ['id' => 1, 'subject' => 'Physics', 'front' => 'What is Faraday\'s Law?', 'back' => 'The EMF induced in a loop is proportional to the rate of change of magnetic flux through the loop.'],
        ['id' => 2, 'subject' => 'Mathematics', 'front' => 'Derivative of sin(x)?', 'back' => 'cos(x)'],
        ['id' => 3, 'subject' => 'Chemistry', 'front' => 'What is an exothermic reaction?', 'back' => 'A reaction that releases heat energy to the surroundings (ΔH < 0).'],
    ];

    public array $ratings = [
        ['value' => 'easy',   'label' => '😄 Easy',   'color' => 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'],
        ['value' => 'medium', 'label' => '🤔 Unsure',  'color' => 'border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100'],
        ['value' => 'hard',   'label' => '😓 Hard',    'color' => 'border-rose-300 bg-rose-50 text-rose-700 hover:bg-rose-100'],
    ];

    public function handleRate(string $rating): void
    {
        $this->history[] = ['cardId' => $this->deck[$this->index]['id'], 'rating' => $rating];

        if ($this->index < count($this->deck) - 1) {
            $this->index++;
        } else {
            $this->done = true;
        }
    }

    public function startOver(): void
    {
        $this->index   = 0;
        $this->done    = false;
        $this->history = [];
    }

    public function render()
    {
        return view('livewire.student.spaced-repetition', [
            'card'       => $this->deck[$this->index] ?? null,
            'index'      => $this->index,
            'totalCards' => count($this->deck),
            'ratings'    => $this->ratings,
            'done'       => $this->done,
            'history'    => $this->history,
        ]);
    }
}
