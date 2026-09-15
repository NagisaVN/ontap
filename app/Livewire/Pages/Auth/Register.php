<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.guest')]
class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|unique:users,email')]
    public string $email = '';

    #[Validate('required|confirmed|min:8')]
    public string $password = '';

    public string $password_confirmation = '';

    #[Validate('required|in:student,teacher')]
    public string $role = 'student';

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name'              => $this->name,
            'email'             => $this->email,
            'password'          => Hash::make($this->password),
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
        // A fresh production database may have completed migrations before
        // its optional demo seeder runs. Ensure the two public roles exist so
        // registration never fails with Spatie's RoleDoesNotExist exception.
        $user->assignRole(Role::findOrCreate($this->role));

        auth()->login($user);

        $this->redirect(
            $this->role === 'teacher' ? route('teacher.dashboard') : route('student.dashboard'),
            navigate: true,
        );
    }

    public function render()
    {
        return view('livewire.pages.auth.register');
    }
}
