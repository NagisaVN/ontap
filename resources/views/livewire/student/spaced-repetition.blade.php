<div class="max-w-2xl mx-auto space-y-5">
    <div class="sp-card p-8 text-center">
        <div class="text-5xl mb-4">🔄</div>
        <h1 class="text-2xl font-bold mb-2">Ôn điểm yếu</h1>
        <p class="text-sm mb-6" style="color:var(--sp-text-muted)">
            Các câu hỏi bạn sai nhiều lần sẽ xuất hiện ở đây để ôn tập lại
        </p>

        <select wire:model.live="selectedMonHocId" class="sp-input sp-select mb-4" style="max-width:280px">
            <option value="0">-- Tất cả môn --</option>
            @foreach($monHocs as $m)
                <option value="{{ $m->id }}">{{ $m->ten }}</option>
            @endforeach
        </select>

        @if($hangDoi->isNotEmpty())
        <div class="text-left space-y-3 mt-4">
            @foreach($hangDoi as $item)
            <div class="sp-card p-4">
                <div class="flex justify-between items-start gap-3">
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ Str::limit($item['cauHoi']?->noi_dung ?? '?', 100) }}</p>
                        <p class="text-xs mt-1" style="color:var(--sp-danger)">
                            Đã sai {{ $item['so_lan_sai'] }} lần
                        </p>
                    </div>
                    <span class="sp-badge sp-badge-red flex-shrink-0">{{ $item['so_lan_sai'] }}x</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-sm mt-4" style="color:var(--sp-text-muted)">
            🎉 Chưa có câu hỏi nào cần ôn tập. Hãy làm thêm bài thi!
        </div>
        @endif
    </div>
</div>
