{{-- Dashboard Layout Loading Skeleton --}}
<div id="global-skeleton-loader" 
     class="fixed inset-0 z-[9999] bg-gray-100 dark:bg-gray-900 transition-opacity duration-300 ease-in-out pointer-events-none flex overflow-hidden"
     style="opacity: 1;"
>
    <!-- Sidebar Skeleton (Desktop) -->
    <div class="hidden md:flex flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shrink-0 h-screen animate-pulse">
        
        <!-- Sidebar Header -->
        <div class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-gray-700"></div>
            <div class="space-y-1.5 flex-1">
                <div class="h-3.5 w-28 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-2.5 w-20 bg-gray-100 dark:bg-gray-700/60 rounded"></div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-4 space-y-2 overflow-hidden">
            <!-- Active item -->
            <div class="h-10 w-full bg-gradient-to-r from-emerald-600/80 to-emerald-800/80 rounded-lg flex items-center px-3 gap-3">
                <div class="w-4 h-4 rounded bg-white/40"></div>
                <div class="h-3.5 w-24 bg-white/40 rounded"></div>
            </div>
            <!-- Other items -->
            <div class="h-10 w-full bg-gray-50 dark:bg-gray-700/40 rounded-lg flex items-center px-3 gap-3">
                <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-600"></div>
                <div class="h-3.5 w-20 bg-gray-200 dark:bg-gray-600 rounded"></div>
            </div>
            <div class="h-10 w-full bg-gray-50 dark:bg-gray-700/40 rounded-lg flex items-center px-3 gap-3">
                <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-600"></div>
                <div class="h-3.5 w-28 bg-gray-200 dark:bg-gray-600 rounded"></div>
            </div>
            <div class="h-10 w-full bg-gray-50 dark:bg-gray-700/40 rounded-lg flex items-center px-3 gap-3">
                <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-600"></div>
                <div class="h-3.5 w-32 bg-gray-200 dark:bg-gray-600 rounded"></div>
            </div>
            <div class="h-10 w-full bg-gray-50 dark:bg-gray-700/40 rounded-lg flex items-center px-3 gap-3">
                <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-600"></div>
                <div class="h-3.5 w-24 bg-gray-200 dark:bg-gray-600 rounded"></div>
            </div>
            <div class="h-10 w-full bg-gray-50 dark:bg-gray-700/40 rounded-lg flex items-center px-3 gap-3">
                <div class="w-4 h-4 rounded bg-gray-200 dark:bg-gray-600"></div>
                <div class="h-3.5 w-28 bg-gray-200 dark:bg-gray-600 rounded"></div>
            </div>
        </div>

        <!-- Bottom User Panel -->
        <div class="border-t border-gray-100 dark:border-gray-700 p-4 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-gray-700"></div>
                <div class="space-y-1 flex-1">
                    <div class="h-3.5 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-2.5 w-16 bg-gray-100 dark:bg-gray-700/60 rounded"></div>
                </div>
            </div>
            <div class="h-8 w-full bg-gray-100 dark:bg-gray-700/40 rounded-lg"></div>
        </div>
    </div>

    <!-- Main View Skeleton -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-100 dark:bg-gray-900 animate-pulse">
        
        <!-- Topbar Header -->
        <div class="h-16 bg-emerald-600 dark:bg-emerald-800 shadow-md flex items-center justify-between px-4 sm:px-6 gap-4 shrink-0">
            <div class="flex items-center gap-4 flex-1">
                <div class="h-6 w-36 bg-white/30 rounded-lg hidden sm:block"></div>
                <div class="h-9 w-full max-w-md bg-white/20 rounded-full sm:ml-4"></div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <div class="h-6 w-[42px] bg-emerald-700/60 dark:bg-emerald-950/60 rounded-full"></div>
            </div>
        </div>

        <!-- Main Content Body -->
        <div class="flex-1 p-6 overflow-y-auto space-y-6">
            
            <!-- 4 KPI Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="h-32 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="h-3.5 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-7 w-16 bg-gray-300 dark:bg-gray-600 rounded-md"></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-gray-700"></div>
                    </div>
                    <div class="h-3 w-28 bg-gray-100 dark:bg-gray-700/60 rounded"></div>
                </div>

                <div class="h-32 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between hidden sm:flex">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="h-3.5 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-7 w-16 bg-gray-300 dark:bg-gray-600 rounded-md"></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-gray-700"></div>
                    </div>
                    <div class="h-3 w-28 bg-gray-100 dark:bg-gray-700/60 rounded"></div>
                </div>

                <div class="h-32 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between hidden lg:flex">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="h-3.5 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-7 w-16 bg-gray-300 dark:bg-gray-600 rounded-md"></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-gray-700"></div>
                    </div>
                    <div class="h-3 w-28 bg-gray-100 dark:bg-gray-700/60 rounded"></div>
                </div>

                <div class="h-32 bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between hidden lg:flex">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="h-3.5 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-7 w-16 bg-gray-300 dark:bg-gray-600 rounded-md"></div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-gray-700"></div>
                    </div>
                    <div class="h-3 w-28 bg-gray-100 dark:bg-gray-700/60 rounded"></div>
                </div>
            </div>

            <!-- Table Card Area -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
                
                <!-- Table Action Header -->
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex flex-wrap justify-between items-center gap-4">
                    <div class="h-6 w-44 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-60 bg-gray-100 dark:bg-gray-700/60 rounded-lg"></div>
                        <div class="h-9 w-24 bg-gray-100 dark:bg-gray-700/60 rounded-lg"></div>
                    </div>
                </div>

                <!-- Table Rows -->
                <div class="p-5 space-y-4">
                    <div class="h-12 w-full bg-gray-50 dark:bg-gray-700/40 rounded-xl flex items-center justify-between px-4">
                        <div class="h-4 w-1/4 bg-gray-200 dark:bg-gray-600 rounded"></div>
                        <div class="h-4 w-1/5 bg-gray-200 dark:bg-gray-600 rounded hidden sm:block"></div>
                        <div class="h-6 w-20 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                        <div class="h-8 w-20 bg-gray-200 dark:bg-gray-600 rounded-lg"></div>
                    </div>
                    <div class="h-12 w-full bg-gray-50 dark:bg-gray-700/40 rounded-xl flex items-center justify-between px-4">
                        <div class="h-4 w-1/3 bg-gray-200 dark:bg-gray-600 rounded"></div>
                        <div class="h-4 w-1/4 bg-gray-200 dark:bg-gray-600 rounded hidden sm:block"></div>
                        <div class="h-6 w-20 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                        <div class="h-8 w-20 bg-gray-200 dark:bg-gray-600 rounded-lg"></div>
                    </div>
                    <div class="h-12 w-full bg-gray-50 dark:bg-gray-700/40 rounded-xl flex items-center justify-between px-4">
                        <div class="h-4 w-1/4 bg-gray-200 dark:bg-gray-600 rounded"></div>
                        <div class="h-4 w-1/5 bg-gray-200 dark:bg-gray-600 rounded hidden sm:block"></div>
                        <div class="h-6 w-20 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                        <div class="h-8 w-20 bg-gray-200 dark:bg-gray-600 rounded-lg"></div>
                    </div>
                    <div class="h-12 w-full bg-gray-50 dark:bg-gray-700/40 rounded-xl flex items-center justify-between px-4">
                        <div class="h-4 w-1/3 bg-gray-200 dark:bg-gray-600 rounded"></div>
                        <div class="h-4 w-1/6 bg-gray-200 dark:bg-gray-600 rounded hidden sm:block"></div>
                        <div class="h-6 w-20 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                        <div class="h-8 w-20 bg-gray-200 dark:bg-gray-600 rounded-lg"></div>
                    </div>
                    <div class="h-12 w-full bg-gray-50 dark:bg-gray-700/40 rounded-xl flex items-center justify-between px-4">
                        <div class="h-4 w-1/4 bg-gray-200 dark:bg-gray-600 rounded"></div>
                        <div class="h-4 w-1/5 bg-gray-200 dark:bg-gray-600 rounded hidden sm:block"></div>
                        <div class="h-6 w-20 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                        <div class="h-8 w-20 bg-gray-200 dark:bg-gray-600 rounded-lg"></div>
                    </div>
                </div>

                <!-- Pagination Bar Skeleton -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <div class="h-4 w-36 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="flex items-center gap-1.5">
                        <div class="h-8 w-8 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                        <div class="h-8 w-8 rounded-lg bg-emerald-600 dark:bg-emerald-700"></div>
                        <div class="h-8 w-8 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                        <div class="h-8 w-8 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Seamless Navigation & Instant Skeleton Lifecycle Script -->
<script>
(function() {
    const loader = document.getElementById('global-skeleton-loader');
    if (!loader) return;

    loader.style.pointerEvents = 'none';
    const mountTime = performance.now();
    const minDisplayTime = 300; // Optimal visibility threshold
    let hideTimer = null;

    function hideSkeleton() {
        if (hideTimer) clearTimeout(hideTimer);
        const elapsed = performance.now() - mountTime;
        const remaining = Math.max(0, minDisplayTime - elapsed);

        hideTimer = setTimeout(() => {
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }, remaining);
    }

    function showSkeleton() {
        if (hideTimer) clearTimeout(hideTimer);
        loader.style.display = 'flex';
        loader.style.pointerEvents = 'none';
        void loader.offsetWidth; // Force reflow
        loader.style.opacity = '1';
        // Auto-dismiss failsafe if user cancels navigation
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => { loader.style.display = 'none'; }, 300);
        }, 4000);
    }

    // Dismiss only when ALL resources (CSS, JS, images) are fully loaded
    // Never dismiss on 'interactive' — the inline script runs while DOM is still parsing,
    // which would hide the skeleton before the page content is visually ready.
    if (document.readyState === 'complete') {
        hideSkeleton();
    } else {
        window.addEventListener('load', hideSkeleton, { once: true });
    }

    // Safety dismissal (prevents skeleton from staying forever on slow pages)
    setTimeout(hideSkeleton, 5000);

    // Instant trigger when clicking any internal link (Pagination, Sidebar, Topbar, Table links)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (btn && (btn.hasAttribute('data-collapse-toggle') || btn.hasAttribute('data-dropdown-toggle') || btn.getAttribute('role') === 'button')) {
            return;
        }

        const link = e.target.closest('a');
        if (!link) return;

        // Skip non-navigational links
        if (
            link.hasAttribute('download') ||
            link.target === '_blank' ||
            !link.href ||
            link.getAttribute('href').startsWith('#') ||
            link.getAttribute('href').startsWith('javascript:') ||
            e.ctrlKey || e.metaKey || e.shiftKey || e.altKey
        ) {
            return;
        }

        // Skip interactive Alpine triggers
        if (
            link.hasAttribute('@click') ||
            link.hasAttribute('x-on:click') ||
            link.getAttribute('role') === 'button'
        ) {
            return;
        }

        // Trigger skeleton immediately on internal link navigation (including pagination)
        if (link.href.startsWith(window.location.origin)) {
            showSkeleton();
        }
    }, true);

    // Trigger on form submissions (search, filter, pagination jump, logins)
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.target === '_blank' || form.dataset.noLoader || form.hasAttribute('x-on:submit')) return;

        showSkeleton();
    }, true);

    // Trigger on beforeunload right before document switch
    window.addEventListener('beforeunload', function() {
        showSkeleton();
    });

    // Reset when restored from browser cache (Back/Forward)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            loader.style.opacity = '0';
            loader.style.display = 'none';
            loader.style.pointerEvents = 'none';
        }
    });
})();
</script>
