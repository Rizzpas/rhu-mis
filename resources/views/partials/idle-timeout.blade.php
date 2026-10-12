<!-- Idle Timeout Modal & Session Security Script -->
<div id="idle-timeout-modal"
     class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0"
     role="dialog"
     aria-modal="true"
     aria-labelledby="idle-modal-title">

    <div id="idle-modal-card"
         class="w-full max-w-[410px] bg-white dark:bg-slate-900 rounded-[28px] sm:rounded-3xl px-6 sm:px-8 pt-8 pb-7 shadow-2xl border border-slate-100 dark:border-slate-800 text-center relative overflow-hidden transform scale-95 transition-all duration-300">
        
        {{-- Title --}}
        <h3 id="idle-modal-title" class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
            {{ __("You've been logged out") }}
        </h3>

        {{-- Message --}}
        <p class="mt-2.5 text-xs sm:text-[13px] text-slate-500 dark:text-slate-400 leading-relaxed max-w-[310px] mx-auto">
            {{ __('For security reasons, your session has expired due to 15 minutes of inactivity. You need to sign in again to continue.') }}
        </p>

        {{-- Hourglass Graphic / Illustration --}}
        <div class="my-4 flex items-center justify-center pointer-events-none select-none">
            <svg class="w-52 h-44" viewBox="0 0 240 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                    <linearGradient id="capGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#8ba7b8" />
                        <stop offset="100%" stop-color="#648394" />
                    </linearGradient>
                    <linearGradient id="glassGradient" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#f0fbfd" stop-opacity="0.8" />
                        <stop offset="50%" stop-color="#d6f2f7" stop-opacity="0.55" />
                        <stop offset="100%" stop-color="#c5ecf3" stop-opacity="0.8" />
                    </linearGradient>
                    <linearGradient id="sandGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#55d4e1" />
                        <stop offset="45%" stop-color="#36b8c9" />
                        <stop offset="100%" stop-color="#239cb0" />
                    </linearGradient>
                    <linearGradient id="sandShadow" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#28a1b5" />
                        <stop offset="100%" stop-color="#1b8396" />
                    </linearGradient>
                </defs>

                <!-- Floating bubbles -->
                <circle cx="54" cy="118" r="16" fill="#daf2f7" opacity="0.85" />
                <circle cx="68" cy="56" r="11" stroke="#8ecde0" stroke-width="2.5" fill="none" opacity="0.9" />
                <circle cx="86" cy="144" r="6" stroke="#a6dceb" stroke-width="2" fill="#edf9fc" opacity="0.85" />
                <circle cx="178" cy="56" r="15" fill="#e4f6fa" opacity="0.8" />
                <circle cx="158" cy="68" r="8" stroke="#8ecde0" stroke-width="2.2" fill="none" opacity="0.9" />
                <circle cx="170" cy="100" r="4.5" fill="#70c0d5" />
                <circle cx="180" cy="136" r="7" fill="#daf2f7" opacity="0.85" />

                <!-- Tilted Hourglass Group (-15 deg) -->
                <g transform="rotate(-15 120 100)">
                    <!-- Glass Body Fill -->
                    <path d="M 86 32 C 86 64, 111 86, 114 97 C 111 108, 86 130, 86 162 L 154 162 C 154 130, 129 108, 126 97 C 129 86, 154 64, 154 32 Z"
                          fill="url(#glassGradient)" />

                    <!-- Sand in Bottom Bulb -->
                    <path d="M 86.5 161 C 86.5 133, 101 116, 114 110 C 117 108, 123 108, 126 110 C 139 116, 153.5 133, 153.5 161 Z"
                          fill="url(#sandGradient)" />
                    
                    <!-- Sand Bottom Wave / 3D Depth Shadow -->
                    <path d="M 87 161 C 94 144, 110 136, 124 139 C 138 142, 148 152, 153.5 161 Z"
                          fill="url(#sandShadow)" />

                    <!-- Trickling Stream -->
                    <path d="M 118.5 88 L 121.5 88 L 121 120 L 119 120 Z"
                          fill="#36b8c9" />

                    <!-- Sand in Top Bulb -->
                    <path d="M 114 97 C 111 92, 104 82, 98 75 C 112 77, 128 77, 142 75 C 136 82, 129 92, 126 97 Z"
                          fill="#52d2df"
                          opacity="0.9" />

                    <!-- Glass Outline -->
                    <path d="M 86 32 C 86 64, 111 86, 114 97 C 111 108, 86 130, 86 162 L 154 162 C 154 130, 129 108, 126 97 C 129 86, 154 64, 154 32 Z"
                          stroke="#7bb9c9"
                          stroke-width="3"
                          stroke-linejoin="round"
                          fill="none" />

                    <!-- Glass Gloss / Highlights -->
                    <path d="M 92 40 C 92 58, 103 76, 108 83"
                          stroke="#ffffff"
                          stroke-width="3"
                          stroke-linecap="round"
                          opacity="0.85" />
                    <path d="M 148 132 C 148 145, 144 153, 139 157"
                          stroke="#ffffff"
                          stroke-width="2.5"
                          stroke-linecap="round"
                          opacity="0.6" />

                    <!-- Top Cap -->
                    <rect x="76" y="20" width="88" height="15" rx="7.5" fill="url(#capGradient)" stroke="#537282" stroke-width="1.5" />
                    <rect x="80" y="22" width="80" height="3" rx="1.5" fill="#a4bfcf" opacity="0.65" />

                    <!-- Bottom Cap -->
                    <rect x="76" y="159" width="88" height="15" rx="7.5" fill="url(#capGradient)" stroke="#496574" stroke-width="1.5" />
                    <rect x="80" y="161" width="80" height="3" rx="1.5" fill="#95b2c2" opacity="0.65" />
                </g>
            </svg>
        </div>

        {{-- Countdown Indicator --}}
        <div class="inline-flex items-center gap-1.5 py-1.5 px-3.5 rounded-full bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 text-xs font-semibold text-amber-800 dark:text-amber-300 shadow-2xs">
            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" stroke-width="2"></circle>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"></path>
            </svg>
            <span>Redirecting to login in <strong id="idle-countdown-number" class="font-bold text-amber-700 dark:text-amber-400">5</strong>s</span>
        </div>

        {{-- Action Button --}}
        <div class="mt-5">
            <button type="button"
                    id="idle-modal-action-btn"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-5 rounded-xl sm:rounded-2xl text-sm font-semibold text-white bg-black hover:bg-neutral-900 active:scale-[0.99] shadow-md transition-all cursor-pointer">
                <span>{{ __('Log In Again') }}</span>
                <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>

        {{-- Micro footer note --}}
        <p class="mt-4 text-[11px] text-slate-400 dark:text-slate-500">
            Protected by RHU Silang Health Information Management System
        </p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let idleTime = 0;
        const timeoutMinutes = 15;
        let isExpired = false;
        let countdownTimer = null;
        let countdownSeconds = 5;

        const modalEl = document.getElementById('idle-timeout-modal');
        const modalCardEl = document.getElementById('idle-modal-card');
        const countdownEl = document.getElementById('idle-countdown-number');
        const actionBtn = document.getElementById('idle-modal-action-btn');

        let idleInterval = setInterval(timerIncrement, 60000); // Check every minute

        // Zero the idle timer on user activity
        ['mousemove', 'mousedown', 'keypress', 'DOMMouseScroll', 'mousewheel', 'touchmove', 'MSPointerMove'].forEach(event => {
            document.addEventListener(event, resetTimer, { passive: true });
        });

        function resetTimer() {
            if (!isExpired) {
                idleTime = 0;
            }
        }

        function timerIncrement() {
            if (isExpired) return;

            idleTime = idleTime + 1;
            if (idleTime >= timeoutMinutes) {
                triggerSessionExpiry();
            }
        }

        function triggerSessionExpiry() {
            if (isExpired) return;
            isExpired = true;
            clearInterval(idleInterval);

            // Invalidate session immediately on backend via fetch POST logout
            try {
                fetch('{{ route("logout") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                }).catch(() => {});
            } catch (e) {}

            // Display the beautiful modal popup instead of browser alert()
            showModal();
        }

        function showModal() {
            if (!modalEl) return;

            modalEl.classList.remove('hidden');
            modalEl.classList.add('flex');

            // Force reflow for smooth transition
            void modalEl.offsetWidth;

            modalEl.classList.remove('opacity-0');
            modalEl.classList.add('opacity-100');

            if (modalCardEl) {
                modalCardEl.classList.remove('scale-95');
                modalCardEl.classList.add('scale-100');
            }

            // Start 5-second countdown
            countdownSeconds = 5;
            if (countdownEl) countdownEl.textContent = countdownSeconds;

            countdownTimer = setInterval(() => {
                countdownSeconds--;
                if (countdownEl) countdownEl.textContent = countdownSeconds;

                if (countdownSeconds <= 0) {
                    clearInterval(countdownTimer);
                    redirectToLogin();
                }
            }, 1000);

            // Click listener
            if (actionBtn) {
                actionBtn.addEventListener('click', function () {
                    clearInterval(countdownTimer);
                    redirectToLogin();
                });
            }
        }

        function redirectToLogin() {
            window.location.href = '{{ route("logout") }}';
        }

        // Test helper for developer/verification
        window.__triggerSessionExpiredModal = triggerSessionExpiry;
    });
</script>
