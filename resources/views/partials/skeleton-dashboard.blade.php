{{-- Dashboard Layout Loading Skeleton --}}
<div id="global-skeleton-loader" 
     class="fixed inset-0 z-[9999] bg-gray-50 dark:bg-gray-900 transition-opacity duration-500 ease-in-out pointer-events-none"
     style="opacity: 1;"
>
    <!-- Topbar Skeleton -->
    <div class="h-16 w-full border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 flex items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-4">
            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
            <div class="h-5 w-32 bg-gray-200 dark:bg-gray-700 rounded animate-pulse hidden sm:block"></div>
        </div>
        <div class="flex items-center gap-4">
            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded-full animate-pulse"></div>
        </div>
    </div>
    
    <div class="flex h-[calc(100vh-4rem)] overflow-hidden">
        <!-- Sidebar Skeleton (Hidden on small screens) -->
        <div class="hidden lg:flex flex-col gap-6 w-64 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-6 shrink-0">
            <div class="space-y-4">
                <div class="h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                <div class="h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
            </div>
            <div class="mt-auto h-10 w-full bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
        </div>

        <!-- Main Content Skeleton -->
        <div class="flex-1 p-4 sm:p-6 lg:p-8 overflow-hidden flex flex-col gap-6">
            <!-- Header area -->
            <div class="flex justify-between items-end">
                <div class="space-y-2 w-1/3">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse w-2/3"></div>
                </div>
                <div class="h-10 w-32 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse hidden sm:block"></div>
            </div>

            <!-- Stats/Cards Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div class="h-32 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 animate-pulse"></div>
                <div class="h-32 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 animate-pulse hidden sm:block"></div>
                <div class="h-32 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 animate-pulse hidden lg:block"></div>
                <div class="h-32 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 animate-pulse hidden xl:block"></div>
            </div>

            <!-- Table/List Area -->
            <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
                <div class="h-14 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center px-6">
                    <div class="h-5 w-1/4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="h-12 bg-gray-100 dark:bg-gray-700/50 rounded-lg animate-pulse"></div>
                    <div class="h-12 bg-gray-100 dark:bg-gray-700/50 rounded-lg animate-pulse"></div>
                    <div class="h-12 bg-gray-100 dark:bg-gray-700/50 rounded-lg animate-pulse"></div>
                    <div class="h-12 bg-gray-100 dark:bg-gray-700/50 rounded-lg animate-pulse"></div>
                    <div class="h-12 bg-gray-100 dark:bg-gray-700/50 rounded-lg animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fades out and removes the skeleton once the page has fully loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('global-skeleton-loader');
        if (loader) {
            // Slight delay for smoother visual transition, makes it feel like it worked harder
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.remove();
                }, 500); // Wait for transition to finish
            }, 100); 
        }
    });

    // Failsafe in case 'load' event doesn't fire due to cached resources or weird network states
    setTimeout(() => {
        const loader = document.getElementById('global-skeleton-loader');
        if (loader && loader.style.opacity !== '0') {
            loader.style.opacity = '0';
            setTimeout(() => loader.remove(), 500);
        }
    }, 5000); // 5 seconds max wait
</script>
