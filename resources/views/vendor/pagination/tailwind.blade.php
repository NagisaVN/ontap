@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center gap-1">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="w-8 h-8 flex items-center justify-center text-sm text-slate-400">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-xl text-sm font-bold text-white bg-indigo-600 shadow-sm">{{ $page }}</span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                                class="w-8 h-8 flex items-center justify-center rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        @else
            <span class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-300 cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        @endif

    </nav>
@endif
