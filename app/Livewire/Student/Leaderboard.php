<?php

namespace App\Livewire\Student;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Leaderboard extends Component
{
    public string $tab = 'week';

    public int $myRank = 3; // TODO: compute from Auth::id()

    public function render()
    {
        $weeklyLeaders = [
            ['rank' => 1, 'name' => 'Tran Minh Khoa',  'score' => 5420, 'avatar' => 'TK', 'badge' => '🥇'],
            ['rank' => 2, 'name' => 'Le Thi Hoa',       'score' => 4980, 'avatar' => 'LH', 'badge' => '🥈'],
            ['rank' => 3, 'name' => 'Nguyen Van Giang',  'score' => 4721, 'avatar' => 'NG', 'badge' => '🥉'],
            ['rank' => 4, 'name' => 'Pham Quoc Bao',    'score' => 3890, 'avatar' => 'PB', 'badge' => null],
            ['rank' => 5, 'name' => 'Hoang Anh Thu',    'score' => 3210, 'avatar' => 'HT', 'badge' => null],
        ];

        $achievements = [
            ['icon' => '🔥', 'label' => '7-day streak',   'unlocked' => true],
            ['icon' => '📚', 'label' => 'First exam',      'unlocked' => true],
            ['icon' => '🏆', 'label' => 'Top 3',           'unlocked' => true],
            ['icon' => '⚡', 'label' => 'Speed demon',     'unlocked' => true],
            ['icon' => '🧠', 'label' => 'Perfect score',   'unlocked' => false],
            ['icon' => '💯', 'label' => '100 questions',   'unlocked' => false],
        ];

        $plans = [
            [
                'name' => 'Free', 'price' => '0', 'period' => '',
                'color' => 'border-slate-200', 'current' => true, 'badge' => null,
                'features' => ['5 exams / month', '100-question bank', 'Basic analytics'],
                'cta' => 'Current Plan', 'ctaClass' => 'bg-slate-100 text-slate-400 cursor-default',
            ],
            [
                'name' => 'Pro', 'price' => '99,000đ', 'period' => '/ month',
                'color' => 'border-indigo-400', 'current' => false, 'badge' => 'Most Popular',
                'features' => ['Unlimited exams', '10,000-question bank', 'Spaced repetition', 'AI hints'],
                'cta' => 'Upgrade to Pro', 'ctaClass' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
            ],
            [
                'name' => 'Team', 'price' => '199,000đ', 'period' => '/ month',
                'color' => 'border-slate-200', 'current' => false, 'badge' => null,
                'features' => ['Everything in Pro', 'Class management', 'Custom exam codes', 'Priority support'],
                'cta' => 'Upgrade to Team', 'ctaClass' => 'bg-slate-800 hover:bg-slate-900 text-white',
            ],
        ];

        return view('livewire.student.leaderboard', compact('weeklyLeaders', 'achievements', 'plans'));
    }

    public function upgradePlan(string $plan): void
    {
        // TODO: integrate with payment gateway (Stripe / VNPay)
        session()->flash('success', "Upgrade to {$plan} — payment integration pending.");
    }
}
