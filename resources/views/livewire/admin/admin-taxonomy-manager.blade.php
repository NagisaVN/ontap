<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold">Cấu trúc Đào tạo</h1>
        <p class="text-sm" style="color:var(--sp-text-muted)">Quản lý danh sách các môn học và các chương tương ứng.</p>
    </div>

    {{-- Notification component (using simple alpine alert) --}}
    <div x-data="{ show: false, message: '' }"
         x-on:notify.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="fixed top-5 right-5 z-50 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg"
         style="display: none;">
        <span x-text="message"></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- CỘT TRÁI: QUẢN LÝ MÔN HỌC --}}
        <div class="lg:col-span-1 space-y-4">
            
            {{-- Form thêm/sửa Môn Học --}}
            <div class="sp-card p-5 border-t-4 border-indigo-500">
                <h2 class="font-bold text-lg mb-4">{{ $subjectId ? 'Sửa môn học' : 'Thêm môn học mới' }}</h2>
                
                <form wire:submit="saveSubject" class="space-y-4">
                    <div>
                        <label class="sp-label">Mã môn học *</label>
                        <input type="text" wire:model="subjectMaMon" class="sp-input uppercase" placeholder="VD: TOAN10">
                        @error('subjectMaMon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="sp-label">Tên môn học *</label>
                        <input type="text" wire:model="subjectTen" class="sp-input" placeholder="VD: Toán học lớp 10">
                        @error('subjectTen') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="sp-label">Mô tả (tuỳ chọn)</label>
                        <textarea wire:model="subjectMoTa" class="sp-input" rows="2" placeholder="Nhập mô tả môn học..."></textarea>
                        @error('subjectMoTa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="sp-btn sp-btn-primary flex-1 justify-center">
                            {{ $subjectId ? 'Cập nhật' : 'Thêm mới' }}
                        </button>
                        @if($subjectId)
                            <button type="button" wire:click="resetSubjectForm" class="sp-btn sp-btn-outline px-3">
                                Huỷ
                            </button>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Danh sách môn học --}}
            <div class="sp-card overflow-hidden">
                <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="font-semibold">Danh sách môn học</h3>
                    <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full font-medium">{{ count($subjects) }} môn</span>
                </div>
                
                <div class="divide-y max-h-[600px] overflow-y-auto">
                    @forelse($subjects as $subject)
                        <div class="p-4 cursor-pointer hover:bg-gray-50 transition-colors {{ $activeSubjectId === $subject->id ? 'bg-indigo-50 border-l-4 border-indigo-500' : '' }}"
                             wire:click="selectSubject({{ $subject->id }})">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-sm text-gray-900">{{ $subject->ten }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">Mã: {{ $subject->ma_mon }} • {{ $subject->chuong->count() }} chương</div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button wire:click.stop="editSubject({{ $subject->id }})" class="p-1 text-gray-400 hover:text-indigo-600 transition-colors" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                    <button wire:click.stop="deleteSubject({{ $subject->id }})" wire:confirm="Bạn có chắc muốn xoá môn học này và TOÀN BỘ CHƯƠNG bên trong?" class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="Xoá">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 text-sm">
                            Chưa có môn học nào.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>


        {{-- CỘT PHẢI: QUẢN LÝ CHƯƠNG (Chỉ hiện khi đã chọn 1 môn) --}}
        <div class="lg:col-span-2">
            @if($activeSubjectId)
                @php $activeSubject = $subjects->firstWhere('id', $activeSubjectId); @endphp
                <div class="sp-card border-t-4 border-emerald-500 h-full flex flex-col">
                    
                    {{-- Header chương --}}
                    <div class="p-5 border-b bg-gray-50">
                        <div class="flex justify-between items-center mb-1">
                            <h2 class="font-bold text-lg">Quản lý Chương</h2>
                            <span class="text-xs text-gray-500">Kéo thả để sắp xếp (Sắp ra mắt)</span>
                        </div>
                        <p class="text-sm text-gray-600">Đang chọn môn: <span class="font-bold text-emerald-700">{{ $activeSubject->ten }}</span></p>
                    </div>

                    {{-- Form thêm/sửa chương (Inline) --}}
                    <div class="p-5 border-b bg-emerald-50/30">
                        <form wire:submit="saveChapter" class="flex flex-col md:flex-row gap-4 items-start">
                            <div class="w-24">
                                <label class="sp-label text-xs">Thứ tự *</label>
                                <input type="number" wire:model="chapterThuTu" min="1" class="sp-input text-center">
                                @error('chapterThuTu') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="flex-1 w-full">
                                <label class="sp-label text-xs">Tên chương (Bài) *</label>
                                <input type="text" wire:model="chapterTen" class="sp-input" placeholder="VD: Chương 1: Mệnh đề và tập hợp">
                                @error('chapterTen') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex gap-2 pt-6 w-full md:w-auto">
                                <button type="submit" class="sp-btn sp-btn-primary bg-emerald-600 hover:bg-emerald-700 border-emerald-600 flex-1 md:flex-none">
                                    {{ $chapterId ? 'Cập nhật' : 'Thêm' }}
                                </button>
                                @if($chapterId)
                                    <button type="button" wire:click="resetChapterForm" class="sp-btn sp-btn-outline px-3">
                                        Huỷ
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- Danh sách chương --}}
                    <div class="flex-1 p-5 overflow-y-auto">
                        @if($activeSubject->chuong->count() > 0)
                            <div class="space-y-3">
                                @foreach($activeSubject->chuong as $chapter)
                                    <div class="flex items-center justify-between p-3 border rounded-xl hover:shadow-sm transition-shadow bg-white {{ $chapterId === $chapter->id ? 'ring-2 ring-emerald-500' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                                                {{ $chapter->thu_tu }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $chapter->ten }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $chapter->cauHoi()->count() }} câu hỏi trong ngân hàng</div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <button wire:click="editChapter({{ $chapter->id }})" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Sửa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                            <button wire:click="deleteChapter({{ $chapter->id }})" wire:confirm="Xoá chương này sẽ làm ảnh hưởng tới các câu hỏi đang thuộc chương. Tiếp tục?" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Xoá">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-center text-gray-500 py-10">
                                <div class="text-4xl mb-3">📭</div>
                                <p class="text-sm">Chưa có chương nào cho môn học này.</p>
                                <p class="text-xs mt-1">Hãy thêm chương đầu tiên ở form phía trên.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Trạng thái Empty khi chưa chọn môn học --}}
                <div class="sp-card h-full flex flex-col items-center justify-center text-center text-gray-500 py-20 bg-gray-50 border-dashed">
                    <div class="text-5xl mb-4 text-gray-300">👈</div>
                    <h3 class="text-lg font-medium text-gray-700">Chưa chọn môn học</h3>
                    <p class="text-sm mt-2">Vui lòng chọn một môn học ở danh sách bên trái<br>hoặc tạo mới môn học để quản lý các chương tương ứng.</p>
                </div>
            @endif
        </div>

    </div>
</div>
