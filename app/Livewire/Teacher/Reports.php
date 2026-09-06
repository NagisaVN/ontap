<?php

namespace App\Livewire\Teacher;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Reports extends Component
{
    public int $failThreshold = 50;

    public function render()
    {
        // TODO: replace with real DB queries (Attempt, Question, User models)
        $roster = [
            ['rank' => 1, 'name' => 'Tran Minh Khoa',   'score' => 92, 'exams' => 12, 'status' => 'active', 'avatar' => 'TK'],
            ['rank' => 2, 'name' => 'Le Thi Hoa',        'score' => 85, 'exams' => 10, 'status' => 'active', 'avatar' => 'LH'],
            ['rank' => 3, 'name' => 'Nguyen Van Giang',   'score' => 78, 'exams' => 11, 'status' => 'active', 'avatar' => 'NG'],
            ['rank' => 4, 'name' => 'Pham Quoc Bao',     'score' => 61, 'exams' => 8,  'status' => 'active', 'avatar' => 'PB'],
            ['rank' => 5, 'name' => 'Hoang Anh Thu',     'score' => 44, 'exams' => 7,  'status' => 'active', 'avatar' => 'HT'],
        ];

        $difficultyAnalysis = [
            ['question' => 'Derive the expression for escape velocity from first principles.', 'topic' => 'Gravitation',      'difficulty' => 'Hard',   'failRate' => 78],
            ['question' => 'What is Faraday\'s Law?',                                          'topic' => 'Electromagnetism', 'difficulty' => 'Medium', 'failRate' => 55],
            ['question' => 'Solve the integral ∫ sin²(x) dx',                                 'topic' => 'Integration',      'difficulty' => 'Hard',   'failRate' => 62],
            ['question' => 'Define an exothermic reaction.',                                   'topic' => 'Thermochemistry',  'difficulty' => 'Easy',   'failRate' => 18],
        ];

        return view('livewire.teacher.reports', compact('roster', 'difficultyAnalysis'));
    }
}
