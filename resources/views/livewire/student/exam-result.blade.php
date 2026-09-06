<div class="p-4 sm:p-6 max-w-2xl mx-auto space-y-4">

    {{-- Score Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 pt-6 pb-4">
            <h1 class="text-xl font-bold text-slate-900">Kết quả bài thi</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $examTitle ?? 'Bài thi' }} &nbsp;·&nbsp; {{ $duration ?? '' }}</p>
        </div>

        {{-- Score Ring --}}
        <div class="flex flex-col items-center py-6">
            @php
                $pct   = $percentage ?? 0;
                $r     = 52;
                $circ  = round(2 * M_PI * $r, 2);
                $dash  = round($circ - ($pct / 100) * $circ, 2);
                $color = $pct >= 80 ? '#4f46e5' : ($pct >= 65 ? '#3b82f6' : ($pct >= 50 ? '#f59e0b' : '#f43f5e'));
            @endphp
            <div class="relative w-36 h-36">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="{{ $r }}" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                    <circle cx="60" cy="60" r="{{ $r }}" fill="none"
                        stroke="{{ $color }}" stroke-width="10"
                        stroke-dasharray="{{ $circ }}"
                        stroke-dashoffset="{{ $dash }}"
                        stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-bold text-slate-900">{{ $pct }}%</span>
                    <span class="text-xs text-slate-500 font-medium">{{ $correct ?? 0 }}/{{ $total ?? 0 }}</span>
                </div>
            </div>

            <span class="mt-4 inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold border
                {{ $rank['color'] === 'emerald' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' :
                  ($rank['color'] === 'indigo'  ? 'bg-indigo-50 text-indigo-700 border-indigo-200' :
                  ($rank['color'] === 'blue'    ? 'bg-blue-50 text-blue-700 border-blue-200' :
                  ($rank['color'] === 'amber'   ? 'bg-amber-50 text-amber-700 border-amber-200' :
                                                  'bg-rose-50 text-rose-700 border-rose-200'))) }}">
                {{ $rank['label'] ?? '' }}
            </span>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-4 gap-0 border-t border-slate-100 divide-x divide-slate-100">
            <div class="flex flex-col items-center justify-center py-5 gap-1.5">
                <div class="w-9 h-9 rounded-full bg-emerald-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
                </div>
                <span class="text-xl font-bold text-slate-900">{{ $correct ?? 0 }}</span>
                <span class="text-xs text-slate-400">Đúng</span>
            </div>
            <div class="flex flex-col items-center justify-center py-5 gap-1.5">
                <div class="w-9 h-9 rounded-full bg-rose-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#f43f5e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <span class="text-xl font-bold text-slate-900">{{ $wrong ?? 0 }}</span>
                <span class="text-xs text-slate-400">Sai</span>
            </div>
            <div class="flex flex-col items-center justify-center py-5 gap-1.5">
                <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                </div>
                <span class="text-xl font-bold text-slate-900">{{ $skipped ?? 0 }}</span>
                <span class="text-xs text-slate-400">Bỏ qua</span>
            </div>
            <div class="flex flex-col items-center justify-center py-5 gap-1.5">
                <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="text-xl font-bold text-slate-900 font-mono tracking-tight">{{ $timeUsed ?? '—' }}</span>
                <span class="text-xs text-slate-400">Thời gian</span>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('student.thi') }}" wire:navigate
           class="flex items-center justify-center gap-2 h-11 bg-indigo-600 hover:bg-indigo-700 rounded-xl text-white text-sm font-semibold transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.17"/></svg>
            Thi lại
        </a>
        <a href="{{ route('student.history') }}" wire:navigate
           class="flex items-center justify-center gap-2 h-11 border border-slate-200 rounded-xl text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Trang chủ
        </a>
    </div>

    {{-- Answer Review --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-slate-900">Xem lại đáp án</h2>
                <p class="text-xs text-slate-500 mt-0.5">Bấm vào câu để xem giải thích · 🤖 để hỏi Gia Sư AI</p>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>Đúng</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-400 shrink-0"></span>Sai</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>Bỏ qua</span>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($reviewItems ?? [] as $i => $item)
            <div class="px-5 py-4" x-data="{ open: false }">
                <div class="flex items-start gap-3 cursor-pointer" @click="open = !open">
                    {{-- Status dot --}}
                    <div class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center shrink-0
                        {{ $item['status'] === 'correct' ? 'bg-emerald-50' : ($item['status'] === 'wrong' ? 'bg-rose-50' : 'bg-slate-100') }}">
                        @if($item['status'] === 'correct')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        @elseif($item['status'] === 'wrong')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#f43f5e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 leading-snug">
                            <span class="text-slate-400 font-normal">Câu {{ $i + 1 }}:</span>
                            {{ $item['question'] ?? '' }}
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if($item['userAnswer'] ?? null)
                            <span class="text-xs font-medium px-2.5 py-1 rounded-lg border
                                {{ $item['status'] === 'correct' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                Bạn chọn: {{ $item['userAnswer'] }}
                            </span>
                            @endif
                            @if($item['status'] !== 'correct' && ($item['correctAnswer'] ?? null))
                            <span class="text-xs font-medium px-2.5 py-1 rounded-lg border bg-emerald-50 text-emerald-700 border-emerald-200">
                                Đáp án: {{ $item['correctAnswer'] }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-400 shrink-0 mt-1 transition-transform duration-200" :class="{ 'rotate-180': open }"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                {{-- Expanded panel --}}
                <div x-show="open" x-collapse class="mt-3 ml-9 space-y-3">

                    {{-- AI Explanation --}}
                    @if($item['explanation'] ?? null)
                        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <p class="text-xs font-semibold text-indigo-700 mb-1 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                Giải thích AI
                            </p>
                            <p class="text-xs text-indigo-800 leading-relaxed">{{ $item['explanation'] }}</p>
                        </div>
                    @elseif($item['status'] !== 'correct')
                        <button wire:click="generateExplanation({{ $item['id'] }})"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            Giải thích bằng AI
                        </button>
                        @if(session('error_' . $item['id']))
                            <p class="text-xs text-rose-500 mt-1">{{ session('error_' . $item['id']) }}</p>
                        @endif
                    @else
                        <p class="text-xs text-slate-400 italic">Bạn đã trả lời đúng câu này.</p>
                    @endif

                    {{-- AI Tutor button --}}
                    @if($item['status'] !== 'correct')
                    <button wire:click="openTutor({{ $item['id'] }})"
                            wire:loading.attr="disabled"
                            wire:target="openTutor({{ $item['id'] }})"
                            class="inline-flex items-center gap-2 text-xs font-semibold px-3 py-2 rounded-xl
                                   bg-gradient-to-r from-violet-500 to-indigo-600 text-white hover:from-violet-600 hover:to-indigo-700 transition-all shadow-sm">
                        <span wire:loading.remove wire:target="openTutor({{ $item['id'] }})">🤖</span>
                        <span wire:loading wire:target="openTutor({{ $item['id'] }})" style="display:none">
                            <svg class="animate-spin w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                        <span wire:loading.remove wire:target="openTutor({{ $item['id'] }})">Hỏi Gia Sư AI</span>
                        <span wire:loading wire:target="openTutor({{ $item['id'] }})" style="display:none">Đang kết nối…</span>
                    </button>
                    @endif

                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-slate-400">Chưa có dữ liệu đáp án.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════
     AI SMART TUTOR — Floating Chat Panel
     Hiển thị khi $activeChatKetQuaId != null
════════════════════════════════════════════════════════════ --}}
@if($activeChatKetQuaId !== null)
<div
    class="fixed bottom-0 right-0 sm:bottom-6 sm:right-6 z-50 w-full sm:w-96 flex flex-col"
    style="height: min(580px, 90vh);"
    x-data="{}"
    x-init="$nextTick(() => { const el = document.getElementById('tutor-messages'); if(el) el.scrollTop = el.scrollHeight; })"
    @tutor-scroll.window="$nextTick(() => { const el = document.getElementById('tutor-messages'); if(el) el.scrollTop = el.scrollHeight; })">

    {{-- Card --}}
    <div class="flex flex-col h-full bg-white sm:rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-3.5 flex-shrink-0"
             style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);">
            <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg flex-shrink-0">🤖</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white">SmartPrep AI Tutor</p>
                <p class="text-xs text-white/70">Gia sư thông minh · Luôn sẵn sàng</p>
            </div>
            <button wire:click="closeTutor"
                    class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Error Banner --}}
        @if($tutorError)
        <div class="px-4 py-2 bg-rose-50 border-b border-rose-100 text-xs text-rose-600 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="shrink-0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $tutorError }}
        </div>
        @endif

        {{-- Messages --}}
        <div id="tutor-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-slate-50/50">
            @foreach($chatHistory as $msg)
                @if($msg['role'] !== 'system')

                @if($msg['role'] === 'model')
                {{-- AI message --}}
                <div class="flex items-end gap-2">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-xs flex-shrink-0">🤖</div>
                    <div class="max-w-[80%] bg-white border border-slate-200 rounded-2xl rounded-bl-sm px-3.5 py-2.5 shadow-sm">
                        <p class="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap">{{ $msg['content'] }}</p>
                    </div>
                </div>

                @else
                {{-- User message --}}
                <div class="flex items-end gap-2 flex-row-reverse">
                    <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="max-w-[80%] bg-indigo-600 rounded-2xl rounded-br-sm px-3.5 py-2.5 shadow-sm">
                        <p class="text-sm text-white leading-relaxed whitespace-pre-wrap">{{ $msg['content'] }}</p>
                    </div>
                </div>
                @endif

                @endif {{-- end role !== system --}}
            @endforeach

            {{-- AI thinking indicator --}}
            <div wire:loading wire:target="sendMessage" style="display:none"
                 class="flex items-end gap-2">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-xs flex-shrink-0">🤖</div>
                <div class="bg-white border border-slate-200 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Input --}}
        <div class="px-3 py-3 border-t border-slate-100 bg-white flex-shrink-0">
            <div class="flex items-end gap-2">
                <div class="flex-1 relative">
                    <textarea
                        wire:model="userMessage"
                        wire:keydown.enter.prevent="sendMessage"
                        id="tutor-input"
                        rows="1"
                        placeholder="Hỏi gia sư AI…"
                        class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 focus:bg-white
                               transition-colors max-h-28 min-h-[42px]"
                        style="field-sizing: content;"
                        oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 112) + 'px'"></textarea>
                </div>
                <button wire:click="sendMessage"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage"
                        class="w-10 h-10 flex-shrink-0 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center transition-colors">
                    <span wire:loading.remove wire:target="sendMessage">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </span>
                    <span wire:loading wire:target="sendMessage" style="display:none">
                        <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
            <p class="text-[10px] text-slate-400 mt-1.5 text-center">Enter để gửi · AI có thể mắc sai sót</p>
        </div>

    </div>
</div>

{{-- Auto-scroll after AI replies --}}
<script>
    window.addEventListener('livewire:update', () => {
        const el = document.getElementById('tutor-messages');
        if (el) setTimeout(() => { el.scrollTop = el.scrollHeight; }, 50);
    });
</script>
@endif