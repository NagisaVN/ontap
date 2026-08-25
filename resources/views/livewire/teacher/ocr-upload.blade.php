<div class="max-w-2xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="text-center">
        <div class="text-5xl mb-3">📄</div>
        <h1 class="text-2xl font-bold">Upload tài liệu để trích xuất câu hỏi</h1>
        <p class="text-sm mt-1" style="color:var(--sp-text-muted)">
            AI sẽ tự động nhận dạng và trích xuất câu hỏi trắc nghiệm từ PDF hoặc ảnh
        </p>
    </div>

    @if($dispatched)
    {{-- AI Processing state with live progress bar --}}
    <div class="sp-card p-8 text-center animate-slide-up"
         wire:poll.2s="checkJobStatus"
         x-data="{
             elapsed: 0,
             max: 180,
             timer: null,
             done: false,
             hasError: false,
             isDelayed: false,
             redirectTimer: null,
             redirectSecs: 5,
             get pct()    { return Math.min((this.elapsed / this.max) * 100, 100) },
             get remain() { return Math.max(this.max - this.elapsed, 0) },
             get color()  {
                 if (this.hasError)   return '#ef4444';
                 if (this.done)       return '#10b981';
                 if (this.pct >= 80)  return '#f59e0b';
                 if (this.pct >= 50)  return '#6366f1';
                 return '#6366f1';
             },
             get label()  {
                 if (this.hasError)   return '❌ Đã xảy ra lỗi khi xử lý. Vui lòng thử lại.';
                 if (this.done)       return `✅ Hoàn tất! Tự động chuyển trang sau ${this.redirectSecs}s...`;
                 if (this.isDelayed)  return '⏳ Chờ xíu nhé, dữ liệu lớn AI chưa xử lý kịp...';
                 if (this.pct >= 80)  return '🔥 AI đang hoàn thiện các câu hỏi cuối...';
                 if (this.pct >= 50)  return '⚙️ AI đang phân tích và phân loại câu hỏi...';
                 if (this.pct >= 20)  return '🔍 AI đang đọc nội dung tài liệu...';
                 return '🚀 AI đang khởi động phân tích...';
             },
             start() {
                 this.timer = setInterval(() => {
                     if (this.done || this.hasError) {
                         clearInterval(this.timer);
                     } else if (this.elapsed >= this.max) {
                         this.isDelayed = true;
                     } else {
                         this.elapsed++;
                     }
                 }, 1000);
             },
             markDone() {
                 this.done = true;
                 this.elapsed = this.max;
                 clearInterval(this.timer);
                 this.redirectTimer = setInterval(() => {
                     this.redirectSecs--;
                     if (this.redirectSecs <= 0) {
                         clearInterval(this.redirectTimer);
                         window.location.href = '{{ route('teacher.pending') }}';
                     }
                 }, 1000);
             },
             markError() {
                 this.hasError = true;
                 this.elapsed = this.max;
                 clearInterval(this.timer);
             }
         }"
         x-init="start()"
         @ocr-job-done.window="markDone()"
         @ocr-job-error.window="markError()">

        {{-- Icon --}}
        <div class="text-5xl mb-4" x-text="done ? '🎉' : '🤖'"></div>

        {{-- Title --}}
        <h2 class="text-xl font-bold mb-1">AI đang xử lý tài liệu</h2>
        <p class="text-sm mb-6" style="color:var(--sp-text-secondary)">{{ $message }}</p>

        {{-- Progress bar container --}}
        <div class="mb-2 mx-auto max-w-sm">
            <div class="flex justify-between text-xs mb-1.5" style="color:var(--sp-text-muted)">
                <span x-text="label"></span>
                <span x-show="!done" x-text="remain + 's còn lại'"></span>
            </div>
            <div class="w-full rounded-full overflow-hidden" style="height:10px;background:#e2e8f0">
                <div class="h-full rounded-full transition-all duration-1000 ease-linear"
                     :style="`width:${pct}%; background:${color}`"></div>
            </div>
            <div class="flex justify-between text-xs mt-1.5" style="color:var(--sp-text-muted)">
                <span x-text="Math.round(elapsed) + 's'"></span>
                <span>{{ config('gemini.timeout', 180) }}s tối đa</span>
            </div>
        </div>

        {{-- Pulsing dots khi đang chờ --}}
        <div x-show="!done" class="flex items-center justify-center gap-1 mt-4 mb-5">
            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:0ms"></span>
            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:150ms"></span>
            <span class="w-2 h-2 rounded-full bg-indigo-400 animate-bounce" style="animation-delay:300ms"></span>
        </div>

        {{-- Action buttons --}}
        <div class="flex gap-3 justify-center mt-4">
            <a href="{{ route('teacher.pending') }}" wire:navigate class="sp-btn sp-btn-primary">
                👁️ Xem câu hỏi chờ duyệt
            </a>
            <button wire:click="$set('dispatched', false)" class="sp-btn sp-btn-outline">
                📄 Upload thêm
            </button>
        </div>

        {{-- Note --}}
        <p class="text-xs mt-4" style="color:var(--sp-text-muted)">
            Câu hỏi sẽ tự xuất hiện ở trang <strong>Chờ duyệt</strong> sau khi AI hoàn tất.
            Bạn có thể rời trang này bất cứ lúc nào.
        </p>

    </div>


    @else
    {{-- Upload form --}}
    <div class="sp-card p-6 space-y-5">

        {{-- Validation summary --}}
        @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1">❌ Vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form wire:submit="guiChoAI" class="space-y-5">

        <div>
            <label class="sp-label">File tài liệu *</label>
            <div x-data="{ isDropping: false }"
                 x-on:dragover.prevent="isDropping = true"
                 x-on:dragleave.prevent="isDropping = false"
                 x-on:drop.prevent="
                     isDropping = false;
                     if ($event.dataTransfer.files.length) {
                         @this.upload('file', $event.dataTransfer.files[0])
                     }
                 "
                 :class="{ 'border-indigo-400 bg-indigo-50': isDropping, 'border-gray-300 bg-[#fafafe]': !isDropping }"
                 class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                 style="border-color:var(--sp-primary-light);background:#fafafe"
                 :style="isDropping ? 'border-color:var(--sp-primary);background:#eef2ff;' : ''">

                {{-- Invisible full-area file input for click-to-browse --}}
                <input type="file"
                       wire:model="file"
                       accept=".pdf,.jpg,.jpeg,.png,.webp"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                {{-- Drop zone content --}}
                @if($file)
                    <div class="text-center pointer-events-none">
                        <div class="text-3xl mb-2">📎</div>
                        <p class="font-medium text-sm">{{ $file->getClientOriginalName() }}</p>
                        <p class="text-xs mt-1" style="color:var(--sp-text-muted)">
                            {{ number_format($file->getSize() / 1024, 0) }} KB
                        </p>
                    </div>
                @else
                    <div class="text-center pointer-events-none">
                        <div class="text-4xl mb-2">☁️</div>
                        <p class="font-medium text-sm">Kéo thả file vào đây hoặc click để chọn</p>
                        <p class="text-xs mt-1" style="color:var(--sp-text-muted)">PDF, JPG, PNG, WEBP — tối đa 10MB</p>
                    </div>
                @endif
            </div>
            @error('file')
                <span class="text-red-500 text-sm mt-1 block italic">* {{ $message }}</span>
            @enderror
            <div wire:loading wire:target="file" class="text-xs mt-2" style="color:var(--sp-primary);display:none">
                ⏳ Đang upload...
            </div>
        </div>

        {{-- Chọn chế độ AI --}}
        <div>
            <label class="sp-label mb-2">Chế độ xử lý *</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="relative flex cursor-pointer rounded-xl border p-4 hover:bg-gray-50 focus:outline-none" :class="$wire.mode === 'extract' ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' : 'border-gray-300'">
                    <input type="radio" wire:model.live="mode" value="extract" class="sr-only">
                    <span class="flex flex-col">
                        <span class="block text-sm font-medium text-gray-900">🔍 Trích xuất (OCR)</span>
                        <span class="block text-xs text-gray-500 mt-1">Quét và nhận dạng các câu hỏi trắc nghiệm ĐÃ CÓ SẴN trong file.</span>
                    </span>
                </label>
                <label class="relative flex cursor-pointer rounded-xl border p-4 hover:bg-gray-50 focus:outline-none" :class="$wire.mode === 'generate' ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' : 'border-gray-300'">
                    <input type="radio" wire:model.live="mode" value="generate" class="sr-only">
                    <span class="flex flex-col">
                        <span class="block text-sm font-medium text-gray-900">✨ Tự động soạn đề</span>
                        <span class="block text-xs text-gray-500 mt-1">AI đọc nội dung lý thuyết và TỰ SOẠN câu hỏi trắc nghiệm mới.</span>
                    </span>
                </label>
            </div>
        </div>

        {{-- Số lượng câu hỏi (chỉ hiện khi chọn Tự soạn đề) --}}
        <div x-data="{ mode: @entangle('mode'), sum: 0 }" 
             x-show="mode === 'generate'" x-transition class="space-y-3">
            <label class="sp-label">Số lượng câu hỏi cần tạo *</label>
            
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Dễ (Nhận biết)</label>
                    <input type="number" wire:model="soLuongDe" min="0" max="50" class="sp-input text-center">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">T.Bình (Hiểu)</label>
                    <input type="number" wire:model="soLuongTrungBinh" min="0" max="50" class="sp-input text-center">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Khó (Vận dụng)</label>
                    <input type="number" wire:model="soLuongKho" min="0" max="50" class="sp-input text-center">
                </div>
            </div>
            
            <div class="flex justify-between items-center text-xs text-gray-500">
                <span>Tổng cộng: <strong x-text="parseInt($wire.soLuongDe) + parseInt($wire.soLuongTrungBinh) + parseInt($wire.soLuongKho) || 0"></strong> câu (Tối đa 50 câu)</span>
            </div>
            
            @error('soLuongDe')
                <span class="text-red-500 text-sm italic">* {{ $message }}</span>
            @enderror
        </div>

        {{-- Chọn chương --}}
        <div>
            <label class="sp-label">Gán vào chương *</label>
            <select wire:model="chuongId" class="sp-input sp-select">
                <option value="0">-- Chọn chương đích --</option>
                @foreach($monHocs as $monHoc)
                    <optgroup label="{{ $monHoc->ten }}">
                        @foreach($monHoc->chuong as $ch)
                            <option value="{{ $ch->id }}">{{ $ch->ten }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('chuongId')
                <span class="text-red-500 text-sm mt-1 block italic">* {{ $message }}</span>
            @enderror
        </div>

        {{-- Info box --}}
        <div class="rounded-xl p-4 text-sm" style="background:#fef3c7">
            <p class="font-semibold mb-1">⚠️ Lưu ý:</p>
            <ul class="list-disc list-inside space-y-1 text-xs" style="color:#92400e">
                <li>Câu hỏi sau khi AI trích xuất sẽ ở trạng thái <strong>"Chờ duyệt"</strong></li>
                <li>Bạn cần vào trang <strong>Duyệt câu hỏi</strong> để xác nhận trước khi đưa vào đề thi</li>
                <li>File PDF nhiều trang có thể mất 1-3 phút để xử lý</li>
            </ul>
        </div>

        <button type="submit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
                wire:target="guiChoAI"
                class="sp-btn sp-btn-primary w-full justify-center py-3 text-base font-semibold">

            {{-- Default state --}}
            <span wire:loading.remove wire:target="guiChoAI" class="flex items-center gap-2">
                🤖 Gửi cho AI xử lý
            </span>

            {{-- Loading state --}}
            <span wire:loading wire:target="guiChoAI" class="flex flex-col items-center gap-1 w-full" style="display:none">
                <span class="text-center w-full">Đang xử lý...</span>
                <svg class="w-5 h-5 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </span>

        </button>

        </form>{{-- /wire:submit form --}}
    </div>
    @endif

</div>
