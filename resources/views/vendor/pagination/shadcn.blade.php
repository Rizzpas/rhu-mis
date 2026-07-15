@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between w-full">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700">
                    {!! __('Previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
                    {!! __('Previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:text-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
                    {!! __('Next') !!}
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-md dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700">
                    {!! __('Next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div class="flex items-center gap-4">
                <ul class="flex items-center gap-1">
                    {{-- First Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li>
                            <span class="inline-flex items-center justify-center h-9 px-3 text-sm font-medium text-gray-400 bg-transparent rounded-md cursor-not-allowed dark:text-gray-500">
                                First
                            </span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->url(1) }}" class="inline-flex items-center justify-center h-9 px-3 text-sm font-medium text-gray-700 transition-colors rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800" aria-label="{{ __('pagination.first') }}">
                                First
                            </a>
                        </li>
                    @endif

                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li>
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-400 bg-transparent rounded-md cursor-not-allowed dark:text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-700 transition-colors rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800" aria-label="{{ __('pagination.previous') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </a>
                        </li>
                    @endif

                    {{-- Custom 5-Page Window --}}
                    @php
                        $start = $paginator->currentPage() - 2;
                        $end = $paginator->currentPage() + 2;

                        if ($start < 1) {
                            $end += 1 - $start;
                            $start = 1;
                        }
                        if ($end > $paginator->lastPage()) {
                            $start -= $end - $paginator->lastPage();
                            $end = $paginator->lastPage();
                        }
                        if ($start < 1) $start = 1;
                    @endphp

                    @if ($start > 1)
                        <li>
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-400 bg-transparent rounded-md cursor-default dark:text-gray-500">...</span>
                        </li>
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-teal-600 rounded-md shadow-sm dark:bg-teal-500 cursor-default">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $paginator->url($page) }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-700 transition-colors rounded-md hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endfor

                    @if ($end < $paginator->lastPage())
                        <li>
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-400 bg-transparent rounded-md cursor-default dark:text-gray-500">...</span>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-700 transition-colors rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800" aria-label="{{ __('pagination.next') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </li>
                    @else
                        <li>
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-gray-400 bg-transparent rounded-md cursor-not-allowed dark:text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </span>
                        </li>
                    @endif

                    {{-- Last Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="inline-flex items-center justify-center h-9 px-3 text-sm font-medium text-gray-700 transition-colors rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800" aria-label="{{ __('pagination.last') }}">
                                Last
                            </a>
                        </li>
                    @else
                        <li>
                            <span class="inline-flex items-center justify-center h-9 px-3 text-sm font-medium text-gray-400 bg-transparent rounded-md cursor-not-allowed dark:text-gray-500">
                                Last
                            </span>
                        </li>
                    @endif
                </ul>

                {{-- Jump to Input --}}
                @if ($paginator->lastPage() > 1)
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-1 border-l border-gray-200 dark:border-gray-700 pl-4 ml-2 m-0" style="margin: 0;">
                    @foreach(request()->except($paginator->getPageName()) as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jump:</span>
                    <input type="number" name="{{ $paginator->getPageName() }}" min="1" max="{{ $paginator->lastPage() }}" required
                           class="w-14 h-8 px-1 text-sm text-center border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white placeholder-gray-400"
                           placeholder="#">
                    <button type="submit" class="h-8 px-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 rounded-md shadow-sm transition-colors">
                        Go
                    </button>
                </form>
                @endif
            </div>
        </div>
    </nav>
@endif
