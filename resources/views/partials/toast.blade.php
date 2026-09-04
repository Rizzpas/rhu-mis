{{-- Global Toast Notification System --}}
{{-- Supports: session('success'), session('error'), session('warning'), session('info') --}}
{{-- Also handles $errors->any() for validation errors --}}

<div x-data="{
    toasts: [],
    addToast(type, message, duration = 5000) {
        const id = Date.now() + Math.random();
        this.toasts.push({ id, type, message, visible: true, duration });
        setTimeout(() => {
            this.remove(id);
        }, duration);
    },
    remove(id) {
        const toast = this.toasts.find(t => t.id === id);
        if (toast) {
            toast.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        }
    },
    config(type) {
        const map = {
            success: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' class='w-5 h-5 text-emerald-500 dark:text-emerald-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>`,
                label: 'Success'
            },
            error: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' class='w-5 h-5 text-red-500 dark:text-red-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>`,
                label: 'Error'
            },
            warning: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' class='w-5 h-5 text-amber-500 dark:text-amber-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'/></svg>`,
                label: 'Warning'
            },
            info: {
                icon: `<svg xmlns='http://www.w3.org/2000/svg' class='w-5 h-5 text-blue-500 dark:text-blue-400' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'/></svg>`,
                label: 'Info'
            },
        };
        return map[type] || map.info;
    },
    init() {
        @if(session('success'))
            this.addToast('success', @js(session('success')));
        @endif
        @if(session('error'))
            this.addToast('error', @js(session('error')));
        @endif
        @if(session('warning'))
            this.addToast('warning', @js(session('warning')));
        @endif
        @if(session('info'))
            this.addToast('info', @js(session('info')));
        @endif
        @if(isset($errors) && $errors->any())
            @foreach($errors->all() as $error)
                this.addToast('error', @js($error));
            @endforeach
        @endif
    }
}"
class="fixed top-4 right-4 sm:top-6 sm:right-6 z-[9999] flex flex-col justify-start gap-3 w-full sm:w-auto min-w-[320px] max-w-md pointer-events-none"
@add-toast.window="addToast($event.detail.type, $event.detail.message, $event.detail.duration || 5000)">
<template x-for="toast in toasts" :key="toast.id">
    <div x-show="toast.visible" 
        x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200" 
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        @click="remove(toast.id)"
        class="pointer-events-auto flex w-full items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-white p-4 shadow-2xl ring-1 ring-black/5 dark:bg-gray-950 dark:ring-white/10 relative overflow-hidden cursor-pointer group transition-all duration-200 hover:scale-[1.02] hover:border-gray-300 dark:hover:border-gray-700">
        
        {{-- Colored Icon --}}
        <div class="shrink-0 mt-0.5 relative z-10" x-html="config(toast.type).icon"></div>

        {{-- Text Content --}}
        <div class="flex-1 min-w-0 pr-2 relative z-10">
            <p class="text-xs font-bold tracking-wider text-gray-900 dark:text-gray-100 uppercase" x-text="config(toast.type).label"></p>
            <p class="mt-0.5 text-sm text-gray-600 dark:text-gray-300 break-words leading-relaxed font-medium"
                x-text="toast.message"></p>
        </div>

        {{-- Close button --}}
        <button type="button" @click.stop="remove(toast.id)"
            class="shrink-0 rounded-md p-1 opacity-60 transition-opacity hover:opacity-100 focus:outline-none text-gray-500 dark:text-gray-400 relative z-20 hover:bg-gray-100 dark:hover:bg-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Progress Bar --}}
        <div class="absolute bottom-0 left-0 h-1 bg-gray-100 dark:bg-gray-800/50 w-full overflow-hidden">
            <div class="h-full bg-emerald-500 dark:bg-emerald-400 rounded-bl-lg" 
                x-data="{ width: '100%' }"
                x-init="setTimeout(() => width = '0%', 30)"
                :style="`width: ${width}; transition: width ${toast.duration || 5000}ms linear`">
            </div>
        </div>
    </div>
</template>
</div>