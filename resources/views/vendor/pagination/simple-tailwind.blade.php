@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed select-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Trước
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                Trước
            </a>
        @endif

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                Tiếp
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-slate-400 bg-slate-100 rounded-lg cursor-not-allowed select-none">
                Tiếp
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        @endif

    </nav>
@endif
