<!DOCTYPE html>
<html lang="vi" class="scroll-smooth" x-data="{ dark: false }" :class="dark ? 'dark' : ''">
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
<body class="antialiased font-sans text-slate-900 transition-colors duration-300" :class="dark ? 'bg-slate-900 text-slate-100' : 'bg-slate-50'">

    <div x-data="{
        collapsed: false,
        notifOpen: false,
        userMenuOpen: false
    }" class="flex h-screen overflow-hidden">
        
        {{-- Sidebar --}}
        <aside
            :class="collapsed ? 'w-16' : 'w-64'"
            class="flex flex-col border-r transition-all duration-300 shrink-0 z-20"
            :class="dark ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-200'"
        >
            {{-- Logo --}}
            <div :class="collapsed ? 'justify-center px-0' : 'px-5'" class="flex items-center h-16 border-b transition-all duration-300" :class="dark ? 'border-slate-700' : 'border-slate-100'">
                <a href="/" class="flex items-center gap-2 min-w-0" style="text-decoration:none;">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white"><path d="M21.42 10.922a2 2 0 0 1-.019 3.138l-8.5 7.14a2 2 0 0 1-2.54 0l-8.5-7.14a2 2 0 0 1-.019-3.138l9-7.4a2 2 0 0 1 2.54 0Z"/><path d="M22 10v6"/><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/></svg>
                    </div>
                    <span x-show="!collapsed" style="display: none;" class="font-bold text-lg tracking-tight whitespace-nowrap" :class="dark ? 'text-white' : 'text-slate-900'">SmartPrep</span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                
                @auth
                    {{-- STUDENT SECTION --}}
                    @can('student')
                        <div x-show="!collapsed" style="display: none;" class="px-2 mt-4 mb-2 text-xs font-bold uppercase tracking-wider" :class="dark ? 'text-slate-500' : 'text-slate-400'">Học tập</div>
                        
                        <a href="{{ route('dashboard') }}" wire:navigate :title="collapsed ? 'Dashboard' : null"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('dashboard') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></span>
                            <span x-show="!collapsed" style="display: none;" class="truncate">Dashboard</span>
                        </a>

                        <a href="{{ route('student.thi') }}" wire:navigate.hover :title="collapsed ? 'Làm bài thi' : null"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors mt-1"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('student.thi') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('student.thi') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></span>
                            <span x-show="!collapsed" style="display: none;" class="truncate">Làm bài thi</span>
                        </a>

                        <a href="{{ route('student.on-tap') }}" wire:navigate :title="collapsed ? 'Ôn điểm yếu' : null"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors mt-1"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('student.on-tap') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('student.on-tap') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></span>
                            <span x-show="!collapsed" style="display: none;" class="truncate">Ôn điểm yếu</span>
                        </a>
                    @endcan

                    {{-- TEACHER SECTION --}}
                    @can('teacher')
                        <div x-show="!collapsed" style="display: none;" class="px-2 mt-6 mb-2 text-xs font-bold uppercase tracking-wider" :class="dark ? 'text-slate-500' : 'text-slate-400'">Quản lý GV</div>
                        
                        <a href="{{ route('teacher.dashboard') }}" wire:navigate :title="collapsed ? 'Tổng quan GV' : null"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('teacher.dashboard') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('teacher.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                            <span x-show="!collapsed" style="display: none;" class="truncate">Tổng quan GV</span>
                        </a>

                        <a href="{{ route('teacher.questions') }}" wire:navigate :title="collapsed ? 'Câu hỏi' : null"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors mt-1"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('teacher.questions') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('teacher.questions') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                            <span x-show="!collapsed" style="display: none;" class="truncate">Câu hỏi</span>
                        </a>

                        <a href="{{ route('teacher.pending') }}" wire:navigate :title="collapsed ? 'Chờ duyệt' : null"
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors mt-1"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('teacher.pending') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('teacher.pending') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <div class="flex items-center gap-3">
                                <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                                <span x-show="!collapsed" style="display: none;" class="truncate">Chờ duyệt</span>
                            </div>
                            @php $choDuyet = \App\Models\Question::choDuyet()->count(); @endphp
                            @if($choDuyet > 0)
                                <span x-show="!collapsed" style="display: none;" class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-rose-500 rounded-full">{{ $choDuyet }}</span>
                            @endif
                        </a>
                    @endcan

                    {{-- ADMIN SECTION --}}
                    @can('admin')
                        <div x-show="!collapsed" style="display: none;" class="px-2 mt-6 mb-2 text-xs font-bold uppercase tracking-wider" :class="dark ? 'text-slate-500' : 'text-slate-400'">Admin</div>
                        
                        <a href="{{ route('admin.dashboard') }}" wire:navigate :title="collapsed ? 'Quản trị hệ thống' : null"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.users') ? 'bg-rose-500/10 text-rose-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.users') ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                            <span x-show="!collapsed" style="display: none;" class="truncate">Quản trị hệ thống</span>
                        </a>
                        
                        <a href="{{ route('admin.taxonomy') }}" wire:navigate :title="collapsed ? 'Cấu trúc Đào tạo' : null"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors mt-1"
                           :class="[collapsed ? 'justify-center' : '', dark ? '{{ request()->routeIs('admin.taxonomy') ? 'bg-rose-500/10 text-rose-400' : 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' }}' : '{{ request()->routeIs('admin.taxonomy') ? 'bg-rose-50 text-rose-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}']">
                            <span class="shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg></span>
                            <span x-show="!collapsed" style="display: none;" class="truncate">Cấu trúc Đào tạo</span>
                        </a>
                    @endcan
                @endauth
            </nav>



            {{-- Collapse button --}}
            <div class="p-3 border-t" :class="dark ? 'border-slate-700' : 'border-slate-100'">
                <button
                    @click="collapsed = !collapsed"
                    class="w-full flex items-center justify-center h-9 rounded-xl transition-colors"
                    :class="dark ? 'text-slate-400 hover:bg-slate-700/50 hover:text-slate-200' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600'"
                >
                    <span x-show="collapsed" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </span>
                    <span x-show="!collapsed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    </span>
                </button>
            </div>
        </aside>

        {{-- Main area --}}
        <div class="flex flex-col flex-1 min-w-0">
            
            {{-- Header --}}
            <header class="h-16 border-b flex items-center justify-between px-6 shrink-0 z-10 transition-colors duration-300" :class="dark ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-200'">
                <div class="flex items-center gap-3 text-sm">
                    {{-- Mobile menu toggle --}}
                    <button @click="collapsed = !collapsed" class="lg:hidden p-1.5 -ml-2 rounded-lg" :class="dark ? 'text-slate-400 hover:bg-slate-700' : 'text-slate-500 hover:bg-slate-100'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="font-bold text-lg" :class="dark ? 'text-white' : 'text-slate-800'">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                
                <div class="flex items-center gap-1.5 sm:gap-3">
                    {{-- Topbar slot --}}
                    {{ $topbar ?? '' }}

                    {{-- Dark mode --}}
                    <button
                        @click="dark = !dark"
                        class="w-9 h-9 flex items-center justify-center rounded-full transition-colors"
                        :class="dark ? 'text-amber-300 hover:bg-slate-700' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600'"
                    >
                        <span x-show="dark" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                        </span>
                        <span x-show="!dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                        </span>
                    </button>

                    {{-- Notifications --}}
                    <button
                        @click="notifOpen = true"
                        class="w-9 h-9 relative flex items-center justify-center rounded-full transition-colors"
                        :class="dark ? 'text-slate-300 hover:bg-slate-700' : 'text-slate-400 hover:bg-slate-100 hover:text-slate-600'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        @php $notifCount = 0; /* Add logic here */ @endphp
                        @if($notifCount > 0)
                            <span class="absolute top-1.5 right-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                        @endif
                    </button>

                    <div class="w-px h-6 mx-1" :class="dark ? 'bg-slate-700' : 'bg-slate-200'"></div>

                    {{-- Avatar --}}
                    @auth
                    <div class="relative" @click.away="userMenuOpen = false">
                        <button
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2.5 pl-1.5 pr-3 h-10 rounded-full transition-colors"
                            :class="dark ? 'hover:bg-slate-700' : 'hover:bg-slate-100'"
                        >
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                                <span class="text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                            </div>
                            <div class="hidden sm:flex flex-col items-start justify-center">
                                <span class="text-sm font-semibold leading-tight" :class="dark ? 'text-slate-200' : 'text-slate-700'">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] font-medium" :class="dark ? 'text-slate-400' : 'text-slate-500'">{{ auth()->user()->roles->first()?->name ?? 'User' }}</span>
                            </div>
                        </button>
                        
                        {{-- Dropdown Menu --}}
                        <div 
                            x-show="userMenuOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            style="display: none;"
                            class="absolute right-0 top-12 w-56 rounded-2xl shadow-xl z-50 py-1.5 border"
                            :class="dark ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-100'"
                        >
                            <div class="px-4 py-3 border-b" :class="dark ? 'border-slate-700' : 'border-slate-100'">
                                <p class="text-sm font-medium" :class="dark ? 'text-white' : 'text-slate-900'">{{ auth()->user()->name }}</p>
                                <p class="text-xs truncate mt-0.5" :class="dark ? 'text-slate-400' : 'text-slate-500'">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1.5">
                                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors" :class="dark ? 'text-slate-300 hover:bg-slate-700/50 hover:text-white' : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900'" @click="userMenuOpen = false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="dark ? 'text-slate-400' : 'text-slate-400'"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Tài khoản của tôi
                                </a>
                            </div>
                            <div class="py-1.5 border-t" :class="dark ? 'border-slate-700' : 'border-slate-100'">
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm transition-colors" :class="dark ? 'text-rose-400 hover:bg-rose-500/10' : 'text-rose-600 hover:bg-rose-50'" @click="userMenuOpen = false">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6" :class="dark ? 'bg-slate-900' : 'bg-slate-50/50'">
                {{ $slot }}
            </main>
        </div>

        {{-- Notification Drawer overlay --}}
        <div
            x-show="notifOpen"
            x-transition.opacity.duration.300ms
            style="display: none;"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"
            @click="notifOpen = false"
        ></div>

        {{-- Notification Drawer --}}
        <div
            class="fixed top-0 right-0 h-full w-80 sm:w-96 border-l shadow-2xl z-50 flex flex-col transition-transform duration-300 translate-x-full"
            :class="[notifOpen ? 'translate-x-0' : 'translate-x-full', dark ? 'bg-slate-800 border-slate-700' : 'bg-white border-slate-200']"
        >
            <div class="flex items-center justify-between px-6 h-16 border-b" :class="dark ? 'border-slate-700' : 'border-slate-100'">
                <h2 class="font-bold text-lg" :class="dark ? 'text-white' : 'text-slate-900'">Thông báo</h2>
                <button
                    @click="notifOpen = false"
                    class="w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
                    :class="dark ? 'text-slate-400 hover:bg-slate-700' : 'text-slate-400 hover:bg-slate-100'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto divide-y" :class="dark ? 'divide-slate-700/50' : 'divide-slate-100'">
                {{-- No notifications state --}}
                <div class="px-6 py-12 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" :class="dark ? 'bg-slate-700' : 'bg-slate-100'">
                        <svg class="w-8 h-8" :class="dark ? 'text-slate-500' : 'text-slate-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <p class="text-sm font-medium" :class="dark ? 'text-slate-400' : 'text-slate-500'">Bạn chưa có thông báo mới nào</p>
                </div>
            </div>
            
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
