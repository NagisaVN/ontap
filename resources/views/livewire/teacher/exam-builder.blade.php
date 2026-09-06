<div class="p-6 max-w-5xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tạo đề thi</h1>
            <p class="text-sm text-slate-500 mt-0.5">Chọn câu hỏi từ ngân hàng và cấu hình đề thi.</p>
        </div>
        <button wire:click="publishExam"
            class="flex items-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-white text-sm font-semibold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.7 14a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 3h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Xuất bản đề thi
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Config panel --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
                <h2 class="font-semibold text-slate-900">Cài đặt đề thi</h2>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tên đề thi</label>
                    <input type="text" wire:model="examTitle" placeholder="VD: Đề thi Vật lý T9/2026"
                        class="w-full h-9 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"/>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Thời gian (phút)</label>
                    <div class="flex gap-2">
                        @foreach([30, 45, 60, 90] as $t)
                        <button type="button" wire:click="$set('timeLimit', {{ $t }})"
                            class="flex-1 h-8 rounded-lg border text-xs font-semibold transition-colors {{ ($timeLimit ?? 45) == $t ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 text-slate-600 hover:border-indigo-300' }}">
                            {{ $t }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Trộn câu hỏi</label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="shuffle" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-700">Xáo trộn thứ tự câu hỏi</span>
                    </label>
                </div>
            </div>

            {{-- Selected summary --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-slate-900">Đã chọn</h2>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{{ count($selectedIds ?? []) }} câu</span>
                </div>
                <div class="space-y-1.5 max-h-64 overflow-y-auto">
                    @forelse($selectedQuestions ?? [] as $q)
                    <div class="flex items-start gap-2 text-xs">
                        <span class="text-slate-400 font-mono shrink-0">#{{ $q->id }}</span>
                        <p class="text-slate-700 truncate flex-1">{{ $q->content }}</p>
                        <button wire:click="removeFromExam({{ $q->id }})" class="text-slate-300 hover:text-rose-500 transition-colors shrink-0">×</button>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">Chưa chọn câu hỏi nào.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Question bank --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="builderSearch" placeholder="Tìm câu hỏi để thêm…"
                        class="w-full pl-9 pr-4 h-9 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400"/>
                </div>
            </div>
            <div class="divide-y divide-slate-100 max-h-[540px] overflow-y-auto">
                @forelse($bankQuestions ?? [] as $q)
                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                    <input type="checkbox" wire:model="selectedIds" value="{{ $q->id }}" class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-900 leading-snug">{{ $q->content }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-slate-500">{{ $q->subject }}</span>
                            <span class="text-xs px-1.5 py-0.5 rounded-full {{ $q->difficulty === 'easy' ? 'bg-emerald-100 text-emerald-700' : ($q->difficulty === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                {{ ucfirst($q->difficulty) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-sm text-slate-400">Không tìm thấy câu hỏi.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>