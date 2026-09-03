{{-- Admin: Quản lý người dùng | Tailwind CSS + Alpine.js + Livewire --}}
<div
    x-data="{
        openDropdown: null,
        toggleDropdown(id) { this.openDropdown = (this.openDropdown === id) ? null : id; },
        closeDropdown() { this.openDropdown = null; },
        selectedUser: { id: null, name: '', email: '', role: 'student', status: 'active' },
        showEditModal: false,
        openEdit(user) { this.selectedUser = { ...user }; this.showEditModal = true; this.openDropdown = null; },
        closeEdit() { this.showEditModal = false; },
        showResetModal: false,
        newPassword: '',
        openReset(user) { this.selectedUser = { ...user }; this.newPassword = ''; this.showResetModal = true; this.openDropdown = null; },
        closeReset() { this.showResetModal = false; },
        generatePassword() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
            this.newPassword = Array.from(crypto.getRandomValues(new Uint8Array(16)))
                .map(b => chars[b % chars.length]).join('');
        }
    }"
    @click.away="closeDropdown()"
    @keydown.escape.window="showEditModal = false; showResetModal = false;"
    class="space-y-6"
>

    {{-- ── HEADER + TÌM KIẾM ──────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-slate-800 tracking-tight">👥 Quản lý người dùng</h2>
            <p class="text-sm text-slate-500 mt-0.5">Quản lý tài khoản, vai trò và trạng thái người dùng</p>
        </div>
        <div class="relative w-full sm:w-72">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
            </span>
            <input type="search" wire:model.live.debounce.300ms="search"
                placeholder="Tìm tên hoặc email..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-xl bg-white shadow-sm
                       text-slate-700 placeholder-slate-400 outline-none
                       focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition">
        </div>
    </div>

    {{-- ── FLASH MESSAGE ───────────────────────────────────────────────── --}}
    @if (session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm3.707-9.293a1 1 0 0 0-1.414-1.414L9 10.586 7.707 9.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 text-red-700 border border-red-200">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm1-13a1 1 0 1 0-2 0v4a1 1 0 0 0 2 0V5zm-1 8a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" clip-rule="evenodd"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── BẢNG DỮ LIỆU ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Người dùng</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Vai trò</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Ngày tạo</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users as $user)
                        @php
                            $role    = $user->roles->first()?->name ?? 'none';
                            $isSelf  = $user->id === auth()->id();
                            $badgeClass = match($role) {
                                'super_admin' => 'bg-red-50 text-red-600 ring-red-200',
                                'teacher'     => 'bg-indigo-50 text-indigo-600 ring-indigo-200',
                                default       => 'bg-slate-100 text-slate-600 ring-slate-200',
                            };
                            $badgeLabel = match($role) {
                                'super_admin' => 'Super Admin',
                                'teacher'     => 'Giáo viên',
                                'student'     => 'Sinh viên',
                                default       => ucfirst($role),
                            };
                            $words    = explode(' ', trim($user->name));
                            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            $avatarGrad = match($role) {
                                'super_admin' => 'from-red-400 to-rose-500',
                                'teacher'     => 'from-indigo-400 to-violet-500',
                                default       => 'from-slate-400 to-slate-500',
                            };
                            $alpineUser = json_encode([
                                'id'     => $user->id,
                                'name'   => $user->name,
                                'email'  => $user->email,
                                'role'   => $role,
                                'status' => ($user->is_active ?? true) ? 'active' : 'suspended',
                            ], JSON_HEX_QUOT | JSON_HEX_APOS);
                        @endphp
                        <tr wire:key="user-row-{{ $user->id }}" class="hover:bg-slate-50/60 transition-colors">

                            {{-- Người dùng --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $avatarGrad }}
                                                flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800 leading-tight">{{ $user->name }}</div>
                                        @if ($isSelf)
                                            <span class="text-[10px] font-medium text-indigo-500 bg-indigo-50 px-1.5 py-0.5 rounded-full">Bạn</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500">{{ $user->email }}</td>

                            {{-- Vai trò --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                    @if ($role === 'super_admin')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                                    @elseif ($role === 'teacher')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 0 0-.788 0l-7 3a1 1 0 0 0 0 1.84L5.25 8.051a.999.999 0 0 1 .356-.257l4-1.714a1 1 0 1 1 .788 1.838l-2.727 1.17 1.94.831a1 1 0 0 0 .787 0l7-3a1 1 0 0 0 0-1.838l-7-3z"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-7 9a7 7 0 1 1 14 0H3z" clip-rule="evenodd"/></svg>
                                    @endif
                                    {{ $badgeLabel }}
                                </span>
                            </td>

                            {{-- Ngày tạo --}}
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500">{{ $user->created_at?->format('d/m/Y') }}</td>

                            {{-- Thao tác --}}
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                @if (!$isSelf)
                                <div class="relative inline-block text-left">
                                    <button @click.stop="toggleDropdown({{ $user->id }})"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400
                                               hover:bg-slate-100 hover:text-slate-700 transition-colors
                                               focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                                        aria-label="Thao tác">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm0 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="openDropdown === {{ $user->id }}"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        @click.stop
                                        class="absolute right-0 z-30 mt-1 w-52 origin-top-right bg-white border border-slate-100 rounded-xl shadow-lg ring-1 ring-black/5 py-1"
                                        style="display: none;">

                                        {{-- Sửa thông tin --}}
                                        <button @click="openEdit({{ $alpineUser }})"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                                            <span class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </span>
                                            Sửa thông tin
                                        </button>

                                        {{-- Đổi mật khẩu --}}
                                        <button @click="openReset({{ $alpineUser }})"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-amber-600 transition-colors">
                                            <span class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 0 1 2 2m4 0a6 6 0 0 1-7.743 5.743L11 17H9v2H7v2H4a1 1 0 0 1-1-1v-2.586a1 1 0 0 1 .293-.707l5.964-5.964A6 6 0 1 1 21 9z"/>
                                                </svg>
                                            </span>
                                            Đổi mật khẩu
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        {{-- Khóa tài khoản --}}
                                        <button wire:click="toggleSuspend({{ $user->id }})" @click="closeDropdown()"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <span class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            </span>
                                            Khóa tài khoản
                                        </button>
                                    </div>
                                </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium">Không tìm thấy người dùng nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/60">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- ══ MODAL 1: SỬA THÔNG TIN ═════════════════════════════════════════ --}}
    <div x-show="showEditModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;"
        aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeEdit()"></div>
        <div x-show="showEditModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-100">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-800">Chỉnh sửa thông tin người dùng</h3>
                </div>
                <button @click="closeEdit()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit.prevent="updateUser" class="px-6 py-5 space-y-5">
                <input type="hidden" wire:model="editUserId" x-bind:value="selectedUser.id">

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Họ và tên <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="editName" x-model="selectedUser.name" placeholder="Nguyễn Văn A"
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-white text-slate-800 placeholder-slate-400 outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">
                        Email <span class="ml-1 text-[10px] font-normal text-slate-400 normal-case">(không thể thay đổi)</span>
                    </label>
                    <div class="relative">
                        <input type="email" x-bind:value="selectedUser.email" readonly
                            class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 text-slate-500 cursor-not-allowed outline-none">
                        <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 pointer-events-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/></svg>
                        </span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Vai trò <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select wire:model="editRole" x-model="selectedUser.role"
                            class="w-full appearance-none px-3.5 py-2.5 pr-9 text-sm border border-slate-200 rounded-xl bg-white text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition cursor-pointer">
                            <option value="student">👨‍🎓 Sinh viên</option>
                            <option value="teacher">👨‍🏫 Giáo viên</option>
                            <option value="super_admin">⭐ Super Admin</option>
                        </select>
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <div class="text-sm font-medium text-slate-700">Trạng thái tài khoản</div>
                        <div class="text-xs text-slate-500 mt-0.5" x-text="selectedUser.status === 'active' ? '✅ Đang hoạt động' : '🔒 Đã bị khóa'"></div>
                    </div>
                    <button type="button"
                        @click="selectedUser.status = (selectedUser.status === 'active') ? 'suspended' : 'active'"
                        :class="selectedUser.status === 'active' ? 'bg-indigo-600' : 'bg-slate-300'"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 items-center rounded-full border-2 border-transparent transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:ring-offset-2 cursor-pointer"
                        role="switch" :aria-checked="selectedUser.status === 'active'">
                        <!-- Safelist for Tailwind JIT -->
                        <span class="hidden bg-indigo-600 bg-slate-300 translate-x-5 translate-x-0"></span>
                        
                        <span :class="selectedUser.status === 'active' ? 'translate-x-5' : 'translate-x-0'"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition-all duration-300 ease-in-out"></span>
                    </button>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="closeEdit()"
                        class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Huỷ
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:ring-offset-2">
                        <span wire:loading.remove wire:target="updateUser">Lưu thay đổi</span>
                        <span wire:loading wire:target="updateUser" class="flex items-center gap-2" style="display:none;">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z"/></svg>
                            Đang lưu...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ MODAL 2: ĐỔI MẬT KHẨU ══════════════════════════════════════════ --}}
    <div x-show="showResetModal"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;"
        aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeReset()"></div>
        <div x-show="showResetModal"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-100">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 0 1 2 2m4 0a6 6 0 0 1-7.743 5.743L11 17H9v2H7v2H4a1 1 0 0 1-1-1v-2.586a1 1 0 0 1 .293-.707l5.964-5.964A6 6 0 1 1 21 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-slate-800">Đặt lại mật khẩu</h3>
                        <p class="text-xs text-slate-500" x-text="selectedUser.name"></p>
                    </div>
                </div>
                <button @click="closeReset()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit.prevent="resetPassword" class="px-6 py-5 space-y-5">
                <input type="hidden" wire:model="resetUserId" x-bind:value="selectedUser.id">

                {{-- Cảnh báo --}}
                <div class="flex gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-1-8a1 1 0 0 0-1 1v3a1 1 0 0 0 2 0V6a1 1 0 0 0-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-amber-800 leading-relaxed">
                        Người dùng sẽ <strong>bị đăng xuất khỏi tất cả phiên hoạt động</strong> ngay khi mật khẩu được thay đổi.
                    </p>
                </div>

                {{-- Mật khẩu mới --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Mật khẩu mới <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="newPassword" x-model="newPassword" placeholder="Nhập mật khẩu mới..."
                            class="flex-1 min-w-0 px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl bg-white text-slate-800 font-mono outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition">
                        <button type="button" @click="generatePassword()"
                            class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-2.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition-colors whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15"/>
                            </svg>
                            Tạo ngẫu nhiên
                        </button>
                    </div>
                    <p class="text-xs text-slate-400">Tối thiểu 8 ký tự</p>
                </div>

                {{-- Độ mạnh mật khẩu --}}
                <div x-show="newPassword.length > 0" class="flex items-center gap-2">
                    <div class="flex gap-1 flex-1">
                        <div :class="newPassword.length >= 1  ? 'bg-red-400'    : 'bg-slate-200'" class="h-1.5 flex-1 rounded-full transition-colors duration-300"></div>
                        <div :class="newPassword.length >= 6  ? 'bg-amber-400'  : 'bg-slate-200'" class="h-1.5 flex-1 rounded-full transition-colors duration-300"></div>
                        <div :class="newPassword.length >= 10 ? 'bg-yellow-400' : 'bg-slate-200'" class="h-1.5 flex-1 rounded-full transition-colors duration-300"></div>
                        <div :class="newPassword.length >= 14 ? 'bg-emerald-400': 'bg-slate-200'" class="h-1.5 flex-1 rounded-full transition-colors duration-300"></div>
                    </div>
                    <span class="text-xs text-slate-500 w-16 text-right"
                        x-text="newPassword.length < 6 ? 'Yếu' : newPassword.length < 10 ? 'Trung bình' : newPassword.length < 14 ? 'Tốt' : '💪 Mạnh'"></span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="closeReset()"
                        class="px-4 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                        Huỷ
                    </button>
                    <button type="submit" :disabled="newPassword.length < 8"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-indigo-600">
                        <span wire:loading.remove wire:target="resetPassword">Xác nhận đặt lại</span>
                        <span wire:loading wire:target="resetPassword" class="flex items-center gap-2" style="display:none;">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8H4z"/></svg>
                            Đang xử lý...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>