<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    // KPI data is computed server-side and passed to the view
    public function render()
    {
        $serverMetrics = [
            ['label' => 'CPU Usage',  'value' => 42, 'color' => 'bg-indigo-500'],
            ['label' => 'Memory',     'value' => 67, 'color' => 'bg-blue-500'],
            ['label' => 'Disk I/O',   'value' => 28, 'color' => 'bg-emerald-500'],
            ['label' => 'Network',    'value' => 55, 'color' => 'bg-amber-500'],
        ];

        $quickStats = [
            ['label' => 'Questions in bank', 'value' => '1,248'],
            ['label' => 'Exams conducted',   'value' => '3,421'],
            ['label' => 'OCR jobs today',    'value' => '28'],
            ['label' => 'Support tickets',   'value' => '7 open'],
        ];

        $performanceStats = [
            ['label' => 'Uptime',      'value' => '99.98%'],
            ['label' => 'Requests/s',  'value' => '2,841'],
            ['label' => 'Avg latency', 'value' => '48ms'],
        ];

        return view('livewire.admin.dashboard', compact('serverMetrics', 'quickStats', 'performanceStats'));
    }
}
