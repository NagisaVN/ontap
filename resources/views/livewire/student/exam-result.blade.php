<div class="max-w-4xl mx-auto space-y-6 animate-slide-up">

    {{-- ── Score Hero ── --}}
    <div class="sp-card p-6 text-center relative overflow-hidden">
        {{-- Background decoration --}}
        <div class="absolute inset-0 opacity-5"
             style="background: radial-gradient(circle at 50% 0%, #6366f1 0%, transparent 70%)"></div>

        <div class="relative">
            <div class="text-4xl mb-2">{{ $rank['emoji'] }}</div>
            <div class="text-6xl font-black mb-1"
                 style="color: {{ $rank['color'] === 'green' ? 'var(--sp-accent)' : ($rank['color'] === 'red' ? 'var(--sp-danger)' : ($rank['color'] === 'indigo' ? 'var(--sp-primary)' : 'var(--sp-warning)')) }}">
                {{ number_format($luotThi->diem_so, 1) }}
            </div>
            <div class="text-lg font-semibold mb-3" style="color:var(--sp-text-secondary)">/10 điểm</div>

            <span class="sp-badge sp-badge-{{ $rank['color'] }} text-sm px-4 py-1.5">
                {{ $rank['label'] }}
            </span>

            <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t" style="border-color:var(--sp-border)">
                <div>
                    <div class="text-2xl font-bold" style="color:var(--sp-accent)">{{ $soDung }}</div>
                    <div class="text-xs mt-0.5" style="color:var(--sp-text-muted)">✅ Đúng</div>
                </div>
                <div>
                    <div class="text-2xl font-bold" style="color:var(--sp-danger)">{{ $soSai }}</div>
                    <div class="text-xs mt-0.5" style="color:var(--sp-text-muted)">❌ Sai</div>
                </div>
                <div>
                    <div class="text-2xl font-bold" style="color:var(--sp-text-muted)">{{ $soBo }}</div>
                    <div class="text-xs mt-0.5" style="color:var(--sp-text-muted)">⬜ Bỏ trống</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Info Cards ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
        <div class="sp-card px-4 py-3">
            <div class="text-xs mb-1" style="color:var(--sp-text-muted)">Môn học</div>
            <div class="font-semibold">{{ $baiThi->monHoc?->ten ?? '—' }}</div>
        </div>
        <div class="sp-card px-4 py-3">
            <div class="text-xs mb-1" style="color:var(--sp-text-muted)">Tổng câu</div>
            <div class="font-semibold">{{ $tongCau }} câu</div>
        </div>
        <div class="sp-card px-4 py-3">
            <div class="text-xs mb-1" style="color:var(--sp-text-muted)">Thời gian làm</div>
            <div class="font-semibold">
                {{ $luotThi->bat_dau_luc && $luotThi->ket_thuc_luc
                    ? $luotThi->bat_dau_luc->diffInMinutes($luotThi->ket_thuc_luc) . ' phút'
                    : '—' }}
            </div>
        </div>
        <div class="sp-card px-4 py-3">
            <div class="text-xs mb-1" style="color:var(--sp-text-muted)">Nộp lúc</div>
            <div class="font-semibold">{{ $luotThi->ket_thuc_luc?->format('H:i d/m') ?? '—' }}</div>
        </div>
    </div>

    {{-- ── Accordion: Chi tiết từng câu ── --}}
    <div class="sp-card overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between"
             style="border-color:var(--sp-border)">
            <h2 class="font-bold text-base">📋 Chi tiết đáp án</h2>
            <span class="text-xs" style="color:var(--sp-text-muted)">
                Click vào câu để xem giải thích AI
            </span>
        </div>

        <div x-data="{ open: null }" class="divide-y" style="border-color:var(--sp-border)">
            @foreach($luotThi->ketQua as $i => $ketQua)
            @php
                $cauHoi    = $ketQua->cauHoi;
                $daDung    = $ketQua->dung_sai;
                $dapAnDung = $cauHoi?->luaChon->firstWhere('la_dap_an', true);
                $daDaChon  = $ketQua->luaChonDaChon;
                $keys      = ['A','B','C','D','E'];
            @endphp

            <div x-data>
                {{-- Header --}}
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 transition-colors text-left">
                    <span class="text-lg flex-shrink-0">{{ $daDung ? '✅' : '❌' }}</span>
                    <span class="text-sm font-medium flex-1">
                        Câu {{ $i + 1 }}: {{ Str::limit($cauHoi?->noi_dung, 80) }}
                    </span>
                    <svg class="w-4 h-4 transition-transform flex-shrink-0"
                         :class="{ 'rotate-180': open === {{ $i }} }"
                         style="color:var(--sp-text-muted)"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Body --}}
                <div x-show="open === {{ $i }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-1"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     style="display:none">
                    <div class="px-5 pb-5 pt-1 space-y-3">

                        {{-- Câu hỏi đầy đủ --}}
                        <p class="text-sm font-medium leading-relaxed" style="color:var(--sp-text-primary)">
                            {{ $cauHoi?->noi_dung }}
                        </p>

                        {{-- Các lựa chọn --}}
                        @if($cauHoi)
                        <div class="space-y-2">
                            @foreach($cauHoi->luaChon as $j => $lc)
                            <div class="exam-option text-sm
                                        {{ $lc->la_dap_an ? 'correct' : '' }}
                                        {{ ($daDaChon?->id === $lc->id && !$lc->la_dap_an) ? 'incorrect' : '' }}">
                                <div class="exam-option-key">{{ $keys[$j] ?? ($j+1) }}</div>
                                <span>{{ $lc->noi_dung }}</span>
                                @if($lc->la_dap_an)
                                    <span class="ml-auto text-xs font-bold" style="color:var(--sp-accent)">✓ Đúng</span>
                                @elseif($daDaChon?->id === $lc->id)
                                    <span class="ml-auto text-xs font-bold" style="color:var(--sp-danger)">✗ Bạn chọn</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- AI Giải thích --}}
                        <div class="rounded-xl p-4 mt-2"
                             style="background: linear-gradient(135deg, #eef2ff, #f0fdf4)">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-base">🤖</span>
                                <span class="text-xs font-bold" style="color:var(--sp-primary)">Giải thích AI</span>
                            </div>
                            @if($ketQua->giai_thich_ai)
                                <div class="text-sm leading-relaxed prose prose-sm max-w-none"
                                     style="color:var(--sp-text-secondary)">
                                    {!! Str::markdown($ketQua->giai_thich_ai) !!}
                                </div>
                            @elseif(!$daDung)
                                <div class="flex items-center gap-2 text-xs" style="color:var(--sp-text-muted)">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    AI đang phân tích, tải lại trang sau vài giây...
                                </div>
                            @else
                                <p class="text-xs" style="color:var(--sp-text-muted)">Bạn đã trả lời đúng câu này! 🎉</p>
                            @endif
                        </div>

                        {{-- Giải thích từ Teacher --}}
                        @if($cauHoi?->giai_thich)
                        <div class="text-xs p-3 rounded-lg" style="background:#f8fafc;color:var(--sp-text-secondary)">
                            📖 <strong>Giải thích:</strong> {{ $cauHoi->giai_thich }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex gap-3 justify-center pb-4">
        <a href="{{ route('dashboard') }}" wire:navigate class="sp-btn sp-btn-outline">🏠 Dashboard</a>
        <a href="{{ route('student.thi') }}" wire:navigate.hover class="sp-btn sp-btn-primary">🔁 Thi lại</a>
        <a href="{{ route('student.on-tap') }}" wire:navigate
           class="sp-btn"
           style="background:#fef3c7;color:#92400e">⚡ Ôn điểm yếu</a>
    </div>

</div>
