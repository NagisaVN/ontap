@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">

    {{-- Previous arrow --}}
    @if ($paginator->onFirstPage())
        <span class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed select-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </span>
    @else
        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev"
           class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="w-8 h-8 inline-flex items-center justify-center text-sm text-slate-400 select-none">...</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-sm font-semibold text-white bg-indigo-600 shadow-sm select-none">{{ $page }}</span>
                @else
                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                       class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors">
                        {{ $page }}
                    </button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next arrow --}}
    @if ($paginator->hasMorePages())
        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next"
           class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    @else
        <span class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed select-none">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
    @endif

</nav>
@endif