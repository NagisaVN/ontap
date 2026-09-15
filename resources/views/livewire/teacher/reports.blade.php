<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Báo cáo lớp & kết quả học tập</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $report['cohortLabel'] }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $exportPdfUrl }}" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 15h8M8 18h5"/></svg>
                Xuất PDF
            </a>
            <a href="{{ $exportExcelUrl }}" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
                Xuất Excel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <label class="block">
            <span class="block text-xs font-semibold text-slate-500 mb-1.5">Môn học</span>
            <select wire:model.live="subjectId" class="w-full h-10 rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-400 focus:ring-indigo-400">
                <option value="">Tất cả môn học</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->ma_mon }} · {{ $subject->ten }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="block text-xs font-semibold text-slate-500 mb-1.5">Từ ngày</span>
            <input type="date" wire:model.live="dateFrom" class="w-full h-10 rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-400 focus:ring-indigo-400">
        </label>
        <label class="block">
            <span class="block text-xs font-semibold text-slate-500 mb-1.5">Đến ngày</span>
            <input type="date" wire:model.live="dateTo" class="w-full h-10 rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-400 focus:ring-indigo-400">
        </label>
        <label class="block">
            <span class="block text-xs font-semibold text-slate-500 mb-1.5">Ngưỡng tỷ lệ sai (%)</span>
            <input type="number" min="0" max="100" wire:model.live="failThreshold" class="w-full h-10 rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-400 focus:ring-indigo-400">
        </label>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Học viên', 'value' => $report['summary']['students'], 'class' => 'text-indigo-600'],
            ['label' => 'Lượt thi', 'value' => $report['summary']['attempts'], 'class' => 'text-blue-600'],
            ['label' => 'Điểm trung bình', 'value' => $report['summary']['averageScore'].'%', 'class' => 'text-emerald-600'],
            ['label' => 'Tỷ lệ đạt', 'value' => $report['summary']['passRate'].'%', 'class' => 'text-amber-600'],
        ] as $stat)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
                <p class="text-xs font-medium text-slate-500">{{ $stat['label'] }}</p>
                <p class="text-2xl font-bold {{ $stat['class'] }} mt-1">{{ $stat['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-900">Danh sách học viên</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ $report['subjectName'] }} · {{ $report['periodLabel'] }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Hạng</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Học viên</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Điểm TB</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Số bài</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report['roster'] as $student)
                        @php
                            $scoreTextClass = $student['score'] >= 80 ? 'text-emerald-600' : ($student['score'] >= 60 ? 'text-amber-600' : 'text-rose-600');
                            $scoreBarClass = $student['score'] >= 80 ? 'bg-emerald-500' : ($student['score'] >= 60 ? 'bg-amber-400' : 'bg-rose-400');
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5"><span class="text-sm font-bold {{ $student['rank'] <= 3 ? 'text-amber-500' : 'text-slate-400' }}">#{{ $student['rank'] }}</span></td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0"><span class="text-xs font-bold text-indigo-700">{{ $student['initials'] }}</span></div>
                                    <span class="font-medium text-slate-900">{{ $student['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2 min-w-[150px]">
                                    <span class="font-bold text-sm {{ $scoreTextClass }}">{{ $student['score'] }}%</span>
                                    <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full rounded-full {{ $scoreBarClass }}" style="width: {{ $student['score'] }}%"></div></div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $student['exams'] }}</td>
                            <td class="px-5 py-3.5"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $student['status'] === 'active' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-slate-100 border border-slate-200 text-slate-600' }}">{{ $student['status'] === 'active' ? 'Đang hoạt động' : 'Không hoạt động' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">Chưa có lượt thi hoàn thành phù hợp bộ lọc.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-900">Phân bố điểm</h2>
                <p class="text-xs text-slate-500 mt-0.5">Theo nhóm dữ liệu đang chọn</p>
            </div>
            <div class="p-5 space-y-4">
                @foreach ($report['scoreDistribution'] as $bucket)
                    <div>
                        <div class="flex justify-between text-xs mb-1.5"><span class="font-medium text-slate-700">{{ $bucket['label'] }} điểm</span><span class="text-slate-500">{{ $bucket['count'] }} lượt · {{ $bucket['percentage'] }}%</span></div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-indigo-500 rounded-full" style="width: {{ $bucket['percentage'] }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-900">Ghi chú dữ liệu</h2>
                <p class="text-xs text-slate-500 mt-0.5">Chưa có model lớp/nhóm riêng; báo cáo nhóm các học viên có lượt thi hoàn thành trên đề được chọn.</p>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="rounded-lg bg-indigo-50 p-4"><p class="font-semibold text-indigo-900">Dữ liệu bài thi</p><p class="text-indigo-700 text-xs mt-1">Điểm, xếp hạng, phân bố và tỷ lệ sai được tính từ lượt thi đã hoàn thành.</p></div>
                <div class="rounded-lg bg-amber-50 p-4"><p class="font-semibold text-amber-900">Dữ liệu luyện tập</p><p class="text-amber-700 text-xs mt-1">Mỗi câu trong bảng bên dưới có thêm tỷ lệ sai từ lịch sử luyện tập của chính cohort.</p></div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-slate-900">Phân tích độ khó câu hỏi</h2>
                <p class="text-xs text-slate-500 mt-0.5">Các câu có tỷ lệ sai vượt ngưỡng được tô hồng. Hiển thị tối đa 50 câu có tỷ lệ sai cao nhất.</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-rose-600 font-medium whitespace-nowrap"><span class="w-2 h-2 rounded-full bg-rose-400"></span>Trên {{ $failThreshold }}% sai</div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Câu hỏi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Chủ đề</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Độ khó</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tỷ lệ sai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report['difficultyAnalysis'] as $item)
                        @php $isHigh = $item['failRate'] > $failThreshold; @endphp
                        <tr class="transition-colors {{ $isHigh ? 'bg-rose-50 hover:bg-rose-100/70' : 'hover:bg-slate-50' }}">
                            <td class="px-5 py-3.5 max-w-md"><p class="text-xs leading-relaxed {{ $isHigh ? 'text-rose-800 font-medium' : 'text-slate-800' }}">{{ $item['question'] }}</p><p class="text-[11px] text-slate-400 mt-1">{{ $item['responses'] }} lượt trả lời · {{ $item['wrongCount'] }} lượt sai</p></td>
                            <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap text-xs"><p>{{ $item['topic'] }}</p><p class="text-slate-400 mt-1">{{ $item['subject'] }}</p></td>
                            <td class="px-5 py-3.5 whitespace-nowrap"><span class="inline-flex px-2 py-0.5 rounded-full border text-xs font-semibold {{ $item['difficulty'] === 'Khó' ? 'border-rose-200 bg-rose-50 text-rose-700' : ($item['difficulty'] === 'Trung bình' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700') }}">{{ $item['difficulty'] }}</span></td>
                            <td class="px-5 py-3.5 whitespace-nowrap"><div class="flex items-center gap-2"><span class="text-sm font-bold {{ $isHigh ? 'text-rose-600' : 'text-slate-700' }}">{{ $item['failRate'] }}%</span><div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full rounded-full {{ $isHigh ? 'bg-rose-400' : 'bg-slate-300' }}" style="width: {{ $item['failRate'] }}%"></div></div></div><p class="text-[11px] text-slate-400 mt-1">Luyện tập: {{ $item['practiceFailRate'] === null ? 'chưa có' : $item['practiceFailRate'].'%' }} · {{ $item['practiceAttempts'] }} lượt</p></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">Chưa có câu trả lời phù hợp bộ lọc.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
