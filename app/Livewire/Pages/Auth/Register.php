<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

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
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
            'role'     => $this->role,
        ]);

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
