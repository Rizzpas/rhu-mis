{{-- Global Modern Clean Toast Notification System --}}
{{-- Supports: session('success'), session('error'), session('warning'), session('info') --}}
{{-- Supports: validation errors $errors->any() --}}
{{-- Supports: window.dispatchEvent(new CustomEvent('add-toast', { detail: { type, message, title, duration } })) --}}
{{-- Supports: Alpine $dispatch('add-toast', { type, message, title, duration }) --}}
{{-- Supports: window.toast.success(message, title, duration), window.showToast(message, type, duration, title) --}}

<div x-data="{
    toasts: [],
    addToast(type, message, duration = 5000, title = null) {
        if (typeof type === 'object' && type !== null && !message) {
            const d = type;
            type = d.type || 'info';
            message = d.message || '';
            duration = d.duration || 5000;
            title = d.title || null;
        } else if (typeof message === 'object' && message !== null) {
            title = message.title || title;
            duration = message.duration || duration;
            message = message.message || JSON.stringify(message);
        } else if (!message && typeof type === 'string') {
            message = type;
            type = 'info';
        }

        // Limit maximum concurrent toasts to 5 to maintain clean viewport hierarchy
        if (this.toasts.length >= 5) {
            const oldest = this.toasts[0];
            if (oldest) this.remove(oldest.id);
        }

        const id = Date.now() + Math.random();
        const parsedDuration = Number(duration) > 0 ? Number(duration) : 5000;
        
        const toast = {
            id,
            type: type || 'info',
            message: message || '',
            title: title || null,
            duration: parsedDuration,
            remaining: parsedDuration,
            progress: 100,
            paused: false,
            visible: true,
            timer: null
        };

        // Smooth tick for countdown and progress bar with interactive hover-pause
        toast.timer = setInterval(() => {
            if (!toast.paused) {
                toast.remaining -= 40;
                toast.progress = Math.max(0, (toast.remaining / toast.duration) * 100);
                if (toast.remaining <= 0) {
                    clearInterval(toast.timer);
                    this.remove(toast.id);
                }
            }
        }, 40);

        this.toasts.push(toast);
    },
    remove(id) {
        const toast = this.toasts.find(t => t.id === id);
        if (!toast || !toast.visible) return;

        if (toast.timer) clearInterval(toast.timer);
        toast.visible = false;
        setTimeout(() => {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }, 220);
    },
    clearAll() {
        this.toasts.forEach(t => {
            if (t.timer) clearInterval(t.timer);
            t.visible = false;
        });
        setTimeout(() => {
            this.toasts = [];
        }, 220);
    },
    config(type) {
        const norm = String(type || 'info').toLowerCase();
        const map = {
            success: {
                title: 'Success',
                badgeBg: 'bg-emerald-50 dark:bg-emerald-950/60 border-emerald-200/80 dark:border-emerald-800/60 text-emerald-600 dark:text-emerald-400',
                glowColor: 'from-emerald-500/15 via-emerald-500/5 to-transparent',
                icon: `<svg class='w-5 h-5' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2.2'><path stroke-linecap='round' stroke-linejoin='round' d='M4.5 12.75l6 6 9-13.5'/></svg>`
            },
            error: {
                title: 'Error',
                badgeBg: 'bg-rose-50 dark:bg-rose-950/60 border-rose-200/80 dark:border-rose-800/60 text-rose-600 dark:text-rose-400',
                glowColor: 'from-rose-500/15 via-rose-500/5 to-transparent',
                icon: `<svg class='w-5 h-5' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2.2'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 7.5h.01'/></svg>`
            },
            danger: null,
            warning: {
                title: 'Warning',
                badgeBg: 'bg-amber-50 dark:bg-amber-950/60 border-amber-200/80 dark:border-amber-800/60 text-amber-600 dark:text-amber-400',
                glowColor: 'from-amber-500/15 via-amber-500/5 to-transparent',
                icon: `<svg class='w-5 h-5' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2.2'><path stroke-linecap='round' stroke-linejoin='round' d='M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 4.878c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18.75h.01'/></svg>`
            },
            info: {
                title: 'Notice',
                badgeBg: 'bg-sky-50 dark:bg-sky-950/60 border-sky-200/80 dark:border-sky-800/60 text-sky-600 dark:text-sky-400',
                glowColor: 'from-sky-500/15 via-sky-500/5 to-transparent',
                icon: `<svg class='w-5 h-5' fill='none' viewBox='0 0 24 24' stroke='currentColor' stroke-width='2.2'><path stroke-linecap='round' stroke-linejoin='round' d='M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'/></svg>`
            }
        };
        map.danger = map.error;
        return map[norm] || map.info;
    },
    init() {
        // Expose global convenience helpers
        window.toast = {
            success: (msg, title, duration) => this.addToast('success', msg, duration, title),
            error: (msg, title, duration) => this.addToast('error', msg, duration, title),
            warning: (msg, title, duration) => this.addToast('warning', msg, duration, title),
            info: (msg, title, duration) => this.addToast('info', msg, duration, title),
            clear: () => this.clearAll()
        };
        window.showToast = (msg, type = 'success', duration = 5000, title = null) => {
            this.addToast(type, msg, duration, title);
        };

        // Flash session messages from backend
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
class="fixed top-4 inset-x-4 sm:inset-x-auto sm:top-5 sm:right-5 z-[9999] flex flex-col items-end gap-2.5 pointer-events-none sm:w-[390px] max-w-full"
@add-toast.window="addToast($event.detail.type, $event.detail.message, $event.detail.duration, $event.detail.title)"
role="region"
aria-label="Notifications"
aria-live="polite">

    {{-- Clear all pill for multiple notifications --}}
    <div x-show="toasts.length > 1" 
        x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
        class="pointer-events-auto flex items-center justify-end w-full mb-0.5">
        <button type="button"
            @click="clearAll()"
            class="group inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold text-slate-500 dark:text-slate-400 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-full border border-slate-200/80 dark:border-slate-800 shadow-xs hover:text-slate-900 dark:hover:text-slate-100 hover:border-slate-300 dark:hover:border-slate-700 transition-all duration-150 cursor-pointer">
            <svg class="w-3 h-3 text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>Clear all (<span x-text="toasts.length"></span>)</span>
        </button>
    </div>

    {{-- Toast Items --}}
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible" 
            x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
            x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
            @mouseenter="toast.paused = true"
            @mouseleave="toast.paused = false"
            class="pointer-events-auto relative w-full overflow-hidden rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/80 dark:border-slate-800/80 p-3.5 sm:p-4 shadow-[0_10px_30px_-4px_rgba(15,23,42,0.1),0_4px_10px_-2px_rgba(15,23,42,0.04)] dark:shadow-[0_16px_36px_-6px_rgba(0,0,0,0.55),0_4px_10px_-2px_rgba(0,0,0,0.35)] ring-1 ring-black/[0.03] dark:ring-white/[0.05] transition-all duration-200 hover:shadow-[0_14px_38px_-4px_rgba(15,23,42,0.14)] dark:hover:shadow-[0_18px_40px_-6px_rgba(0,0,0,0.65)] group select-none">
            
            {{-- Ambient radial background glow --}}
            <div class="absolute -top-10 -left-10 w-28 h-28 rounded-full pointer-events-none blur-2xl opacity-40 dark:opacity-30 bg-gradient-to-br"
                :class="config(toast.type).glowColor"></div>

            {{-- Main Content Row --}}
            <div class="relative z-10 flex items-start gap-3 w-full">
                {{-- Refined Icon Badge with soft tinted ring --}}
                <div class="shrink-0 w-9 h-9 rounded-xl flex items-center justify-center border shadow-xs transition-transform duration-200 group-hover:scale-105"
                    :class="config(toast.type).badgeBg"
                    x-html="config(toast.type).icon">
                </div>

                {{-- Text Content --}}
                <div class="flex-1 min-w-0 pt-0.5">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs sm:text-[13px] font-semibold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-1.5"
                            x-text="toast.title || config(toast.type).title">
                        </h4>
                        
                        {{-- Timestamp or Paused indicator --}}
                        <span class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 tracking-tight shrink-0 flex items-center gap-1"
                            x-show="toast.paused">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Paused
                        </span>
                        <span class="text-[10px] font-medium text-slate-400 dark:text-slate-500 tracking-tight shrink-0"
                            x-show="!toast.paused">just now</span>
                    </div>
                    <p class="mt-0.5 text-xs sm:text-[13px] font-normal leading-relaxed text-slate-600 dark:text-slate-300 break-words"
                        x-text="toast.message"></p>
                </div>

                {{-- Close Button --}}
                <button type="button" 
                    @click.stop="remove(toast.id)"
                    aria-label="Dismiss notification"
                    class="shrink-0 -mr-1 -mt-1 w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100/80 dark:text-slate-500 dark:hover:text-slate-200 dark:hover:bg-slate-800/80 transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:ring-slate-400/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>