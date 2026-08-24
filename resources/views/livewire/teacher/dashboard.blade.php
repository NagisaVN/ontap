<div class="space-y-5">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['icon'=>'📝','v'=>$tongCauHoi,'label'=>'Câu hỏi','color'=>'#6366f1','bg'=>'#eef2ff'],
            ['icon'=>'⏳','v'=>$choDuyet,'label'=>'Chờ duyệt','color'=>'#f59e0b','bg'=>'#fef3c7'],
            ['icon'=>'🤖','v'=>$aiSinh,'label'=>'AI tạo','color'=>'#10b981','bg'=>'#d1fae5'],
            ['icon'=>'👥','v'=>$tongSinhVien,'label'=>'Sinh viên','color'=>'#8b5cf6','bg'=>'#ede9fe'],
        ] as $card)
        <div class="sp-card sp-stat-card sp-stat-card-wrap">
            <div class="sp-stat-icon" style="background:{{ $card['bg'] }}">{{ $card['icon'] }}</div>
            <div>
                <div class="sp-stat-value" style="color:{{ $card['color'] }}">{{ $card['v'] }}</div>
                <div class="sp-stat-label">{{ $card['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick actions --}}
    <div class="flex gap-3 flex-wrap">
        <a href="{{ route('teacher.questions') }}?action=create" wire:navigate.hover
           class="sp-btn sp-btn-primary">➕ Thêm câu hỏi</a>
        <a href="{{ route('teacher.ocr') }}" wire:navigate
           class="sp-btn sp-btn-accent">📄 Upload PDF/Ảnh (OCR)</a>
        @if($choDuyet > 0)
        <a href="{{ route('teacher.pending') }}" wire:navigate
           class="sp-btn" style="background:#fef3c7;color:#92400e">
            ✅ Duyệt {{ $choDuyet }} câu đang chờ
        </a>
        @endif
    </div>

    {{-- Recent questions --}}
    <div class="sp-card overflow-hidden">
        <div class="px-5 py-4 border-b" style="border-color:var(--sp-border)">
            <h2 class="font-bold">Câu hỏi vừa thêm</h2>
        </div>
        <table class="sp-table">
            <thead>
                <tr>
                    <th>Nội dung</th>
                    <th>Môn / Chương</th>
                    <th>Độ khó</th>
                    <th>Trạng thái</th>
                    <th>Nguồn</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cauHoiMoi as $cq)
                <tr>
                    <td class="max-w-xs">
                        <span class="font-medium text-sm">{{ Str::limit($cq->noi_dung, 60) }}</span>
                    </td>
                    <td class="text-xs" style="color:var(--sp-text-muted)">
                        {{ $cq->chuong?->monHoc?->ten ?? '?' }} / {{ $cq->chuong?->ten ?? '?' }}
                    </td>
                    <td>
                        @php $doKhoMap = ['de'=>['🟢','green'],'trung_binh'=>['🟡','yellow'],'kho'=>['🔴','red']] @endphp
                        <span class="sp-badge sp-badge-{{ $doKhoMap[$cq->do_kho->value][1] ?? 'gray' }}">
                            {{ $doKhoMap[$cq->do_kho->value][0] ?? '' }} {{ $cq->do_kho->nhanHien() }}
                        </span>
                    </td>
                    <td>
                        @php $ts = $cq->trang_thai @endphp
                        <span class="sp-badge sp-badge-{{ $ts->mauSac() }}">{{ $ts->nhanHien() }}</span>
                    </td>
                    <td>
                        <span class="sp-badge sp-badge-gray">{{ $cq->nguon->nhanHien() }}</span>
                    </td>
                    <td>
                        <a href="{{ route('teacher.questions') }}?edit={{ $cq->id }}" wire:navigate
                           class="sp-btn sp-btn-outline text-xs py-1 px-2">Sửa</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
