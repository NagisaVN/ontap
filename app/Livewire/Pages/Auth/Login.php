<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        session()->regenerate();

        $user = Auth::user();

        match ($user->role) {
            'admin'   => $this->redirect(route('admin.dashboard'),   navigate: true),
            'teacher' => $this->redirect(route('teacher.dashboard'), navigate: true),
            default   => $this->redirect(route('student.dashboard'), navigate: true),
        };
    }

    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
