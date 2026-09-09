@props([
    'placeholder' => 'Search…'
])

<div {{ $attributes->onlyProps(['class'])->merge(['class' => 'relative']) }}>
    <!-- SVG Icon: Search -->
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
    <input
        type="text"
        placeholder="{{ $placeholder }}"
        {{ $attributes->except(['class', 'placeholder']) }}
        class="w-full h-9 pl-9 pr-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors"
    />
</div>
