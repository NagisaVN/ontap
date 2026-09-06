<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SmartPrep') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased text-slate-900 bg-slate-50">
    <div class="min-h-full flex font-sans">
        <!-- Left decorative panel -->
        <div class="hidden lg:flex w-[480px] shrink-0 bg-indigo-600 flex-col p-12 relative overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-16 left-16 w-48 h-48 rounded-full bg-white"></div>
                <div class="absolute bottom-32 right-8 w-64 h-64 rounded-full bg-white"></div>
                <div class="absolute top-1/2 left-1/3 w-32 h-32 rounded-full bg-white"></div>
            </div>

            <!-- Logo -->
            <div class="flex items-center gap-3 relative">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span class="font-bold text-white text-2xl tracking-tight">SmartPrep</span>
            </div>

            <!-- Tagline -->
            <div class="relative mt-auto">
                <h2 class="text-3xl font-bold text-white leading-tight mb-4">
                    Chinh phục mọi kỳ thi<br />với sự tự tin
                </h2>
                <p class="text-indigo-200 text-sm leading-relaxed mb-10">
                    SmartPrep kết hợp ôn tập ngắt quãng, đề thi thích ứng và phân tích chuyên sâu để giúp học sinh Việt Nam nắm vững môn học.
                </p>

                <!-- Feature pills -->
                <div class="flex flex-col gap-3">
                    @php
                        $features = [
                            ['icon' => 'book-open', 'label' => '10.000+ câu hỏi thi tuyển chọn'],
                            ['icon' => 'award', 'label' => 'Flashcard ôn tập ngắt quãng'],
                            ['icon' => 'trending-up', 'label' => 'Phân tích hiệu suất cá nhân'],
                        ];
                    @endphp

                    @foreach ($features as $feature)
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center text-white shrink-0">
                                @if($feature['icon'] === 'book-open')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                    </svg>
                                @elseif($feature['icon'] === 'award')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                @elseif($feature['icon'] === 'trending-up')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="block">
                                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                                        <polyline points="16 7 22 7 22 13"/>
                                    </svg>
                                @endif
                            </div>
                            <span class="text-sm text-indigo-100 leading-tight">{{ $feature['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right auth form -->
        <div class="flex-1 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <!-- Mobile logo -->
                <div class="flex items-center gap-2 mb-8 lg:hidden">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <span class="font-bold text-slate-900 text-xl tracking-tight">SmartPrep</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
