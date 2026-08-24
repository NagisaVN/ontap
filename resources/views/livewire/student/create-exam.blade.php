<div class="max-w-xl mx-auto">
    <div class="sp-card p-8 text-center">
        <div class="text-5xl mb-4">⚡</div>
        <h1 class="text-2xl font-bold mb-2">Tạo bài thi mới</h1>
        <p class="text-sm mb-6" style="color:var(--sp-text-muted)">Cấu hình đề thi theo ý muốn</p>

        <div class="text-left space-y-4">

            <div>
                <label class="sp-label">Tên bài thi</label>
                <input wire:model="tenBaiThi" type="text" class="sp-input"
                       placeholder="Bài thi Cơ sở dữ liệu...">
            </div>

            <div>
                <label class="sp-label">Môn học *</label>
                <select wire:model="monHocId" class="sp-input sp-select">
                    <option value="0">-- Chọn môn học --</option>
                    @foreach($monHocs as $m)
                        <option value="{{ $m->id }}">{{ $m->ten }}</option>
                    @endforeach
                </select>
                @error('monHocId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="sp-label">Chế độ thi</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($cheDoList as $mode)
                    <label class="flex flex-col items-center gap-1 p-3 border-2 rounded-xl cursor-pointer transition-all
                                  {{ $cheDoThi === $mode->value ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200' }}">
                        <input type="radio" wire:model="cheDoThi" value="{{ $mode->value }}" class="hidden">
                        <span class="text-xl">
                            {{ match($mode->value) { 'ngau_nhien'=>'🎲', 'theo_do_kho'=>'📊', 'ty_le_tuy_chon'=>'⚙️', default=>'🎯' } }}
                        </span>
                        <span class="text-xs font-medium text-center">{{ $mode->nhanHien() }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="sp-label">Số câu hỏi</label>
                    <input wire:model="soCauHoi" type="number" min="5" max="100" class="sp-input">
                    @error('soCauHoi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="sp-label">Thời gian (phút)</label>
                    <input wire:model="thoiGianPhut" type="number" min="5" max="120" class="sp-input">
                    @error('thoiGianPhut') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <button wire:click="batDauThi"
                    class="sp-btn sp-btn-primary w-full justify-center py-3 text-base font-semibold">
                <svg wire:loading wire:target="batDauThi" class="w-5 h-5 animate-spin"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span wire:loading.remove wire:target="batDauThi">🚀 Bắt đầu thi</span>
                <span wire:loading wire:target="batDauThi">Đang tạo đề...</span>
            </button>
        </div>
    </div>
</div>
