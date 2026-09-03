<div class="space-y-5">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach([
            ['icon'=>'👥','v'=>$stats['tong_user'],'label'=>'Tổng users','color'=>'#6366f1','bg'=>'#eef2ff'],
            ['icon'=>'👨‍🏫','v'=>$stats['teacher'],'label'=>'Giáo viên','color'=>'#10b981','bg'=>'#d1fae5'],
            ['icon'=>'👨‍🎓','v'=>$stats['student'],'label'=>'Sinh viên','color'=>'#8b5cf6','bg'=>'#ede9fe'],
            ['icon'=>'📝','v'=>$stats['cau_hoi'],'label'=>'Câu hỏi hoạt động','color'=>'#f59e0b','bg'=>'#fef3c7'],
            ['icon'=>'⏳','v'=>$stats['cho_duyet'],'label'=>'Chờ duyệt','color'=>'#ef4444','bg'=>'#fee2e2'],
            ['icon'=>'🏆','v'=>$stats['luot_thi'],'label'=>'Lượt thi hoàn thành','color'=>'#06b6d4','bg'=>'#cffafe'],
        ] as $card)
        <div class="sp-card sp-stat-card">
            <div class="sp-stat-icon" style="background:{{ $card['bg'] }}">{{ $card['icon'] }}</div>
            <div>
                <div class="sp-stat-value" style="color:{{ $card['color'] }}">{{ $card['v'] }}</div>
                <div class="sp-stat-label">{{ $card['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-lg text-sm font-medium" style="background:#d1fae5;color:#065f46">
        ✅ {{ session('success') }}
    </div>
    @endif

    {{-- Quick Actions Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Card: Quản lý người dùng --}}
        <a href="{{ route('admin.users') }}"
           class="sp-card flex items-center gap-4 px-5 py-4 hover:border-indigo-300 transition-colors group"
           style="border: 1.5px solid var(--sp-border); text-decoration: none;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl"
                 style="background:#eef2ff;">
                👥
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm" style="color:#1e293b;">Quản lý người dùng</div>
                <div class="text-xs mt-0.5" style="color:var(--sp-text-muted)">
                    Sửa thông tin, đổi role, khóa/mở tài khoản
                </div>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 opacity-40 group-hover:opacity-100 group-hover:translate-x-1 transition-all"
                 style="color:#6366f1;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Card: Cấu trúc đào tạo --}}
        <a href="{{ route('admin.taxonomy') }}"
           class="sp-card flex items-center gap-4 px-5 py-4 hover:border-violet-300 transition-colors group"
           style="border: 1.5px solid var(--sp-border); text-decoration: none;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 text-2xl"
                 style="background:#ede9fe;">
                🏗️
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm" style="color:#1e293b;">Cấu trúc đào tạo</div>
                <div class="text-xs mt-0.5" style="color:var(--sp-text-muted)">
                    Quản lý ngành, môn học, chuyên đề
                </div>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 opacity-40 group-hover:opacity-100 group-hover:translate-x-1 transition-all"
                 style="color:#8b5cf6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

    </div>

</div>
