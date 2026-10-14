{{-- Login Layout Loading Skeleton --}}
<div id="global-skeleton-loader" 
     class="fixed inset-0 z-[9999] bg-[#faf8f2] dark:bg-[#081a1c] transition-opacity duration-300 ease-in-out flex items-center justify-center pointer-events-none p-4"
     style="opacity: 1;"
>
    <!-- Top-left Branding Skeleton (matches actual: logo + "RHU SILANG" text) -->
    <div class="absolute top-6 left-6 md:top-8 md:left-10 flex items-center gap-3">
        <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
        <div class="hidden sm:flex items-center gap-1 animate-pulse">
            <div class="h-5 w-12 bg-slate-200 dark:bg-slate-800 rounded-lg"></div>
            <div class="h-5 w-16 bg-teal-200 dark:bg-teal-900/60 rounded-lg"></div>
        </div>
    </div>

    <!-- Top-right Theme Toggle Skeleton -->
    <div class="absolute top-6 right-6 md:top-8 md:right-10">
        <div class="h-7 w-14 rounded-full bg-slate-200 dark:bg-slate-800 animate-pulse"></div>
    </div>

    <!-- Main Login Modal Container Skeleton -->
    <div class="w-full max-w-[900px] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-teal-900/10 dark:shadow-teal-900/30 overflow-hidden flex flex-col md:flex-row m-4 border border-slate-100 dark:border-slate-700 animate-pulse">
        
        <!-- Left Branding Banner (Desktop) -->
        <div class="w-full md:w-5/12 bg-gradient-to-br from-teal-600 to-emerald-800 dark:from-teal-800 dark:to-slate-900 p-10 text-white flex flex-col justify-center items-center text-center relative overflow-hidden hidden md:flex">
            <!-- Decorative background circles -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-teal-900/40 blur-3xl"></div>
            
            <!-- Logo circle shimmer -->
            <div class="relative z-10 w-24 h-24 rounded-full bg-white/10 backdrop-blur-md p-2 mb-8 border border-white/20 shadow-xl flex items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-white/30"></div>
            </div>
            
            <div class="relative z-10 space-y-3 flex flex-col items-center">
                <div class="h-7 w-48 bg-white/30 rounded-lg"></div>
                <div class="h-7 w-36 bg-teal-200/40 rounded-lg"></div>
            </div>
            
            <div class="relative z-10 space-y-2 w-full max-w-xs flex flex-col items-center mt-6">
                <div class="h-3.5 w-56 bg-white/20 rounded"></div>
                <div class="h-3.5 w-48 bg-white/20 rounded"></div>
                <div class="h-3.5 w-40 bg-white/20 rounded"></div>
            </div>
        </div>

        <!-- Right Form Area -->
        <div class="w-full md:w-7/12 p-8 md:p-12 bg-white dark:bg-slate-800 flex flex-col justify-center">
            
            <!-- Mobile Logo -->
            <div class="md:hidden flex justify-center mb-6">
                <div class="w-16 h-16 rounded-full bg-teal-50 dark:bg-slate-700"></div>
            </div>

            <!-- Form Header -->
            <div class="mb-8">
                <div class="h-7 w-56 bg-slate-200 dark:bg-slate-700 rounded-lg mb-2"></div>
                <div class="h-4 w-72 bg-slate-100 dark:bg-slate-700/60 rounded"></div>
            </div>

            <!-- Inputs -->
            <div class="space-y-5">
                <!-- Email -->
                <div>
                    <div class="h-4 w-28 bg-slate-200 dark:bg-slate-700 rounded mb-2"></div>
                    <div class="h-12 w-full bg-slate-50 dark:bg-slate-700/60 rounded-xl border border-slate-200 dark:border-slate-600"></div>
                </div>

                <!-- Password -->
                <div>
                    <div class="h-4 w-20 bg-slate-200 dark:bg-slate-700 rounded mb-2"></div>
                    <div class="h-12 w-full bg-slate-50 dark:bg-slate-700/60 rounded-xl border border-slate-200 dark:border-slate-600"></div>
                </div>

                <!-- Options Row -->
                <div class="flex justify-between items-center pt-1">
                    <div class="h-4 w-28 bg-slate-100 dark:bg-slate-700/60 rounded"></div>
                    <div class="h-4 w-24 bg-slate-100 dark:bg-slate-700/60 rounded"></div>
                </div>

                <!-- Submit Button -->
                <div class="h-12 w-full bg-gradient-to-r from-teal-600 to-emerald-700 dark:from-teal-700 dark:to-emerald-800 rounded-xl shadow-lg mt-6"></div>
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
        loader.style.display = 'flex';
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

    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.target === '_blank' || form.dataset.noLoader) return;

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
