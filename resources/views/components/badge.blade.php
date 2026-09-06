@props([
    'variant' => 'neutral'
])

@php
    $variants = [
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'error' => 'bg-rose-50 text-rose-700 border-rose-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'info' => 'bg-blue-50 text-blue-700 border-blue-200',
        'neutral' => 'bg-slate-100 text-slate-600 border-slate-200',
        'primary' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    ];

    $variantClass = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border ' . $variantClass]) }}>
    {{ $slot }}
</span>
