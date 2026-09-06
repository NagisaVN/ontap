<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Tổng quan Quản trị</h1>
        <p class="text-sm text-slate-500 mt-0.5">Phân tích toàn hệ thống và tình trạng server.</p>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0 text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Tổng người dùng</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{{ number_format($totalUsers ?? 0) }}</p>
                <p class="text-xs font-medium mt-1.5 text-emerald-600">↑ {{ $newUsersThisWeek ?? 0 }} tuần này</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Phiên đang hoạt động</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{{ $activeSessions ?? 0 }}</p>
                <p class="text-xs font-medium mt-1.5 text-emerald-600">Đang online</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Tải CPU</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{{ $cpuLoad ?? 0 }}%</p>
                <p class="text-xs font-medium mt-1.5 text-emerald-600">Hoạt động bình thường</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- MRR Chart placeholder --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-1">
                <h2 class="font-semibold text-slate-900">Doanh thu hàng tháng (MRR)</h2>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <p class="text-3xl font-bold text-slate-900 mb-0.5">{{ number_format($mrr ?? 0) }}₫</p>
            <p class="text-xs text-emerald-600 font-medium mb-4">↑ {{ $mrrGrowth ?? 0 }}% so với tháng trước</p>
            <div id="mrrChart" class="h-28">
                {{-- ApexCharts will be initialized here --}}
                <div class="h-full flex items-end gap-1">
                    @foreach($mrrData ?? [] as $m)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full rounded-t bg-indigo-200 hover:bg-indigo-400 transition-colors"
                             style="height: {{ $m['pct'] ?? 50 }}%"
                             title="{{ $m['month'] }}: {{ number_format($m['mrr']) }}₫"></div>
                        <span class="text-[9px] text-slate-400">{{ $m['month'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Server Resources --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-semibold text-slate-900 mb-4">Tài nguyên Server</h2>
            <div class="space-y-4">
                @foreach($serverMetrics ?? [
                    ['label' => 'CPU', 'value' => $cpuLoad ?? 0, 'color' => 'bg-indigo-500'],
                    ['label' => 'Bộ nhớ', 'value' => $memoryUsage ?? 0, 'color' => 'bg-blue-500'],
                    ['label' => 'Disk I/O', 'value' => $diskIo ?? 0, 'color' => 'bg-emerald-500'],
                    ['label' => 'Network', 'value' => $network ?? 0, 'color' => 'bg-amber-500'],
                ] as $m)
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="font-medium text-slate-700">{{ $m['label'] }}</span>
                        <span class="font-bold text-slate-900">{{ $m['value'] }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all {{ $m['color'] }}" style="width: {{ $m['value'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-3 text-center">
                <div>
                    <p class="text-sm font-bold text-slate-900">{{ $uptime ?? '99.98%' }}</p>
                    <p class="text-[11px] text-slate-500">Thời gian hoạt động</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">{{ number_format($requestsPerSec ?? 0) }}</p>
                    <p class="text-[11px] text-slate-500">Req/giây</p>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">{{ $avgLatency ?? '0ms' }}</p>
                    <p class="text-[11px] text-slate-500">Độ trễ</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Câu hỏi trong ngân hàng', 'value' => number_format($totalQuestions ?? 0)],
            ['label' => 'Bài thi đã thực hiện', 'value' => number_format($totalExams ?? 0)],
            ['label' => 'Công việc OCR hôm nay', 'value' => $ocrToday ?? 0],
            ['label' => 'Ticket hỗ trợ', 'value' => ($openTickets ?? 0) . ' mở'],
        ] as $s)
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 text-center">
            <p class="text-xl font-bold text-slate-900">{{ $s['value'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $s['label'] }}</p>
        </div>
        @endforeach
    </div>
</div>