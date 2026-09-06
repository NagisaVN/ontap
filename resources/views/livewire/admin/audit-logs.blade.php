<div class="p-6 max-w-5xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Nhật ký hoạt động</h1>
            <p class="text-sm text-slate-500 mt-0.5">Theo dõi toàn bộ hành động trong hệ thống.</p>
        </div>
        <button wire:click="exportLogs"
            class="flex items-center gap-2 h-9 px-4 border border-slate-200 rounded-lg text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Xuất CSV
        </button>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm hành động, người dùng…"
                class="pl-9 pr-4 h-9 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 w-72"/>
        </div>
        <div class="relative">
            <select wire:model.live="filterType" class="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                <option value="">Tất cả loại</option>
                <option value="create">Tạo mới</option>
                <option value="update">Cập nhật</option>
                <option value="delete">Xóa</option>
                <option value="login">Đăng nhập</option>
                <option value="logout">Đăng xuất</option>
            </select>
            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
        </div>
        <input type="date" wire:model.live="filterDate"
            class="h-9 px-3 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"/>
    </div>

    {{-- Logs table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Người dùng</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Hành động</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Đối tượng</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs ?? [] as $log)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap text-xs font-mono">{{ $log->created_at->format('d/m H:i:s') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold text-indigo-700">{{ strtoupper(substr($log->causer?->name ?? '?', 0, 2)) }}</span>
                                </div>
                                <span class="text-slate-700 text-xs">{{ $log->causer?->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ in_array($log->description, ['created']) ? 'bg-emerald-100 text-emerald-700'
                                   : (in_array($log->description, ['deleted']) ? 'bg-rose-100 text-rose-700'
                                   : 'bg-slate-100 text-slate-600') }}">
                                {{ $log->description }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600 text-xs">{{ class_basename($log->subject_type ?? '') }} #{{ $log->subject_id }}</td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs font-mono">{{ $log->properties['ip'] ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">Không có nhật ký nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-500">{{ $logs?->firstItem() ?? 0 }}–{{ $logs?->lastItem() ?? 0 }} / {{ $logs?->total() ?? 0 }}</p>
            <div>{{ $logs?->links('vendor.pagination.simple-tailwind') }}</div>
        </div>
    </div>
</div>