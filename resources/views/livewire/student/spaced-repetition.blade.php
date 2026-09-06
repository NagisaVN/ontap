<div class="p-6 max-w-2xl mx-auto space-y-6" x-data="{ flipped: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Ôn điểm yếu</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $totalCards ?? 0 }} thẻ cần ôn · Còn {{ $dueToday ?? 0 }} hôm nay</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-500 bg-slate-100 rounded-lg px-3 py-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            {{ $sessionCount ?? 0 }}/{{ $sessionTotal ?? 20 }} hôm nay
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
        @php $pct = ($sessionTotal ?? 20) > 0 ? round(($sessionCount ?? 0) / ($sessionTotal ?? 20) * 100) : 0; @endphp
        <div class="h-full bg-indigo-500 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
    </div>

    @if($currentCard ?? null)
    {{-- Flash Card --}}
    <div class="flip-card-container h-72 cursor-pointer" @click="flipped = !flipped">
        <div class="flip-card-inner h-full" :class="{ flipped: flipped }">
            {{-- Front --}}
            <div class="flip-card-front bg-white rounded-2xl border border-slate-200 shadow-sm p-8 flex flex-col items-center justify-center text-center h-full">
                <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full mb-4">{{ $currentCard->subject }}</span>
                <p class="text-slate-900 font-medium leading-relaxed">{{ $currentCard->question }}</p>
                <p class="text-xs text-slate-400 mt-6">Nhấp để xem đáp án →</p>
            </div>
            {{-- Back --}}
            <div class="flip-card-back bg-indigo-600 rounded-2xl shadow-sm p-8 flex flex-col items-center justify-center text-center h-full">
                <p class="text-white font-bold text-lg leading-relaxed">{{ $currentCard->answer }}</p>
                @if($currentCard->explanation)
                <p class="text-indigo-200 text-sm mt-3 leading-relaxed">{{ $currentCard->explanation }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Rating buttons --}}
    <div x-show="flipped" style="display:none" class="grid grid-cols-3 gap-3">
        <button wire:click="rateCard('hard')" class="h-11 rounded-xl border-2 border-rose-200 bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100 transition-colors" @click="flipped = false">
            😓 Khó
        </button>
        <button wire:click="rateCard('medium')" class="h-11 rounded-xl border-2 border-amber-200 bg-amber-50 text-amber-700 text-sm font-semibold hover:bg-amber-100 transition-colors" @click="flipped = false">
            🤔 Ổn
        </button>
        <button wire:click="rateCard('easy')" class="h-11 rounded-xl border-2 border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-semibold hover:bg-emerald-100 transition-colors" @click="flipped = false">
            😊 Dễ
        </button>
    </div>
    @else
    {{-- All done state --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" class="text-emerald-600" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mb-1">Hoàn thành phiên ôn tập!</h2>
        <p class="text-sm text-slate-500">Bạn đã ôn tập {{ $sessionCount ?? 0 }} thẻ hôm nay. Hẹn gặp lại ngày mai!</p>
        <a href="{{ route('dashboard') }}" wire:navigate class="mt-6 inline-flex items-center gap-2 h-10 px-6 bg-indigo-600 hover:bg-indigo-700 rounded-xl text-white text-sm font-semibold transition-colors">
            Quay về trang chủ
        </a>
    </div>
    @endif
</div>