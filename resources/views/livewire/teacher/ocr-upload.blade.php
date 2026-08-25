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
    {{-- Success state --}}
    <div class="sp-card p-8 text-center animate-slide-up">
        <div class="text-5xl mb-4">🚀</div>
        <h2 class="text-xl font-bold mb-2">Đã gửi lên AI xử lý!</h2>
        <p class="text-sm mb-5" style="color:var(--sp-text-secondary)">{{ $message }}</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ route('teacher.pending') }}" wire:navigate class="sp-btn sp-btn-primary">
                👁️ Xem câu hỏi chờ duyệt
            </a>
            <button wire:click="$set('dispatched', false)" class="sp-btn sp-btn-outline">
                📄 Upload thêm
            </button>
        </div>
    </div>

    @else
    {{-- Upload form --}}
    <div class="sp-card p-6 space-y-5">

        {{-- Dropzone --}}
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
            @error('file') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            <div wire:loading wire:target="file" class="text-xs mt-2" style="color:var(--sp-primary)">
                ⏳ Đang upload...
            </div>
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
            @error('chuongId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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

        <button wire:click="upload"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75 cursor-not-allowed"
                wire:target="upload"
                class="sp-btn sp-btn-primary w-full justify-center py-3 text-base font-semibold">
            <svg wire:loading wire:target="upload" class="w-5 h-5 animate-spin"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span wire:loading.remove wire:target="upload">🤖 Gửi cho AI xử lý</span>
            <span wire:loading wire:target="upload">Đang xử lý...</span>
        </button>
    </div>
    @endif

</div>
