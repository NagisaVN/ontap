<div class="p-6 max-w-4xl mx-auto">
    <h1 class="text-xl font-bold text-slate-900 mb-1">Câu hỏi mới</h1>
    <p class="text-sm text-slate-500 mb-6">Dùng $...$ để ghi công thức toán học. Bọc phương trình display trong $$...$$.</p>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Editor --}}
        <div class="space-y-4">
            {{-- Type toggle --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Loại câu hỏi</label>
                <div class="flex gap-2">
                    <button type="button" wire:click="$set('type', 'mcq')"
                        class="flex-1 h-9 rounded-lg border-2 text-sm font-semibold transition-all {{ $type === 'mcq' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300' }}">
                        Trắc nghiệm
                    </button>
                    <button type="button" wire:click="$set('type', 'tf')"
                        class="flex-1 h-9 rounded-lg border-2 text-sm font-semibold transition-all {{ $type === 'tf' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300' }}">
                        Đúng / Sai
                    </button>
                </div>
            </div>

            {{-- Question text --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Nội dung câu hỏi</label>
                <textarea wire:model.live="questionText" rows="5" placeholder="Nhập câu hỏi tại đây…"
                    class="w-full px-3.5 py-3 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 resize-none transition-colors"></textarea>
            </div>

            {{-- Metadata --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Môn học</label>
                    <div class="relative">
                        <select wire:model="subject" class="w-full h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                            <option value="">Chọn môn học</option>
                            @foreach($subjects ?? [] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Độ khó</label>
                    <div class="flex gap-2">
                        @foreach(['easy' => 'Dễ', 'medium' => 'Trung bình', 'hard' => 'Khó'] as $val => $label)
                        <button type="button" wire:click="$set('difficulty', '{{ $val }}')"
                            class="flex-1 h-8 rounded-lg border text-xs font-semibold transition-colors
                            {{ ($difficulty ?? 'medium') === $val ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 text-slate-600 hover:border-indigo-300' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Options (MCQ) --}}
            @if(($type ?? 'mcq') === 'mcq')
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-slate-700">Các đáp án</label>
                    <button type="button" wire:click="addOption" class="flex items-center gap-1 text-xs text-indigo-600 font-medium hover:text-indigo-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Thêm đáp án
                    </button>
                </div>
                <div class="space-y-2">
                    @foreach($options ?? [] as $i => $opt)
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="setCorrect({{ $i }})"
                            class="w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs font-bold shrink-0 transition-colors
                            {{ ($correctIndex ?? 0) === $i ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 text-slate-500 hover:border-emerald-400' }}">
                            {{ chr(65 + $i) }}
                        </button>
                        <input type="text" wire:model="options.{{ $i }}" placeholder="Đáp án {{ chr(65 + $i) }}…"
                            class="flex-1 h-9 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"/>
                        @if(count($options ?? []) > 2)
                        <button type="button" wire:click="removeOption({{ $i }})" class="text-slate-400 hover:text-rose-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Save button --}}
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                class="w-full h-11 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 rounded-xl text-white text-sm font-bold transition-colors flex items-center justify-center gap-2">
                <span wire:loading wire:target="save">
                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
                <span wire:loading.remove wire:target="save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                </span>
                Lưu câu hỏi
            </button>
        </div>

        {{-- Preview --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-6">
                <h2 class="text-sm font-semibold text-slate-700 mb-3">Xem trước</h2>
                @if($questionText ?? null)
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 mb-4">
                    <p class="text-sm text-slate-900 leading-relaxed">{!! nl2br(e($questionText ?? '')) !!}</p>
                </div>
                @if(($type ?? 'mcq') === 'mcq')
                <div class="space-y-2">
                    @foreach($options ?? [] as $i => $opt)
                    <div class="flex items-start gap-3 p-3 rounded-lg border {{ ($correctIndex ?? 0) === $i ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200' }}">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ ($correctIndex ?? 0) === $i ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600' }}">
                            {{ chr(65 + $i) }}
                        </span>
                        <p class="text-sm text-slate-700">{{ $opt ?: 'Đáp án ' . chr(65 + $i) }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <div class="py-12 text-center text-sm text-slate-400">Nhập câu hỏi để xem trước…</div>
                @endif
            </div>
        </div>
    </div>
</div>