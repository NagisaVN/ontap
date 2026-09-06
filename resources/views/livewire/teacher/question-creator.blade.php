<div class="p-3 sm:p-6 max-w-4xl mx-auto">
    <h1 class="text-lg sm:text-xl font-bold text-slate-900 mb-0.5">Câu hỏi mới</h1>
    <p class="text-xs sm:text-sm text-slate-500 mb-4 sm:mb-6">Tạo câu hỏi trắc nghiệm cho ngân hàng đề thi.</p>

    {{-- Flash --}}
    @if(session('status'))
    <div class="mb-4 flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('status') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5">
        {{-- ── Editor column ── --}}
        <div class="space-y-4">

            {{-- Loại câu hỏi --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Loại câu hỏi</label>
                <div class="flex gap-2">
                    <button type="button" wire:click="$set('type', 'mcq')"
                        class="flex-1 h-9 rounded-lg border-2 text-sm font-semibold transition-all
                               {{ $type === 'mcq' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300' }}">
                        Trắc nghiệm
                    </button>
                    <button type="button" wire:click="$set('type', 'tf')"
                        class="flex-1 h-9 rounded-lg border-2 text-sm font-semibold transition-all
                               {{ $type === 'tf' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-600 hover:border-slate-300' }}">
                        Đúng / Sai
                    </button>
                </div>
            </div>

            {{-- Nội dung câu hỏi --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-5">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Nội dung câu hỏi <span class="text-rose-500">*</span>
                </label>
                <textarea wire:model.live="questionText" rows="5" placeholder="Nhập câu hỏi tại đây…"
                    class="w-full px-3.5 py-3 border rounded-lg text-sm text-slate-900 placeholder:text-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 resize-none transition-colors
                           {{ $errors->has('questionText') ? 'border-rose-400 bg-rose-50' : 'border-slate-200' }}"></textarea>
                @error('questionText') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Metadata --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-5 space-y-3">
                {{-- Chương --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Chương <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select wire:model="subject"
                            class="w-full h-9 pl-3 pr-8 appearance-none bg-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400
                                   {{ $errors->has('subject') ? 'border-rose-400' : 'border border-slate-200' }}">
                            <option value="">— Chọn chương —</option>
                            @foreach($chuongs ?? [] as $ch)
                            <option value="{{ $ch->id }}">{{ $ch->monHoc?->ten }} › {{ $ch->ten }}</option>
                            @endforeach
                        </select>
                        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                    @error('subject') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Độ khó --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Độ khó</label>
                    <div class="flex gap-2">
                        @foreach(['de' => 'Dễ', 'trung_binh' => 'Trung bình', 'kho' => 'Khó'] as $val => $label)
                        <button type="button" wire:click="$set('difficulty', '{{ $val }}')"
                            class="flex-1 h-8 rounded-lg border text-xs font-semibold transition-colors
                                   {{ $difficulty === $val ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 text-slate-600 hover:border-indigo-300' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Các đáp án (MCQ) --}}
            @if($type === 'mcq')
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Các đáp án</label>
                        <span class="text-xs text-slate-400 ml-1.5 hidden sm:inline">(bấm badge để đặt đáp án đúng)</span>
                    </div>
                    @if(count($options) < 6)
                    <button type="button" wire:click="addOption"
                            class="flex items-center gap-1 text-xs text-indigo-600 font-semibold hover:text-indigo-800 h-7 px-2 rounded-lg hover:bg-indigo-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Thêm
                    </button>
                    @endif
                </div>

                @error('options') <p class="text-xs text-rose-500 mb-2">{{ $message }}</p> @enderror

                <div class="space-y-2">
                    @foreach($options as $i => $opt)
                    <div class="flex items-center gap-2 group">
                        {{-- Badge bấm để chọn đáp án đúng --}}
                        <button type="button" wire:click="setCorrect({{ $i }})"
                            class="w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs font-bold shrink-0 transition-colors
                                   {{ $correctIndex === $i ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 text-slate-500 hover:border-emerald-400 hover:text-emerald-600' }}"
                            title="{{ $correctIndex === $i ? 'Đáp án đúng' : 'Đặt làm đáp án đúng' }}">
                            {{ chr(65 + $i) }}
                        </button>

                        <input type="text" wire:model="options.{{ $i }}"
                               placeholder="Đáp án {{ chr(65 + $i) }}…"
                               class="flex-1 h-9 px-3 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400
                                      {{ $correctIndex === $i ? 'border-emerald-300 bg-emerald-50/50' : 'border-slate-200' }}
                                      {{ $errors->has('options.'.$i) ? 'border-rose-400' : '' }}"/>

                        @if(count($options) > 2)
                        <button type="button" wire:click="removeOption({{ $i }})"
                                class="sm:opacity-0 sm:group-hover:opacity-100 w-7 h-7 flex items-center justify-center rounded text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                        @endif
                    </div>
                    @error('options.'.$i) <p class="text-xs text-rose-500 ml-10">{{ $message }}</p> @enderror
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Đúng / Sai --}}
            @if($type === 'tf')
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-5">
                <label class="block text-sm font-semibold text-slate-700 mb-3">Đáp án đúng</label>
                <div class="flex gap-3">
                    @foreach([0 => 'Đúng', 1 => 'Sai'] as $idx => $label)
                    <button type="button" wire:click="setCorrect({{ $idx }})"
                        class="flex-1 h-10 rounded-xl border-2 text-sm font-semibold transition-all
                               {{ $correctIndex === $idx ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-emerald-300' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Lưu --}}
            <button type="button" wire:click="save" wire:loading.attr="disabled"
                class="w-full h-11 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 rounded-xl text-white text-sm font-bold transition-colors flex items-center justify-center gap-2">
                <span wire:loading wire:target="save">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
                <span wire:loading.remove wire:target="save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                </span>
                <span wire:loading.remove wire:target="save">Lưu câu hỏi</span>
                <span wire:loading wire:target="save">Đang lưu…</span>
            </button>
        </div>

        {{-- ── Preview column ── --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 sm:p-5 lg:sticky lg:top-6">
                <h2 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Xem trước
                </h2>
                @if($questionText)
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 mb-4">
                    <p class="text-sm text-slate-900 leading-relaxed">{!! nl2br(e($questionText)) !!}</p>
                </div>
                <div class="space-y-2">
                    @foreach($options as $i => $opt)
                    <div class="flex items-start gap-3 p-3 rounded-lg border transition-colors
                                {{ $correctIndex === $i ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-white' }}">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                     {{ $correctIndex === $i ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600' }}">
                            {{ chr(65 + $i) }}
                        </span>
                        <p class="text-sm text-slate-700 leading-snug">{{ $opt ?: ('Đáp án ' . chr(65 + $i)) }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="text-slate-300 mx-auto mb-3"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <p class="text-sm text-slate-400">Nhập câu hỏi để xem trước…</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>