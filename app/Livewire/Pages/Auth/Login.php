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
            $this->addError('email', 'Email hoặc mật khẩu không đúng.');
            return;
        }

        $user = Auth::user();
        if (! $user->isActive()) {
            Auth::logout();
            $this->addError('email', 'Tài khoản này đã bị khóa.');
            return;
        }

        session()->regenerate();

        match (true) {
            $user->hasRole('super_admin') => $this->redirect(route('admin.dashboard'), navigate: false),
            $user->hasRole('teacher') => $this->redirect(route('teacher.dashboard'), navigate: false),
            default => $this->redirect(route('student.dashboard'), navigate: false),
        };
    }

    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
