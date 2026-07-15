{{-- Login Layout Loading Skeleton --}}
<div id="global-skeleton-loader" 
     class="fixed inset-0 z-[9999] bg-emerald-50 dark:bg-gray-900 transition-opacity duration-500 ease-in-out flex items-center justify-center pointer-events-none p-4"
     style="opacity: 1;"
>
    <!-- Login Box Skeleton -->
    <div class="w-full max-w-sm sm:max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 p-8 flex flex-col gap-6 animate-pulse">
        <div class="flex justify-center">
            <div class="h-16 w-16 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
        </div>
        <div class="flex flex-col items-center gap-2 mt-2">
			<div class="h-8 w-1/2 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
			<div class="h-4 w-2/3 bg-gray-200 dark:bg-gray-700 rounded-md"></div>
		</div>
        
        <div class="space-y-5 mt-6">
            <div class="space-y-2">
				<div class="h-4 w-1/4 bg-gray-200 dark:bg-gray-700 rounded"></div>
				<div class="h-11 w-full bg-gray-100 dark:bg-gray-700/50 rounded-lg"></div>
			</div>
            <div class="space-y-2">
				<div class="h-4 w-1/4 bg-gray-200 dark:bg-gray-700 rounded"></div>
				<div class="h-11 w-full bg-gray-100 dark:bg-gray-700/50 rounded-lg"></div>
			</div>
        </div>
        
        <div class="h-11 w-full bg-emerald-300 dark:bg-emerald-800 rounded-lg mt-6"></div>
    </div>
</div>

<script>
     window.addEventListener('load', function() {
        const loader = document.getElementById('global-skeleton-loader');
        if (loader) {
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => loader.remove(), 500); 
            }, 100); 
        }
    });
    setTimeout(() => {
        const loader = document.getElementById('global-skeleton-loader');
        if (loader && loader.style.opacity !== '0') {
            loader.style.opacity = '0';
            setTimeout(() => loader.remove(), 500);
        }
    }, 5000);
</script>
