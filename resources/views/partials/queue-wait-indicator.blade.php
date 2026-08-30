@php
    $totalSeconds = isset($createdAt) ? (int) \Carbon\Carbon::parse($createdAt)->diffInSeconds(now()) : 0;
    $totalMins = (int) floor($totalSeconds / 60);
    $hours = (int) floor($totalMins / 60);
    $mins = $totalMins % 60;
    $secs = $totalSeconds % 60;

    if ($hours >= 1) {
        $waitLabel = $mins > 0 ? "{$hours}h {$mins}m wait" : "{$hours}h wait";
        $titleLabel = "Waiting for {$hours} hr" . ($hours > 1 ? 's' : '') . ($mins > 0 ? " and {$mins} mins" : '');
    } elseif ($totalMins >= 1) {
        $waitLabel = $totalMins . 'm wait';
        $titleLabel = "Waiting for {$totalMins} minute" . ($totalMins !== 1 ? 's' : '');
    } else {
        $waitLabel = $secs . 's wait';
        $titleLabel = "Waiting for {$secs} second" . ($secs !== 1 ? 's' : '');
    }
@endphp

@if($mins > 30)
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-700 shadow-sm animate-pulse" title="{{ $titleLabel }} (> 30 mins delay)">
        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
        {{ $waitLabel }} (High)
    </span>
@elseif($mins >= 15)
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-700 shadow-sm" title="{{ $titleLabel }} (15-30 mins)">
        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
        {{ $waitLabel }}
    </span>
@else
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 shadow-sm" title="{{ $titleLabel }} (< 15 mins)">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
        {{ $waitLabel }}
    </span>
@endif
