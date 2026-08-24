<div class="space-y-6">

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['icon'=>'🎯','value'=>$tongQuan['tong_luot_thi'] ?? 0,'label'=>'Lượt thi','color'=>'#6366f1','bg'=>'#eef2ff'],
                ['icon'=>'📊','value'=>number_format($tongQuan['diem_trung_binh'] ?? 0, 1),'label'=>'Điểm TB','color'=>'#10b981','bg'=>'#d1fae5'],
                ['icon'=>'📚','value'=>$tongQuan['so_chuong_da_hoc'] ?? 0,'label'=>'Chương đã học','color'=>'#f59e0b','bg'=>'#fef3c7'],
                ['icon'=>'⚠️','value'=>$tongQuan['so_cau_diem_yeu'] ?? 0,'label'=>'Điểm yếu','color'=>'#ef4444','bg'=>'#fee2e2'],
            ];
        @endphp

        @foreach($statCards as $card)
        <div class="sp-card sp-stat-card sp-stat-card-wrap">
            <div class="sp-stat-icon" style="background:{{ $card['bg'] }}">
                <span>{{ $card['icon'] }}</span>
            </div>
            <div>
                <div class="sp-stat-value" style="color:{{ $card['color'] }}">{{ $card['value'] }}</div>
                <div class="sp-stat-label">{{ $card['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Charts Row ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Radar Chart --}}
        <div class="lg:col-span-2 sp-card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-base" style="color:var(--sp-text-primary)">Bản đồ kiến thức</h2>
                    <p class="text-xs mt-0.5" style="color:var(--sp-text-muted)">Mức độ thành thạo theo chương</p>
                </div>
                <select wire:model.live="selectedMonHocId"
                        class="sp-input sp-select text-sm" style="width:auto">
                    <option value="0">-- Chọn môn --</option>
                    @foreach($monHocs as $m)
                        <option value="{{ $m->id }}">{{ $m->ten }}</option>
                    @endforeach
                </select>
            </div>

            @if(!empty($radarData['categories']))
                <div id="radarChart" style="min-height:280px"></div>
            @else
                <div class="flex items-center justify-center h-48 text-sm"
                     style="color:var(--sp-text-muted)">
                    Chưa có dữ liệu — Hãy làm ít nhất 1 bài thi 🎯
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="sp-card p-5 flex flex-col gap-3">
            <h2 class="font-bold text-base mb-1" style="color:var(--sp-text-primary)">Bắt đầu ngay</h2>

            <a href="{{ route('student.thi') }}"
               class="sp-btn sp-btn-primary w-full justify-center py-3 text-sm font-semibold">
                ⚡ Thi thử ngay
            </a>

            <a href="{{ route('student.on-tap') }}"
               class="sp-btn w-full justify-center py-3 text-sm font-semibold"
               style="background:#fef3c7;color:#92400e;border:none">
                🔄 Ôn điểm yếu
            </a>

            <a href="{{ route('student.thi') }}?mode=kho"
               class="sp-btn sp-btn-outline w-full justify-center py-3 text-sm font-semibold">
                🔥 Thử thách khó
            </a>

            {{-- Progress circular --}}
            @if(isset($tongQuan['diem_trung_binh']))
            <div class="mt-auto pt-4 border-t" style="border-color:var(--sp-border)">
                <div class="text-xs font-medium mb-2" style="color:var(--sp-text-secondary)">Tiến độ tổng thể</div>
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center justify-center w-14 h-14">
                        <svg viewBox="0 0 36 36" class="w-14 h-14 -rotate-90">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#6366f1" stroke-width="3"
                                    stroke-dasharray="{{ min(100, ($tongQuan['diem_trung_binh'] / 10) * 100) }} 100"
                                    stroke-linecap="round"/>
                        </svg>
                        <span class="absolute text-xs font-bold" style="color:var(--sp-primary)">
                            {{ number_format($tongQuan['diem_trung_binh'] ?? 0, 0) }}
                        </span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold">Điểm trung bình</div>
                        <div class="text-xs" style="color:var(--sp-text-muted)">{{ $tongQuan['tong_luot_thi'] ?? 0 }} lượt thi</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Mistake Heatmap ── --}}
    @if($cauHoiSai->isNotEmpty())
    <div class="sp-card p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-bold text-base" style="color:var(--sp-text-primary)">Bản đồ lỗi sai</h2>
                <p class="text-xs mt-0.5" style="color:var(--sp-text-muted)">Click vào ô để xem chi tiết câu hỏi</p>
            </div>
            <div class="flex items-center gap-2 text-xs" style="color:var(--sp-text-muted)">
                <span>Ít sai</span>
                <div class="flex gap-1">
                    @foreach([0,1,2,3,4] as $l)
                        <div class="w-4 h-4 heatmap-cell level-{{ $l }}"></div>
                    @endforeach
                </div>
                <span>Nhiều sai</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($cauHoiSai as $stat)
                @php
                    $soLanSai = $stat['so_lan_sai'] ?? 0;
                    $level = match(true) {
                        $soLanSai >= 5 => 4,
                        $soLanSai >= 3 => 3,
                        $soLanSai >= 2 => 2,
                        default => 1,
                    };
                @endphp
                <div x-data="{ show: false }" class="relative">
                    <div class="heatmap-cell level-{{ $level }} w-9 h-9 flex items-center justify-center
                                text-xs font-bold cursor-pointer"
                         @mouseenter="show=true" @mouseleave="show=false"
                         title="{{ Str::limit($stat['noi_dung'] ?? 'Câu hỏi', 60) }}">
                        {{ $soLanSai }}
                    </div>
                    <div x-show="show" x-transition
                         class="absolute z-10 bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 p-3
                                bg-slate-800 text-white text-xs rounded-lg shadow-xl"
                         style="display:none">
                        <p class="font-medium mb-1">{{ Str::limit($stat['noi_dung'] ?? 'Câu hỏi', 80) }}</p>
                        <p style="color:#94a3b8">Sai {{ $soLanSai }} lần • {{ $stat['chuong'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Recent Exams ── --}}
    @if($luotThiGanDay->isNotEmpty())
    <div class="sp-card overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between"
             style="border-color:var(--sp-border)">
            <h2 class="font-bold text-base" style="color:var(--sp-text-primary)">Lịch sử thi gần đây</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Tên bài thi</th>
                        <th>Môn học</th>
                        <th>Điểm</th>
                        <th>Thời gian</th>
                        <th>Kết quả</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($luotThiGanDay as $lt)
                    <tr>
                        <td class="font-medium">{{ $lt->baiThi?->ten_bai_thi ?? 'Không rõ' }}</td>
                        <td>
                            <span class="sp-badge sp-badge-indigo">
                                {{ $lt->baiThi?->monHoc?->ten ?? '?' }}
                            </span>
                        </td>
                        <td>
                            <span class="font-bold text-lg"
                                  style="color: {{ $lt->diem_so >= 5 ? 'var(--sp-accent)' : 'var(--sp-danger)' }}">
                                {{ number_format($lt->diem_so, 1) }}
                            </span>
                            <span class="text-xs" style="color:var(--sp-text-muted)">/10</span>
                        </td>
                        <td class="text-xs" style="color:var(--sp-text-muted)">
                            {{ $lt->ket_thuc_luc?->diffForHumans() ?? '—' }}
                        </td>
                        <td>
                            @if($lt->diem_so >= 5)
                                <span class="sp-badge sp-badge-green">✅ Đạt</span>
                            @else
                                <span class="sp-badge sp-badge-red">❌ Chưa đạt</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('exam.result', $lt) }}"
                               class="sp-btn sp-btn-outline text-xs py-1 px-2">Xem lại</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(!empty($radarData['categories']))
    const radarOptions = {
        chart: { type: 'radar', height: 280, toolbar: { show: false },
                 fontFamily: 'Inter, sans-serif' },
        series: [{ name: 'Thành thạo', data: @json($radarData['series'] ?? []) }],
        xaxis:  { categories: @json($radarData['categories'] ?? []) },
        yaxis:  { show: false, min: 0, max: 100 },
        fill:   { opacity: 0.25, colors: ['#6366f1'] },
        stroke: { width: 2, colors: ['#6366f1'] },
        markers:{ size: 4, colors: ['#6366f1'] },
        tooltip: {
            y: { formatter: val => val + '%' }
        },
        plotOptions: {
            radar: { polygons: { strokeColors: '#e2e8f0', fill: { colors: ['#f8fafc','#fff'] } } }
        }
    };
    new ApexCharts(document.querySelector('#radarChart'), radarOptions).render();
    @endif
});
</script>
@endpush
