import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (window.Echo) {
        window.Echo.channel('queues')
            .listen('QueueUpdated', (e) => {
                // Only auto-reload if the user is on a dashboard/queue page
                const path = window.location.pathname;
                const shouldReload = path.includes('dashboard') || path.includes('queue-overview') || path.includes('registration');
                
                // Create and show toast
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 animate-fade-in-up';
                toast.style.transition = 'all 0.5s ease-in-out';
                toast.innerHTML = `
                    <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold text-sm">System Update</p>
                        <p class="text-xs text-emerald-100">${e.message || 'Queue has been updated'}</p>
                    </div>
                `;
                document.body.appendChild(toast);
                
                if (shouldReload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2500);
                } else {
                    // Just remove the toast after a few seconds if we aren't reloading
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 500);
                    }, 4000);
                }
            });
    }
});
