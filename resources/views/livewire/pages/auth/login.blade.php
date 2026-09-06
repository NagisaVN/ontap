<div>
  <h1 class="text-2xl font-bold text-slate-900 mb-1">Chào mừng trở lại</h1>
  <p class="text-sm text-slate-500 mb-8">Đăng nhập vào tài khoản SmartPrep của bạn để tiếp tục.</p>

  <form wire:submit="login" class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1.5">Địa chỉ email</label>
      <input
        type="email"
        wire:model="email"
        placeholder="ban@example.com"
        required
        class="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
      />
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
    
    <div x-data="{ showPw: false }">
      <label class="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
      <div class="relative">
        <input
          x-bind:type="showPw ? 'text' : 'password'"
          wire:model="password"
          placeholder="••••••••"
          required
          class="w-full h-10 px-3.5 pr-10 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
        />
        <button
          type="button"
          @click="showPw = !showPw"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
        >
          <template x-if="!showPw">
            <!-- Icon: Eye -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
          </template>
          <template x-if="showPw">
            <!-- Icon: EyeOff -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
          </template>
        </button>
      </div>
      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="flex items-center justify-between">
      <label class="flex items-center gap-2 cursor-pointer">
        <input
          type="checkbox"
          wire:model="remember"
          class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
        />
        <span class="text-sm text-slate-600">Ghi nhớ đăng nhập</span>
      </label>
      <a href="{{ route('password.request') }}" wire:navigate class="text-sm text-indigo-600 font-medium hover:text-indigo-700 transition-colors">
        Quên mật khẩu?
      </a>
    </div>

    <button
      type="submit"
      class="w-full h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2 mt-4"
    >
      <span wire:loading wire:target="login">
        <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
      </span>
      <span wire:loading.remove wire:target="login">Đăng nhập</span>
      <span wire:loading wire:target="login">Đang đăng nhập…</span>
    </button>
  </form>

  <p class="text-sm text-slate-500 text-center mt-6">
    Chưa có tài khoản? 
    <a href="{{ route('register') }}" wire:navigate class="text-indigo-600 font-medium hover:text-indigo-700">
      Đăng ký miễn phí
    </a>
  </p>
</div>
