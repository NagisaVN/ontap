<?php

namespace App\Livewire\Pages\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', $status);
            return;
        }

        $this->addError('email', __($status));
    }

    public function render()
    {
        return view('livewire.pages.auth.forgot-password');
    }
}
