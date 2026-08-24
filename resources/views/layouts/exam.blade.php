<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Phòng thi' }} — SmartPrep</title>
    <meta name="description" content="Phòng thi SmartPrep — Tập trung và làm bài tốt nhé!">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Exam-specific overrides */
        body { background: #f8fafc; }
        .exam-header {
            background: var(--sp-sidebar-bg);
            color: white;
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 2px 12px rgba(0,0,0,0.2);
        }
        .exam-logo { font-weight: 800; font-size: 1.1rem; color: #a5b4fc; }
        .exam-title { flex: 1; font-size: 0.9rem; font-weight: 600; color: #e2e8f0; }
        .exam-layout { display: grid; grid-template-columns: 1fr 280px; gap: 1.25rem;
                        max-width: 1200px; margin: 1.25rem auto; padding: 0 1.25rem; }
        @media (max-width: 900px) { .exam-layout { grid-template-columns: 1fr; } }
    </style>

    @stack('head')
</head>
<body class="antialiased exam-room-body">

    {{ $slot }}

@stack('scripts')
</body>
</html>
