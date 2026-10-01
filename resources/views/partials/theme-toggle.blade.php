{{-- Unified Theme Toggle Switch (Day / Night Mode) --}}
@php
    $toggleId = $id ?? 'theme-toggle';
    $customClass = $class ?? '';
@endphp

<button id="{{ $toggleId }}"
        type="button"
        onclick="if (window.toggleTheme) { window.toggleTheme(); } else { var d = document.documentElement.classList.toggle('dark'); localStorage.setItem('color-theme', d ? 'dark' : 'light'); window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: d } })); }"
        class="theme-toggle-btn group relative inline-flex items-center h-7 w-14 shrink-0 cursor-pointer rounded-full border p-0.5 transition-colors duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 bg-slate-200/90 border-slate-300/80 hover:border-slate-400 dark:bg-slate-800/90 dark:border-slate-700 dark:hover:border-slate-600 select-none shadow-xs {{ $customClass }}"
        role="switch"
        aria-label="Toggle light and dark theme"
        title="Toggle Light / Dark Theme">
    <span class="sr-only">Toggle theme</span>

    {{-- Ambient Sun Icon on Track (visible when in dark mode on the left track) --}}
    <span class="absolute left-1.5 inset-y-0 flex items-center justify-center pointer-events-none transition-opacity duration-200 opacity-0 dark:opacity-75">
        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.06 1.06l1.06 1.06z"/>
        </svg>
    </span>

    {{-- Ambient Moon Icon on Track (visible when in light mode on the right track) --}}
    <span class="absolute right-1.5 inset-y-0 flex items-center justify-center pointer-events-none transition-opacity duration-200 opacity-75 dark:opacity-0">
        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition-colors" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.766.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z" clip-rule="evenodd"/>
        </svg>
    </span>

    {{-- Sliding Knob --}}
    <span class="pointer-events-none inline-flex h-[22px] w-[22px] transform items-center justify-center rounded-full transition-transform duration-200 ease-in-out z-10 translate-x-0 bg-white text-amber-500 shadow-sm border border-slate-200 dark:translate-x-7 dark:bg-slate-900 dark:text-amber-300 dark:shadow-md dark:border-slate-700/80">
        {{-- Sun Icon on Knob (Light Mode Active) --}}
        <svg class="w-3.5 h-3.5 block dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
        </svg>
        {{-- Moon Icon on Knob (Dark Mode Active) --}}
        <svg class="w-3 h-3 hidden dark:block text-indigo-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.766.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z" clip-rule="evenodd"/>
        </svg>
    </span>
</button>

@once
<script>
    if (!window.toggleTheme) {
        window.toggleTheme = function() {
            var isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('color-theme', isDark ? 'dark' : 'light');
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: isDark } }));
        };
    }
</script>
@endonce
