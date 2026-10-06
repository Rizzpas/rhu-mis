<nav role="navigation" aria-label="Pagination Navigation" class="flex flex-wrap items-center justify-center gap-1 sm:gap-1.5 select-none">
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-400 dark:text-slate-500 bg-slate-100/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-xl cursor-not-allowed select-none opacity-60" aria-disabled="true" aria-label="Previous page">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Previous</span>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" data-page-link class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 shadow-2xs transition-all active:scale-95 cursor-pointer" aria-label="Previous page">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Previous</span>
        </a>
    @endif

    {{-- Page Numbers Window --}}
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = max(1, $paginator->lastPage());

        $pages = [];
        if ($lastPage <= 7) {
            for ($i = 1; $i <= $lastPage; $i++) {
                $pages[] = $i;
            }
        } else {
            if ($currentPage <= 4) {
                $pages = [1, 2, 3, 4, 5, '...', $lastPage];
            } elseif ($currentPage >= $lastPage - 3) {
                $pages = [1, '...', $lastPage - 4, $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
            } else {
                $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
            }
        }
    @endphp

    @foreach ($pages as $p)
        @if ($p === '...')
            <span class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 text-xs sm:text-sm font-bold text-slate-400 dark:text-slate-500 cursor-default select-none">
                ...
            </span>
        @elseif ($p == $currentPage)
            <span aria-current="page" class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 text-xs sm:text-sm font-black text-white bg-gradient-to-r from-emerald-600 to-teal-600 border border-emerald-600 dark:border-teal-500 rounded-xl shadow-xs cursor-default select-none">
                {{ $p }}
            </span>
        @else
            <a href="{{ $paginator->url($p) }}" data-page-link class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 shadow-2xs transition-all active:scale-95 cursor-pointer" aria-label="Go to page {{ $p }}">
                {{ $p }}
            </a>
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" data-page-link class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-emerald-600 dark:hover:text-emerald-400 shadow-2xs transition-all active:scale-95 cursor-pointer" aria-label="Next page">
            <span>Next</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <span class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-400 dark:text-slate-500 bg-slate-100/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-xl cursor-not-allowed select-none opacity-60" aria-disabled="true" aria-label="Next page">
            <span>Next</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </span>
    @endif
</nav>
