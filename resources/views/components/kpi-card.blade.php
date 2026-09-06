@props([
    'label',
    'value',
    'delta' => null,
    'deltaPositive' => true,
    'color' => 'indigo',
    'icon' => null,
])

@php
    $colors = [
        'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'text-indigo-600', 'border' => 'border-indigo-100'],
        'emerald' => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-600', 'border' => 'border-emerald-100'],
        'blue' => ['bg' => 'bg-blue-50', 'icon' => 'text-blue-600', 'border' => 'border-blue-100'],
        'amber' => ['bg' => 'bg-amber-50', 'icon' => 'text-amber-600', 'border' => 'border-amber-100'],
        'rose' => ['bg' => 'bg-rose-50', 'icon' => 'text-rose-600', 'border' => 'border-rose-100'],
    ];

    $c = $colors[$color] ?? $colors['indigo'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex items-start gap-4']) }}>
    <div class="w-11 h-11 rounded-xl {{ $c['bg'] }} border {{ $c['border'] }} flex items-center justify-center shrink-0 {{ $c['icon'] }} leading-[0]">
        {{ $icon ?? $slot }}
    </div>
    <div class="min-w-0">
        <p class="text-sm text-slate-500 font-medium truncate">{{ $label }}</p>
        <p class="text-2xl font-bold text-slate-900 mt-0.5 leading-none">{{ $value }}</p>
        @if($delta)
            <p class="text-xs font-medium mt-1.5 {{ $deltaPositive ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $deltaPositive ? '↑' : '↓' }} {{ $delta }}
            </p>
        @endif
    </div>
</div>
