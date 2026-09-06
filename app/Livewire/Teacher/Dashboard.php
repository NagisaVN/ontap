<?php

namespace App\Livewire\Teacher;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $timeline = [
            ['actor' => 'Admin', 'action' => 'approved question', 'subject' => 'Faraday\'s Law – Physics', 'time' => '5 min ago', 'avatar' => 'AD', 'color' => 'bg-indigo-100 text-indigo-700'],
            ['actor' => 'You',   'action' => 'uploaded 12 questions via OCR', 'subject' => 'Organic Chemistry', 'time' => '2 hours ago', 'avatar' => 'ME', 'color' => 'bg-emerald-100 text-emerald-700'],
            ['actor' => 'System','action' => 'generated exam paper', 'subject' => 'Mathematics – Calculus Final', 'time' => 'Yesterday', 'avatar' => 'SY', 'color' => 'bg-slate-100 text-slate-600'],
        ];

        return view('livewire.teacher.dashboard', compact('timeline'));
    }
}
