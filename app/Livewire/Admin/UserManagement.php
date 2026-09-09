<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────────
    public string $search     = '';
    public string $roleFilter = '';
    public int    $perPage    = 10;

    // ── Bulk select ──────────────────────────────────────────────────────────
    public bool  $selectAll = false;
    public array $selected  = [];

    // ── Edit user modal ──────────────────────────────────────────────────────
    public bool   $showEditModal = false;
    public ?int   $editUserId   = null;
    public string $editName     = '';
    public string $editEmail    = '';

    // ── Change password modal ────────────────────────────────────────────────
    public bool   $showPasswordModal       = false;
    public string $newPassword             = '';
    public string $newPasswordConfirmation = '';

    // ── Create user modal ────────────────────────────────────────────────────
    public string $newUserName     = '';
    public string $newUserEmail    = '';
    public string $newUserPassword = '';
    public string $newUserRole     = 'student';

    protected $queryString = [
        'search'     => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    // ── Hooks: reset page on filter change ───────────────────────────────────
    public function updatingSearch(): void     { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }

    public function updatedSelectAll(bool $value): void
    {
        $this->selected = $value
            ? User::with('roles')
                ->when($this->search, fn($q) =>
                    $q->where(fn($i) =>
                        $i->where('name',  'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%")
                    )
                )
                ->when($this->roleFilter, fn($q) =>
                    $q->whereHas('roles', fn($r) => $r->where('name', $this->roleFilter))
                )
                ->pluck('id')->toArray()
            : [];
    }

    // ── Computed: paginated users ─────────────────────────────────────────────
    #[Computed]
    public function users()
    {
        return User::with('roles')
            ->when($this->search, fn($q) =>
                $q->where(fn($inner) =>
                    $inner->where('name',  'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->when($this->roleFilter, fn($q) =>
                $q->whereHas('roles', fn($r) => $r->where('name', $this->roleFilter))
            )
            ->latest()
            ->paginate($this->perPage);
    }

    // ── Computed: all roles for the dropdowns ────────────────────────────────
    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->get();
    }

    // ── Computed: total user count for the header badge ──────────────────────
    #[Computed]
    public function total()
    {
        return User::count();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // ACTIONS — all protected with authorization checks (defense-in-depth)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Create a new user account.
     * Restricted to super_admin only.
     */
    public function createUser(): void
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $validated = $this->validate([
            'newUserName'     => ['required', 'string', 'max:255'],
            'newUserEmail'    => ['required', 'email', 'unique:users,email'],
            'newUserPassword' => ['required', Password::min(8)->letters()->numbers()],
            'newUserRole'     => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'              => $validated['newUserName'],
            'email'             => $validated['newUserEmail'],
            'password'          => Hash::make($validated['newUserPassword']),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $user->assignRole($validated['newUserRole']);

        activity()->performedOn($user)->causedBy(auth()->user())
            ->withProperties(['role' => $validated['newUserRole']])
            ->log("Tạo tài khoản mới với vai trò '{$validated['newUserRole']}'");

        // Reset form
        $this->reset(['newUserName', 'newUserEmail', 'newUserPassword', 'newUserRole']);
        $this->newUserRole = 'student';

        $this->dispatch('close-modal', 'create-user');
        session()->flash('success', "Đã tạo tài khoản {$user->name} thành công.");
    }

    /**
     * Change a user's role via Spatie syncRoles().
     * Only super_admin may reassign roles.
     */
    public function changeRole(int $userId, string $newRole): void
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $user    = User::with('roles')->findOrFail($userId);
        $oldRole = $user->getRoleNames()->first() ?? 'None';
        $user->syncRoles([$newRole]);
        activity()->performedOn($user)->causedBy(auth()->user())
            ->withProperties(['old' => ['role' => $oldRole], 'new' => ['role' => $newRole]])
            ->log("User role changed from '$oldRole' to '$newRole'");
        session()->flash('success', "Đã đổi vai trò của {$user->name} thành $newRole.");
    }

    /**
     * Toggle user ban status.
     * is_active=true => active, is_active=false => banned.
     */
    public function toggleBan(int $userId): void
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Không thể tự khóa chính mình.');
            return;
        }
        $user->is_active = !$user->is_active;
        $user->save();
        session()->flash('success', ($user->is_active ? 'Mở khóa' : 'Khóa') . " tài khoản {$user->name}.");
    }

    public function editUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editUserId    = $userId;
        $this->editName      = $user->name;
        $this->editEmail     = $user->email;
        $this->showEditModal = true;
    }

    public function updateUser(): void
    {
        abort_unless(auth()->user()->hasPermissionTo('edit users'), 403);

        $v = $this->validate([
            'editName'  => ['required', 'string', 'max:255'],
            'editEmail' => ['required', 'email', 'unique:users,email,' . $this->editUserId],
        ]);
        User::findOrFail($this->editUserId)->update([
            'name'  => $v['editName'],
            'email' => $v['editEmail'],
        ]);
        $this->showEditModal = false;
        $this->editUserId    = null;
        session()->flash('success', 'Đã cập nhật thông tin người dùng.');
    }

    public function openPasswordModal(int $userId): void
    {
        $this->editUserId              = $userId;
        $this->newPassword             = '';
        $this->newPasswordConfirmation = '';
        $this->showPasswordModal       = true;
    }

    public function updatePassword(): void
    {
        abort_unless(auth()->user()->hasPermissionTo('change user password'), 403);

        $this->validate([
            'newPassword' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
        $user = User::findOrFail($this->editUserId);
        $user->update(['password' => Hash::make($this->newPassword)]);
        activity()->performedOn($user)->causedBy(auth()->user())
            ->log('User password was changed by admin');
        $this->showPasswordModal = false;
        $this->editUserId        = null;
        session()->flash('success', 'Đã đổi mật khẩu thành công.');
    }

    public function deleteUser(int $userId): void
    {
        abort_unless(auth()->user()->hasPermissionTo('delete users'), 403);

        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Không thể tự xóa chính mình.');
            return;
        }
        $name = $user->name;
        $user->delete();
        $this->selected  = array_values(array_filter($this->selected, fn($id) => $id !== $userId));
        $this->selectAll = false;
        session()->flash('success', "Đã xóa người dùng {$name}.");
    }

    public function bulkDelete(): void
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $ids = array_values(array_filter($this->selected, fn($id) => $id !== auth()->id()));
        User::whereIn('id', $ids)->each(fn($u) => $u->delete());
        $this->selected  = [];
        $this->selectAll = false;
        session()->flash('success', 'Đã xóa ' . count($ids) . ' người dùng.');
    }

    public function bulkBan(): void
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $ids = array_values(array_filter($this->selected, fn($id) => $id !== auth()->id()));
        User::whereIn('id', $ids)->update(['is_active' => false]);
        $this->selected  = [];
        $this->selectAll = false;
        session()->flash('success', 'Đã khóa các tài khoản được chọn.');
    }

    // ── render() only returns the view — NO data fetching here ───────────────
    public function render()
    {
        return view('livewire.admin.user-management');
    }
}