<div class="min-h-screen bg-slate-50/50" x-data="{
    dragging: false,
    handleDrop(event) {
        this.dragging = false;
        const file = event.dataTransfer.files[0];
        if (!file) return;
        const input = document.getElementById('ocrFileInput');
        if (!input) return;
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }
}">
<div class="p-6 max-w-7xl mx-auto space-y-5">

    {{-- ══════════════════════════════════════════════════════
         STATE A: UPLOAD MODE (chưa có kết quả)
    ══════════════════════════════════════════════════════ --}}
    @if(count($extractedQuestions) === 0)

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Nhập câu hỏi bằng AI</h1>
        <p class="text-sm text-slate-500 mt-1">Tải lên ảnh hoặc PDF đề thi — AI sẽ tự động trích xuất hoặc soạn câu hỏi mới.</p>
    </div>

    {{-- Error --}}
    @if($errorMessage)
    <div class="flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errorMessage }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- LEFT: Upload & Config --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Drop Zone --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-700">1. Chọn tài liệu</p>
                </div>
                <div class="p-5">
                    <label for="ocrFileInput"
                        class="flex flex-col items-center justify-center gap-3 border-2 border-dashed rounded-xl p-8 cursor-pointer transition-all"
                        :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200 hover:border-indigo-300 hover:bg-slate-50'"
                        @dragover.prevent="dragging = true"
                        @dragleave.prevent="dragging = false"
                        @drop.prevent="handleDrop($event)">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-colors"
                             :class="dragging ? 'bg-indigo-100' : 'bg-slate-100'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" class="transition-colors" :class="dragging ? 'text-indigo-600' : 'text-slate-500'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-slate-700">Kéo thả hoặc click để chọn</p>
                            <p class="text-xs text-slate-400 mt-1">PDF, PNG, JPG, WEBP — tối đa 10MB</p>
                        </div>
                        <input type="file" id="ocrFileInput" wire:model="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="sr-only">
                    </label>

                    <div wire:loading wire:target="file" style="display:none"
                         class="mt-3 flex items-center gap-2 text-sm text-indigo-600">
                        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Đang tải file lên…
                    </div>

                    @if($file)
                    <div class="mt-3 flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                             style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                            @if(in_array($file->extension(), ['jpg','jpeg','png','webp']))
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $file->getClientOriginalName() }}</p>
                            <p class="text-xs text-slate-400">{{ round($file->getSize() / 1024) }} KB</p>
                        </div>
                        <button wire:click="clearFile" class="text-slate-400 hover:text-rose-500 transition-colors p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    @endif
                    @error('file')<p class="mt-2 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Mode --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-700">2. Chế độ AI</p>
                </div>
                <div class="p-4 space-y-2">
                    <label class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all {{ $mode === 'extract' ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-slate-300' }}">
                        <input type="radio" wire:model.live="mode" value="extract" class="mt-0.5 text-indigo-600">
                        <div><p class="text-sm font-semibold text-slate-800">🔍 Trích xuất câu hỏi</p><p class="text-xs text-slate-500 mt-0.5">Đọc và trích xuất câu hỏi có sẵn trong tài liệu</p></div>
                    </label>
                    <label class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all {{ $mode === 'generate' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300' }}">
                        <input type="radio" wire:model.live="mode" value="generate" class="mt-0.5 text-violet-600">
                        <div><p class="text-sm font-semibold text-slate-800">✨ Tự soạn câu hỏi mới</p><p class="text-xs text-slate-500 mt-0.5">AI đọc nội dung và soạn câu hỏi theo số lượng đặt</p></div>
                    </label>
                    @if($mode === 'generate')
                    <div class="mt-3 p-3 bg-violet-50 rounded-xl border border-violet-100 space-y-3">
                        <p class="text-xs font-semibold text-violet-700 uppercase tracking-wide">Số lượng câu theo độ khó</p>
                        @foreach([['soLuongDe','Dễ','emerald'],['soLuongTrungBinh','Trung bình','amber'],['soLuongKho','Khó','rose']] as [$prop,$label,$color])
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-{{ $color }}-400 inline-block"></span>{{ $label }}
                            </span>
                            <input type="number" wire:model.live="{{ $prop }}" min="0" max="20"
                                   class="w-16 h-7 text-center text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400/30">
                        </div>
                        @endforeach
                        <p class="text-xs text-violet-600 font-medium text-right">Tổng: {{ $soLuongDe + $soLuongTrungBinh + $soLuongKho }} câu</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Chapter --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-700">3. Chương đích <span class="text-slate-400 font-normal">(để lưu vào DB)</span></p>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($monHocs as $monHoc)
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $monHoc->ten }}</p>
                        <div class="space-y-1">
                            @foreach($monHoc->chuong as $ch)
                            <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg cursor-pointer transition-colors {{ $chuongId == $ch->id ? 'bg-indigo-50 border border-indigo-200' : 'hover:bg-slate-50' }}">
                                <input type="radio" wire:model.live="chuongId" value="{{ $ch->id }}" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-slate-700">{{ $ch->ten }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @error('chuongId')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Submit --}}
            <button wire:click="processOcr" wire:loading.attr="disabled" @disabled(!$file)
                    class="w-full h-12 rounded-2xl text-sm font-bold transition-all flex items-center justify-center gap-2.5 shadow-sm
                           {{ $file ? 'bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white' : 'bg-slate-100 text-slate-400 cursor-not-allowed' }}">
                <span wire:loading.remove wire:target="processOcr">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </span>
                <span wire:loading wire:target="processOcr" style="display:none">
                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
                <span wire:loading.remove wire:target="processOcr">{{ $mode === 'generate' ? '✨ Soạn câu hỏi bằng AI' : '🔍 Phân tích OCR' }}</span>
                <span wire:loading wire:target="processOcr" style="display:none">AI đang phân tích…</span>
            </button>
        </div>

        {{-- RIGHT: empty state --}}
        <div class="lg:col-span-3">
            {{-- Loading --}}
            <div wire:loading wire:target="processOcr" style="display:none"
                 class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 flex flex-col items-center justify-center gap-4">
                <div class="relative w-16 h-16">
                    <div class="absolute inset-0 rounded-full animate-spin"
                         style="background: conic-gradient(from 0deg, #6366f1, #8b5cf6, transparent); mask: radial-gradient(farthest-side, transparent calc(100% - 4px), black calc(100% - 3px));-webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 4px), black calc(100% - 3px));"></div>
                    <div class="absolute inset-0 flex items-center justify-center text-2xl">🤖</div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-semibold text-slate-700">AI đang phân tích tài liệu…</p>
                    <p class="text-xs text-slate-400 mt-1">Quá trình này có thể mất 30–120 giây</p>
                </div>
            </div>
            {{-- Empty --}}
            <div wire:loading.remove wire:target="processOcr" style="display:block"
                 class="bg-white rounded-2xl border border-dashed border-slate-200 p-16 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-300" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <p class="text-sm font-semibold text-slate-500">Kết quả sẽ hiển thị ở đây</p>
                <p class="text-xs text-slate-400 mt-1.5">Tải file lên và nhấn nút phân tích</p>
                <div class="flex items-center gap-4 mt-5 text-xs text-slate-400">
                    <span>📄 PDF</span><span>🖼 JPG/PNG</span><span>🤖 AI tự động</span>
                </div>
            </div>
        </div>

    </div>{{-- end grid --}}

    @else
    {{-- ══════════════════════════════════════════════════════
         STATE B: RESULTS MODE (đã có kết quả — full width)
    ══════════════════════════════════════════════════════ --}}

    @php
        $pagedQ     = $this->getPagedQuestions();
        $totalPages = $this->getTotalPages();
        $total      = count($extractedQuestions);
        $unsaved    = $total - count($savedIndices);
        $startIdx   = ($resultsPage - 1) * $perPage;
    @endphp

    {{-- Header kết quả --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <button wire:click="backToUpload"
                    class="flex items-center gap-1.5 h-9 px-3 border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-xl text-sm font-semibold transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Upload lại
            </button>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Kết quả OCR</h1>
                <p class="text-sm text-slate-500">{{ $total }} câu hỏi được trích xuất</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($successMessage && $allSaved)
                <span class="text-sm text-emerald-600 font-semibold">✓ {{ $successMessage }}</span>
            @endif
            @if(!$allSaved && $chuongId)
            <button wire:click="saveAllToDatabase" wire:loading.attr="disabled"
                    class="flex items-center gap-2 h-9 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
                <span wire:loading.remove wire:target="saveAllToDatabase">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                </span>
                <span wire:loading wire:target="saveAllToDatabase" style="display:none">
                    <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
                Lưu tất cả ({{ $unsaved }} câu)
            </button>
            @elseif(!$chuongId)
            <span class="text-xs text-amber-600 font-semibold px-3 py-2 bg-amber-50 rounded-xl border border-amber-200">
                ⚠ Chưa chọn chương đích — không thể lưu
            </span>
            @endif
        </div>
    </div>

    {{-- Error --}}
    @if($errorMessage)
    <div class="flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl text-sm text-rose-700">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ $errorMessage }}</span>
    </div>
    @endif

    {{-- Pagination top --}}
    @if($totalPages > 1)
    <div class="flex items-center justify-between bg-white rounded-2xl border border-slate-200 px-5 py-3 shadow-sm">
        <p class="text-sm text-slate-600">
            Hiển thị câu <strong>{{ $startIdx + 1 }}</strong>–<strong>{{ min($startIdx + $perPage, $total) }}</strong> / {{ $total }}
        </p>
        <div class="flex items-center gap-1">
            <button wire:click="prevResultPage" @disabled($resultsPage <= 1)
                    class="w-8 h-8 flex items-center justify-center rounded-lg {{ $resultsPage <= 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-100' }} transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            @for($p = 1; $p <= $totalPages; $p++)
            <button wire:click="gotoResultPage({{ $p }})"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-sm font-semibold transition-colors
                           {{ $p === $resultsPage ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $p }}
            </button>
            @endfor
            <button wire:click="nextResultPage" @disabled($resultsPage >= $totalPages)
                    class="w-8 h-8 flex items-center justify-center rounded-lg {{ $resultsPage >= $totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-500 hover:bg-slate-100' }} transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
    @endif

    {{-- Question cards --}}
    <div class="space-y-3">
        @foreach($pagedQ as $i => $q)
        @php
            $isSaved = in_array($i, $savedIndices);
            $options = $q['lua_chon'] ?? [];
            $doKho   = $q['do_kho'] ?? 'trung_binh';
            $badge   = match($doKho) {
                'de'    => ['Dễ',         'bg-emerald-100 text-emerald-700'],
                'kho'   => ['Khó',        'bg-rose-100 text-rose-700'],
                default => ['Trung bình', 'bg-amber-100 text-amber-700'],
            };
        @endphp
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden {{ $isSaved ? 'border-emerald-200 opacity-75' : 'border-slate-200' }}">
            <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold flex items-center justify-center">{{ $startIdx + $loop->iteration }}</span>
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $badge[1] }}">{{ $badge[0] }}</span>
                    @if($isSaved)
                    <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                        Đã lưu
                    </span>
                    @endif
                </div>
                @if(!$isSaved && $chuongId)
                <button wire:click="saveQuestion({{ $i }})"
                        class="h-7 px-3 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                    Lưu riêng
                </button>
                @endif
            </div>
            <div class="px-5 pt-4 pb-3">
                <p class="text-sm font-medium text-slate-900 leading-relaxed">{{ $q['noi_dung'] ?? ($q['question_text'] ?? '—') }}</p>
            </div>
            @if(!empty($options))
            <div class="px-5 pb-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                @foreach($options as $j => $opt)
                @php
                    $text    = $opt['noi_dung'] ?? $opt['text'] ?? '';
                    $correct = (bool)($opt['la_dap_an'] ?? $opt['is_correct'] ?? false);
                @endphp
                <div class="flex items-start gap-2.5 px-3 py-2 rounded-xl text-xs {{ $correct ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-slate-50 text-slate-600' }}">
                    <span class="shrink-0 font-bold {{ $correct ? 'text-emerald-600' : 'text-slate-400' }}">{{ chr(65 + $j) }}.</span>
                    <span class="{{ $correct ? 'font-semibold' : '' }}">{{ $text }}</span>
                    @if($correct)
                    <svg class="ml-auto shrink-0 text-emerald-500" xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
            @if(!empty($q['giai_thich']))
            <div class="mx-5 mb-4 px-3.5 py-2.5 bg-blue-50 border border-blue-100 rounded-xl">
                <p class="text-xs font-semibold text-blue-700 mb-1">💡 Giải thích</p>
                <p class="text-xs text-blue-600 leading-relaxed">{{ $q['giai_thich'] }}</p>
            </div>
            @endif
            @if(!empty($q['chuong_goi_y']))
            <div class="px-5 pb-4">
                <span class="text-xs text-slate-400"><span class="font-medium text-slate-500">Chủ đề:</span> {{ $q['chuong_goi_y'] }}</span>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination bottom --}}
    @if($totalPages > 1)
    <div class="flex items-center justify-between bg-white rounded-2xl border border-slate-200 px-5 py-3 shadow-sm">
        <p class="text-sm text-slate-600">
            Trang <strong>{{ $resultsPage }}</strong> / {{ $totalPages }}
        </p>
        <div class="flex items-center gap-1">
            <button wire:click="prevResultPage" @disabled($resultsPage <= 1)
                    class="flex items-center gap-1.5 h-8 px-3 rounded-lg text-sm font-semibold {{ $resultsPage <= 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100' }} transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Trang trước
            </button>
            <button wire:click="nextResultPage" @disabled($resultsPage >= $totalPages)
                    class="flex items-center gap-1.5 h-8 px-3 rounded-lg text-sm font-semibold {{ $resultsPage >= $totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-white bg-indigo-600 hover:bg-indigo-700' }} transition-colors">
                Trang sau
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
    @endif

    @if($allSaved)
    <div class="p-6 bg-emerald-50 border border-emerald-200 rounded-2xl text-center">
        <div class="text-4xl mb-2">🎉</div>
        <p class="font-bold text-emerald-800">Tất cả {{ $total }} câu hỏi đã được lưu!</p>
        <p class="text-sm text-emerald-600 mt-1">Vào phần Chờ duyệt để xem xét và phê duyệt.</p>
        <div class="flex items-center justify-center gap-3 mt-4">
            <a href="{{ route('teacher.pending') }}" wire:navigate
               class="inline-flex items-center gap-2 h-9 px-5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors">
                Xem chờ duyệt →
            </a>
            <button wire:click="backToUpload"
                    class="inline-flex items-center gap-2 h-9 px-5 border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl transition-colors">
                Upload file khác
            </button>
        </div>
    </div>
    @endif

    @endif{{-- end STATE B --}}

</div>
</div>