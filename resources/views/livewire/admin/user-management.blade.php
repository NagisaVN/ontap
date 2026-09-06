<div class="p-6 max-w-5xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Quản lý người dùng</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $total ?? 0 }} người dùng trong hệ thống</p>
        </div>
        <button wire:click="$dispatch('open-create-user')"
            class="flex items-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-sm font-semibold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Thêm người dùng
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm tên, email…"
                class="pl-9 pr-4 h-9 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 w-64"/>
        </div>
        <div class="relative">
            <select wire:model.live="filterRole" class="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                <option value="">Tất cả vai trò</option>
                <option value="student">Học sinh</option>
                <option value="teacher">Giáo viên</option>
                <option value="admin">Quản trị viên</option>
            </select>
            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Người dùng</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Vai trò</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ngày tham gia</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users ?? [] as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                                <span class="font-medium text-slate-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                            @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $role->name === 'admin' ? 'bg-rose-100 text-rose-700' : ($role->name === 'teacher' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700') }}">
                                {{ ucfirst($role->name) }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3.5">
                            <button wire:click="toggleActive({{ $user->id }})"
                                class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none
                                {{ $user->is_active ? 'bg-indigo-600' : 'bg-slate-200' }}">
                                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                                    {{ $user->is_active ? 'translate-x-4.5' : 'translate-x-0.5' }}"></span>
                            </button>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-1">
                                <button wire:click="editUser({{ $user->id }})"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Bạn có chắc muốn xóa người dùng này?"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">Không tìm thấy người dùng.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-500">{{ $users?->firstItem() ?? 0 }}–{{ $users?->lastItem() ?? 0 }} / {{ $users?->total() ?? 0 }}</p>
            <div>{{ $users?->links('vendor.pagination.simple-tailwind') }}</div>
        </div>
    </div>
</div>