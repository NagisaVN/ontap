<div class="spaced-review p-6 max-w-xl mx-auto">
    @if ($currentCard)
        <div wire:key="review-card-{{ $currentCard->id }}" x-data="{ flipped: false }">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-lg font-bold text-slate-900">Ôn tập lặp lại</h1>
                <span class="text-xs font-medium text-slate-500">{{ $currentPosition }} / {{ $totalCards }} thẻ</span>
            </div>

            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden mb-6">
                @php
                    $progress = $totalCards > 0
                        ? min(100, round(($currentPosition / $totalCards) * 100))
                        : 0;
                @endphp
                <div class="h-full bg-indigo-500 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
            </div>

            <p class="text-xs font-medium text-slate-500 mb-4">{{ $currentCard->subject }}</p>

            <div
                class="flip-card-container w-full h-[280px] mb-6 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 rounded-2xl"
                role="button"
                tabindex="0"
                aria-label="Lật thẻ để xem đáp án"
                @click="flipped = !flipped"
                @keydown.enter.prevent="flipped = !flipped"
                @keydown.space.prevent="flipped = !flipped"
            >
                <div class="flip-card-inner w-full h-full" :class="{ flipped: flipped }">
                    <div class="flip-card-front bg-white rounded-2xl border-2 border-slate-200 shadow-sm p-8 flex flex-col items-center justify-center text-center">
                        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-wider mb-4">Câu hỏi</p>
                        <p class="text-center text-slate-900 font-semibold text-base leading-relaxed">{{ $currentCard->question }}</p>
                        <p class="text-xs text-slate-400 mt-6">Nhấp để xem đáp án</p>
                    </div>

                    <div class="flip-card-back bg-indigo-50 rounded-2xl border-2 border-indigo-200 shadow-sm p-8 flex flex-col items-start justify-center overflow-y-auto">
                        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-wider mb-4">Đáp án</p>
                        <p class="text-slate-800 text-sm leading-relaxed whitespace-pre-line">{{ $currentCard->answer }}</p>
                        @if ($currentCard->explanation)
                            <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line mt-4 pt-4 border-t border-indigo-100">{{ $currentCard->explanation }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div x-show="flipped" x-transition.opacity style="display: none;" class="space-y-2">
                <p class="text-xs font-semibold text-slate-500 text-center uppercase tracking-wider mb-3">Bạn nhớ thẻ này đến đâu?</p>
                <div class="grid grid-cols-3 gap-3">
                    <button wire:click="rateCard('hard')" wire:loading.attr="disabled" wire:target="rateCard" class="h-11 rounded-xl border-2 border-rose-200 bg-rose-50 text-rose-700 text-sm font-bold hover:bg-rose-100 disabled:opacity-50 transition-all">
                        Khó
                    </button>
                    <button wire:click="rateCard('medium')" wire:loading.attr="disabled" wire:target="rateCard" class="h-11 rounded-xl border-2 border-amber-200 bg-amber-50 text-amber-700 text-sm font-bold hover:bg-amber-100 disabled:opacity-50 transition-all">
                        Ổn
                    </button>
                    <button wire:click="rateCard('easy')" wire:loading.attr="disabled" wire:target="rateCard" class="h-11 rounded-xl border-2 border-emerald-200 bg-emerald-50 text-emerald-700 text-sm font-bold hover:bg-emerald-100 disabled:opacity-50 transition-all">
                        Dễ
                    </button>
                </div>
            </div>

            <p x-show="!flipped" class="text-center text-sm text-slate-400">Lật thẻ để tự đánh giá mức độ ghi nhớ</p>
        </div>
    @else
        <div class="text-center py-6">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">🎉</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Hoàn thành phiên ôn tập!</h2>
            <p class="text-sm text-slate-500 mb-6">Bạn đã ôn {{ $sessionCount }} thẻ trong phiên này.</p>

            <div class="grid grid-cols-3 gap-3 mb-8">
                <div class="py-3 rounded-xl border border-rose-200 bg-rose-50 text-rose-700">
                    <p class="text-lg font-bold">{{ $ratingCounts['hard'] }}</p>
                    <p class="text-xs font-medium mt-0.5">Khó</p>
                </div>
                <div class="py-3 rounded-xl border border-amber-200 bg-amber-50 text-amber-700">
                    <p class="text-lg font-bold">{{ $ratingCounts['medium'] }}</p>
                    <p class="text-xs font-medium mt-0.5">Ổn</p>
                </div>
                <div class="py-3 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700">
                    <p class="text-lg font-bold">{{ $ratingCounts['easy'] }}</p>
                    <p class="text-xs font-medium mt-0.5">Dễ</p>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center justify-center h-10 px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors">
                Về trang chủ
            </a>
        </div>
    @endif
</div>
