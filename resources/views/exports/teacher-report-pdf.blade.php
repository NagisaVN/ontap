<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 22px; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #0f172a; font-size: 10px; }
        h1 { font-size: 19px; margin: 0 0 4px; }
        h2 { font-size: 12px; margin: 0; }
        .muted { color: #64748b; margin: 0; }
        .summary { width: 100%; margin: 18px 0; border-collapse: separate; border-spacing: 8px 0; }
        .summary td { width: 25%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; }
        .summary .value { font-size: 17px; font-weight: bold; color: #4f46e5; margin-top: 4px; }
        .section { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 9px; }
        th { background: #f8fafc; color: #64748b; text-align: left; text-transform: uppercase; font-size: 8px; letter-spacing: .04em; }
        th, td { border: 1px solid #e2e8f0; padding: 7px; vertical-align: top; }
        .right { text-align: right; }
        .high { background: #fff1f2; color: #be123c; }
        .badge { border-radius: 20px; padding: 2px 6px; display: inline-block; }
        .easy { background: #ecfdf5; color: #047857; }
        .medium { background: #fffbeb; color: #b45309; }
        .hard { background: #fff1f2; color: #be123c; }
        .footer { color: #94a3b8; font-size: 8px; margin-top: 18px; }
    </style>
</head>
<body>
    <h1>{{ $report['title'] }}</h1>
    <p class="muted">{{ $report['subjectName'] }} · {{ $report['periodLabel'] }}</p>
    <p class="muted">{{ $report['cohortLabel'] }}</p>

    <table class="summary">
        <tr>
            <td>Học viên<div class="value">{{ $report['summary']['students'] }}</div></td>
            <td>Lượt thi<div class="value">{{ $report['summary']['attempts'] }}</div></td>
            <td>Điểm trung bình<div class="value">{{ $report['summary']['averageScore'] }}%</div></td>
            <td>Tỷ lệ đạt<div class="value">{{ $report['summary']['passRate'] }}%</div></td>
        </tr>
    </table>

    <div class="section">
        <h2>Danh sách học viên</h2>
        <table>
            <thead><tr><th>Hạng</th><th>Học viên</th><th>Điểm TB</th><th>Số bài</th><th>Trạng thái</th></tr></thead>
            <tbody>
                @forelse ($report['roster'] as $student)
                    <tr>
                        <td>#{{ $student['rank'] }}</td><td>{{ $student['name'] }}</td><td>{{ $student['score'] }}%</td><td>{{ $student['exams'] }}</td>
                        <td>{{ $student['status'] === 'active' ? 'Đang hoạt động' : 'Không hoạt động' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Chưa có lượt thi hoàn thành phù hợp bộ lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Phân bố điểm</h2>
        <table>
            <thead><tr><th>Khoảng điểm</th><th>Số lượt thi</th><th>Tỷ lệ</th></tr></thead>
            <tbody>@foreach ($report['scoreDistribution'] as $bucket)<tr><td>{{ $bucket['label'] }}</td><td>{{ $bucket['count'] }}</td><td>{{ $bucket['percentage'] }}%</td></tr>@endforeach</tbody>
        </table>
    </div>

    <div class="section">
        <h2>Phân tích độ khó câu hỏi</h2>
        <table>
            <thead><tr><th>Câu hỏi</th><th>Chủ đề</th><th>Độ khó</th><th>Lượt trả lời</th><th>Tỷ lệ sai</th><th>Luyện tập</th></tr></thead>
            <tbody>
                @forelse ($report['difficultyAnalysis'] as $item)
                    <tr class="{{ $item['failRate'] > $report['filters']['fail_threshold'] ? 'high' : '' }}">
                        <td>{{ $item['question'] }}</td><td>{{ $item['topic'] }}</td>
                        <td><span class="badge {{ $item['difficulty'] === 'Khó' ? 'hard' : ($item['difficulty'] === 'Trung bình' ? 'medium' : 'easy') }}">{{ $item['difficulty'] }}</span></td>
                        <td>{{ $item['responses'] }}</td><td>{{ $item['failRate'] }}%</td><td>{{ $item['practiceFailRate'] === null ? '—' : $item['practiceFailRate'].'%' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">Chưa có câu trả lời phù hợp bộ lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="footer">Xuất lúc {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
