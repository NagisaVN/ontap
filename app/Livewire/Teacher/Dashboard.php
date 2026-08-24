<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use App\Models\Subject;
use App\Services\ProgressTrackingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard Giáo viên')]
class Dashboard extends Component
{
    public function render()
    {
        $tongCauHoi   = Question::daDuyet()->count();
        $choDuyet     = Question::choDuyet()->count();
        $aiSinh       = Question::where('nguon', 'ai_sinh')->count();
        $tongSinhVien = \App\Models\User::role('student')->count();

        $cauHoiMoi = Question::with('chuong.monHoc')
            ->latest()
            ->take(8)
            ->get();

        $monHocs = Subject::withCount(['chuong as tong_chuong'])->get();

        return view('livewire.teacher.dashboard', compact(
            'tongCauHoi', 'choDuyet', 'aiSinh', 'tongSinhVien',
            'cauHoiMoi', 'monHocs'
        ));
    }
}
