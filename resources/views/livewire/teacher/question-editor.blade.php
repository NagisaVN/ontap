<div class="p-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('teacher.questions') }}" wire:navigate
           class="flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-900">Chỉnh sửa câu hỏi</h1>
            <p class="text-sm text-slate-500 mt-0.5">#{{ $questionId }}</p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

        {{-- Nội dung câu hỏi --}}
        <div class="p-6 border-b border-slate-100">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Nội dung câu hỏi <span class="text-rose-500">*</span>
            </label>
            <textarea wire:model="noiDung" rows="5"
                class="w-full border rounded-xl px-4 py-3 text-sm text-slate-800 resize-y
                       focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                       {{ $errors->has('noiDung') ? 'border-rose-400 bg-rose-50' : 'border-slate-200' }}"
                placeholder="Nhập nội dung câu hỏi…"></textarea>
            @error('noiDung')
                <p class="text-xs text-rose-500 mt-1.5 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Meta: Chương + Độ khó + Trạng thái --}}
        <div class="p-6 border-b border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Chương --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Chương <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select wire:model="chuongId"
                        class="w-full h-10 border rounded-xl pl-3 pr-8 text-sm text-slate-700 appearance-none
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                               {{ $errors->has('chuongId') ? 'border-rose-400 bg-rose-50' : 'border-slate-200' }}">
                        <option value="">— Chọn chương —</option>
                        @foreach($chuongs ?? [] as $chuong)
                        <option value="{{ $chuong->id }}">{{ $chuong->monHoc?->ten }} › {{ $chuong->ten }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                </div>
                @error('chuongId')
                    <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Độ khó --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Độ khó</label>
                <div class="relative">
                    <select wire:model="doKho"
                        class="w-full h-10 border border-slate-200 rounded-xl pl-3 pr-8 text-sm text-slate-700 appearance-none
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400">
                        <option value="de">Dễ</option>
                        <option value="trung_binh">Trung bình</option>
                        <option value="kho">Khó</option>
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="sm:col-span-3">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Trạng thái</label>
                <div class="flex gap-4">
                    @foreach(['cho_duyet' => ['label' => 'Chờ duyệt', 'color' => 'amber'], 'da_duyet' => ['label' => 'Đã duyệt', 'color' => 'emerald'], 'tu_choi' => ['label' => 'Từ chối', 'color' => 'rose']] as $val => $cfg)
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="trangThai" value="{{ $val }}"
                               class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ $cfg['label'] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Lựa chọn --}}
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-700">Các lựa chọn</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Bấm vòng tròn bên trái để chọn đáp án đúng</p>
                </div>
                @if(count($luaChon) < 6)
                <button wire:click="addOption" type="button"
                        class="flex items-center gap-1.5 h-8 px-3 border border-indigo-200 text-indigo-600 hover:bg-indigo-50 text-xs font-semibold rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Thêm lựa chọn
                </button>
                @endif
            </div>

            @error('luaChon')
                <div class="mb-3 px-3 py-2 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-600">
                    {{ $message }}
                </div>
            @enderror

            <div class="space-y-3">
                @foreach($luaChon as $i => $lc)
                <div class="flex items-center gap-3 group">

                    {{-- Correct toggle button --}}
                    <button wire:click="setCorrectAnswer({{ $i }})" type="button"
                            title="{{ $lc['la_dap_an'] ? 'Đáp án đúng' : 'Đặt làm đáp án đúng' }}"
                            class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-150
                                   {{ $lc['la_dap_an']
                                       ? 'border-emerald-500 bg-emerald-500 shadow-sm shadow-emerald-200'
                                       : 'border-slate-300 hover:border-emerald-400 hover:bg-emerald-50' }}">
                        @if($lc['la_dap_an'])
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        @endif
                    </button>

                    {{-- Letter badge --}}
                    <span class="flex-shrink-0 w-7 h-7 rounded-lg text-xs font-bold flex items-center justify-center transition-colors
                                 {{ $lc['la_dap_an'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ chr(65 + $i) }}
                    </span>

                    {{-- Text input --}}
                    <input type="text"
                           wire:model="luaChon.{{ $i }}.noi_dung"
                           placeholder="Nội dung lựa chọn {{ chr(65 + $i) }}…"
                           class="flex-1 h-10 border rounded-xl px-3.5 text-sm text-slate-800 transition-colors
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                  {{ $lc['la_dap_an'] ? 'border-emerald-300 bg-emerald-50/60' : 'border-slate-200 bg-white' }}
                                  {{ $errors->has('luaChon.'.$i.'.noi_dung') ? 'border-rose-400 bg-rose-50' : '' }}">

                    {{-- Remove button (show on hover) --}}
                    @if(count($luaChon) > 2)
                    <button wire:click="removeOption({{ $i }})" type="button"
                            class="flex-shrink-0 w-7 h-7 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50
                                   flex items-center justify-center transition-colors
                                   opacity-0 group-hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    @endif
                </div>

                @error('luaChon.'.$i.'.noi_dung')
                    <p class="text-xs text-rose-500 ml-16">{{ $message }}</p>
                @enderror
                @endforeach
            </div>
        </div>

        {{-- Footer actions --}}
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-2xl flex items-center justify-between gap-3">
            <a href="{{ route('teacher.questions') }}" wire:navigate
               class="h-10 px-5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors flex items-center">
                Hủy
            </a>

            <button wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="h-10 px-6 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 rounded-xl text-sm font-semibold text-white transition-colors flex items-center gap-2">

                <span wire:loading.remove wire:target="save">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                </span>
                <span wire:loading wire:target="save">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>

                <span wire:loading.remove wire:target="save">Lưu thay đổi</span>
                <span wire:loading wire:target="save">Đang lưu…</span>
            </button>
        </div>

    </div>

</div>
