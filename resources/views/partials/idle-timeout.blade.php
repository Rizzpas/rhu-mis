<!-- Idle Timeout Modal & Session Security Script -->
<div id="idle-timeout-modal"
     class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 transition-opacity duration-300 opacity-0"
     role="dialog"
     aria-modal="true"
     aria-labelledby="idle-modal-title">

    <div id="idle-modal-card"
         class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200/90 dark:border-slate-800 text-center relative overflow-hidden transform scale-95 transition-all duration-300">
        
        {{-- Top decorative gradient line --}}
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-amber-500 via-rose-500 to-amber-600"></div>

        {{-- Icon --}}
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200/80 dark:border-amber-800/60 flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-inner">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Title --}}
        <h3 id="idle-modal-title" class="font-display text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
            Session Expired
        </h3>

        {{-- Message --}}
        <p class="mt-2 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-sm mx-auto">
            Your session has expired due to 15 minutes of inactivity. For patient confidentiality and security (RA 10173), you have been signed out.
        </p>

        {{-- Countdown Indicator --}}
        <div class="mt-4 py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/70 dark:border-slate-700/70 inline-flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-400">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            <span>Redirecting to login in <strong id="idle-countdown-number" class="text-amber-600 dark:text-amber-400 font-mono text-sm font-bold">10</strong>s</span>
        </div>

        {{-- Action Button --}}
        <div class="mt-6">
            <button type="button"
                    id="idle-modal-action-btn"
                    class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 shadow-md shadow-emerald-900/10 active:scale-[0.99] transition-all cursor-pointer">
                <span>{{ __('Log In Again') }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        let countdownSeconds = 10;

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

            // Start 10-second countdown
            countdownSeconds = 10;
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
            // Submit fallback form if needed, or redirect directly to login
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';

            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }

        // Test helper for developer/verification
        window.__triggerSessionExpiredModal = triggerSessionExpiry;
    });
</script>
