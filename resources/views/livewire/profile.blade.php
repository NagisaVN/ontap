<div class="p-6 max-w-2xl mx-auto">
  <h1 class="text-xl font-bold text-slate-900 mb-6">Hồ sơ cá nhân</h1>

  <!-- Avatar upload -->
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-5">
    <h2 class="text-sm font-semibold text-slate-700 mb-4">Ảnh đại diện</h2>
    <div class="flex items-center gap-5">
      <div class="relative shrink-0">
        <div class="w-20 h-20 rounded-2xl bg-indigo-100 flex items-center justify-center overflow-hidden">
          <span class="text-3xl font-bold text-indigo-600">NG</span>
        </div>
        <label class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center cursor-pointer shadow">
          <!-- Icon: Camera -->
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
          <input type="file" wire:model="photo" accept="image/*" class="sr-only" />
        </label>
      </div>
      <div>
        <p class="text-sm font-medium text-slate-900">Tải lên ảnh mới</p>
        <p class="text-xs text-slate-500 mt-0.5">JPG, PNG tối đa 2 MB. Nên dùng ảnh vuong.</p>
      </div>
    </div>
  </div>

  <!-- Personal info -->
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-5">
    <h2 class="text-sm font-semibold text-slate-700 mb-4">Thông tin cá nhân</h2>
    <form wire:submit="saveProfile" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Họ và tên</label>
          <input
            type="text"
            wire:model="name"
            class="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Số điện thoại</label>
          <input
            type="tel"
            wire:model="phone"
            class="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
          />
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
        <input
          type="email"
          wire:model="email"
          class="w-full h-10 px-3.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
        />
      </div>
      <div class="flex items-center gap-3 pt-2">
        <button
          type="submit"
          class="h-9 px-5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 rounded-lg text-white text-sm font-semibold transition-colors flex items-center gap-2"
        >
          <span wire:loading wire:target="saveProfile">
            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
          </span>
          <span wire:loading.remove wire:target="saveProfile">Lưu thay đổi</span>
          <span wire:loading wire:target="saveProfile">Đang lưu…</span>
        </button>
        <div wire:loading.remove wire:target="saveProfile">
           @if(session('status') === 'profile-updated')
             <span class="flex items-center gap-1.5 text-sm text-emerald-600 font-medium transition-opacity">
                <!-- Icon: CheckCircle2 -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                Đã lưu!
             </span>
           @endif
        </div>
      </div>
    </form>
  </div>

  <!-- Appearance -->
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6" x-data="{ dark: false }">
    <h2 class="text-sm font-semibold text-slate-700 mb-4">Giao diện</h2>
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-slate-900">Tùy chỉnh giao diện</p>
        <p class="text-xs text-slate-500 mt-0.5">Chọn giữa chế độ sáng và tối.</p>
      </div>
      <button
        @click="dark = !dark"
        :class="dark ? 'bg-indigo-600' : 'bg-slate-200'"
        class="relative w-14 h-7 rounded-full transition-colors duration-200"
      >
        <span
          :class="dark ? 'translate-x-7' : 'translate-x-0'"
          class="absolute top-0.5 left-0.5 w-6 h-6 rounded-full bg-white shadow flex items-center justify-center transition-transform duration-200"
        >
          <template x-if="dark">
            <!-- Icon: Moon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
          </template>
          <template x-if="!dark">
            <!-- Icon: Sun -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-500"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
          </template>
        </span>
      </button>
    </div>
  </div>
</div>
