<div>
  @if (session('status'))
    <div class="text-center">
      <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-5">
        <!-- Icon: CheckCircle2 -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
      </div>
      <h1 class="text-xl font-bold text-slate-900 mb-2">Kiểm tra email của bạn</h1>
      <p class="text-sm text-slate-500 mb-6 leading-relaxed">
        Chúng tôi đã gửi liên kết đặt lại mật khẩu đến <strong class="text-slate-700">{{ $email ?? '' }}</strong>. Liên kết có hiệu lực trong 15 phút.
      </p>
      <a
        href="{{ route('login') }}" wire:navigate
        class="inline-flex items-center gap-2 text-sm text-indigo-600 font-medium hover:text-indigo-700"
      >
        <!-- Icon: ArrowLeft -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Quay lại đăng nhập
      </a>
    </div>
  @else
    <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Quay lại đăng nhập
    </a>
    <h1 class="text-2xl font-bold text-slate-900 mb-1">Đặt lại mật khẩu</h1>
    <p class="text-sm text-slate-500 mb-8">Nhập email của bạn và chúng tôi sẽ gửi liên kết đặt lại mật khẩu.</p>

    <form wire:submit="sendPasswordResetLink" class="space-y-4">
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
      <button
        type="submit"
        class="w-full h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2"
      >
        <span wire:loading wire:target="sendPasswordResetLink">
          <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </span>
        <span wire:loading.remove wire:target="sendPasswordResetLink">Gửi liên kết đặt lại</span>
        <span wire:loading wire:target="sendPasswordResetLink">Đang gửi…</span>
      </button>
    </form>
  @endif
</div>
