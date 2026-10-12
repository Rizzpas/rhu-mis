@php
    $layout = match(auth()->user()->role) {
        'super_admin', 'admin' => 'layouts.admin',
        'regular_doctor', 'pedia_doctor' => 'layouts.doctor',
        'clinical_nurse', 'vitals_nurse' => 'layouts.nurse',
        'information_desk' => 'layouts.frontdesk',
        'laboratory', 'radiology' => 'layouts.lab',
        'pharmacy' => 'layouts.pharmacy',
        default => 'layouts.app'
    };

    // Classify an action into a visual category
    $classify = function (string $action) {
        $a = strtolower($action);
        return match (true) {
            str_contains($a, 'logout') => 'logout',
            str_contains($a, 'login') => 'login',
            str_starts_with($a, 'viewed') => 'viewed',
            str_contains($a, 'cancel') || str_contains($a, 'reject') || str_contains($a, 'purge') || str_contains($a, 'no-show') => 'danger',
            default => 'change',
        };
    };

    $styles = [
        'login'  => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'fg' => 'text-emerald-600 dark:text-emerald-400', 'label' => 'Sign-in',
                     'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1'],
        'logout' => ['bg' => 'bg-slate-100 dark:bg-slate-700/60', 'fg' => 'text-slate-500 dark:text-slate-300', 'label' => 'Sign-out',
                     'icon' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'],
        'viewed' => ['bg' => 'bg-sky-100 dark:bg-sky-900/40', 'fg' => 'text-sky-600 dark:text-sky-400', 'label' => 'Viewed',
                     'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
        'danger' => ['bg' => 'bg-rose-100 dark:bg-rose-900/40', 'fg' => 'text-rose-600 dark:text-rose-400', 'label' => 'Cancelled',
                     'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
        'change' => ['bg' => 'bg-amber-100 dark:bg-amber-900/40', 'fg' => 'text-amber-600 dark:text-amber-400', 'label' => 'Action',
                     'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
    ];

    // Lightweight user-agent summary
    $device = function (?string $ua) {
        if (!$ua) return 'Unknown device';
        $browser = match (true) {
            str_contains($ua, 'Edg/') => 'Edge',
            str_contains($ua, 'OPR/') => 'Opera',
            str_contains($ua, 'Chrome/') => 'Chrome',
            str_contains($ua, 'Firefox/') => 'Firefox',
            str_contains($ua, 'Safari/') => 'Safari',
            default => 'Browser',
        };
        $os = match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Mac OS') => 'macOS',
            str_contains($ua, 'Linux') => 'Linux',
            default => 'Unknown OS',
        };
        return "{$browser} on {$os}";
    };

    $grouped = $logs->getCollection()->groupBy(function ($log) {
        $d = $log->created_at;
        return $d->isToday() ? 'Today' : ($d->isYesterday() ? 'Yesterday' : $d->format('l, F j, Y'));
    });

    $ranges = ['all' => 'All time', 'today' => 'Today', '7d' => 'Last 7 days', '30d' => 'Last 30 days'];
    $categoryOptions = [
        'all' => [
            'label' => 'All activity',
            'desc' => 'All account events & interactions',
            'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
            'badge' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60',
        ],
        'auth' => [
            'label' => 'Sign-ins & sign-outs',
            'desc' => 'Session authentication & logouts',
            'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
            'badge' => 'bg-teal-100 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 border border-teal-200/60 dark:border-teal-800/60',
        ],
        'changes' => [
            'label' => 'Actions & changes',
            'desc' => 'Clinical edits, records & updates',
            'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'badge' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60',
        ],
        'viewed' => [
            'label' => 'Records viewed',
            'desc' => 'Inspections & patient lookups',
            'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
            'badge' => 'bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 border border-sky-200/60 dark:border-sky-800/60',
        ],
    ];
    $activeCategory = $categoryOptions[$category] ?? $categoryOptions['all'];
@endphp

@extends($layout)

@section('header', 'Activity Log')

@section('content')
<div class="w-full max-w-[1120px] mx-auto py-8 px-4 sm:px-6">

    {{-- Page heading --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:underline mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Account settings
            </a>
            <h2 class="text-[22px] font-bold text-slate-900 dark:text-white tracking-tight">Your activity</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Review sign-ins and actions recorded under your account. Only you and system auditors can see this.</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total activities</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</p>
            </div>
        </div>
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Today</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white">{{ number_format($stats['today']) }}</p>
            </div>
        </div>
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 p-4 flex items-center gap-3.5 shadow-xs">
            <div class="w-11 h-11 rounded-xl bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Last sign-in</p>
                @if($stats['last_login'])
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate" title="{{ $stats['last_login']->created_at->format('M j, Y g:i A') }}">
                        {{ $stats['last_login']->created_at->diffForHumans() }}
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ $device($stats['last_login']->user_agent) }} · {{ $stats['last_login']->ip_address ?? '—' }}</p>
                @else
                    <p class="text-sm font-bold text-slate-900 dark:text-white">—</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('profile.activity') }}"
          class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 p-3 mb-6 flex flex-col lg:flex-row lg:items-center gap-3 shadow-xs">
        <div class="relative flex-1">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="activity-search" name="search" value="{{ request('search') }}" placeholder="Search your activity…"
                   class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900/60 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500">
        </div>

        {{-- Keeps the current range when search/category submits; a clicked range button (later in DOM order) overrides it --}}
        <input type="hidden" name="range" value="{{ $range }}">
        <div class="flex flex-wrap items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-900/60">
            @foreach($ranges as $key => $label)
                <button type="submit" name="range" value="{{ $key }}" id="activity-range-{{ $key }}"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer {{ $range === $key ? 'bg-white dark:bg-slate-700 text-emerald-700 dark:text-emerald-300 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>


        {{-- Custom Elevated Category Dropdown --}}
        <div class="relative shrink-0" 
             x-data="{ open: false }" 
             @click.outside="open = false" 
             @keydown.escape.window="open = false">
            
            <input type="hidden" name="category" value="{{ $category }}" x-ref="categoryInput">
            
            <button type="button"
                    id="activity-category-button"
                    @click="open = !open"
                    :aria-expanded="open.toString()"
                    class="h-10 px-3.5 inline-flex items-center justify-between gap-2.5 rounded-xl border text-xs sm:text-sm font-semibold transition-all duration-150 cursor-pointer shadow-2xs select-none focus:outline-none"
                    :class="open 
                        ? 'border-emerald-500 dark:border-emerald-500 ring-2 ring-emerald-500/20 bg-white dark:bg-slate-900 text-slate-900 dark:text-white' 
                        : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60 hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-slate-300 dark:hover:border-slate-600 text-slate-800 dark:text-slate-100'">
                
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 {{ $activeCategory['badge'] }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $activeCategory['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="truncate">{{ $activeCategory['label'] }}</span>
                </div>

                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0"
                     :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 x-cloak
                 style="display: none;"
                 class="absolute right-0 top-full mt-2 w-72 sm:w-80 z-50 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/80 shadow-2xl p-1.5 backdrop-blur-md focus:outline-none">
                
                <div class="px-3 py-2 mb-1 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500">Event Categories</span>
                    @if($category !== 'all')
                        <button type="button" 
                                @click="$refs.categoryInput.value = 'all'; open = false; $el.closest('form').submit();"
                                class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                            Reset
                        </button>
                    @endif
                </div>

                <div class="space-y-1">
                    @foreach($categoryOptions as $key => $opt)
                        @php $isSelected = ($category === $key); @endphp
                        <button type="button"
                                @click="$refs.categoryInput.value = '{{ $key }}'; open = false; $el.closest('form').submit();"
                                class="w-full group flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-left transition-all duration-150 cursor-pointer select-none border {{ $isSelected ? 'bg-emerald-50 dark:bg-emerald-950/60 border-emerald-300/80 dark:border-emerald-800/80 text-emerald-900 dark:text-emerald-300 font-semibold shadow-2xs' : 'border-transparent text-slate-700 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60' }}">
                            
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $opt['badge'] }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $opt['icon'] }}"/>
                                    </svg>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs sm:text-sm font-semibold truncate {{ $isSelected ? 'text-emerald-900 dark:text-emerald-200' : 'text-slate-800 dark:text-slate-200 group-hover:text-slate-900 dark:group-hover:text-white' }}">
                                        {{ $opt['label'] }}
                                    </p>
                                    <p class="text-[10px] sm:text-[11px] text-slate-400 dark:text-slate-500 font-normal truncate">
                                        {{ $opt['desc'] }}
                                    </p>
                                </div>
                            </div>

                            @if($isSelected)
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        @if(request()->hasAny(['search', 'range', 'category']))
            <a href="{{ route('profile.activity') }}" class="text-xs font-semibold text-slate-500 hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400 px-2">Clear</a>
        @endif
    </form>

    {{-- Timeline --}}
    @if($logs->isEmpty())
        <div class="rounded-2xl bg-white dark:bg-slate-800 border border-dashed border-slate-300 dark:border-slate-600 py-16 px-6 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400 mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">No activity found</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Try a different time range or clear your filters.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($grouped as $day => $items)
                <section>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5 px-1">{{ $day }}</h3>
                    <ul class="rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 divide-y divide-slate-100 dark:divide-slate-700/70 overflow-hidden shadow-xs">
                        @foreach($items as $log)
                            @php
                                $cat = $classify($log->action ?? '');
                                $s = $styles[$cat];
                                $details = $log->changes ? app(\App\Http\Controllers\Admin\AuditLogController::class)->formatAuditChangesForCsv($log->changes) : null;
                            @endphp
                            <li x-data="{ open: false }" class="group">
                                <button type="button" @click="open = !open" @if(!$details) disabled @endif
                                        class="w-full text-left flex items-start gap-3.5 px-4 py-3.5 transition-colors {{ $details ? 'hover:bg-slate-50 dark:hover:bg-slate-700/40 cursor-pointer' : 'cursor-default' }}">
                                    <div class="w-9 h-9 rounded-full {{ $s['bg'] }} {{ $s['fg'] }} flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ ucfirst($log->action) }}</p>
                                            @if($log->model_type && $log->model_id && !in_array($cat, ['login', 'logout']))
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                                                    {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex flex-wrap items-center gap-x-1.5">
                                            <span title="{{ $log->created_at->format('M j, Y g:i:s A') }}">{{ $log->created_at->format('g:i A') }}</span>
                                            <span class="text-slate-300 dark:text-slate-600">•</span>
                                            <span>{{ $device($log->user_agent) }}</span>
                                            @if($log->ip_address)
                                                <span class="text-slate-300 dark:text-slate-600">•</span>
                                                <span class="font-mono text-[11px]">{{ $log->ip_address }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    @if($details)
                                        <svg class="w-4 h-4 text-slate-400 shrink-0 mt-2.5 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    @endif
                                </button>
                                @if($details)
                                    <div x-show="open" x-transition.opacity.duration.150ms x-cloak style="display:none" class="px-4 pb-4 pl-[66px]">
                                        <div class="text-xs leading-relaxed text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-700 rounded-xl p-3 break-words">
                                            {{ $details }}
                                        </div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>

        @if($logs->hasPages())
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
