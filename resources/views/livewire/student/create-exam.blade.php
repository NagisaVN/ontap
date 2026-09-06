<div class="p-6 max-w-2xl mx-auto">
    <h1 class="text-xl font-bold text-slate-900 mb-1">Tạo bài thi mới</h1>
    <p class="text-sm text-slate-500 mb-6">Chọn môn học và cấu hình bài thi bên dưới.</p>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">

        {{-- Major (Môn học) --}}
        <div>
            <label for="monHocId" class="block text-sm font-semibold text-slate-700 mb-2">Ngành học</label>
            <div class="relative">
                <select id="monHocId" wire:model.live="monHocId"
                    class="w-full h-10 pl-3.5 pr-10 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors">
                    <option value="0">Chọn ngành học...</option>
                    @foreach($monHocs as $m)
                    <option value="{{ $m->id }}">{{ $m->ten }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            @error('monHocId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Subject / Chế độ thi --}}
        <div>
            <label for="cheDoThi" class="block text-sm font-semibold text-slate-700 mb-2">Chế độ thi</label>
            <div class="relative">
                <select id="cheDoThi" wire:model.live="cheDoThi"
                    @disabled(!$monHocId)
                    class="w-full h-10 pl-3.5 pr-10 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">Chọn chế độ thi...</option>
                    @foreach($cheDoList as $mode)
                    <option value="{{ $mode->value }}">{{ $mode->nhanHien() }}</option>
                    @endforeach
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
        </div>

        {{-- ===== THEO ĐỘ KHÓ: Hiện cấu hình tỷ lệ dễ/TB/khó ===== --}}
        @if($cheDoThi === 'theo_do_kho')
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-4"
             x-data="{}" wire:key="difficulty-config">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Phân bố độ khó</p>
                @php $tongDK = $doKhoDe + $doKhoTrungBinh + $doKhoKho; @endphp
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $tongDK === 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                    {{ $tongDK }}% / 100%
                </span>
            </div>

            {{-- Preview bar --}}
            @php
                $gradStops = [];
                $cur = 0;
                if ($doKhoDe > 0) { $gradStops[] = "#34d399 {$cur}% " . ($cur + $doKhoDe) . '%'; $cur += $doKhoDe; }
                if ($doKhoTrungBinh > 0) { $gradStops[] = "#fbbf24 {$cur}% " . ($cur + $doKhoTrungBinh) . '%'; $cur += $doKhoTrungBinh; }
                if ($doKhoKho > 0) { $gradStops[] = "#f87171 {$cur}% " . ($cur + $doKhoKho) . '%'; $cur += $doKhoKho; }
                $gradDK = count($gradStops) ? 'linear-gradient(to right,' . implode(',', $gradStops) . ')' : 'none';
            @endphp
            <div class="rounded-lg overflow-hidden h-2 w-full bg-slate-200">
                <div class="h-full transition-all duration-300" style="width: {{ min(100, $doKhoDe + $doKhoTrungBinh + $doKhoKho) }}%; background: {{ $gradDK }};"></div>
            </div>

            {{-- Dễ --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block"></span>
                        Dễ
                    </label>
                    <div class="flex items-center gap-1.5">
                        <input type="number" wire:model.live="doKhoDe" min="0" max="100"
                            class="w-16 h-7 text-center text-sm font-semibold border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                        <span class="text-xs text-slate-400">%</span>
                    </div>
                </div>
                <input type="range" wire:model.live="doKhoDe" min="0" max="100" step="5"
                    class="w-full cursor-pointer" style="accent-color: #34d399;">
            </div>

            {{-- Trung bình --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>
                        Trung bình
                    </label>
                    <div class="flex items-center gap-1.5">
                        <input type="number" wire:model.live="doKhoTrungBinh" min="0" max="100"
                            class="w-16 h-7 text-center text-sm font-semibold border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                        <span class="text-xs text-slate-400">%</span>
                    </div>
                </div>
                <input type="range" wire:model.live="doKhoTrungBinh" min="0" max="100" step="5"
                    class="w-full cursor-pointer" style="accent-color: #fbbf24;">
            </div>

            {{-- Khó --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>
                        Khó
                    </label>
                    <div class="flex items-center gap-1.5">
                        <input type="number" wire:model.live="doKhoKho" min="0" max="100"
                            class="w-16 h-7 text-center text-sm font-semibold border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                        <span class="text-xs text-slate-400">%</span>
                    </div>
                </div>
                <input type="range" wire:model.live="doKhoKho" min="0" max="100" step="5"
                    class="w-full cursor-pointer" style="accent-color: #f87171;">
            </div>

            @if($tongDK !== 100)
            <p class="text-xs text-red-500 flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Tổng phải bằng 100%. Hiện tại: {{ $tongDK }}%
            </p>
            @endif
            @error('doKho') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        @endif

        {{-- ===== TỶ LỆ TÙY CHỈNH: Hiện cấu hình theo chương ===== --}}
        @if($cheDoThi === 'ty_le_tuy_chon')
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 space-y-4"
             wire:key="chapter-config">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Phân bố theo chương</p>
                @php $tongTL = array_sum($tyLeChuong); @endphp
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $tongTL === 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                    {{ $tongTL }}% / 100%
                </span>
            </div>

            @if($chuongs->isEmpty())
                <p class="text-sm text-slate-400 text-center py-2">Môn học này chưa có chương nào.</p>
            @else
                {{-- Preview bar theo chương (gradient) --}}
                @php
                    $colors = ['#818cf8','#34d399','#fbbf24','#f87171','#a78bfa','#60a5fa','#fb923c','#2dd4bf'];
                    $chGradStops = [];
                    $chCur = 0;
                    foreach($chuongs as $chIdx => $chItem) {
                        $chPct = $tyLeChuong[$chItem->id] ?? 0;
                        if ($chPct > 0) {
                            $chColor = $colors[$chIdx % count($colors)];
                            $chGradStops[] = "{$chColor} {$chCur}% " . ($chCur + $chPct) . '%';
                            $chCur += $chPct;
                        }
                    }
                    $chGrad = count($chGradStops) ? 'linear-gradient(to right,' . implode(',', $chGradStops) . ')' : 'none';
                    $tongTLBar = min(100, $tongTL);
                @endphp
                <div class="rounded-lg overflow-hidden h-2 w-full bg-slate-200">
                    <div class="h-full transition-all duration-300" style="width: {{ $tongTLBar }}%; background: {{ $chGrad }};"></div>
                </div>

                {{-- Danh sách chương --}}
                <div class="space-y-3">
                    @foreach($chuongs as $idx => $ch)
                    @php
                        $color = $colors[$idx % count($colors)];
                        $pct   = $tyLeChuong[$ch->id] ?? 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="flex items-center gap-2 text-sm font-medium text-slate-700 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $color }};"></span>
                                <span class="truncate">{{ $ch->ten }}</span>
                            </label>
                            <div class="flex items-center gap-1.5 flex-shrink-0 ml-2">
                                <input type="number"
                                    wire:model.live="tyLeChuong.{{ $ch->id }}"
                                    min="0" max="100"
                                    class="w-16 h-7 text-center text-sm font-semibold border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                                <span class="text-xs text-slate-400">%</span>
                            </div>
                        </div>
                        <input type="range"
                            wire:model.live="tyLeChuong.{{ $ch->id }}"
                            min="0" max="100" step="5"
                            class="w-full cursor-pointer"
                            style="accent-color: {{ $color }};">
                    </div>
                    @endforeach
                </div>

                @if($tongTL !== 100)
                <p class="text-xs text-red-500 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Tổng phải bằng 100%. Hiện tại: {{ $tongTL }}%
                </p>
                @endif
                @error('tyLeChuong') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            @endif
        </div>
        @endif

        {{-- Sub-subject (Chuyên đề) — optional --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Chuyên đề <span class="font-normal text-slate-400">— tùy chọn</span>
            </label>
            <div class="relative">
                <select
                    @disabled(!$cheDoThi)
                    class="w-full h-10 pl-3.5 pr-10 appearance-none bg-white border border-slate-200 rounded-lg text-sm text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <option value="">Tất cả chuyên đề</option>
                </select>
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
        </div>

        <hr class="border-slate-100">

        {{-- Number of questions — slider --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-semibold text-slate-700">Số câu hỏi</label>
                <span class="text-sm font-bold text-indigo-600" x-text="$wire.soCauHoi + ' câu'">{{ $soCauHoi }} câu</span>
            </div>
            <input
                type="range"
                min="10"
                max="80"
                step="5"
                wire:model.live.debounce.200ms="soCauHoi"
                class="w-full cursor-pointer"
                style="accent-color: #4f46e5;"
            >
            <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                <span>10</span><span>40</span><span>80</span>
            </div>
            @error('soCauHoi') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Time limit --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Giới hạn thời gian</label>
            <div class="grid grid-cols-4 gap-2"
                 x-data="{ selected: $wire.entangle('thoiGianPhut').live }">
                @foreach([15, 30, 45, 60] as $t)
                <button type="button"
                    x-on:click="selected = {{ $t }}"
                    :class="selected === {{ $t }}
                        ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
                        : 'border-slate-200 text-slate-600 hover:border-slate-300'"
                    class="flex items-center justify-center gap-1.5 h-10 rounded-xl border-2 text-sm font-semibold transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    {{ $t }}m
                </button>
                @endforeach
            </div>
            @error('thoiGianPhut') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Exam Summary --}}
        @if($monHocId)
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Tóm tắt bài thi</p>
            <div class="space-y-1">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Môn học</span>
                    <span class="font-medium text-slate-900">{{ $monHocs->firstWhere('id', $monHocId)?->ten ?? '—' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Chế độ</span>
                    <span class="font-medium text-slate-900">
                        @foreach($cheDoList as $mode)
                            @if($mode->value === $cheDoThi){{ $mode->nhanHien() }}@endif
                        @endforeach
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Số câu</span>
                    <span class="font-medium text-slate-900">{{ $soCauHoi }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Thời gian</span>
                    <span class="font-medium text-slate-900">{{ $thoiGianPhut }} phút</span>
                </div>
                @if($cheDoThi === 'theo_do_kho')
                <div class="flex justify-between text-sm pt-1 border-t border-slate-200 mt-1">
                    <span class="text-slate-600">Phân bố</span>
                    <span class="font-medium text-slate-900 text-xs">
                        Dễ {{ $doKhoDe }}% · TB {{ $doKhoTrungBinh }}% · Khó {{ $doKhoKho }}%
                    </span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Nút Start Exam --}}
        @php
            $canStart = $monHocId && $cheDoThi &&
                ($cheDoThi === 'ngau_nhien' ||
                ($cheDoThi === 'theo_do_kho' && ($doKhoDe + $doKhoTrungBinh + $doKhoKho) === 100) ||
                ($cheDoThi === 'ty_le_tuy_chon' && array_sum($tyLeChuong) === 100));
        @endphp
        <button type="button" wire:click="batDauThi"
            @disabled(!$canStart)
            class="w-full h-11 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl text-white text-sm font-bold transition-colors flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            <span wire:loading.remove wire:target="batDauThi">Bắt đầu thi</span>
            <span wire:loading wire:target="batDauThi">Đang chuẩn bị…</span>
        </button>
    </div>
</div>