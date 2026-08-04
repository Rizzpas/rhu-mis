@php
    $mins = isset($createdAt) ? \Carbon\Carbon::parse($createdAt)->diffInMinutes(now()) : 0;
@endphp

@if($mins > 30)
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-700 shadow-sm animate-pulse" title="Waiting for {{ $mins }} minutes (> 30 mins delay)">
        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
        🔴 {{ $mins }}m wait (High)
    </span>
@elseif($mins >= 15)
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700 shadow-sm" title="Waiting for {{ $mins }} minutes (15-30 mins)">
        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
        🟡 {{ $mins }}m wait
    </span>
@else
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 shadow-sm" title="Waiting for {{ $mins }} minutes (< 15 mins)">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        🟢 {{ $mins }}m wait
    </span>
@endif
