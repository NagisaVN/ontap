<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Tổng quan Giáo viên</h1>
        <p class="text-sm text-slate-500 mt-0.5">Quản lý câu hỏi, đề thi, và báo cáo học sinh.</p>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0 text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Câu hỏi đã tải lên</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{{ $totalQuestions ?? 0 }}</p>
                <p class="text-xs font-medium mt-1.5 text-emerald-600">↑ {{ $newThisWeek ?? 0 }} tuần này</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0 text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Chờ duyệt</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{{ $pendingCount ?? 0 }}</p>
                <p class="text-xs font-medium mt-1.5 text-rose-600">↑ {{ $pendingSince ?? 0 }} từ hôm qua</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Hàng đợi OCR</p>
                <p class="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{{ $ocrQueue ?? 0 }}</p>
                <p class="text-xs font-medium mt-1.5 text-blue-600">Đang xử lý {{ $ocrProcessing ?? 0 }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Quick actions --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <h2 class="font-semibold text-slate-900 mb-4">Thao tác nhanh</h2>
            <div class="space-y-2">
                <a href="{{ route('teacher.create-question') }}" wire:navigate
                   class="flex items-center gap-3 px-4 h-10 rounded-xl text-sm font-medium transition-colors text-indigo-600 bg-indigo-50 hover:bg-indigo-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Thêm câu hỏi mới
                </a>
                <a href="{{ route('teacher.ocr') }}" wire:navigate
                   class="flex items-center gap-3 px-4 h-10 rounded-xl text-sm font-medium transition-colors text-emerald-600 bg-emerald-50 hover:bg-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                    Tải ảnh OCR
                </a>
                <a href="{{ route('teacher.exam-builder') }}" wire:navigate
                   class="flex items-center gap-3 px-4 h-10 rounded-xl text-sm font-medium transition-colors text-blue-600 bg-blue-50 hover:bg-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    Tạo đề thi
                </a>
                <a href="{{ route('teacher.pending') }}" wire:navigate
                   class="flex items-center gap-3 px-4 h-10 rounded-xl text-sm font-medium transition-colors text-amber-600 bg-amber-50 hover:bg-amber-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Duyệt câu hỏi
                </a>
            </div>
        </div>

        {{-- Activity timeline --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-5 pt-5 pb-3 border-b border-slate-100">
                <h2 class="font-semibold text-slate-900">Hoạt động gần đây</h2>
            </div>
            <div class="px-5 py-2 divide-y divide-slate-100">
                @forelse($activities ?? [] as $item)
                <div class="flex items-start gap-3 py-3.5">
                    <div class="w-8 h-8 rounded-full {{ $item['color'] ?? 'bg-slate-100 text-slate-600' }} flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                        {{ $item['avatar'] ?? '?' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm text-slate-900">
                            <span class="font-semibold">{{ $item['actor'] ?? '' }}</span>
                            <span class="text-slate-600"> {{ $item['action'] ?? '' }}</span>
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $item['subject'] ?? '' }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $item['time'] ?? '' }}</p>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-sm text-slate-400">Chưa có hoạt động nào.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>