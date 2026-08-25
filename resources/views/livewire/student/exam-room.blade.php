{{-- ExamRoom view: KHÔNG wrap <x-layouts.exam> vì #[Layout] trên class đã handle --}}
{{-- Layout (layouts/exam.blade.php) inject content này vào $slot --}}
<div>
{{-- ══ EXAM HEADER ══ --}}
<header class="exam-header">
    <div class="exam-logo">🧠 SmartPrep</div>
    <div class="exam-title">{{ $baiThi->ten_bai_thi ?? 'Bài thi' }}</div>

    {{-- Timer --}}
    <div x-data="examTimer(@entangle('thoiGianConLai').live, @entangle('soLanRoiTab').live)"
         x-init="startTimer()" class="flex items-center gap-3">

        {{-- Anti-cheat warning --}}
        <div x-show="roiTab >= 3"
             class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold animate-pulse"
             style="background:#7f1d1d;color:#fca5a5;display:none">
            ⚠️ Cảnh báo: Rời màn hình <span x-text="roiTab"></span> lần
        </div>

        <div class="exam-timer" :class="{ 'danger': seconds < 300 }" x-text="formatTime(seconds)">
            {{ sprintf('%02d:%02d', intdiv($thoiGianConLai, 60), $thoiGianConLai % 60) }}
        </div>
    </div>

    {{-- Nộp bài --}}
    <div x-data="{ confirm: false }">
        <button @click="confirm = true"
                class="sp-btn px-4 py-2 text-sm font-semibold"
                style="background:#10b981;color:white">
            ✅ Nộp bài
        </button>

        <div x-show="confirm" x-transition
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
             style="display:none">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 animate-slide-up">
                <div class="text-center mb-4">
                    <div class="text-4xl mb-2">📋</div>
                    <h3 class="font-bold text-lg mb-1">Xác nhận nộp bài</h3>
                    <p class="text-sm" style="color:var(--sp-text-secondary)">
                        Đã trả lời <strong>{{ count($dapAnDaChon) }}/{{ count($cauHoiList) }}</strong> câu.
                    </p>
                </div>
                <div class="flex gap-2">
                    <button @click="confirm = false"
                            class="sp-btn sp-btn-outline flex-1 justify-center">Tiếp tục làm</button>
                    <button wire:click="nopBai" @click="confirm = false"
                            class="sp-btn sp-btn-primary flex-1 justify-center">Nộp ngay</button>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ══ EXAM BODY ══ --}}
<div class="exam-layout" wire:poll.30000ms="tuDongLuu">

    {{-- LEFT: Question Panel --}}
    <div class="space-y-4">

        <div wire:loading wire:target="cauTruoc, cauTiep, denCau, nopBai" class="sp-card p-8 text-center w-full">
            <div class="sp-skeleton h-6 w-3/4 mx-auto mb-3" style="height:24px;border-radius:4px"></div>
            <div class="sp-skeleton h-4 w-1/2 mx-auto" style="height:16px;border-radius:4px"></div>
        </div>

        @if(!empty($cauHoiList))
        @php $cq = $cauHoiList[$cauHienTai]; $keys = ['A','B','C','D','E']; @endphp

        <div wire:loading.remove wire:target="cauTruoc, cauTiep, denCau, nopBai" class="w-full">
            <div class="sp-card p-5 animate-fade-in" wire:key="cau-{{ $cauHienTai }}">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold" style="color:var(--sp-text-muted)">
                    Câu {{ $cauHienTai + 1 }} / {{ count($cauHoiList) }}
                </span>
                <div class="flex-1 mx-3 h-1.5 rounded-full" style="background:#e2e8f0">
                    <div class="h-1.5 rounded-full transition-all duration-500"
                         style="width:{{ (($cauHienTai+1)/count($cauHoiList))*100 }}%;background:var(--sp-primary)"></div>
                </div>
                <span class="text-xs" style="color:var(--sp-text-muted)">{{ count($dapAnDaChon) }} đã trả lời</span>
            </div>

            <div class="text-base font-medium mb-5 leading-relaxed" style="color:var(--sp-text-primary)">
                {!! nl2br(e($cq['noi_dung'])) !!}
            </div>

            @if($cq['hinh_anh'] ?? null)
                <img src="{{ Storage::url($cq['hinh_anh']) }}"
                     alt="Hình câu hỏi" class="rounded-lg mb-4 max-h-48 object-contain w-full">
            @endif

            <div class="space-y-2.5">
                @foreach($cq['lua_chon'] as $i => $lc)
                <div wire:click="chonDapAn({{ $cq['id'] }}, {{ $lc['id'] }})"
                     class="exam-option {{ ($dapAnDaChon[$cq['id']] ?? null) == $lc['id'] ? 'selected' : '' }}"
                     style="cursor:pointer">
                    <div class="exam-option-key">{{ $keys[$i] ?? ($i+1) }}</div>
                    <div class="text-sm leading-relaxed">{{ $lc['noi_dung'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between">
            <button wire:click="cauTruoc" class="sp-btn sp-btn-outline"
                    @disabled($cauHienTai === 0)>← Câu trước</button>
            <button wire:click="cauTiep" class="sp-btn sp-btn-primary"
                    @disabled($cauHienTai === count($cauHoiList) - 1)>Câu tiếp →</button>
        </div>
        </div>
        @endif
    </div>

    {{-- RIGHT: Navigator --}}
    <div class="space-y-4">
        <div class="sp-card p-4">
            <div class="text-xs font-semibold mb-3" style="color:var(--sp-text-secondary)">BẢNG CÂU HỎI</div>
            <div class="grid gap-2" style="grid-template-columns: repeat(5, 1fr)">
                @foreach($cauHoiList as $i => $cq)
                <button wire:click="denCau({{ $i }})"
                        class="exam-q-nav-btn
                               {{ $i === $cauHienTai ? 'current' : '' }}
                               {{ isset($dapAnDaChon[$cq['id']]) ? 'answered' : '' }}">
                    {{ $i + 1 }}
                </button>
                @endforeach
            </div>
            <div class="mt-3 pt-3 border-t text-xs space-y-1" style="border-color:var(--sp-border)">
                <div class="flex items-center gap-2">
                    <div style="width:16px;height:16px;border-radius:4px;background:var(--sp-primary)"></div>
                    <span style="color:var(--sp-text-muted)">Đã trả lời ({{ count($dapAnDaChon) }})</span>
                </div>
                <div class="flex items-center gap-2">
                    <div style="width:16px;height:16px;border-radius:4px;border:2px solid var(--sp-accent)"></div>
                    <span style="color:var(--sp-text-muted)">Đang xem</span>
                </div>
            </div>
        </div>

        <div class="sp-card px-4 py-3 flex items-center gap-2 text-xs" style="color:var(--sp-text-muted)">
            <span wire:loading wire:target="tuDongLuu" style="display:none">⏳ Đang lưu...</span>
            <span wire:loading.remove wire:target="tuDongLuu">💾 Tự động lưu mỗi 30s</span>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
function examTimer(seconds, roiTab) {
    return {
        seconds: seconds,
        roiTab: roiTab,
        interval: null,

        startTimer() {
            this.$watch('seconds', val => { if (val <= 0) this.autoSubmit(); });
            this.interval = setInterval(() => {
                if (this.seconds > 0) {
                    this.seconds--;
                    if (this.seconds === 0) this.autoSubmit();
                }
            }, 1000);

            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.roiTab++;
                    @this.ghiNhanRoiTab();
                }
            });

            document.addEventListener('contextmenu', e => e.preventDefault());
        },

        formatTime(secs) {
            const m = String(Math.floor(secs / 60)).padStart(2, '0');
            const s = String(secs % 60).padStart(2, '0');
            return `${m}:${s}`;
        },

        autoSubmit() {
            clearInterval(this.interval);
            @this.nopBai();
        }
    }
}
</script>
@endpush
