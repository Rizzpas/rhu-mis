{{-- Public App Layout Loading Skeleton --}}
<div id="global-skeleton-loader" 
     class="fixed inset-0 z-[9999] bg-[#eefcf1] dark:bg-gray-900 transition-opacity duration-500 ease-in-out pointer-events-none overflow-y-auto"
     style="opacity: 1;"
>
    <!-- Top Nav Skeleton -->
    <div class="h-[72px] w-full border-b border-green-200/50 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md flex items-center justify-between px-4 md:px-8 shrink-0 relative mt-9">
        <div class="flex items-center gap-2">
			<div class="h-10 w-10 bg-green-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
			<div class="h-6 w-32 bg-green-200 dark:bg-gray-700 rounded animate-pulse hidden sm:block"></div>
		</div>
        <div class="hidden md:flex gap-6">
             <div class="h-4 w-16 bg-green-100 dark:bg-gray-700 rounded animate-pulse"></div>
             <div class="h-4 w-16 bg-green-100 dark:bg-gray-700 rounded animate-pulse"></div>
             <div class="h-4 w-16 bg-green-100 dark:bg-gray-700 rounded animate-pulse"></div>
             <div class="h-4 w-16 bg-green-100 dark:bg-gray-700 rounded animate-pulse"></div>
        </div>
		<div>
			<div class="h-9 w-24 bg-green-300 dark:bg-green-800 rounded animate-pulse"></div>
		</div>
    </div>
    
    <!-- Hero / Main Content Skeleton -->
    <div class="w-full flex flex-col items-center px-4 py-16 gap-8">
        <!-- Hero section skeleton -->
        <div class="flex flex-col items-center gap-6 mt-8 w-full max-w-3xl text-center">
			<div class="h-8 w-48 bg-green-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
			<div class="h-16 w-full bg-green-200 dark:bg-gray-700 rounded animate-pulse"></div>
			<div class="h-16 w-2/3 bg-green-200 dark:bg-gray-700 rounded animate-pulse"></div>
			<div class="h-6 w-3/4 bg-green-100 dark:bg-gray-800 rounded animate-pulse mt-4"></div>
			
			<div class="flex gap-4 mt-6">
				<div class="h-12 w-32 bg-green-300 dark:bg-green-800 rounded animate-pulse"></div>
				<div class="h-12 w-32 border border-green-300 dark:border-green-800 rounded animate-pulse"></div>
			</div>
		</div>
        
        <!-- Cards section skeleton -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-7xl mt-16 pb-32">
            <div class="h-56 bg-white dark:bg-gray-800 rounded-2xl animate-pulse shadow-sm border border-gray-100 dark:border-gray-700"></div>
            <div class="h-56 bg-white dark:bg-gray-800 rounded-2xl animate-pulse shadow-sm border border-gray-100 dark:border-gray-700"></div>
            <div class="h-56 bg-white dark:bg-gray-800 rounded-2xl animate-pulse shadow-sm border border-gray-100 dark:border-gray-700"></div>
        </div>
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
