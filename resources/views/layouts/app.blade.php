<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SmartPrep' }} — SmartPrep</title>
    <meta name="description" content="SmartPrep — Nền tảng học tập thích ứng và luyện thi thông minh với AI">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js" defer></script>

    @stack('head')
</head>
<body class="antialiased" style="background:var(--sp-bg)">

<div x-data="{ sidebarOpen: false }" class="flex">

    <!-- Sidebar Overlay (mobile) -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"
         style="display:none"></div>

    <!-- ══════════ SIDEBAR ══════════ -->
    <aside class="sp-sidebar" :class="{ 'open': sidebarOpen }">

        <!-- Logo -->
        <div class="sp-sidebar-logo">
            <div class="sp-sidebar-logo-icon">🧠</div>
            <div class="sp-sidebar-logo-text">Smart<span>Prep</span></div>
        </div>

        <!-- Navigation -->
        <nav class="sp-sidebar-nav">

            @auth
                {{-- Student nav --}}
                @can('student')
                    <span class="sp-nav-section-label">Học tập</span>

                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="sp-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('student.thi') }}" wire:navigate.hover
                       class="sp-nav-item {{ request()->routeIs('student.thi') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Làm bài thi
                    </a>

                    <a href="{{ route('student.on-tap') }}" wire:navigate
                       class="sp-nav-item {{ request()->routeIs('student.on-tap') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Ôn điểm yếu
                    </a>
                @endcan

                {{-- Teacher nav --}}
                @can('teacher')
                    <span class="sp-nav-section-label">Quản lý</span>

                    <a href="{{ route('teacher.dashboard') }}" wire:navigate
                       class="sp-nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tổng quan GV
                    </a>

                    <a href="{{ route('teacher.questions') }}" wire:navigate
                       class="sp-nav-item {{ request()->routeIs('teacher.questions') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Câu hỏi
                    </a>

                    <a href="{{ route('teacher.pending') }}" wire:navigate
                       class="sp-nav-item {{ request()->routeIs('teacher.pending') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Chờ duyệt
                        @php $choDuyet = \App\Models\Question::choDuyet()->count(); @endphp
                        @if($choDuyet > 0)
                            <span class="sp-nav-badge">{{ $choDuyet }}</span>
                        @endif
                    </a>
                @endcan

                {{-- Admin nav --}}
                @can('admin')
                    <span class="sp-nav-section-label">Admin</span>

                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                       class="sp-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Quản trị hệ thống
                    </a>
                    
                    <a href="{{ route('admin.taxonomy') }}" wire:navigate
                       class="sp-nav-item {{ request()->routeIs('admin.taxonomy') ? 'active' : '' }}">
                        <svg class="sp-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                        Cấu trúc Đào tạo
                    </a>
                @endcan
            @endauth

        </nav>

        <!-- User Footer -->
        <div class="sp-sidebar-footer">
            @auth
            <div x-data="{ open: false }" class="relative">
                <div class="sp-sidebar-user" @click="open = !open">
                    <div class="sp-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    <div class="sp-user-info">
                        <div class="sp-user-name">{{ auth()->user()->name }}</div>
                        <div class="sp-user-role">{{ auth()->user()->roles->first()?->name ?? 'User' }}</div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{'rotate-180': open}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div x-show="open" x-transition @click.outside="open = false"
                     class="absolute bottom-full left-0 right-0 mb-2 rounded-lg border border-slate-700 bg-slate-800 shadow-xl overflow-hidden"
                     style="display:none">
                    <a href="{{ route('profile') }}" wire:navigate
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Hồ sơ
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-400 hover:bg-slate-700 hover:text-red-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </aside>

    <!-- ══════════ MAIN CONTENT ══════════ -->
    <div class="sp-main flex-1">

        <!-- Topbar -->
        <header class="sp-topbar">
            <!-- Mobile menu toggle -->
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-1.5 rounded-md text-slate-500 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <h1 class="sp-topbar-title">{{ $title ?? 'Dashboard' }}</h1>

            <!-- Topbar actions slot -->
            {{ $topbar ?? '' }}

            <!-- Notifications bell (placeholder) -->
            <button class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </button>
        </header>

        <!-- Page Content -->
        <main class="sp-content">
            {{ $slot }}
        </main>

    </div><!-- /.sp-main -->

</div><!-- /flex -->

@livewireScripts
@stack('scripts')
</body>
</html>
