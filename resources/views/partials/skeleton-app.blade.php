{{-- Public App Layout Loading Skeleton --}}
<div id="global-skeleton-loader" 
     class="fixed inset-0 z-[9999] bg-[#faf8f2] dark:bg-[#081a1c] transition-opacity duration-300 ease-in-out pointer-events-none overflow-y-auto"
     style="opacity: 1;"
>
    <!-- Top Bar: Official Republic Header Skeleton -->
    <div class="bg-emerald-900 text-white h-9 px-4 md:px-8 flex justify-between items-center animate-pulse border-b border-emerald-800">
        <div class="h-3.5 w-64 bg-emerald-700/60 rounded"></div>
        <div class="h-3.5 w-48 bg-emerald-700/60 rounded hidden md:block"></div>
    </div>

    <!-- Main Navigation Skeleton -->
    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 md:px-8 py-3.5 animate-pulse">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Brand Logo & Title -->
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                <div class="space-y-1">
                    <div class="h-4 w-40 bg-slate-200 dark:bg-slate-700 rounded"></div>
                    <div class="h-2.5 w-32 bg-slate-100 dark:bg-slate-800 rounded"></div>
                </div>
            </div>

            <!-- Nav Links (Desktop) -->
            <div class="hidden md:flex items-center gap-8">
                <div class="h-4 w-12 bg-slate-200 dark:bg-slate-700 rounded"></div>
                <div class="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded"></div>
                <div class="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded"></div>
                <div class="h-4 w-28 bg-slate-200 dark:bg-slate-700 rounded"></div>
                <div class="h-6 w-11 bg-slate-200 dark:bg-slate-700 rounded-full"></div>
                <div class="h-10 w-36 bg-emerald-600/80 dark:bg-emerald-700/80 rounded-xl"></div>
            </div>
        </div>
    </div>
    
    <!-- Hero Section Skeleton -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 animate-pulse">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left Column: Hero Carousel Placeholder -->
            <div class="lg:col-span-6 order-1">
                <div class="relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl bg-slate-800 border border-slate-700 flex flex-col justify-end p-6">
                    <div class="space-y-3 relative z-10">
                        <div class="h-4 w-28 bg-emerald-600/80 rounded-md"></div>
                        <div class="h-7 w-3/4 bg-white/40 rounded-lg"></div>
                        <div class="h-4 w-5/6 bg-white/20 rounded"></div>
                    </div>
                    <!-- Carousel Dots -->
                    <div class="flex justify-center gap-2 mt-6">
                        <div class="h-1.5 w-6 bg-white/80 rounded-full"></div>
                        <div class="h-1.5 w-2 bg-white/30 rounded-full"></div>
                        <div class="h-1.5 w-2 bg-white/30 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Hero Content & CTAs -->
            <div class="lg:col-span-6 order-2 space-y-6">
                <!-- Institutional Badge -->
                <div class="h-7 w-72 bg-emerald-100 dark:bg-emerald-950/70 rounded-md"></div>
                
                <!-- Main Heading -->
                <div class="space-y-3">
                    <div class="h-10 w-full bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    <div class="h-10 w-4/5 bg-slate-200 dark:bg-slate-700 rounded-xl"></div>
                    <div class="h-10 w-3/5 bg-emerald-200 dark:bg-emerald-900/60 rounded-xl"></div>
                </div>

                <!-- Subtitle -->
                <div class="space-y-2 pt-2">
                    <div class="h-4 w-full bg-slate-200/80 dark:bg-slate-800 rounded"></div>
                    <div class="h-4 w-5/6 bg-slate-200/80 dark:bg-slate-800 rounded"></div>
                    <div class="h-4 w-3/4 bg-slate-200/80 dark:bg-slate-800 rounded"></div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 pt-4">
                    <div class="h-12 w-44 bg-emerald-700 rounded-xl"></div>
                    <div class="h-12 w-40 border-2 border-emerald-700/40 rounded-xl"></div>
                </div>
            </div>
        </div>

        <!-- 3 Feature Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 pb-20">
            <div class="h-60 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col justify-between">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40"></div>
                <div class="space-y-2">
                    <div class="h-5 w-3/4 bg-slate-200 dark:bg-slate-700 rounded"></div>
                    <div class="h-3.5 w-full bg-slate-100 dark:bg-slate-800 rounded"></div>
                    <div class="h-3.5 w-4/5 bg-slate-100 dark:bg-slate-800 rounded"></div>
                </div>
                <div class="h-4 w-24 bg-emerald-200 dark:bg-emerald-900/60 rounded"></div>
            </div>

            <div class="h-60 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col justify-between hidden md:flex">
                <div class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/40"></div>
                <div class="space-y-2">
                    <div class="h-5 w-3/4 bg-slate-200 dark:bg-slate-700 rounded"></div>
                    <div class="h-3.5 w-full bg-slate-100 dark:bg-slate-800 rounded"></div>
                    <div class="h-3.5 w-4/5 bg-slate-100 dark:bg-slate-800 rounded"></div>
                </div>
                <div class="h-4 w-24 bg-teal-200 dark:bg-teal-900/60 rounded"></div>
            </div>

            <div class="h-60 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col justify-between hidden md:flex">
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40"></div>
                <div class="space-y-2">
                    <div class="h-5 w-3/4 bg-slate-200 dark:bg-slate-700 rounded"></div>
                    <div class="h-3.5 w-full bg-slate-100 dark:bg-slate-800 rounded"></div>
                    <div class="h-3.5 w-4/5 bg-slate-100 dark:bg-slate-800 rounded"></div>
                </div>
                <div class="h-4 w-24 bg-blue-200 dark:bg-blue-900/60 rounded"></div>
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
    const minDisplayTime = 300;
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
        loader.style.display = 'block';
        loader.style.pointerEvents = 'none';
        void loader.offsetWidth;
        loader.style.opacity = '1';
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => { loader.style.display = 'none'; }, 300);
        }, 4000);
    }

    // Dismiss only when ALL resources (CSS, JS, images) are fully loaded
    if (document.readyState === 'complete') {
        hideSkeleton();
    } else {
        window.addEventListener('load', hideSkeleton, { once: true });
    }

    // Safety dismissal (prevents skeleton from staying forever on slow pages)
    setTimeout(hideSkeleton, 5000);

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (btn && (btn.hasAttribute('data-collapse-toggle') || btn.hasAttribute('data-dropdown-toggle') || btn.getAttribute('role') === 'button')) {
            return;
        }

        const link = e.target.closest('a');
        if (!link) return;

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

        if (
            link.hasAttribute('@click') ||
            link.hasAttribute('x-on:click') ||
            link.getAttribute('role') === 'button'
        ) {
            return;
        }

        if (link.href.startsWith(window.location.origin)) {
            showSkeleton();
        }
    }, true);

    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.target === '_blank' || form.dataset.noLoader || form.hasAttribute('x-on:submit')) return;

        showSkeleton();
    }, true);

    window.addEventListener('beforeunload', function() {
        showSkeleton();
    });

    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            loader.style.opacity = '0';
            loader.style.display = 'none';
            loader.style.pointerEvents = 'none';
        }
    });
})();
</script>
