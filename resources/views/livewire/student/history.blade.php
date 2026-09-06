<div class="p-6 max-w-5xl mx-auto space-y-6">
    <h1 class="text-xl font-bold text-slate-900">Lịch sử &amp; Phân tích</h1>

    {{-- Subject Strengths Radar Chart --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h2 class="font-semibold text-slate-900 mb-0.5">Điểm mạnh theo môn</h2>
        <p class="text-xs text-slate-500 mb-5">Điểm trung bình theo từng môn qua tất cả các lần thi.</p>

        @if(count($subjectStats) > 0)
        <div class="flex justify-center">
            <div class="w-full max-w-lg">
                <canvas id="subjectRadarChart" style="max-height:380px"></canvas>
            </div>
        </div>
        @else
        <div class="py-10 text-center">
            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-300"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
            </div>
            <p class="text-sm text-slate-400">Chưa có dữ liệu phân tích. Hãy làm một số bài thi!</p>
        </div>
        @endif
    </div>

    {{-- Past Exams Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Các bài thi đã làm</h2>
            <span class="text-xs font-medium text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">{{ $examLog->total() }} bài</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ngày</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Môn học</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Điểm</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Xếp loại</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($examLog as $lt)
                    @php
                        $pct = $lt->baiThi->so_cau_hoi > 0
                            ? round($lt->so_cau_dung / $lt->baiThi->so_cau_hoi * 100)
                            : round(($lt->diem_so ?? 0) * 10);
                        $grade = match(true) {
                            $pct >= 90 => 'A+',
                            $pct >= 85 => 'A',
                            $pct >= 80 => 'A-',
                            $pct >= 75 => 'B+',
                            $pct >= 70 => 'B',
                            $pct >= 65 => 'B-',
                            $pct >= 60 => 'C+',
                            $pct >= 55 => 'C',
                            $pct >= 50 => 'C-',
                            default    => 'F',
                        };
                        $gradeColor = match($grade[0]) {
                            'A' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'B' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                            'C' => 'bg-amber-100 text-amber-700 border-amber-200',
                            default => 'bg-rose-100 text-rose-700 border-rose-200',
                        };
                        $thoiGianGiay = $lt->thoi_gian_lam ?? 0;
                        $durationFmt = $thoiGianGiay > 0
                            ? sprintf('%dm %ds', intdiv($thoiGianGiay, 60), $thoiGianGiay % 60)
                            : '—';
                        $subjectName = ($lt->baiThi->monHoc?->ten ?? '') . ($lt->baiThi->ten_bai_thi ? ' – ' . $lt->baiThi->ten_bai_thi : '');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-slate-500 font-medium whitespace-nowrap text-sm">
                            {{ $lt->ket_thuc_luc?->format('Y-m-d') ?? $lt->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-900 font-medium">{{ $subjectName }}</td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold text-sm {{ $pct >= 80 ? 'text-emerald-600' : ($pct >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ $pct }}%
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-sm">{{ $durationFmt }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center justify-center w-10 h-7 rounded-full text-xs font-bold border {{ $gradeColor }}">
                                {{ $grade }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <a href="{{ route('exam.result', $lt->id) }}" wire:navigate
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                Xem
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-200"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm text-slate-400">Bạn chưa hoàn thành bài thi nào.</p>
                                <a href="{{ route('student.thi') }}" wire:navigate class="text-xs font-semibold text-indigo-600 hover:underline">Bắt đầu thi ngay →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($examLog->hasPages())
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-500">
                Hiển thị {{ $examLog->firstItem() }}–{{ $examLog->lastItem() }} / {{ $examLog->total() }}
            </p>
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if($examLog->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                @else
                    <button wire:click="previousPage" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                @endif

                {{-- Page numbers --}}
                @foreach($examLog->getUrlRange(1, $examLog->lastPage()) as $page => $url)
                    <button wire:click="gotoPage({{ $page }})"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-semibold transition-colors
                            {{ $page == $examLog->currentPage() ? 'bg-indigo-600 text-white' : 'hover:bg-slate-200 text-slate-600' }}">
                        {{ $page }}
                    </button>
                @endforeach

                {{-- Next --}}
                @if($examLog->hasMorePages())
                    <button wire:click="nextPage" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-200 text-slate-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                @else
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

@if(count($subjectStats) > 0)
@push('scripts')
<script>
(function initRadarChart() {
    var labels = @json(collect($subjectStats)->pluck('subject')->values());
    var scores = @json(collect($subjectStats)->pluck('score')->values());

    // External custom tooltip matching Figma design
    var tooltipEl = null;
    function getOrCreateTooltip(chart) {
        if (!tooltipEl) {
            tooltipEl = document.createElement('div');
            tooltipEl.style.cssText = [
                'background:#fff',
                'border:1px solid #e2e8f0',
                'border-radius:10px',
                'box-shadow:0 4px 16px rgba(0,0,0,0.08)',
                'padding:10px 14px',
                'pointer-events:none',
                'position:absolute',
                'transition:opacity 0.15s',
                'font-family:Inter,sans-serif',
                'min-width:120px',
            ].join(';');
            chart.canvas.parentNode.style.position = 'relative';
            chart.canvas.parentNode.appendChild(tooltipEl);
        }
        return tooltipEl;
    }

    function externalTooltip(context) {
        var tooltip = context.tooltip;
        var el = getOrCreateTooltip(context.chart);

        if (tooltip.opacity === 0) {
            el.style.opacity = '0';
            return;
        }

        var label  = tooltip.dataPoints[0].label;
        var value  = tooltip.dataPoints[0].raw;

        el.innerHTML =
            '<p style="font-size:13px;font-weight:700;color:#1e293b;margin:0 0 2px">' + label + '</p>' +
            '<p style="font-size:13px;font-weight:600;color:#6366f1;margin:0">Score : ' + value + '%</p>';

        var pos = context.chart.canvas.getBoundingClientRect();
        var cPos = context.chart.canvas.parentNode.getBoundingClientRect();
        el.style.opacity = '1';
        el.style.left = (tooltip.caretX - cPos.left + pos.left - cPos.left) + 'px';
        el.style.top  = (tooltip.caretY - cPos.top  + pos.top  - cPos.top - el.offsetHeight - 8) + 'px';
    }

    function buildChart() {
        var el = document.getElementById('subjectRadarChart');
        if (!el) return;
        if (el._chartInstance) { el._chartInstance.destroy(); el._chartInstance = null; tooltipEl = null; }
        el._chartInstance = new Chart(el, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    data: scores,
                    backgroundColor: 'rgba(99, 102, 241, 0.15)',
                    borderColor: '#6366f1',
                    borderWidth: 2,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#6366f1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    r: {
                        min: 0, max: 100,
                        ticks: { display: false },
                        grid: { color: '#e2e8f0' },
                        angleLines: { color: '#e2e8f0' },
                        pointLabels: {
                            font: { size: 12, family: "'Inter', sans-serif" },
                            color: '#64748b',
                            padding: 10,
                        },
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: false,
                        external: externalTooltip,
                    },
                },
                animation: { duration: 500 },
            }
        });
    }

    if (typeof Chart !== 'undefined') {
        buildChart();
    } else {
        var s = document.querySelector('script[src*="chart.js"]');
        if (s) s.addEventListener('load', buildChart);
    }
})();
</script>
@endpush
@endif