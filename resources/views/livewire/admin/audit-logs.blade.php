<div class="p-6 max-w-6xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Nhật ký hoạt động</h1>
        <p class="text-sm text-slate-500 mt-0.5">Lịch sử tất cả thao tác của quản trị viên và giáo viên.</p>
    </div>

    {{-- Filters row — matches Figma exactly: Date(from) + Action type --}}
    <div class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Từ ngày</label>
            <input wire:model.live="dateFrom" type="date"
                class="h-9 px-3 border border-slate-200 rounded-lg text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Loại thao tác</label>
            <select wire:model.live="actionFilter"
                class="h-9 pl-3 pr-8 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 cursor-pointer">
                <option value="">Tất cả thao tác</option>
                <option value="created">TẠO MỚI</option>
                <option value="updated">CẬP NHẬT</option>
                <option value="deleted">XÓA</option>
            </select>
        </div>
        {{-- Search --}}
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Tìm kiếm</label>
            <x-search-input wire:model.live.debounce.350ms="search" placeholder="Tìm theo người dùng hoặc mô tả..." class="w-full"/>
        </div>
    </div>

    {{-- Log stream card — matches Figma feed layout --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        @forelse ($logs as $log)
        @php
            $isLast = $loop->last;
            $badgeMeta = [
                'created' => ['label' => 'TẠO MỚI', 'variant' => 'success'],
                'updated' => ['label' => 'CẬP NHẬT', 'variant' => 'info'],
                'deleted' => ['label' => 'XÓA', 'variant' => 'error'],
            ];
            $badge = $badgeMeta[$log['event']] ?? ['label' => strtoupper($log['event']), 'variant' => 'neutral'];
        @endphp
        <div wire:key="log-{{ $log['id'] }}" class="flex items-start gap-4 px-6 py-4 {{ $isLast ? '' : 'border-b border-slate-100' }} hover:bg-slate-50/50 transition-colors">

            {{-- Avatar bubble — same color logic as user table --}}
            <div class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center text-xs font-bold text-white
                {{ in_array(strtolower(explode(' ', $log['causer_name'])[0]), ['admin', 'super']) ? 'bg-rose-500' : (str_contains(strtolower($log['causer_name']), 'teacher') ? 'bg-emerald-500' : 'bg-indigo-500') }}">
                {{ strtoupper(substr($log['causer_name'], 0, 2)) }}
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-semibold text-slate-800 text-sm">{{ $log['causer_name'] }}</span>
                    <x-badge :variant="$badge['variant']">{{ $badge['label'] }}</x-badge>
                    <span class="text-sm text-slate-600">{{ $log['description'] }}</span>
                </div>

                {{-- Subject + diff details --}}
                <div class="mt-1 text-xs text-slate-400 flex flex-wrap items-center gap-x-3 gap-y-0.5">
                    @if($log['subject_label'] !== '-')
                    <span>{{ $log['subject_type'] }}: <span class="text-slate-500">{{ $log['subject_label'] }}</span></span>
                    @endif
                    {{-- Field-level diff --}}
                    @foreach($log['changes'] as $changeIdx => $change)
                    <span wire:key="change-{{ $loop->parent->iteration }}-{{ $changeIdx }}" class="font-mono">
                        <span class="line-through text-rose-400">{{ $change['from'] }}</span>
                        <span class="text-slate-400 mx-0.5">→</span>
                        <span class="text-emerald-600">{{ $change['to'] }}</span>
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Timestamp (right-aligned, mono, matches Figma) --}}
            <time class="text-xs font-mono text-slate-400 whitespace-nowrap shrink-0 pt-0.5"
                  title="{{ $log['diff_for_humans'] }}">
                {{ $log['created_at'] }}
            </time>
        </div>
        @empty
        <div class="flex flex-col items-center gap-2 py-16 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" class="opacity-30"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <p class="text-sm font-medium">Không tìm thấy nhật ký hoạt động</p>
            <p class="text-xs">Hãy thử điều chỉnh bộ lọc hoặc xóa khoảng thời gian.</p>
        </div>
        @endforelse

        {{-- Pagination footer --}}
        @if ($logs->hasPages())
        <div class="flex items-center justify-between px-6 py-3 border-t border-slate-100 bg-slate-50/70">
            <p class="text-xs text-slate-500">
                {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}
            </p>
            <div>{{ $logs->links('vendor.pagination.tailwind') }}</div>
        </div>
        @endif
    </div>
</div>
