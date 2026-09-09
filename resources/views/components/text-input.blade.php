@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'w-full h-9 px-3 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder:text-slate-400 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed']) }}
>
