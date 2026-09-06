<div>
  <h1 class="text-2xl font-bold text-slate-900 mb-1">Tạo tài khoản</h1>
  <p class="text-sm text-slate-500 mb-8">Tham gia cùng hàng nghìn học sinh đang ôn tập thông minh hơn.</p>

  <form wire:submit="register" class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-slate-700 mb-1.5">Họ và tên</label>
      <input
        type="text"
        wire:model="name"
        placeholder="Nguyễn Văn A"
        required
        class="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
      />
      <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

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

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Mật khẩu</label>
        <input
          type="password"
          wire:model="password"
          placeholder="••••••••"
          required
          class="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Xác nhận</label>
        <input
          type="password"
          wire:model="password_confirmation"
          placeholder="••••••••"
          required
          class="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
        />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-slate-700 mb-2">Tôi là…</label>
      <div class="grid grid-cols-2 gap-3" x-data="{ role: @entangle('role') }">
        <button
          type="button"
          @click="role = 'student'"
          :class="role === 'student' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'"
          class="flex items-center justify-center gap-2.5 h-12 px-4 rounded-xl border-2 text-sm font-medium transition-all"
        >
          <span class="inline-flex items-center justify-center shrink-0" :class="role === 'student' ? 'text-indigo-600' : 'text-slate-400'">
            <!-- Icon: GraduationCap -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </span>
          Học sinh
        </button>
        <button
          type="button"
          @click="role = 'teacher'"
          :class="role === 'teacher' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'"
          class="flex items-center justify-center gap-2.5 h-12 px-4 rounded-xl border-2 text-sm font-medium transition-all"
        >
          <span class="inline-flex items-center justify-center shrink-0" :class="role === 'teacher' ? 'text-indigo-600' : 'text-slate-400'">
            <!-- Icon: BookOpen -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </span>
          Giáo viên
        </button>
      </div>
      <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <button
      type="submit"
      class="w-full h-10 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center justify-center gap-2 mt-2"
    >
      <span wire:loading wire:target="register">
        <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
      </span>
      <span wire:loading.remove wire:target="register">Tạo tài khoản</span>
      <span wire:loading wire:target="register">Đang tạo tài khoản…</span>
    </button>
  </form>

  <p class="text-sm text-slate-500 text-center mt-6">
    Đã có tài khoản? 
    <a href="{{ route('login') }}" wire:navigate class="text-indigo-600 font-medium hover:text-indigo-700">
      Đăng nhập
    </a>
  </p>
</div>
