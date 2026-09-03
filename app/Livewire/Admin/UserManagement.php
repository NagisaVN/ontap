<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Quản lý người dùng')]
class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int   $editUserId = null;
    public string $editName   = '';
    public string $editRole   = 'student';
    public string $editStatus = 'active';
    public ?int   $resetUserId = null;
    public string $newPassword = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updateUser(): void
    {
        $this->validate([
            'editUserId' => ['required', 'integer', 'exists:users,id'],
            'editName'   => ['required', 'string', 'min:2', 'max:255'],
            'editRole'   => ['required', 'in:student,teacher,super_admin'],
            'editStatus' => ['required', 'in:active,suspended'],
        ]);
        $user = User::findOrFail($this->editUserId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Bạn không thể tự sửa tài khoản của mình.');
            return;
        }
        $user->update(['name' => $this->editName, 'is_active' => $this->editStatus === 'active']);
        $user->syncRoles([$this->editRole]);
        session()->flash('success', 'Đã cập nhật thông tin của ' . $user->name . '.');
        $this->reset(['editUserId', 'editName', 'editRole', 'editStatus']);
        $this->dispatch('user-updated');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'resetUserId' => ['required', 'integer', 'exists:users,id'],
            'newPassword' => ['required', 'string', 'min:8'],
        ]);
        $user = User::findOrFail($this->resetUserId);
        $user->update(['password' => Hash::make($this->newPassword), 'remember_token' => null]);
        session()->flash('success', 'Đã đặt lại mật khẩu cho ' . $user->name . '.');
        $this->reset(['resetUserId', 'newPassword']);
        $this->dispatch('password-reset');
    }

    public function toggleSuspend(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Bạn không thể tự khóa tài khoản của mình.');
            return;
        }
        $user->update(['is_active' => !($user->is_active ?? true)]);
        $label = $user->is_active ? 'mở khóa' : 'khóa';
        session()->flash('success', 'Đã ' . $label . ' tài khoản ' . $user->name . '.');
    }

    public function render()
    {
        $users = User::with('roles')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(15);
        return view('livewire.admin.user-management', compact('users'));
    }
}
