@php
$intensityClass = function($v) {
  if ($v === 0) return "bg-slate-100";
  if ($v === 1) return "bg-indigo-100";
  if ($v === 2) return "bg-indigo-300";
  if ($v === 3) return "bg-indigo-500";
  return "bg-indigo-700";
};
@endphp
<div class="p-6 max-w-5xl mx-auto space-y-6">
  <!-- Greeting banner -->
  <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-2xl p-6 flex items-center justify-between text-white shadow-sm">
    <div>
      <p class="text-indigo-200 text-sm font-medium">
        {{ now()->format('l, j F Y') }}
      </p>
      <h1 class="text-2xl font-bold mt-0.5">
        {{ now()->hour < 12 ? 'Chào buổi sáng' : (now()->hour < 18 ? 'Chào buổi chiều' : 'Chào buổi tối') }}, {{ auth()->user()->name }}! 👋
      </h1>
      <p class="text-indigo-100 text-sm mt-1">Hôm nay bạn có kế hoạch ôn tập chưa? Hãy duy trì chuỗi ngày học!</p>
    </div>
    <a
      href="/student/exam/setup" wire:navigate
      class="hidden sm:flex items-center gap-2 bg-white/20 hover:bg-white/30 transition-colors rounded-xl px-4 h-10 text-sm font-semibold text-white shrink-0"
    >
      Bắt đầu thi 
      <!-- Icon: ArrowRight -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
    </a>
  </div>

  <!-- KPI cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <x-kpi-card label="Tổng bài đã thi" value="47" delta="5 bài tuần này" :deltaPositive="true" color="indigo">
        <x-slot:icon>
            <!-- Icon: BookOpen -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        </x-slot:icon>
    </x-kpi-card>
    <x-kpi-card label="Điểm trung bình" value="78.4%" delta="+2.3% so tuần trước" :deltaPositive="true" color="blue">
        <x-slot:icon>
            <!-- Icon: Target -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
        </x-slot:icon>
    </x-kpi-card>
    <x-kpi-card label="Tỷ lệ chính xác" value="81%" delta="+1.2% so tuần trước" :deltaPositive="true" color="emerald">
        <x-slot:icon>
            <!-- Icon: Zap -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </x-slot:icon>
    </x-kpi-card>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Continue learning -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
      <div class="flex items-center justify-between px-5 pt-5 pb-3 border-b border-slate-100">
        <h2 class="font-semibold text-slate-900">Tiếp tục ôn tập</h2>
        <a href="{{ route('student.history') }}" wire:navigate class="text-xs font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
          Xem tất cả
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
      </div>
      <div class="divide-y divide-slate-100">
        @forelse($continueItems as $item)
          <div class="px-5 py-4 flex items-center gap-4 hover:bg-slate-50/50 transition-colors">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-900 truncate">{{ $item['topic'] }}</p>
              <p class="text-xs text-slate-500">{{ $item['subject'] }}
                @if($item['started_at'])
                  · {{ $item['started_at'] }}
                @endif
              </p>
              <div class="mt-2 flex items-center gap-2">
                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                  <div class="h-full bg-indigo-500 rounded-full transition-all" style="width: {{ $item['progress'] }}%"></div>
                </div>
                <span class="text-xs text-slate-500 shrink-0">{{ $item['questions'] }}/{{ $item['total'] }}</span>
              </div>
            </div>
            <a href="{{ route('exam.room', $item['bai_thi_id']) }}" wire:navigate
               class="shrink-0 h-8 px-3 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-xs font-semibold text-indigo-700 transition-colors flex items-center gap-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
              Tiếp tục
            </a>
          </div>
        @empty
          <div class="px-5 py-12 flex flex-col items-center justify-center text-center">
            <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-3">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <p class="text-sm font-medium text-slate-500">Chưa có bài thi nào đang dở</p>
            <p class="text-xs text-slate-400 mt-1 mb-4">Bắt đầu một bài thi mới để luyện tập</p>
            <a href="{{ route('student.thi') }}" wire:navigate
               class="inline-flex items-center gap-2 h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Tạo bài thi mới
            </a>
          </div>
        @endforelse
      </div>
    </div>

    <!-- Streak heatmap -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-slate-900">Chuỗi ngày học</h2>
        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">🔥 14 ngày</span>
      </div>

      <!-- Day labels -->
      <div class="grid grid-cols-7 gap-1 mb-1">
        @foreach(['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'] as $d)
          <div class="text-center text-[10px] text-slate-400 font-medium">{{ substr($d, 0, 1) }}</div>
        @endforeach
      </div>

      <!-- Grid -->
      <div class="space-y-1">
        @if(isset($streakData))
          @foreach($streakData as $wi => $week)
            <div class="grid grid-cols-7 gap-1">
              @foreach($week as $di => $val)
                <div
                  title="W{{ $wi + 1 }} Day {{ $di + 1 }}: {{ $val * 10 }} mins"
                  class="h-6 rounded-sm {{ $intensityClass($val) }} transition-colors"
                ></div>
              @endforeach
            </div>
          @endforeach
        @endif
      </div>

      <div class="flex items-center justify-end gap-1.5 mt-3">
        <span class="text-[10px] text-slate-400">Ít</span>
        @foreach([0, 1, 2, 3, 4] as $v)
          <div class="w-3 h-3 rounded-sm {{ $intensityClass($v) }}"></div>
        @endforeach
        <span class="text-[10px] text-slate-400">Nhiều</span>
      </div>
    </div>
  </div>
</div>
