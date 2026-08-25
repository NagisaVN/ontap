<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div x-data="{ showPass: false }" class="space-y-6">

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Chào mừng trở lại! 👋</h2>
        <p class="mt-1.5 text-sm text-slate-500">Đăng nhập để tiếp tục lộ trình học của bạn.</p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status :status="session('status')" />

    {{-- Google OAuth (placeholder UI — cần cài socialite để activate) --}}
    <button type="button"
            class="w-full flex items-center justify-center gap-3 h-12 rounded-xl border border-slate-200
                   bg-white hover:bg-slate-50 text-slate-700 font-medium text-sm
                   transition-all duration-200 shadow-sm hover:shadow-md">
        <svg width="20" height="20" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.35-8.16 2.35-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            <path fill="none" d="M0 0h48v48H0z"/>
        </svg>
        Tiếp tục với Google
    </button>

    {{-- Divider --}}
    <div class="flex items-center gap-4">
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-xs text-slate-400 font-medium whitespace-nowrap">Hoặc đăng nhập bằng email</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </div>

    {{-- Form --}}
    <form wire:submit="login" class="space-y-4">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input wire:model="form.email"
                   id="email" type="email" name="email"
                   required autofocus autocomplete="username"
                   placeholder="ban@example.com"
                   class="sp-input-auth w-full h-12 px-4 rounded-xl bg-slate-50 border border-slate-200
                          text-slate-900 placeholder-slate-400 text-sm">
            @error('form.email')
                <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password + show/hide --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-slate-700">Mật khẩu</label>
                @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" wire:navigate
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition-colors">
                    Quên mật khẩu?
                </a>
                @endif
            </div>
            <div class="relative">
                <input wire:model="form.password"
                       id="password" name="password"
                       :type="showPass ? 'text' : 'password'"
                       required autocomplete="current-password"
                       placeholder="••••••••"
                       class="sp-input-auth w-full h-12 px-4 pr-12 rounded-xl bg-slate-50 border border-slate-200
                              text-slate-900 placeholder-slate-400 text-sm">
                <button type="button" @click="showPass = !showPass"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('form.password')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center gap-2">
            <input wire:model="form.remember" id="remember" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
            <label for="remember" class="text-sm text-slate-600 cursor-pointer select-none">Ghi nhớ đăng nhập</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="w-full h-12 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                       text-white font-semibold text-sm shadow-md hover:shadow-lg
                       transition-all duration-200 flex items-center justify-center gap-2 mt-2">

            {{-- Default state --}}
            <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                Đăng nhập →
            </span>

            {{-- Loading state --}}
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Đang xử lý...</span>
            </span>

        </button>

    </form>

    {{-- Register link --}}
    @if(Route::has('register'))
    <p class="text-center text-sm text-slate-500">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" wire:navigate
           class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
            Đăng ký miễn phí →
        </a>
    </p>
    @endif

</div>
