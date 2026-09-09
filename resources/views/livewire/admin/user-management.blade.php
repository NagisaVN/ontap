<div class="p-6 max-w-6xl mx-auto space-y-6">

    {{-- Flash Message --}}
    @if (session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><polyline points="20 6 9 16 4 11"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Quản lý người dùng</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $this->total }} người dùng đã đăng ký</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Bulk actions (Alpine-driven, visible only when rows are selected) --}}
            <div x-data="{ count: @entangle('selected').live }">
                <template x-if="count.length > 0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-slate-500 font-medium" x-text="count.length + ' đã chọn'"></span>
                        @role('super_admin')
                        <button wire:click="bulkBan"
                            @click="$dispatch('open-modal', 'confirm-bulk-ban')"
                            class="h-8 px-3 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold hover:bg-amber-200 transition-colors">
                            Khóa tất cả
                        </button>
                        <button @click="$dispatch('open-modal', 'confirm-bulk-delete')"
                            class="h-8 px-3 rounded-lg bg-rose-100 text-rose-700 text-xs font-semibold hover:bg-rose-200 transition-colors">
                            Xóa tất cả
                        </button>
                        @endrole
                    </div>
                </template>
            </div>

            @role('super_admin')
            <button @click="$dispatch('open-modal', 'create-user')"
                class="h-9 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tạo mới
            </button>
            @endrole
        </div>
    </div>

    {{-- Filters bar — matches Figma: search + role dropdown --}}
    <div class="flex flex-wrap items-center gap-3">
        <x-search-input
            wire:model.live.debounce.300ms="search"
            placeholder="Tìm kiếm người dùng..."
            class="w-64"/>

        {{-- Role filter --}}
            <select wire:model.live="roleFilter"
                class="h-9 pl-3 pr-8 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 cursor-pointer">
                <option value="">Tất cả vai trò</option>
                @foreach ($this->roles as $role)
                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70">
                        <th class="w-10 px-4 py-3.5">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"/>
                        </th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Người dùng</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Vai trò</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Ngày tham gia</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($this->users as $user)
                    <tr wire:key="{{ $user->id }}"
                        class="hover:bg-slate-50/60 transition-colors group {{ !$user->is_active ? 'opacity-60' : '' }}">

                        {{-- Checkbox --}}
                        <td class="px-4 py-4">
                            <input type="checkbox" wire:model.live="selected" value="{{ $user->id }}"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"/>
                        </td>

                        {{-- User avatar + name --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold text-white
                                    {{ match($user->getRoleNames()->first()) {
                                        'super_admin' => 'bg-rose-500',
                                        'teacher'     => 'bg-emerald-500',
                                        default        => 'bg-indigo-500',
                                    } }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-slate-900">{{ $user->name }}</span>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-5 py-4 text-slate-500">{{ $user->email }}</td>

                        {{-- Role select — calls changeRole() via Spatie syncRoles() --}}
                        <td class="px-5 py-4">
                            @role('super_admin')
                                <select wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                    class="h-7 pl-2.5 pr-2 text-xs font-medium rounded-full bg-slate-100 text-slate-700 border-0 focus:outline-none focus:ring-2 focus:ring-indigo-400 cursor-pointer">
                                    @foreach ($this->roles as $role)
                                    <option wire:key="role-opt-{{ $user->id }}-{{ $role->name }}" value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                    @endforeach
                                </select>
                            @else
                                <span class="h-7 px-2.5 inline-flex items-center text-xs font-medium rounded-full bg-slate-100 text-slate-700">
                                    {{ ucfirst($user->getRoleNames()->first() ?? 'None') }}
                                </span>
                            @endrole
                        </td>

                        {{-- Status badge --}}
                        <td class="px-5 py-4">
                            @if ($user->is_active)
                                <x-badge variant="success">Hoạt động</x-badge>
                            @else
                                <x-badge variant="error">Đã khóa</x-badge>
                            @endif
                        </td>

                        {{-- Joined date --}}
                        <td class="px-5 py-4 text-slate-500 whitespace-nowrap font-mono text-xs">
                            {{ $user->created_at->format('Y-m-d') }}
                        </td>

                        {{-- Actions: edit, password, ban, delete --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1 transition-opacity">

                                {{-- Edit user --}}
                                @can('edit users')
                                <button wire:click="editUser({{ $user->id }})"
                                    @click="$dispatch('open-modal', 'edit-user')"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-amber-400 hover:bg-amber-50 hover:text-amber-600 transition-colors"
                                    title="Chỉnh sửa">
                                    {{-- Star / edit icon matches Figma --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                @endcan

                                {{-- Change password --}}
                                @can('change user password')
                                <button wire:click="openPasswordModal({{ $user->id }})"
                                    @click="$dispatch('open-modal', 'change-password')"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                    title="Đổi mật khẩu">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </button>
                                @endcan

                                {{-- Ban / Unban toggle --}}
                                @role('super_admin')
                                <button wire:click="toggleBan({{ $user->id }})"
                                    @disabled($user->id === auth()->id())
                                    class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors {{ $user->is_active ? 'text-slate-400 hover:bg-amber-50 hover:text-amber-600' : 'text-emerald-500 hover:bg-emerald-50' }}"
                                    title="{{ $user->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                    @if ($user->is_active)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                    @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 16 4 11"/></svg>
                                    @endif
                                </button>
                                @endrole

                                {{-- Delete --}}
                                @can('delete users')
                                <button @click="$dispatch('open-modal', 'confirm-delete-{{ $user->id }}')"
                                    @disabled($user->id === auth()->id())
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors"
                                    title="Xóa người dùng">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    {{-- Per-row confirm-delete modal --}}
                    @can('delete users')
                    <x-confirm-modal
                        name="confirm-delete-{{ $user->id }}"
                        title="Xóa {{ $user->name }}?"
                        message="Hành động này không thể hoàn tác."
                        confirm-label="Xóa"
                        :danger="true"
                        x-on:confirm.window="$wire.deleteUser({{ $user->id }})"/>
                    @endcan

                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" class="opacity-30"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <p class="text-sm font-medium">Không tìm thấy người dùng</p>
                                <p class="text-xs">Hãy thử điều chỉnh tìm kiếm hoặc bộ lọc vai trò.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination footer --}}
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50/70">
            <p class="text-xs text-slate-500">
                {{ $this->users->firstItem() ?? 0 }}–{{ $this->users->lastItem() ?? 0 }} trên {{ $this->users->total() }} người dùng
            </p>
            <div>{{ $this->users->links('vendor.pagination.simple-tailwind') }}</div>
        </div>
    </div>

    {{-- -------------------------------------------------------- --}}
    {{-- MODALS --}}
    {{-- -------------------------------------------------------- --}}

    {{-- Create User Modal --}}
    @role('super_admin')
    <x-modal name="create-user" title="Tạo người dùng mới" size="md">
        <form wire:submit.prevent="createUser" class="space-y-4">
            <div>
                <x-input-label for="newUserName" value="Họ và tên"/>
                <x-text-input wire:model="newUserName" id="newUserName" type="text" class="mt-1 w-full" autocomplete="off" required/>
                <x-input-error :messages="$errors->get('newUserName')" class="mt-1"/>
            </div>
            <div>
                <x-input-label for="newUserEmail" value="Địa chỉ email"/>
                <x-text-input wire:model="newUserEmail" id="newUserEmail" type="email" class="mt-1 w-full" autocomplete="off" required/>
                <x-input-error :messages="$errors->get('newUserEmail')" class="mt-1"/>
            </div>
            <div>
                <x-input-label for="newUserPassword" value="Mật khẩu"/>
                <x-text-input wire:model="newUserPassword" id="newUserPassword" type="password" class="mt-1 w-full" required/>
                <x-input-error :messages="$errors->get('newUserPassword')" class="mt-1"/>
            </div>
            <div>
                <x-input-label for="newUserRole" value="Vai trò"/>
                <select wire:model="newUserRole" id="newUserRole"
                    class="mt-1 h-10 w-full pl-3 pr-8 bg-white border border-slate-300 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 cursor-pointer" required>
                    @foreach ($this->roles as $role)
                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('newUserRole')" class="mt-1"/>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="$dispatch('close-modal', 'create-user')"
                    class="h-9 px-4 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="h-9 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors">
                    <span wire:loading.remove wire:target="createUser">Tạo người dùng</span>
                    <span wire:loading wire:target="createUser">Đang tạo...</span>
                </button>
            </div>
        </form>
    </x-modal>
    @endrole

    {{-- Edit User Modal --}}
    <x-modal name="edit-user" title="Chỉnh sửa người dùng" size="md">
        <form wire:submit.prevent="updateUser" class="space-y-4">
            <div>
                <x-input-label for="editName" value="Họ và tên"/>
                <x-text-input wire:model="editName" id="editName" type="text" class="mt-1 w-full" autocomplete="off"/>
                <x-input-error :messages="$errors->get('editName')" class="mt-1"/>
            </div>
            <div>
                <x-input-label for="editEmail" value="Địa chỉ email"/>
                <x-text-input wire:model="editEmail" id="editEmail" type="email" class="mt-1 w-full" autocomplete="off"/>
                <x-input-error :messages="$errors->get('editEmail')" class="mt-1"/>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="$dispatch('close-modal', 'edit-user')"
                    class="h-9 px-4 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="h-9 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors">
                    <span wire:loading.remove>Lưu thay đổi</span>
                    <span wire:loading wire:target="updateUser">Đang lưu...</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Change Password Modal --}}
    <x-modal name="change-password" title="Đổi mật khẩu" size="md">
        <form wire:submit.prevent="updatePassword" class="space-y-4">
            <div>
                <x-input-label for="newPassword" value="Mật khẩu mới"/>
                <x-text-input wire:model="newPassword" id="newPassword" type="password" class="mt-1 w-full"/>
                <x-input-error :messages="$errors->get('newPassword')" class="mt-1"/>
            </div>
            <div>
                <x-input-label for="newPasswordConfirmation" value="Xác nhận mật khẩu"/>
                <x-text-input wire:model="newPasswordConfirmation" id="newPasswordConfirmation" type="password" class="mt-1 w-full"/>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="$dispatch('close-modal', 'change-password')"
                    class="h-9 px-4 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
                    Hủy
                </button>
                <button type="submit"
                    class="h-9 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition-colors">
                    <span wire:loading.remove>Cập nhật mật khẩu</span>
                    <span wire:loading wire:target="updatePassword">Đang cập nhật...</span>
                </button>
            </div>
        </form>
    </x-modal>

    {{-- Bulk confirm modals --}}
    <x-confirm-modal
        name="confirm-bulk-ban"
        title="Khóa các tài khoản đã chọn?"
        message="Tất cả người dùng được chọn sẽ bị mất quyền truy cập ngay lập tức."
        confirm-label="Khóa tất cả"
        :danger="false"
        x-on:confirm.window="$wire.bulkBan()"/>

    <x-confirm-modal
        name="confirm-bulk-delete"
        title="Xóa người dùng đã chọn?"
        message="Hành động này không thể hoàn tác. Tất cả dữ liệu của những người dùng này sẽ bị xóa."
        confirm-label="Xóa tất cả"
        :danger="true"
        x-on:confirm.window="$wire.bulkDelete()"/>

</div>
