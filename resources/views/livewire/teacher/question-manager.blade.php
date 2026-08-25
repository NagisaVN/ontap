<div class="space-y-4" x-data="{ deleteId: null }">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="sp-badge-green px-4 py-3 rounded-lg text-sm font-medium animate-slide-up">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- ── Toolbar ── --}}
    <div class="sp-card p-4 flex flex-wrap gap-3 items-center">
        <input wire:model.live.debounce.300ms="search"
               type="text" placeholder="🔍 Tìm câu hỏi..."
               class="sp-input flex-1 min-w-48">

        <select wire:model.live="monHocId" class="sp-input sp-select" style="width:auto">
            <option value="0">Tất cả môn</option>
            @foreach($monHocs as $m)
                <option value="{{ $m->id }}">{{ $m->ten }}</option>
            @endforeach
        </select>

        @if($chuongs->isNotEmpty())
        <select wire:model.live="chuongId" class="sp-input sp-select" style="width:auto">
            <option value="0">Tất cả chương</option>
            @foreach($chuongs as $ch)
                <option value="{{ $ch->id }}">{{ $ch->ten }}</option>
            @endforeach
        </select>
        @endif

        <select wire:model.live="doKho" class="sp-input sp-select" style="width:auto">
            <option value="">Độ khó</option>
            <option value="de">🟢 Dễ</option>
            <option value="trung_binh">🟡 Trung bình</option>
            <option value="kho">🔴 Khó</option>
        </select>

        <select wire:model.live="trangThai" class="sp-input sp-select" style="width:auto">
            <option value="">Trạng thái</option>
            <option value="da_duyet">✅ Đã duyệt</option>
            <option value="cho_duyet">⏳ Chờ duyệt</option>
            <option value="tu_choi">❌ Từ chối</option>
        </select>

        <button wire:click="openCreate" class="sp-btn sp-btn-primary flex-shrink-0">
            ➕ Thêm câu hỏi
        </button>
    </div>

    {{-- ── Table ── --}}
    <div class="sp-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nội dung câu hỏi</th>
                        <th>Chương</th>
                        <th>Độ khó</th>
                        <th>Trạng thái</th>
                        <th>Nguồn</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cauHois as $cq)
                    <tr wire:key="cq-{{ $cq->id }}">
                        <td class="text-xs text-center" style="color:var(--sp-text-muted)">#{{ $cq->id }}</td>
                        <td class="max-w-xs">
                            <p class="text-sm font-medium leading-snug">{{ Str::limit($cq->noi_dung, 70) }}</p>
                        </td>
                        <td class="text-xs" style="color:var(--sp-text-muted)">
                            {{ $cq->chuong?->ten ?? '—' }}
                            <br>
                            <span style="color:#94a3b8">{{ $cq->chuong?->monHoc?->ten }}</span>
                        </td>
                        <td>
                            @php $doKhoMap = ['de'=>['🟢','green'],'trung_binh'=>['🟡','yellow'],'kho'=>['🔴','red']] @endphp
                            <span class="sp-badge sp-badge-{{ $doKhoMap[$cq->do_kho->value][1] ?? 'gray' }}">
                                {{ $cq->do_kho->nhanHien() }}
                            </span>
                        </td>
                        <td>
                            <span class="sp-badge sp-badge-{{ $cq->trang_thai->mauSac() }}">
                                {{ $cq->trang_thai->nhanHien() }}
                            </span>
                        </td>
                        <td>
                            <span class="sp-badge sp-badge-gray text-xs">
                                {{ $cq->nguon->nhanHien() }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-1.5">
                                <button wire:click="openEdit({{ $cq->id }})"
                                        class="sp-btn sp-btn-outline text-xs py-1 px-2">✏️</button>
                                <button wire:click="xoa({{ $cq->id }})"
                                        wire:confirm="Bạn có chắc muốn xóa câu hỏi này?"
                                        class="sp-btn sp-btn-danger text-xs py-1 px-2">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12" style="color:var(--sp-text-muted)">
                            Không tìm thấy câu hỏi nào 🔍
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t" style="border-color:var(--sp-border)">
            {{ $cauHois->links() }}
        </div>
    </div>

    {{-- ── Create/Edit Modal ── --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         x-data x-init="$el.scrollIntoView()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-slide-up">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between z-10"
                 style="border-color:var(--sp-border)">
                <h2 class="font-bold text-lg">{{ $editingId ? '✏️ Sửa câu hỏi #'.$editingId : '➕ Thêm câu hỏi mới' }}</h2>
                <button wire:click="$set('showModal', false)"
                        class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                {{-- Nội dung --}}
                <div>
                    <label class="sp-label">Nội dung câu hỏi *</label>
                    <textarea wire:model="formNoiDung" rows="3"
                              class="sp-input resize-none"
                              placeholder="Nhập nội dung câu hỏi..."></textarea>
                    @error('formNoiDung') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Chương + Độ khó --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="sp-label">Chương *</label>
                        <select wire:model="formChuongId" class="sp-input sp-select">
                            <option value="0">-- Chọn chương --</option>
                            @foreach($allChuong as $ch)
                                <option value="{{ $ch->id }}">{{ $ch->monHoc?->ten }} / {{ $ch->ten }}</option>
                            @endforeach
                        </select>
                        @error('formChuongId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="sp-label">Độ khó *</label>
                        <select wire:model="formDoKho" class="sp-input sp-select">
                            <option value="de">🟢 Dễ</option>
                            <option value="trung_binh">🟡 Trung bình</option>
                            <option value="kho">🔴 Khó</option>
                        </select>
                    </div>
                </div>

                {{-- Lựa chọn --}}
                <div>
                    <label class="sp-label">Lựa chọn (chọn radio = đáp án đúng) *</label>
                    @error('formLuaChon') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror
                    <div class="space-y-2">
                        @foreach($formLuaChon as $i => $lc)
                        <div class="flex items-center gap-2">
                            <span class="w-6 text-sm font-bold text-center" style="color:var(--sp-text-muted)">
                                {{ chr(65+$i) }}
                            </span>
                            <input wire:model="formLuaChon.{{ $i }}.noi_dung"
                                   type="text" class="sp-input flex-1"
                                   placeholder="Nhập lựa chọn {{ chr(65+$i) }}...">
                            <input wire:model="formLuaChon.{{ $i }}.la_dap_an"
                                   type="radio" name="dap_an_dung" value="1"
                                   {{ $lc['la_dap_an'] ? 'checked' : '' }}
                                   wire:click="$set('formLuaChon.{{ $i }}.la_dap_an', true)"
                                   class="w-4 h-4 cursor-pointer accent-indigo-500">
                            <span class="text-xs" style="color:var(--sp-text-muted)">Đúng</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Giải thích --}}
                <div>
                    <label class="sp-label">Giải thích (tùy chọn)</label>
                    <textarea wire:model="formGiaiThich" rows="2"
                              class="sp-input resize-none"
                              placeholder="Giải thích tại sao đáp án đúng..."></textarea>
                </div>
            </div>

            <div class="sticky bottom-0 bg-white border-t px-6 py-4 flex justify-end gap-3"
                 style="border-color:var(--sp-border)">
                <button wire:click="$set('showModal', false)" class="sp-btn sp-btn-outline">Hủy</button>
                <button wire:click="luu" wire:loading.attr="disabled" wire:target="luu" class="sp-btn sp-btn-primary">

                    {{-- Default state --}}
                    <span wire:loading.remove wire:target="luu" class="flex items-center gap-2">
                        💾 Lưu câu hỏi
                    </span>

                    {{-- Loading state --}}
                    <span wire:loading wire:target="luu" class="flex items-center gap-2" style="display:none">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Đang lưu...</span>
                    </span>

                </button>
            </div>
        </div>
    </div>
    @endif

</div>
