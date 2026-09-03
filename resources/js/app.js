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
        const nav = document.getElementById('main-navbar');
        const isInsideDropdown = dropdowns.some(({ menu }) => menu && menu.contains(e.target));
        if (nav && !nav.contains(e.target) && !isInsideDropdown) {
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

// =========================================================================
// Global Custom Select Enhancement (Styled like "Edit Staff Member" modal)
// =========================================================================
function setupCustomSelect(select) {
    if (select.dataset.customSelectInitialized || select.multiple || select.size > 1) return;
    if (select.hasAttribute('data-no-custom') || select.classList.contains('no-custom-select')) return;
    
    select.dataset.customSelectInitialized = 'true';

    // Create wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'relative w-full custom-select-container';
    
    // Transfer spacing/width classes if needed
    if (select.classList.contains('mt-1')) wrapper.classList.add('mt-1');
    if (select.classList.contains('mb-2')) wrapper.classList.add('mb-2');
    if (select.classList.contains('mb-4')) wrapper.classList.add('mb-4');
    
    // Insert wrapper before select, then place select inside
    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    // Visually hide native select while keeping it functional for HTML5 form submissions & accessibility
    select.style.cssText = 'position: absolute !important; width: 1px !important; height: 1px !important; padding: 0 !important; margin: -1px !important; overflow: hidden !important; clip: rect(0,0,0,0) !important; white-space: nowrap !important; border: 0 !important; opacity: 0 !important; pointer-events: none !important;';

    // Trigger button
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer';

    const labelSpan = document.createElement('span');
    labelSpan.className = 'truncate';

    const chevronSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    chevronSvg.setAttribute('class', 'w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0 ml-2');
    chevronSvg.setAttribute('fill', 'none');
    chevronSvg.setAttribute('viewBox', '0 0 24 24');
    chevronSvg.setAttribute('stroke', 'currentColor');
    chevronSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';

    btn.appendChild(labelSpan);
    btn.appendChild(chevronSvg);
    wrapper.appendChild(btn);

    // Floating menu
    const menu = document.createElement('div');
    menu.className = 'absolute left-0 right-0 z-50 mt-1.5 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/15 p-1.5 custom-scrollbar backdrop-blur-md hidden';
    menu.style.display = 'none';
    wrapper.appendChild(menu);

    let isOpen = false;
    let searchQuery = '';

    function openMenu() {
        if (select.disabled) return;
        // Close all other open custom selects
        document.querySelectorAll('.custom-select-container').forEach(other => {
            if (other !== wrapper && other._closeCustomSelect) {
                other._closeCustomSelect();
            }
        });
        isOpen = true;
        menu.style.display = 'block';
        menu.classList.remove('hidden');
        btn.classList.add('border-teal-500', 'ring-2', 'ring-teal-500/20');
        chevronSvg.classList.add('rotate-180', 'text-teal-600', 'dark:text-teal-400');
        
        // Auto-focus search input if present
        const searchInput = menu.querySelector('.custom-select-search');
        if (searchInput) {
            setTimeout(() => searchInput.focus(), 50);
        }
    }

    function closeMenu() {
        isOpen = false;
        menu.style.display = 'none';
        menu.classList.add('hidden');
        btn.classList.remove('border-teal-500', 'ring-2', 'ring-teal-500/20');
        chevronSvg.classList.remove('rotate-180', 'text-teal-600', 'dark:text-teal-400');
        searchQuery = '';
    }

    wrapper._closeCustomSelect = closeMenu;

    btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (isOpen) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    function renderOptions() {
        menu.innerHTML = '';
        const options = Array.from(select.options);
        
        // Update label
        const selectedOpt = select.selectedIndex >= 0 ? select.options[select.selectedIndex] : null;
        if (selectedOpt && selectedOpt.text.trim()) {
            labelSpan.textContent = selectedOpt.text;
            if (!selectedOpt.value && select.selectedIndex === 0) {
                labelSpan.classList.add('text-slate-400', 'dark:text-slate-400');
                labelSpan.classList.remove('text-slate-900', 'dark:text-white');
            } else {
                labelSpan.classList.remove('text-slate-400', 'dark:text-slate-400');
                labelSpan.classList.add('text-slate-900', 'dark:text-white');
            }
        } else {
            labelSpan.textContent = 'Select...';
            labelSpan.classList.add('text-slate-400', 'dark:text-slate-400');
        }

        // Sync disabled state
        if (select.disabled) {
            btn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
        } else {
            btn.classList.remove('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
        }

        // Sync validation error state
        if (select.classList.contains('border-red-500') || select.classList.contains('ring-red-500')) {
            btn.classList.add('border-red-500', 'ring-1', 'ring-red-500');
        } else {
            btn.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
        }

        // Search input for lists with 8+ items
        if (options.length > 8) {
            const searchWrap = document.createElement('div');
            searchWrap.className = 'p-1 mb-1 border-b border-slate-100 dark:border-slate-700/60 sticky top-0 bg-white dark:bg-slate-800 z-10';
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Search options...';
            searchInput.value = searchQuery;
            searchInput.className = 'custom-select-search w-full px-2.5 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500';
            searchInput.addEventListener('click', (e) => e.stopPropagation());
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                itemsContainer.querySelectorAll('.custom-select-option').forEach(itemEl => {
                    const text = itemEl.getAttribute('data-text') || '';
                    if (!searchQuery || text.includes(searchQuery)) {
                        itemEl.style.display = 'flex';
                    } else {
                        itemEl.style.display = 'none';
                    }
                });
            });
            searchWrap.appendChild(searchInput);
            menu.appendChild(searchWrap);
        }

        const itemsContainer = document.createElement('div');
        itemsContainer.className = 'space-y-0.5';

        options.forEach(opt => {
            const isSelected = opt.selected;
            const item = document.createElement('div');
            item.className = 'custom-select-option px-3 py-2 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between cursor-pointer transition-colors ' +
                (isSelected
                    ? 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold'
                    : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60');
            
            item.setAttribute('data-text', opt.text.toLowerCase());
            if (searchQuery && !opt.text.toLowerCase().includes(searchQuery)) {
                item.style.display = 'none';
            }

            const span = document.createElement('span');
            span.textContent = opt.text;
            item.appendChild(span);

            if (isSelected) {
                const checkSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                checkSvg.setAttribute('class', 'w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0 ml-2');
                checkSvg.setAttribute('fill', 'none');
                checkSvg.setAttribute('viewBox', '0 0 24 24');
                checkSvg.setAttribute('stroke', 'currentColor');
                checkSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>';
                item.appendChild(checkSvg);
            }

            if (opt.disabled) {
                item.classList.add('opacity-40', 'cursor-not-allowed');
            } else {
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    select.value = opt.value;
                    select.dispatchEvent(new Event('input', { bubbles: true }));
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    closeMenu();
                    renderOptions();
                });
            }

            itemsContainer.appendChild(item);
        });

        menu.appendChild(itemsContainer);
    }

    // Initial render
    renderOptions();

    // Listen for select updates from Alpine x-model or script
    select.addEventListener('change', renderOptions);
    select.addEventListener('input', renderOptions);

    // Watch for dynamic changes (like Alpine x-for options, disabled, or validation classes)
    const observer = new MutationObserver(() => {
        renderOptions();
    });
    observer.observe(select, { childList: true, subtree: true, attributes: true, attributeFilter: ['disabled', 'class'] });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            closeMenu();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            closeMenu();
        }
    });
}

function initAllCustomSelects(root = document) {
    const selects = root.querySelectorAll('select:not([data-custom-select-initialized]):not([data-no-custom]):not(.no-custom-select)');
    selects.forEach(select => {
        // Skip multiple selects or selects already initialized
        if (select.multiple || select.size > 1) return;
        setupCustomSelect(select);
    });
}

// Run on page load
document.addEventListener('DOMContentLoaded', () => {
    initAllCustomSelects();

    // Watch for dynamically added select elements (modals, Alpine steps, AJAX)
    const bodyObserver = new MutationObserver((mutations) => {
        let shouldScan = false;
        mutations.forEach(m => {
            if (m.addedNodes.length) {
                m.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'SELECT' || node.querySelector?.('select')) {
                            shouldScan = true;
                        }
                    }
                });
            }
        });
        if (shouldScan) {
            initAllCustomSelects();
        }
    });

    bodyObserver.observe(document.body, { childList: true, subtree: true });
});

