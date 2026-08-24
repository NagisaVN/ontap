<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Question;
use App\Models\ExamAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Quản trị hệ thống')]
class Dashboard extends Component
{
    public function assignRole(int $userId, string $role): void
    {
        $user = User::findOrFail($userId);
        $user->syncRoles([$role]);
        session()->flash('success', "Đã cập nhật role cho {$user->name}.");
    }

    public function render()
    {
        $stats = [
            'tong_user'     => User::count(),
            'teacher'       => User::role('teacher')->count(),
            'student'       => User::role('student')->count(),
            'cau_hoi'       => Question::daDuyet()->count(),
            'cho_duyet'     => Question::choDuyet()->count(),
            'luot_thi'      => ExamAttempt::where('trang_thai','hoan_thanh')->count(),
        ];

        $users = User::with('roles')->latest()->take(20)->get();

        return view('livewire.admin.dashboard', compact('stats', 'users'));
    }
}
