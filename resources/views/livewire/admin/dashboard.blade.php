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

    {{-- User table --}}
    <div class="sp-card overflow-hidden">
        <div class="px-5 py-4 border-b font-bold" style="border-color:var(--sp-border)">👥 Quản lý người dùng</div>
        <table class="sp-table">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th>Role hiện tại</th>
                    <th>Đổi role</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr wire:key="user-{{ $user->id }}">
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="sp-avatar" style="width:32px;height:32px;font-size:0.75rem">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span class="font-medium text-sm">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-sm" style="color:var(--sp-text-muted)">{{ $user->email }}</td>
                    <td>
                        @php $role = $user->roles->first()?->name ?? 'none'; @endphp
                        <span class="sp-badge {{ $role === 'super_admin' ? 'sp-badge-red' : ($role === 'teacher' ? 'sp-badge-indigo' : 'sp-badge-gray') }}">
                            {{ $role }}
                        </span>
                    </td>
                    <td>
                        @if($user->id !== auth()->id())
                        <select wire:change="assignRole({{ $user->id }}, $event.target.value)"
                                class="sp-input sp-select text-xs" style="width:auto;padding:0.25rem 0.5rem">
                            <option value="">-- Đổi role --</option>
                            <option value="student">student</option>
                            <option value="teacher">teacher</option>
                            <option value="super_admin">super_admin</option>
                        </select>
                        @else
                            <span class="text-xs" style="color:var(--sp-text-muted)">Admin hiện tại</span>
                        @endif
                    </td>
                    <td class="text-xs" style="color:var(--sp-text-muted)">
                        {{ $user->created_at?->format('d/m/Y') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
