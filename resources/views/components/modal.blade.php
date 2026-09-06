@props([
    'title',
    'size' => 'md',
    'name' => null,
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div
    x-data="{ show: false }"
    x-show="show"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50 flex items-center justify-center p-4']) }}
>
    <!-- Background backdrop -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-slate-900/40" 
        x-on:click="show = false"
    ></div>

    <!-- Modal panel -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full {{ $sizeClass }} bg-white rounded-2xl shadow-md overflow-hidden transform transition-all"
    >
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-900" id="modal-title">{{ $title }}</h3>
            <button
                type="button"
                x-on:click="show = false"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 transition-colors"
            >
                <!-- SVG Icon: X -->
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="px-6 py-5">
            {{ $slot }}
        </div>
    </div>
</div>
