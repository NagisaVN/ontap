<div class="p-4 sm:p-6 max-w-4xl mx-auto">
  <div class="flex items-center justify-between mb-4 sm:mb-6">
    <div>
      <h1 class="text-lg sm:text-xl font-bold text-slate-900">Quản lý Cấu trúc Đào tạo</h1>
      <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Quản lý Khối ngành → Môn học → Chương</p>
    </div>
    <button wire:click="createMajor" class="flex items-center gap-1.5 h-9 px-3 sm:px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-xs sm:text-sm font-semibold transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Thêm Khối ngành
    </button>
  </div>

  @if(session('status'))
  <div class="mb-4 flex items-center gap-2 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ session('status') }}
  </div>
  @endif

  @if(session('error'))
  <div class="mb-4 flex items-center gap-2 px-4 py-2.5 bg-rose-50 border border-rose-200 rounded-lg text-sm text-rose-700">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ session('error') }}
  </div>
  @endif

  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3 sm:p-5">
    <!-- Depth 0: Major -->
    @if(isset($tree) && count($tree) > 0)
    @foreach($tree as $major)
      <div x-data="{ open: false, addingChild: false, newName: '' }">
        <div class="flex items-center gap-1 py-2 rounded-lg hover:bg-slate-50 group transition-colors mb-0.5" style="padding-left: 8px">
          <!-- Toggle -->
          <button @click="open = !open" class="w-6 h-6 flex items-center justify-center rounded text-slate-400 transition-colors hover:bg-slate-200">
            <svg x-show="open" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
          </button>

          <!-- Name -->
          <span class="flex-1 text-[15px] font-bold text-slate-900">{{ $major['name'] }}</span>

          <!-- Actions -->
          <div class="flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
            <button wire:click="createChild('subject', {{ $major['id'] }})" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Thêm môn học">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
            <button wire:click="editNode('major', {{ $major['id'] }})" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors" title="Sửa">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
            </button>
            <button wire:click="deleteNode('major', {{ $major['id'] }})" wire:confirm="Xóa khối ngành này?" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Xóa">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </button>
          </div>
        </div>

        <!-- Add child inline form -->
        <div x-show="addingChild" style="display: none;" class="flex items-center gap-2 py-1.5 pr-2" style="padding-left: 28px">
          <div class="w-5 shrink-0"></div>
          <input x-model="newName" @keydown.enter="$wire.addChild('subject', {{ $major['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" @keydown.escape="addingChild = false; newName = '';" placeholder="Tên môn học mới…" class="flex-1 h-8 px-3 border border-indigo-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 bg-indigo-50" />
          <button @click="$wire.addChild('subject', {{ $major['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" class="w-7 h-7 flex items-center justify-center text-emerald-600 hover:bg-emerald-50 rounded transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </button>
          <button @click="addingChild = false; newName = '';" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:bg-slate-100 rounded transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>

        <!-- Depth 1: Subjects -->
        <div x-show="open" x-transition style="display: none;">
          @if(isset($major['children']) && count($major['children']) > 0)
            @foreach($major['children'] as $subject)
              <div x-data="{ open: false, addingChild: false, newName: '' }">
                <div class="flex items-center gap-1 py-1.5 rounded-lg hover:bg-slate-50 group transition-colors" style="padding-left: 28px">
                  <button @click="open = !open" class="w-6 h-6 flex items-center justify-center rounded text-slate-400 transition-colors hover:bg-slate-200">
                    <svg x-show="open" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                  </button>
                  <span class="flex-1 text-[14px] font-semibold text-slate-800">{{ $subject['name'] }}</span>
                  <div class="flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                    <button @click="addingChild = true" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Thêm nhanh chương">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </button>
                    <button wire:click="editNode('subject', {{ $subject['id'] }})" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors" title="Sửa">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                    </button>
                    <button wire:click="deleteNode('subject', {{ $subject['id'] }})" wire:confirm="Xóa môn học này?" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Xóa">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                  </div>
                </div>

                <!-- Add child inline form -->
                <div x-show="addingChild" style="display: none;" class="flex items-center gap-2 py-1.5 pr-2" style="padding-left: 48px">
                  <div class="w-5 shrink-0"></div>
                  <input x-model="newName" @keydown.enter="$wire.addChild('subsubject', {{ $subject['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" @keydown.escape="addingChild = false; newName = '';" placeholder="Tên chương mới…" class="flex-1 h-8 px-3 border border-indigo-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 bg-indigo-50" />
                  <button @click="$wire.addChild('subsubject', {{ $subject['id'] }}, newName).then(() => { addingChild = false; newName = ''; })" class="w-7 h-7 flex items-center justify-center text-emerald-600 hover:bg-emerald-50 rounded transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </button>
                  <button @click="addingChild = false; newName = '';" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:bg-slate-100 rounded transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                </div>

                <!-- Depth 2: Sub-subjects -->
                <div x-show="open" x-transition style="display: none;">
                  @if(isset($subject['children']))
                    @foreach($subject['children'] as $sub)
                      <div>
                        <div class="flex items-center gap-1 py-1.5 rounded-lg hover:bg-slate-50 group transition-colors" style="padding-left: 54px">
                          <div class="w-4 h-4 shrink-0 flex items-center justify-center text-slate-300">
                             <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14"/></svg>
                          </div>
                          <span class="flex-1 text-[13.5px] font-medium text-slate-700 ml-1">{{ $sub['name'] }}</span>
                          <div class="flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                            <button wire:click="editNode('subsubject', {{ $sub['id'] }})" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-slate-200 transition-colors" title="Sửa">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                            </button>
                            <button wire:click="deleteNode('subsubject', {{ $sub['id'] }})" wire:confirm="Xóa chương này?" class="w-7 h-7 rounded flex items-center justify-center text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Xóa">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
            @endforeach
          @else
            <div class="text-sm text-slate-400 italic pl-10 py-1">Chưa có môn học nào.</div>
          @endif
        </div>
      </div>
    @endforeach
    @else
      <div class="py-10 text-center text-slate-400 text-sm">Chưa có dữ liệu Cấu trúc Đào tạo. Hãy bắt đầu bằng cách thêm Khối ngành.</div>
    @endif
  </div>

  <!-- Legend -->
  <div class="mt-4 flex items-center gap-4 text-xs text-slate-400 px-1">
    <div class="flex items-center gap-1.5"><span class="font-bold text-slate-600">In đậm</span> = Khối ngành</div>
    <div class="flex items-center gap-1.5"><span class="font-semibold text-slate-500">Bán đậm</span> = Môn học</div>
    <div class="flex items-center gap-1.5"><span class="font-medium text-slate-500">Thường</span> = Chương</div>
  </div>

  {{-- ════════════ MODAL THÊM / SỬA ════════════ --}}
  @if($showModal)
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" wire:click="$set('showModal', false)"></div>
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md z-10 overflow-hidden">
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
              <h2 class="text-base font-bold text-slate-800">
                  {{ $modalMode === 'create' ? 'Thêm mới' : 'Chỉnh sửa' }}
                  {{ $nodeType === 'major' ? 'Khối ngành' : ($nodeType === 'subject' ? 'Môn học' : 'Chương') }}
              </h2>
              <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </button>
          </div>
          
          <div class="p-5 space-y-4">
              <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tên <span class="text-rose-500">*</span></label>
                  <input type="text" wire:model="ten" class="w-full h-10 px-3 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 {{ $errors->has('ten') ? 'border-rose-400 focus:border-rose-400' : 'border-slate-200 focus:border-indigo-400' }}" placeholder="Nhập tên..." autofocus>
                  @error('ten') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
              </div>

              @if($nodeType === 'subject')
              <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mã môn học</label>
                  <input type="text" wire:model="ma_mon" class="w-full h-10 px-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400" placeholder="VD: PHY101">
              </div>
              @endif

              @if(in_array($nodeType, ['major', 'subject']))
              <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">Mô tả</label>
                  <textarea wire:model="mo_ta" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 resize-none" placeholder="Mô tả ngắn gọn..."></textarea>
              </div>
              @endif
          </div>

          <div class="px-5 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
              <button wire:click="$set('showModal', false)" class="h-9 px-4 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-200 transition-colors">
                  Hủy
              </button>
              <button wire:click="save" class="h-9 px-5 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors flex items-center gap-2">
                  <span wire:loading.remove wire:target="save">Lưu</span>
                  <span wire:loading wire:target="save">
                      <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  </span>
              </button>
          </div>
      </div>
  </div>
  @endif
</div>
