<div class="p-6 max-w-5xl mx-auto space-y-6">
    <h1 class="text-xl font-bold text-slate-900">Báo cáo & Phân tích lớp học</h1>

    {{-- Class Roster --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-900">Danh sách học sinh</h2>
            <p class="text-xs text-slate-500 mt-0.5">{{ $examTitle ?? 'Bài thi gần nhất' }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Hạng</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Học sinh</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Điểm TB</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Số bài</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roster ?? [] as $s)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="text-sm font-bold {{ $s['rank'] <= 3 ? 'text-amber-500' : 'text-slate-400' }}">#{{ $s['rank'] }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-indigo-700">{{ strtoupper(substr($s['name'], 0, 2)) }}</span>
                                </div>
                                <span class="font-medium text-slate-900">{{ $s['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm {{ $s['score'] >= 80 ? 'text-emerald-600' : ($s['score'] >= 60 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $s['score'] }}%
                                </span>
                                <div class="w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $s['score'] >= 80 ? 'bg-emerald-500' : ($s['score'] >= 60 ? 'bg-amber-400' : 'bg-rose-400') }}"
                                         style="width: {{ $s['score'] }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $s['exams'] }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $s['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $s['status'] === 'active' ? 'Đang học' : 'Không hoạt động' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400">Không có dữ liệu học sinh.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Hard Questions Analysis --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" class="text-rose-500" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <h2 class="font-semibold text-slate-900">Câu hỏi khó (tỷ lệ sai cao)</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($hardQuestions ?? [] as $q)
            <div class="px-5 py-4 flex items-start gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-900 leading-snug">{{ $q['question'] }}</p>
                    <div class="flex items-center gap-2 mt-1.5">
                        <span class="text-xs text-slate-500">{{ $q['topic'] }}</span>
                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $q['difficulty'] === 'Hard' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $q['difficulty'] }}
                        </span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold {{ $q['failRate'] >= 50 ? 'text-rose-600' : 'text-amber-600' }}">{{ $q['failRate'] }}%</p>
                    <p class="text-xs text-slate-400">tỷ lệ sai</p>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-slate-400">Không có dữ liệu.</div>
            @endforelse
        </div>
    </div>
</div>