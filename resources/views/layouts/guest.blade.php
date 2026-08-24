<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SmartPrep') }} — Ôn thi thông minh</title>
    <meta name="description" content="Nền tảng ôn thi AI-powered với Spaced Repetition và phân tích năng lực cá nhân hoá.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }

        /* Gradient mesh background */
        .hero-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 40%, #2563eb 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(139,92,246,0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(59,130,246,0.4) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(99,102,241,0.2) 0%, transparent 70%);
        }
        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 300px; height: 300px; background: #818cf8; top: -80px; left: -80px; animation-delay: 0s; }
        .orb-2 { width: 250px; height: 250px; background: #a78bfa; bottom: -60px; right: -60px; animation-delay: 3s; }
        .orb-3 { width: 180px; height: 180px; background: #60a5fa; top: 40%; left: 60%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }

        /* Glassmorphism card */
        .glass-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        /* Score ring animation */
        .score-ring {
            animation: ring-fill 2s ease-out 0.5s both;
        }
        @keyframes ring-fill {
            from { stroke-dasharray: 0 100; }
            to   { stroke-dasharray: 95 100; }
        }

        /* Feature items */
        .feature-item {
            animation: slide-up 0.6s ease-out both;
        }
        .feature-item:nth-child(1) { animation-delay: 0.1s; }
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slide-up {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Input focus */
        .sp-input-auth {
            transition: all 0.2s;
        }
        .sp-input-auth:focus {
            box-shadow: 0 0 0 4px rgba(99,102,241,0.1);
            border-color: #6366f1;
            outline: none;
        }

        /* Pulse dot */
        .live-dot {
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
    </style>
</head>

<body class="h-full bg-slate-50 antialiased">
<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- ══════════════════════════════════════════════════════
         LEFT SIDE — Hero / Landing Panel (hidden on mobile)
    ══════════════════════════════════════════════════════ --}}
    <div class="hidden lg:flex lg:w-1/2 hero-bg relative flex-col justify-between p-12 xl:p-16">
        <!-- Orbs -->
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Top: Logo -->
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl font-bold shadow-lg"
                     style="background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3)">
                    🧠
                </div>
                <span class="text-white text-2xl font-bold tracking-tight">SmartPrep <span class="text-indigo-200 font-light">AI</span></span>
            </div>
        </div>

        <!-- Middle: Headlines + Features -->
        <div class="relative z-10 space-y-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold mb-5"
                     style="background:rgba(255,255,255,0.15);color:#e0e7ff;border:1px solid rgba(255,255,255,0.2)">
                    <span class="live-dot w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
                    Powered by Google Gemini AI
                </div>

                <h1 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight">
                    Ôn thi thông minh.<br>
                    <span class="text-indigo-200">Xóa hổng kiến thức.</span>
                </h1>
                <p class="mt-4 text-indigo-200 text-lg leading-relaxed max-w-md">
                    Nền tảng học thích nghi đầu tiên tại Việt Nam kết hợp AI phân tích điểm yếu
                    và tự động tạo câu hỏi theo lỗ hổng của bạn.
                </p>
            </div>

            <!-- Feature list -->
            <div class="space-y-4">
                @foreach([
                    ['🎯', 'Phân tích năng lực cá nhân', 'Radar Chart trực quan theo từng chương'],
                    ['🧠', 'AI tạo câu hỏi bù lỗ hổng', 'Adaptive Learning tự động điều chỉnh độ khó'],
                    ['⚡', 'Spaced Repetition thông minh', 'Ôn tập đúng lúc, nhớ lâu hơn 3x'],
                ] as $f)
                <div class="feature-item flex items-start gap-4">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl flex-shrink-0"
                         style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2)">
                        {{ $f[0] }}
                    </div>
                    <div>
                        <p class="text-white font-semibold text-sm leading-snug">{{ $f[1] }}</p>
                        <p class="text-indigo-200 text-xs mt-0.5">{{ $f[2] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Bottom: Glassmorphism Score Card -->
        <div class="relative z-10">
            <div class="glass-card rounded-3xl p-5 max-w-xs">
                <div class="flex items-center gap-4">
                    <!-- Score ring -->
                    <div class="relative w-16 h-16 flex-shrink-0">
                        <svg viewBox="0 0 36 36" class="w-16 h-16 -rotate-90">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#34d399" stroke-width="3"
                                    stroke-dasharray="95 100" stroke-linecap="round" class="score-ring"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-white font-bold text-sm">9.5</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-base">Excellent! 🏆</p>
                        <p class="text-indigo-200 text-xs mt-0.5">Bài thi Cơ sở dữ liệu</p>
                        <div class="flex items-center gap-1.5 mt-2">
                            @foreach(['DBMS', 'SQL', 'NoSQL'] as $tag)
                            <span class="px-2 py-0.5 rounded-md text-xs font-medium"
                                  style="background:rgba(52,211,153,0.2);color:#6ee7b7">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t" style="border-color:rgba(255,255,255,0.1)">
                    <div class="flex justify-between text-xs text-indigo-200 mb-2">
                        <span>Tiến độ môn học</span>
                        <span class="text-white font-semibold">95%</span>
                    </div>
                    <div class="h-1.5 rounded-full" style="background:rgba(255,255,255,0.15)">
                        <div class="h-1.5 rounded-full bg-emerald-400 transition-all duration-1000" style="width:95%"></div>
                    </div>
                    <p class="text-xs text-indigo-200 mt-2">
                        📈 Tăng <span class="text-white font-semibold">+32%</span> sau 2 tuần ôn tập
                    </p>
                </div>
            </div>

            <!-- Social proof -->
            <div class="mt-5 flex items-center gap-3">
                <div class="flex -space-x-2">
                    @foreach(['NG','TM','PH','LK'] as $i => $init)
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white/20 text-white"
                         style="background: {{ ['#6366f1','#8b5cf6','#06b6d4','#10b981'][$i] }}">{{ $init }}</div>
                    @endforeach
                </div>
                <p class="text-indigo-200 text-xs">
                    <span class="text-white font-semibold">1,200+</span> sinh viên đang dùng
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         RIGHT SIDE — Auth Form
    ══════════════════════════════════════════════════════ --}}
    <div class="flex-1 lg:w-1/2 flex flex-col">

        <!-- Mobile: top bar -->
        <div class="lg:hidden flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="text-xl">🧠</span>
                <span class="font-bold text-slate-900 text-lg">SmartPrep <span class="text-indigo-600 font-light">AI</span></span>
            </div>
        </div>

        <!-- Form area -->
        <div class="flex-1 flex items-center justify-center px-6 py-10 sm:px-12">
            <div class="w-full max-w-md">

                {{ $slot }}

            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-5 text-center text-xs text-slate-400">
            Bằng cách đăng nhập, bạn đồng ý với
            <a href="#" class="text-indigo-500 hover:text-indigo-700 transition-colors">Điều khoản dịch vụ</a>
            và
            <a href="#" class="text-indigo-500 hover:text-indigo-700 transition-colors">Chính sách bảo mật</a>.
        </div>
    </div>

</div>

@livewireScripts
</body>
</html>
