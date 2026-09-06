<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Profile extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    public $photo = null;

    public function mount(): void
    {
        $user        = Auth::user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function saveProfile(): void
    {
        $this->validate();

        $user        = Auth::user();
        $user->name  = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;

        if ($this->photo) {
            $user->avatar = $this->photo->store('avatars', 'public');
        }

        $user->save();

        session()->flash('status', 'profile-updated');
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
