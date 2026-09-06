<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    public string $search = '';
    public string $roleFilter = '';
    public bool $selectAll = false;
    public array $selected = [];
    public ?int $deleteId = null;
    public ?int $editUserId = null;

    public array $roles = ['admin', 'teacher', 'student'];

    // Sync selectAll toggle with individual checkboxes
    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value
            ? $this->getFilteredUsers()->pluck('id')->toArray()
            : [];
    }

    public function getFilteredUsers()
    {
        return User::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->latest()
            ->get();
    }

    public function changeRole(int $userId, string $role): void
    {
        User::findOrFail($userId)->update(['role' => $role]);
        session()->flash('success', 'Role updated.');
    }

    public function toggleBan(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->is_active = ! $user->is_active;
        $user->save();
    }

    public function confirmDelete(): void
    {
        if ($this->deleteId) {
            User::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->selected = array_filter($this->selected, fn($id) => $id !== $this->deleteId);
            session()->flash('success', 'User deleted.');
        }
    }

    public function bulkDelete(): void
    {
        User::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        session()->flash('success', count($this->selected) . ' users deleted.');
    }

    public function bulkBan(): void
    {
        User::whereIn('id', $this->selected)->update(['is_active' => false]);
        $this->selected = [];
        session()->flash('success', 'Selected users banned.');
    }

    public function render()
    {
        $users = $this->getFilteredUsers()->map(fn($u) => [
            'id'     => $u->id,
            'name'   => $u->name,
            'email'  => $u->email,
            'role'   => $u->role,
            'status' => $u->is_active ? 'active' : 'banned',
            'joined' => $u->created_at->format('d M Y'),
            'avatar' => strtoupper(substr($u->name, 0, 2)),
        ])->toArray();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => $this->roles,
        ]);
    }
}
