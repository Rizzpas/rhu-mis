<!-- Account Menu Partial (Used in all user portal navigation bars) -->
<style>
    .account-avatar-btn.is-active,
    .account-avatar-btn[aria-expanded="true"] {
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8), 0 0 0 4px rgba(16, 185, 129, 0.4) !important;
    }
</style>
<div class="relative shrink-0"
    x-data="{ accountMenuOpen: false }"
    @click.outside="accountMenuOpen = false"
    @keydown.escape.window="if (accountMenuOpen) { accountMenuOpen = false; $nextTick(() => $refs.accountMenuBtn?.focus()); }">
    
    <!-- Avatar Button -->
    <button type="button"
        x-ref="accountMenuBtn"
        id="account-menu-button"
        @click="accountMenuOpen = !accountMenuOpen"
        :aria-expanded="accountMenuOpen.toString()"
        aria-haspopup="true"
        aria-label="Account menu"
        class="account-avatar-btn group relative w-10 h-10 rounded-full focus:outline-none transition-all duration-200 shrink-0 cursor-pointer flex items-center justify-center select-none"
        :class="accountMenuOpen ? 'ring-2 ring-white ring-offset-2 ring-offset-emerald-600 dark:ring-offset-emerald-800 scale-102 shadow-md' : 'ring-1 ring-white/30 hover:ring-white/80 hover:scale-105 active:scale-95 shadow-xs'">
        
        <!-- Profile Photo or Fallback to Initials -->
        <div class="w-full h-full rounded-full overflow-hidden flex items-center justify-center bg-gradient-to-br from-emerald-700 to-teal-800 dark:from-emerald-900 dark:to-teal-950 text-white font-bold text-xs shadow-inner">
            @if(auth()->user()->avatar_url)
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
            @else
                <span class="text-xs font-bold text-white tracking-wider">
                    {{ auth()->user()->initials }}
                </span>
            @endif
        </div>

        <!-- Sleek Floating Chevron Badge at bottom-right corner -->
        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-white dark:bg-slate-900 rounded-full ring-2 ring-emerald-600 dark:ring-emerald-800/95 flex items-center justify-center shadow-xs text-emerald-800 dark:text-emerald-300 pointer-events-none transition-transform duration-200"
              :class="accountMenuOpen ? 'rotate-180 scale-110 bg-emerald-50 dark:bg-emerald-950 text-emerald-700' : ''">
            <svg class="w-2.5 h-2.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </span>
    </button>

    <!-- Dropdown Card -->
    <div x-show="accountMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        x-cloak
        style="display: none;"
        class="absolute right-0 top-full mt-2.5 w-[330px] max-w-[calc(100vw-24px)] z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl shadow-slate-900/15 dark:shadow-black/60 border border-slate-200/80 dark:border-slate-800/80 p-2.5 focus:outline-none ring-1 ring-black/5 dark:ring-white/5"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="account-menu-button"
        tabindex="-1"
        @keydown.escape.stop="accountMenuOpen = false; $nextTick(() => $refs.accountMenuBtn?.focus());">
        
        <!-- Profile Header -->
        <div class="relative p-3.5 rounded-xl bg-gradient-to-br from-slate-50 via-slate-50/70 to-emerald-50/40 dark:from-slate-800/90 dark:via-slate-800/60 dark:to-emerald-950/30 border border-slate-200/70 dark:border-slate-700/60 mb-2 overflow-hidden">
            <!-- Subtle decorative ambient background glow -->
            <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-500/10 dark:bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative flex items-center gap-3">
                <!-- User Avatar in Header -->
                <div class="relative w-12 h-12 rounded-xl ring-2 ring-emerald-500/20 dark:ring-emerald-400/30 bg-emerald-700 dark:bg-emerald-950 flex items-center justify-center shrink-0 overflow-hidden shadow-xs">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-base font-bold text-white tracking-wider">
                            {{ auth()->user()->initials }}
                        </span>
                    @endif
                    <!-- Online status dot -->
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-slate-800"></span>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate leading-tight">
                        {{ auth()->user()->name ?? 'User' }}
                    </p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5 font-medium">
                        {{ auth()->user()->email }}
                    </p>
                    @php
                        $roleTitles = [
                            'super_admin' => 'Super Admin',
                            'admin' => 'Administrator',
                            'regular_doctor' => 'General Physician',
                            'pedia_doctor' => 'Pediatrician',
                            'clinical_nurse' => 'Clinical Nurse',
                            'vitals_nurse' => 'Vitals Nurse',
                            'information_desk' => 'Front Desk Officer',
                            'laboratory' => 'Medical Technologist',
                            'radiology' => 'Radiologic Technologist',
                            'pharmacy' => 'Pharmacist',
                        ];
                        $userRole = auth()->user()->role ?? '';
                        $displayRole = $roleTitles[$userRole] ?? ucwords(str_replace('_', ' ', $userRole ?: 'Staff Member'));
                    @endphp

                    @if(auth()->user()->hasRole('super_admin'))
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-purple-100/90 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 border border-purple-200/90 dark:border-purple-800/70 shadow-2xs">
                                <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 01.832.445l2.168 3.253 3.864.561a1 1 0 01.554 1.705l-2.796 2.725.66 3.848a1 1 0 01-1.451 1.054L10 13.788l-3.467 1.803a1 1 0 01-1.451-1.054l.66-3.848-2.796-2.725a1 1 0 01.554-1.705l3.864-.561L9.168 2.445A1 1 0 0110 2z" clip-rule="evenodd"/>
                                </svg>
                                <span>Super Admin</span>
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100/90 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 border border-emerald-200/90 dark:border-emerald-800/70 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>{{ $displayRole }}</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Menu Items -->
        <div class="space-y-1">
            <!-- Settings Row -->
            <a href="{{ route('profile.edit') }}"
                @click="accountMenuOpen = false"
                role="menuitem"
                class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white transition-all duration-150">
                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-teal-50 dark:group-hover:bg-teal-950/60 group-hover:text-teal-600 dark:group-hover:text-teal-400 group-hover:scale-105 transition-all duration-200 flex items-center justify-center shrink-0 border border-slate-200/50 dark:border-slate-700/50 shadow-2xs">
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="block leading-tight">{{ __('Settings') }}</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Profile, security & preferences</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-600 dark:group-hover:text-teal-400 group-hover:translate-x-0.5 transition-all shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <!-- Activity Log Row -->
            <a href="{{ route('profile.activity') }}"
                id="account-menu-activity"
                @click="accountMenuOpen = false"
                role="menuitem"
                class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('profile.activity') ? 'bg-teal-50/90 dark:bg-teal-950/60 text-teal-800 dark:text-teal-200 border border-teal-200/80 dark:border-teal-800/60' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="w-8 h-8 rounded-xl {{ request()->routeIs('profile.activity') ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 group-hover:bg-teal-50 dark:group-hover:bg-teal-950/60 group-hover:text-teal-600 dark:group-hover:text-teal-400 group-hover:scale-105' }} transition-all duration-200 flex items-center justify-center shrink-0 border border-slate-200/50 dark:border-slate-700/50 shadow-2xs">
                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="block leading-tight">{{ __('Activity log') }}</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-normal">Recent session actions</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-teal-600 dark:group-hover:text-teal-400 group-hover:translate-x-0.5 transition-all shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Subtle Divider -->
        <div class="my-1.5 border-t border-slate-100 dark:border-slate-800/80"></div>

        <!-- Logout Row -->
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                @click="accountMenuOpen = false"
                role="menuitem"
                class="group w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50/80 dark:hover:bg-rose-950/40 hover:text-rose-700 dark:hover:text-rose-300 transition-all duration-150 cursor-pointer text-left">
                <div class="w-8 h-8 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-500 dark:text-rose-400 group-hover:bg-rose-600 group-hover:text-white transition-all duration-200 flex items-center justify-center shrink-0 border border-rose-200/50 dark:border-rose-900/50 shadow-2xs">
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="block leading-tight font-bold">{{ __('Log out') }}</span>
                    <span class="text-[10px] text-rose-500/70 dark:text-rose-400/60 font-normal">End your current session</span>
                </div>
                <svg class="w-4 h-4 text-rose-400/60 group-hover:text-rose-600 dark:group-hover:text-rose-300 group-hover:translate-x-0.5 transition-all shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </form>
    </div>
</div>
