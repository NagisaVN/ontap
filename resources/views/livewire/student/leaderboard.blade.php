<div class="p-6 max-w-4xl mx-auto space-y-6" x-data="{ tab: 'week' }">
    <h1 class="text-xl font-bold text-slate-900">Bảng xếp hạng & Phần thưởng</h1>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-slate-100 rounded-xl p-1 w-fit">
        <button @click="tab = 'week'" :class="tab === 'week' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 h-8 rounded-lg text-sm font-semibold transition-all">Tuần này</button>
        <button @click="tab = 'all'" :class="tab === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 h-8 rounded-lg text-sm font-semibold transition-all">Toàn thời gian</button>
    </div>

    {{-- Leaderboard table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" class="text-amber-500" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
            <h2 class="font-semibold text-slate-900">Xếp hạng theo XP</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($leaderboard ?? [] as $user)
            <div class="flex items-center gap-4 px-5 py-3.5 transition-colors {{ $user['isMe'] ?? false ? 'bg-indigo-50' : 'hover:bg-slate-50' }}">
                <span class="w-6 text-sm font-bold text-slate-400 shrink-0">
                    {{ $user['rank'] <= 3 ? ['🥇','🥈','🥉'][$user['rank']-1] : '#'.$user['rank'] }}
                </span>
                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-indigo-700">{{ strtoupper(substr($user['name'], 0, 2)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold {{ ($user['isMe'] ?? false) ? 'text-indigo-700' : 'text-slate-900' }}">
                        {{ $user['name'] }} @if($user['isMe'] ?? false)<span class="text-xs text-indigo-400 font-normal">(Bạn)</span>@endif
                    </p>
                </div>
                <span class="text-sm font-bold text-slate-900">{{ number_format($user['score']) }} XP</span>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-slate-400">Chưa có dữ liệu xếp hạng.</div>
            @endforelse
        </div>
    </div>

    {{-- Achievements --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" class="text-amber-500" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <h2 class="font-semibold text-slate-900">Thành tích</h2>
            <span class="ml-auto text-xs text-slate-500">{{ $unlockedCount ?? 0 }}/{{ $totalAchievements ?? 12 }} mở khóa</span>
        </div>
        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
            @forelse($achievements ?? [] as $a)
            <div title="{{ $a['label'] }}"
                class="flex flex-col items-center gap-1.5 p-3 rounded-xl border transition-colors {{ $a['unlocked'] ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-slate-50 opacity-60' }}">
                <span class="text-2xl">{{ $a['unlocked'] ? $a['icon'] : '🔒' }}</span>
                <span class="text-[10px] font-medium text-center leading-tight {{ $a['unlocked'] ? 'text-amber-700' : 'text-slate-500' }}">{{ $a['label'] }}</span>
            </div>
            @empty
            <div class="col-span-6 text-center py-4 text-sm text-slate-400">Chưa có thành tích.</div>
            @endforelse
        </div>
    </div>
</div>