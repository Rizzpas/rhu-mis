import './bootstrap';
import 'flowbite';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// --- Navbar Mega Menu Dropdown Coordination & Chevron Animation ---
document.addEventListener('DOMContentLoaded', () => {
    const unitsBtn = document.getElementById('mega-menu-full-dropdown-button');
    const apptBtn = document.getElementById('mega-menu-appointment-dropdown-button');
    const updatesBtn = document.getElementById('mega-menu-updates-dropdown-button');

    const unitsMenu = document.getElementById('mega-menu-full-dropdown');
    const apptMenu = document.getElementById('mega-menu-appointment-dropdown');
    const updatesMenu = document.getElementById('mega-menu-updates-dropdown');

    const dropdowns = [
        { btn: unitsBtn, menu: unitsMenu },
        { btn: apptBtn, menu: apptMenu },
        { btn: updatesBtn, menu: updatesMenu },
    ];

    function syncState() {
        dropdowns.forEach(({ btn, menu }) => {
            if (btn && menu) {
                const isOpen = !menu.classList.contains('hidden');
                btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }
        });
    }

    // When clicking any button, close all other dropdowns
    dropdowns.forEach(({ btn, menu }) => {
        if (btn) {
            btn.addEventListener('click', () => {
                dropdowns.forEach(other => {
                    if (other.menu && other.menu !== menu && !other.menu.classList.contains('hidden')) {
                        other.menu.classList.add('hidden');
                        if (other.btn) other.btn.setAttribute('aria-expanded', 'false');
                    }
                });
                setTimeout(syncState, 50);
            });
        }
    });

    // Close when clicking outside navbar
    document.addEventListener('click', (e) => {
        const nav = document.querySelector('nav');
        if (nav && !nav.contains(e.target)) {
            dropdowns.forEach(({ btn, menu }) => {
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });

    // Observe changes on dropdown menus to keep rotation animated properly
    dropdowns.forEach(({ menu }) => {
        if (menu) {
            const observer = new MutationObserver(syncState);
            observer.observe(menu, { attributes: true, attributeFilter: ['class'] });
        }
    });
});

// --- Scroll Reveal (IntersectionObserver) ---
document.addEventListener('DOMContentLoaded', () => {
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.01, rootMargin: '50px 0px 50px 0px' });

        document.querySelectorAll('[data-reveal]').forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight) {
                el.classList.add('is-visible');
            } else {
                revealObserver.observe(el);
            }
        });

        // Re-observe after Alpine live-update refreshes content
        const announcementsContainer = document.querySelector('#announcements-container');
        if (announcementsContainer) {
            const mutationObserver = new MutationObserver(() => {
                announcementsContainer.querySelectorAll('[data-reveal]:not(.is-visible)').forEach(el => {
                    revealObserver.observe(el);
                });
            });
            mutationObserver.observe(announcementsContainer, { childList: true, subtree: true });
        }
    } else {
        // Reduced motion: make everything visible immediately
        document.querySelectorAll('[data-reveal]').forEach(el => el.classList.add('is-visible'));
    }
});


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
