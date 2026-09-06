<?php

namespace App\Livewire\Teacher;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QuestionCreator extends Component
{
    public string $type = 'mcq';
    public string $question = '';
    public array  $options = ['', '', '', ''];
    public int    $correct = 0;
    public string $subject = '';
    public string $difficulty = 'Medium';

    public array $subjects    = ['Mathematics', 'Physics', 'Chemistry', 'Biology'];
    public array $difficulties = ['Easy', 'Medium', 'Hard'];

    public function addOption(): void
    {
        if (count($this->options) < 6) {
            $this->options[] = '';
        }
    }

    public function removeOption(int $index): void
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);

        if ($this->correct >= count($this->options)) {
            $this->correct = 0;
        }
    }

    public function updatedType(): void
    {
        $this->correct = 0;
        $this->options = $this->type === 'tf' ? ['True', 'False'] : ['', '', '', ''];
    }

    public function handleSave(): void
    {
        $this->validate([
            'question'   => 'required|min:5',
            'subject'    => 'required',
            'difficulty' => 'required',
            'options'    => 'required|array|min:2',
        ]);

        // TODO: Question::create([...]) with authenticated teacher_id
        session()->flash('status', 'saved');
        $this->reset(['question', 'options', 'correct']);
        $this->options = ['', '', '', ''];
    }

    public function render()
    {
        return view('livewire.teacher.question-creator');
    }
}
