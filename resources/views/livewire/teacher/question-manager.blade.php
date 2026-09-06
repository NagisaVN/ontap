<div>
<div class="p-6 max-w-5xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Quản lý câu hỏi</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $total ?? 0 }} câu hỏi trong ngân hàng</p>
        </div>
        <a href="{{ route('teacher.create-question') }}" wire:navigate
           class="flex items-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-sm font-semibold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Thêm câu hỏi
        </a>
    </div>

    {{-- Flash message --}}
    @if(session('status'))
        <div class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('status') }}
        </div>
    @endif

    {{-- Filters + Bulk toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        {{-- Search --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm câu hỏi…"
                class="pl-9 pr-4 h-9 border border-slate-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 w-64"/>
        </div>

        {{-- Subject filter --}}
        <div class="relative">
            <select wire:model.live="filterSubject" class="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                <option value="">Tất cả môn</option>
                @foreach($subjects ?? [] as $s)
                <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
        </div>

        {{-- Difficulty filter --}}
        <div class="relative">
            <select wire:model.live="filterDifficulty" class="h-9 pl-3 pr-8 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                <option value="">Tất cả độ khó</option>
                <option value="de">Dễ</option>
                <option value="trung_binh">Trung bình</option>
                <option value="kho">Khó</option>
            </select>
            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
        </div>

        {{-- Bulk delete toolbar --}}
        @if(count($selectedIds) > 0)
        <div class="flex items-center gap-2 ml-auto pl-3 border-l border-slate-200">
            <span class="text-sm text-slate-600 font-medium">Đã chọn <strong>{{ count($selectedIds) }}</strong></span>
            <button wire:click="deleteSelected"
                    wire:confirm="Xóa {{ count($selectedIds) }} câu hỏi đã chọn?"
                    class="flex items-center gap-1.5 h-8 px-3 bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Xóa đã chọn
            </button>
        </div>
        @endif

        {{-- Delete all --}}
        <div class="{{ count($selectedIds) > 0 ? '' : 'ml-auto' }}">
            <button wire:click="deleteAll"
                    wire:confirm="Xóa tất cả câu hỏi đang lọc? Hành động này không thể hoàn tác!"
                    class="flex items-center gap-1.5 h-8 px-3 border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-semibold rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                Xóa tất cả
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" wire:model.live="selectAll"
                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Câu hỏi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Môn</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Độ khó</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions ?? [] as $q)
                    <tr class="hover:bg-slate-50 transition-colors {{ in_array((string)$q->id, $selectedIds) ? 'bg-indigo-50/40' : '' }}">
                        <td class="px-4 py-3.5">
                            <input type="checkbox" wire:model.live="selectedIds" value="{{ $q->id }}"
                                class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 font-mono text-xs">#{{ $q->id }}</td>
                        <td class="px-5 py-3.5 text-slate-900 max-w-xs">
                            <p class="truncate">{{ $q->noi_dung }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $q->chuong?->monHoc?->ten ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $q->do_kho?->value === 'de' ? 'bg-emerald-100 text-emerald-700' : ($q->do_kho?->value === 'trung_binh' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                {{ $q->do_kho?->nhanHien() ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $q->created_at?->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                {{-- Edit button --}}
                                <button wire:click="openEdit({{ $q->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="openEdit({{ $q->id }})"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                        title="Chỉnh sửa câu hỏi #{{ $q->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                {{-- Delete button --}}
                                <button wire:click="deleteQuestion({{ $q->id }})" wire:confirm="Xóa câu hỏi #{{ $q->id }}?"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400">Không tìm thấy câu hỏi nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-100 bg-slate-50">
            <p class="text-xs text-slate-500">{{ $questions->firstItem() ?? 0 }}–{{ $questions->lastItem() ?? 0 }} / {{ $questions->total() }}</p>
            <div>{{ $questions->links('vendor.pagination.tailwind') }}</div>
        </div>
    </div>

</div>

</div>{{-- end main content --}}

{{-- ════════════════════════════════════════════════════════════
     EDIT SLIDE-OVER MODAL
════════════════════════════════════════════════════════════ --}}
@if($showEditModal)
<div>{{-- Livewire single-root wrapper for modal --}}
{{-- Backdrop --}}
<div class="fixed inset-0 bg-black/40 z-40 transition-opacity"
     wire:click="closeEdit"></div>

{{-- Slide-over panel --}}
<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[600px] bg-white shadow-2xl flex flex-col
            transform transition-transform duration-300 ease-out">

    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 flex-shrink-0">
        <div>
            <h2 class="text-base font-bold text-slate-900">Chỉnh sửa câu hỏi</h2>
            <p class="text-xs text-slate-500 mt-0.5">#{{ $editingId }}</p>
        </div>
        <button wire:click="closeEdit"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    {{-- Scrollable body --}}
    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

        {{-- Nội dung câu hỏi --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                Nội dung câu hỏi <span class="text-rose-500">*</span>
            </label>
            <textarea wire:model="editNoiDung" rows="4"
                class="w-full border rounded-xl px-3.5 py-2.5 text-sm text-slate-800 resize-y
                       focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                       {{ $errors->has('editNoiDung') ? 'border-rose-400 bg-rose-50' : 'border-slate-200' }}"
                placeholder="Nhập nội dung câu hỏi…"></textarea>
            @error('editNoiDung')
                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Chương + Độ khó --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Chương <span class="text-rose-500">*</span>
                </label>
                <select wire:model="editChuongId"
                    class="w-full h-10 border rounded-xl px-3 text-sm text-slate-700 appearance-none
                           focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                           {{ $errors->has('editChuongId') ? 'border-rose-400 bg-rose-50' : 'border-slate-200' }}">
                    <option value="">— Chọn chương —</option>
                    @foreach($chuongs ?? [] as $chuong)
                    <option value="{{ $chuong->id }}">{{ $chuong->monHoc?->ten }} › {{ $chuong->ten }}</option>
                    @endforeach
                </select>
                @error('editChuongId')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Độ khó</label>
                <select wire:model="editDoKho"
                    class="w-full h-10 border border-slate-200 rounded-xl px-3 text-sm text-slate-700 appearance-none
                           focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400">
                    <option value="de">Dễ</option>
                    <option value="trung_binh">Trung bình</option>
                    <option value="kho">Khó</option>
                </select>
            </div>
        </div>

        {{-- Trạng thái --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Trạng thái</label>
            <div class="flex gap-3">
                @foreach(['cho_duyet' => 'Chờ duyệt', 'da_duyet' => 'Đã duyệt', 'tu_choi' => 'Từ chối'] as $val => $label)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" wire:model="editTrangThai" value="{{ $val }}"
                           class="text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-slate-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Lựa chọn --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-semibold text-slate-700">
                    Lựa chọn <span class="text-rose-500">*</span>
                    <span class="text-xs font-normal text-slate-400 ml-1">(bấm ◎ để đặt đáp án đúng)</span>
                </label>
                @if(count($editLuaChon) < 6)
                <button wire:click="addOption" type="button"
                        class="flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Thêm lựa chọn
                </button>
                @endif
            </div>

            @error('editLuaChon')
                <p class="text-xs text-rose-500 mb-2">{{ $message }}</p>
            @enderror

            <div class="space-y-2.5">
                @foreach($editLuaChon as $i => $lc)
                <div class="flex items-center gap-2.5 group">
                    {{-- Correct answer toggle --}}
                    <button wire:click="setCorrectAnswer({{ $i }})" type="button"
                            class="flex-shrink-0 w-6 h-6 rounded-full border-2 transition-all flex items-center justify-center
                                   {{ $lc['la_dap_an'] ? 'border-emerald-500 bg-emerald-500' : 'border-slate-300 hover:border-emerald-400' }}"
                            title="{{ $lc['la_dap_an'] ? 'Đáp án đúng' : 'Đặt làm đáp án đúng' }}">
                        @if($lc['la_dap_an'])
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="none" stroke="white" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        @endif
                    </button>

                    {{-- Option letter badge --}}
                    <span class="flex-shrink-0 w-6 h-6 rounded-md text-xs font-bold flex items-center justify-center
                                 {{ $lc['la_dap_an'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ chr(65 + $i) }}
                    </span>

                    {{-- Text input --}}
                    <input type="text" wire:model="editLuaChon.{{ $i }}.noi_dung"
                           placeholder="Nội dung lựa chọn {{ chr(65 + $i) }}…"
                           class="flex-1 h-9 border rounded-lg px-3 text-sm text-slate-800
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                  {{ $lc['la_dap_an'] ? 'border-emerald-300 bg-emerald-50/50' : 'border-slate-200' }}
                                  {{ ($errors->has("editLuaChon.{$i}.noi_dung")) ? 'border-rose-400' : '' }}">

                    {{-- Remove button --}}
                    @if(count($editLuaChon) > 2)
                    <button wire:click="removeOption({{ $i }})" type="button"
                            class="flex-shrink-0 w-6 h-6 rounded-md text-slate-300 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition-colors opacity-0 group-hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    @endif
                </div>
                @if($errors->has("editLuaChon.{$i}.noi_dung"))
                    <p class="text-xs text-rose-500 ml-14">{{ $errors->first("editLuaChon.{$i}.noi_dung") }}</p>
                @endif
                @endforeach
            </div>
        </div>

    </div>

    {{-- Footer actions --}}
    <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 flex-shrink-0">
        <button wire:click="closeEdit" type="button"
                class="h-10 px-5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
            Hủy
        </button>
        <button wire:click="saveEdit"
                wire:loading.attr="disabled"
                wire:target="saveEdit"
                class="h-10 px-6 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 rounded-xl text-sm font-semibold text-white transition-colors flex items-center gap-2">
            <span wire:loading.remove wire:target="saveEdit">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            </span>
            <span wire:loading wire:target="saveEdit" style="display:none">
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </span>
            <span wire:loading.remove wire:target="saveEdit">Lưu thay đổi</span>
            <span wire:loading wire:target="saveEdit" style="display:none">Đang lưu…</span>
        </button>
    </div>

</div>
</div>{{-- end modal wrapper --}}
@endif

</div>{{-- end Livewire root --}}