@props([
    'items' => [],
    'homeUrl' => route('welcome'),
    'homeLabel' => 'Home'
])

<!-- Breadcrumb Navigation -->
<nav class="flex items-center justify-between gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 mb-6 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
    <div class="flex items-center gap-2 flex-wrap">
        {{-- Home Link with SVG Icon matching Admin/System Style --}}
        <a href="{{ $homeUrl }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>{{ $homeLabel }}</span>
        </a>

        {{-- Dynamic Items --}}
        @foreach($items as $label => $url)
            <span class="text-slate-300 dark:text-slate-700">/</span>
            @if($loop->last || empty($url))
                <span class="text-slate-700 dark:text-slate-300 font-semibold truncate">{{ is_numeric($label) ? $url : $label }}</span>
            @else
                <a href="{{ $url }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors shrink-0">
                    {{ is_numeric($label) ? $url : $label }}
                </a>
            @endif
        @endforeach
    </div>

    {{-- Optional Right Side Action or Slot --}}
    @if(isset($right))
        <div class="shrink-0 flex items-center">
            {{ $right }}
        </div>
    @endif
</nav>
