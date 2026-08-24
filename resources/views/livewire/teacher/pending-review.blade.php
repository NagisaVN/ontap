<div class="space-y-4">

    @if(session('success'))
    <div class="px-4 py-3 rounded-lg text-sm font-medium animate-slide-up"
         style="background:#d1fae5;color:#065f46">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Header + Batch --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold">Câu hỏi chờ duyệt</h2>
            <p class="text-xs mt-0.5" style="color:var(--sp-text-muted)">
                {{ $cauHois->total() }} câu đang chờ xác nhận từ OCR/AI
            </p>
        </div>
        @if(!empty($selected))
        <button wire:click="duyetHangLoat"
                class="sp-btn sp-btn-accent">
            ✅ Duyệt {{ count($selected) }} câu đã chọn
        </button>
        @endif
    </div>

    {{-- List --}}
    <div class="space-y-3">
        @forelse($cauHois as $cq)
        <div class="sp-card p-5 animate-slide-up" wire:key="cq-{{ $cq->id }}">
            <div class="flex gap-4">

                {{-- Checkbox --}}
                <div class="pt-0.5">
                    <input type="checkbox" wire:model.live="selected" value="{{ $cq->id }}"
                           class="w-4 h-4 cursor-pointer accent-indigo-500">
                </div>

                <div class="flex-1 space-y-3">
                    {{-- Meta --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="sp-badge sp-badge-yellow">⏳ Chờ duyệt</span>
                        <span class="sp-badge sp-badge-gray">{{ $cq->nguon->nhanHien() }}</span>
                        <span class="sp-badge sp-badge-indigo">{{ $cq->chuong?->monHoc?->ten ?? '?' }}</span>
                        <span class="sp-badge sp-badge-gray">{{ $cq->chuong?->ten ?? '?' }}</span>
                        <span class="text-xs" style="color:var(--sp-text-muted)">
                            {{ $cq->created_at?->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Câu hỏi --}}
                    <p class="text-sm font-medium leading-relaxed">{{ $cq->noi_dung }}</p>

                    {{-- Lựa chọn --}}
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($cq->luaChon->sortBy('thu_tu') as $i => $lc)
                        <div class="flex items-center gap-2 text-xs px-3 py-2 rounded-lg
                                    {{ $lc->la_dap_an ? 'text-emerald-700' : '' }}"
                             style="{{ $lc->la_dap_an ? 'background:#d1fae5' : 'background:#f8fafc' }}">
                            <span class="font-bold">{{ chr(65+$i) }}.</span>
                            <span>{{ $lc->noi_dung }}</span>
                            @if($lc->la_dap_an)
                                <span class="ml-auto">✅</span>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Giải thích nếu có --}}
                    @if($cq->giai_thich)
                    <p class="text-xs px-3 py-2 rounded-lg" style="background:#f1f5f9;color:var(--sp-text-muted)">
                        💡 {{ $cq->giai_thich }}
                    </p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2 flex-shrink-0">
                    <button wire:click="duyet({{ $cq->id }})"
                            class="sp-btn sp-btn-accent text-sm px-4 py-2">
                        ✅ Duyệt
                    </button>
                    <button wire:click="tuChoi({{ $cq->id }})"
                            class="sp-btn sp-btn-danger text-sm px-4 py-2">
                        ❌ Từ chối
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="sp-card p-12 text-center">
            <div class="text-5xl mb-3">🎉</div>
            <h3 class="font-bold text-lg mb-1">Không có câu hỏi nào chờ duyệt!</h3>
            <p class="text-sm" style="color:var(--sp-text-muted)">
                Upload PDF/Ảnh để AI trích xuất câu hỏi mới
            </p>
            <a href="{{ route('teacher.ocr') }}" wire:navigate class="sp-btn sp-btn-primary mt-4 inline-flex">
                📄 Upload tài liệu
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($cauHois->hasPages())
    <div class="sp-card px-4 py-3">
        {{ $cauHois->links() }}
    </div>
    @endif

</div>
