{{-- Modern Executive Leadership & On-Demand Departmental Directory --}}
<section id="org-chart" class="transition-colors"
         x-data="{
    searchQuery: '',
    expandedDivision: null,
    toggleDivision(divId) {
        if (this.expandedDivision === divId) {
            this.expandedDivision = null;
        } else {
            this.expandedDivision = divId;
            this.$nextTick(() => {
                const el = document.getElementById('division-roster-panel');
                if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
            });
        }
    },
    closeRoster() {
        this.expandedDivision = null;
    },
    matchesSearch(text) {
        if (!this.searchQuery || this.searchQuery.trim().length < 2) return true;
        const q = this.searchQuery.toLowerCase().trim();
        return text.toLowerCase().includes(q);
    }
}">

    {{-- Section Header --}}
    <div class="text-center max-w-3xl mx-auto mb-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-md border border-emerald-300/50 dark:border-emerald-700/50 mb-3">
            <svg class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
            </svg>
            Leadership & Governance
        </span>
        <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            Organizational Structure & Leadership
        </h2>
        <p class="text-slate-600 dark:text-slate-400 mt-2 text-sm sm:text-base leading-relaxed">
            The dedicated healthcare administrators, medical doctors, nurses, midwives, and diagnostic specialists of Rural Health Unit — Silang, Cavite.
        </p>
    </div>

    {{-- ============================================================
         COMPACT EXECUTIVE LEADERSHIP SPOTLIGHT
    ============================================================ --}}
    <div class="mb-10">
        <div class="rounded-3xl border border-slate-200/90 dark:border-slate-800 bg-gradient-to-br from-slate-50 via-white to-emerald-50/30 dark:from-slate-800/80 dark:via-slate-800/60 dark:to-slate-900/90 p-6 sm:p-8 shadow-xs">
            
            {{-- Executive Head: Municipal Health Officer --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 pb-6 border-b border-slate-200/80 dark:border-slate-700/70">
                <div class="flex items-center gap-4 sm:gap-5">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white flex items-center justify-center font-display text-xl sm:text-2xl font-extrabold shadow-md shrink-0 ring-4 ring-emerald-500/10">
                        JP
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-300/50 dark:border-emerald-700/50">
                                Executive Head
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Head of Agency</span>
                        </div>
                        <h3 class="font-display font-extrabold text-lg sm:text-xl text-slate-900 dark:text-white">
                            Jericho Joshua E. Palay, MD
                        </h3>
                        <p class="text-xs sm:text-sm font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5">
                            Municipal Health Officer
                        </p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl px-3.5 py-2.5 border border-slate-200/80 dark:border-slate-700/70 text-xs text-slate-600 dark:text-slate-300 max-w-xs">
                    <span class="font-bold text-slate-900 dark:text-white block">Executive Oversight:</span>
                    Clinical governance, health policy, and public healthcare across all 64 barangays.
                </div>
            </div>

            {{-- Direct Clinical Reports: 4 Doctors --}}
            <div class="pt-5">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Medical Officers & Clinical Specialists (Direct Reports)
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium hidden sm:inline">4 Practicing Physicians</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @php
                        $medStaff = [
                            ['name' => 'Angel Casapao, MD', 'role' => 'Specialist I', 'initials' => 'AC'],
                            ['name' => 'Michelle Mae Brofas, MD', 'role' => 'Medical Officer III', 'initials' => 'MB'],
                            ['name' => 'Jebriel Allen Desacada, MD', 'role' => 'Medical Officer III', 'initials' => 'JD'],
                            ['name' => 'Junee Elleigh Oway, MD', 'role' => 'Medical Officer II', 'initials' => 'JO']
                        ];
                    @endphp
                    @foreach($medStaff as $ms)
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0 border border-emerald-200/60 dark:border-emerald-800/60">
                                {{ $ms['initials'] }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $ms['name'] }}</h4>
                                <p class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5 truncate">{{ $ms['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================
         SEARCH BAR & DEPARTMENT DIRECTORY CARDS (COLLAPSED BY DEFAULT)
    ============================================================ --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="font-display font-extrabold text-lg sm:text-xl text-slate-900 dark:text-white">
                    Operational Divisions & Units
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Select a department below to view its specific staff roster and clinical units.
                </p>
            </div>

            {{-- Live Search Input --}}
            <div class="relative w-full sm:w-72">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input x-model="searchQuery" 
                       type="text" 
                       placeholder="Quick search personnel..." 
                       class="w-full pl-10 pr-9 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition shadow-2xs"
                       id="org-search">
                <button x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 p-1 cursor-pointer"
                        aria-label="Clear search">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- 4 Division Selection Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Division 1 Card --}}
            <button @click="toggleDivision('primary')" 
                    :class="expandedDivision === 'primary' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/40 dark:bg-emerald-950/30' : 'border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800'"
                    class="p-5 rounded-2xl border text-left transition-all duration-200 shadow-2xs group cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs">
                            1
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100/60 dark:bg-emerald-950/60 px-2 py-0.5 rounded">
                            28 Staff
                        </span>
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                        Primary Health
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                        Immunization (NIP), Animal Bite, TB DOTS, Disease Surveillance & Emergency Transport
                    </p>
                </div>
                <div class="pt-4 mt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                    <span x-text="expandedDivision === 'primary' ? 'Hide Roster ▲' : 'View Staff Roster ▼'"></span>
                    <svg class="w-4 h-4 transform transition-transform" :class="expandedDivision === 'primary' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

            {{-- Division 2 Card --}}
            <button @click="toggleDivision('maternal')" 
                    :class="expandedDivision === 'maternal' ? 'border-teal-500 ring-2 ring-teal-500/20 bg-teal-50/40 dark:bg-teal-950/30' : 'border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800'"
                    class="p-5 rounded-2xl border text-left transition-all duration-200 shadow-2xs group cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-xs">
                            2
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 bg-teal-100/60 dark:bg-teal-950/60 px-2 py-0.5 rounded">
                            24 Staff
                        </span>
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-teal-700 dark:group-hover:text-teal-400 transition-colors">
                        Maternal & Child Health
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                        Family Planning, Child Nutrition, BEmONC Birthing & 19 Licensed Barangay Midwives
                    </p>
                </div>
                <div class="pt-4 mt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs font-semibold text-teal-700 dark:text-teal-400">
                    <span x-text="expandedDivision === 'maternal' ? 'Hide Roster ▲' : 'View Staff Roster ▼'"></span>
                    <svg class="w-4 h-4 transform transition-transform" :class="expandedDivision === 'maternal' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

            {{-- Division 3 Card --}}
            <button @click="toggleDivision('ancillary')" 
                    :class="expandedDivision === 'ancillary' ? 'border-cyan-500 ring-2 ring-cyan-500/20 bg-cyan-50/40 dark:bg-cyan-950/30' : 'border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800'"
                    class="p-5 rounded-2xl border text-left transition-all duration-200 shadow-2xs group cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-cyan-100 dark:bg-cyan-950/70 text-cyan-700 dark:text-cyan-300 flex items-center justify-center font-bold text-xs">
                            3
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-400 bg-cyan-100/60 dark:bg-cyan-950/60 px-2 py-0.5 rounded">
                            20 Staff
                        </span>
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-cyan-700 dark:group-hover:text-cyan-400 transition-colors">
                        Ancillary & Allied Health
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                        Dental Care, Pharmacy Supplies, Medical Laboratory, X-Ray & Public Sanitation
                    </p>
                </div>
                <div class="pt-4 mt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs font-semibold text-cyan-700 dark:text-cyan-400">
                    <span x-text="expandedDivision === 'ancillary' ? 'Hide Roster ▲' : 'View Staff Roster ▼'"></span>
                    <svg class="w-4 h-4 transform transition-transform" :class="expandedDivision === 'ancillary' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

            {{-- Division 4 Card --}}
            <button @click="toggleDivision('admin')" 
                    :class="expandedDivision === 'admin' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/40 dark:bg-amber-950/30' : 'border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800'"
                    class="p-5 rounded-2xl border text-left transition-all duration-200 shadow-2xs group cursor-pointer flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xs">
                            4
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400 bg-amber-100/60 dark:bg-amber-950/60 px-2 py-0.5 rounded">
                            3 Staff
                        </span>
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-amber-700 dark:group-hover:text-amber-400 transition-colors">
                        Administrative Staff
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                        Institutional Governance, Records Management, Procurement & Public Assistance
                    </p>
                </div>
                <div class="pt-4 mt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs font-semibold text-amber-700 dark:text-amber-400">
                    <span x-text="expandedDivision === 'admin' ? 'Hide Roster ▲' : 'View Staff Roster ▼'"></span>
                    <svg class="w-4 h-4 transform transition-transform" :class="expandedDivision === 'admin' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

        </div>
    </div>

    {{-- ============================================================
         ON-DEMAND EXPANDABLE PERSONNEL ROSTER (FOCUSED VIEW)
    ============================================================ --}}
    <div id="division-roster-panel" 
         x-show="expandedDivision !== null || searchQuery.trim().length >= 2" 
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="pt-6 border-t border-slate-200/80 dark:border-slate-800">
        
        {{-- Close / Dismiss Banner --}}
        <div class="flex items-center justify-between gap-4 mb-6 p-4 rounded-2xl bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/70">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-900 dark:text-white"
                      x-text="searchQuery.trim().length >= 2 ? 'Personnel Search Results' : 'Department Staff Directory'"></span>
                <span class="text-xs text-slate-500 dark:text-slate-400 hidden sm:inline"
                      x-show="expandedDivision !== null && searchQuery.trim().length < 2">
                    (Showing focused view)
                </span>
            </div>
            <button @click="closeRoster(); searchQuery = ''" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-600 transition shadow-2xs cursor-pointer">
                <span>✕ Close Roster</span>
            </button>
        </div>

        {{-- ────────────────────────────────────────────────────────────
             PRIMARY HEALTH DIVISION DETAILS
        ──────────────────────────────────────────────────────────── --}}
        <div x-show="(expandedDivision === 'primary' || searchQuery.trim().length >= 2)" class="space-y-4 mb-8">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs">1</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">Primary Health Division Roster</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                
                {{-- NIP --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('NIP National Immunization Program Razelle Bendo Merlita Leyban Kyle Jaydee Buklatin')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block mb-1">Immunization</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">National Immunization (NIP)</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Razelle Bendo, RN</span>
                            <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded">Nurse II</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Merlita Leyban</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Kyle Jaydee Buklatin</div>
                    </div>
                </div>

                {{-- ABTC --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('ABTC Animal Bite Treatment Elaine Mae Bayacal Stanley Emelo Maribel Ramos Czar Ian Calaycay Hafisudin Adil Aiby Villavicencio')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block mb-1">Specialized Clinic</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Animal Bite Treatment (ABTC)</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Elaine Mae Bayacal, RN</span>
                            <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded">Nurse</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Stanley Emelo • Maribel Ramos</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Czar Ian Calaycay • Hafisudin Adil • Aiby Villavicencio</div>
                    </div>
                </div>

                {{-- TB DOTS --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('TB Program DOTS James Lee Ambojia Edna Laureles Nelson Malate Neil Bryan Velando Patricia Reyes')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block mb-1">Infectious Diseases</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">TB DOTS Clinic & Program</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>James Lee Ambojia, RN</span>
                            <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded">Nurse II</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Edna Laureles • Nelson Malate</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Neil Bryan Velando • Patricia Reyes</div>
                    </div>
                </div>

                {{-- MESU --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('MESU Epidemiology Surveillance Roniben Garde Chaz Angelo Palumpon Emiliano Asas Elaine Mae Bayacal')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block mb-1">Epidemiology</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">MESU & Disease Surveillance</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Roniben Garde, RN, MAN</span>
                            <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded">Nurse IV</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Elaine Mae Bayacal, RN (Nurse III)</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Chaz Angelo Palumpon • Emiliano Asas</div>
                    </div>
                </div>

                {{-- NCD --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('NCD Non-Communicable Diseases Annaliza Marquina Jenalyn De Castro Mon Christian Maneja Corazon Medina Narissa Agustin Cecilia Amagan Ivan Casapao')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block mb-1">Wellness & Lifestyle</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Non-Communicable Diseases</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Annaliza Marquina, RN</span>
                            <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded">Nurse II</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Jenalyn De Castro, RN • Mon Christian Maneja, RN</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Corazon Medina, RN • Narissa Agustin • Cecilia Amagan • Ivan Casapao</div>
                    </div>
                </div>

                {{-- Emergency Medic & Transport --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('Emergency First Aid Medic Team Ambulance Transport Edgar Bayan Redentor Mojica Renato Loyola Roniben Garde Mon Christian Maneja')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block mb-1">Emergency Care</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Medic Team & Transport</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Roniben Garde, RN, MAN • Mon Christian Maneja, RN</div>
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Edgar Bayan</span>
                            <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-1.5 py-0.2 rounded">Driver I</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Redentor Mojica • Renato Loyola</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ────────────────────────────────────────────────────────────
             MATERNAL & CHILD HEALTH DIVISION DETAILS
        ──────────────────────────────────────────────────────────── --}}
        <div x-show="(expandedDivision === 'maternal' || searchQuery.trim().length >= 2)" class="space-y-4 mb-8">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-xs">2</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">Maternal & Child Health Division Roster</h4>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                
                {{-- Family Planning & Nutrition --}}
                <div class="lg:col-span-4 space-y-3.5">
                    <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                         x-show="matchesSearch('Family Planning Reproductive Health Tristan Voltaire Eguia Maribel Ramos Vanessa Erika Amon Razelle Bendo')">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 block mb-1">Maternal Care</span>
                        <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Family Planning & Clinical Care</h5>
                        <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                            <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                <span>Tristan Voltaire Eguia, RN</span>
                                <span class="text-[10px] font-semibold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-1.5 py-0.2 rounded">Nurse II</span>
                            </div>
                            <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Razelle Bendo, RN (Maternal Health)</div>
                            <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Maribel Ramos • Vanessa Erika Amon</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                         x-show="matchesSearch('Child Health Nutrition Cyrus James Navarro Charlene Paggao Jenalyn De Castro')">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 block mb-1">Child Nutrition</span>
                        <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Child & Adolescent Health</h5>
                        <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                            <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                <span>Charlene Paggao, RN</span>
                                <span class="text-[10px] font-semibold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-1.5 py-0.2 rounded">Nutrition II</span>
                            </div>
                            <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Cyrus James Navarro, RN (Nurse I)</div>
                            <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Jenalyn De Castro, RN (Adolescent Health)</div>
                        </div>
                    </div>
                </div>

                {{-- 19 Barangay Midwives Grid --}}
                <div class="lg:col-span-8 p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('Midwives Midwife BEmONC Birthing Maria Mendoza Zosima Aquino Nena Cotoner Engracia Dominguez Felilia Marino Evangeline Pulido Emma Yaya Charlene Gallardo Lara Vanessa Beaton Erlinda Videña Anabelle Revilla Anna Lissa Belardo Silvestina Loyola Ma. Dolores Lumagda Merwinda Ignas Charo Halili Andrea Lei Javier Vanessa Erika Amon Marisa Seran')">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 block">BEmONC Birthing Center</span>
                            <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">19 Frontline Barangay Midwives</h5>
                        </div>
                    </div>

                    @php
                        $midwives = [
                            ['name' => 'Maria Mendoza, RM', 'rank' => 'Midwife III'],
                            ['name' => 'Zosima Aquino, RM', 'rank' => 'Midwife III'],
                            ['name' => 'Nena Cotoner, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Engracia Dominguez, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Felilia Marino, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Evangeline Pulido, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Emma Yaya, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Charlene Gallardo, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Lara Vanessa Beaton, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Erlinda Videña, RM', 'rank' => 'Midwife II'],
                            ['name' => 'Anabelle Revilla, RM', 'rank' => 'Midwife I'],
                            ['name' => 'Anna Lissa Belardo, RM', 'rank' => 'Midwife I'],
                            ['name' => 'Silvestina Loyola, RM', 'rank' => 'Midwife I'],
                            ['name' => 'Ma. Dolores Lumagda, RM', 'rank' => 'Midwife I'],
                            ['name' => 'Merwinda Ignas, RM', 'rank' => 'Midwife'],
                            ['name' => 'Charo Halili, RM', 'rank' => 'Midwife'],
                            ['name' => 'Andrea Lei Javier, RM', 'rank' => 'Midwife'],
                            ['name' => 'Vanessa Erika Amon, RM', 'rank' => 'Midwife'],
                            ['name' => 'Marisa Seran', 'rank' => 'Staff']
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-1.5 mt-2">
                        @foreach($midwives as $mw)
                            <div class="flex items-center justify-between p-1.5 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60 text-xs"
                                 x-show="matchesSearch('{{ addslashes($mw['name'] . ' ' . $mw['rank']) }}')">
                                <span class="font-medium text-slate-800 dark:text-slate-200 truncate">{{ $mw['name'] }}</span>
                                <span class="text-[10px] font-bold text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/60 px-1.5 py-0.2 rounded shrink-0">
                                    {{ $mw['rank'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- ────────────────────────────────────────────────────────────
             ANCILLARY & ALLIED HEALTH DIVISION DETAILS
        ──────────────────────────────────────────────────────────── --}}
        <div x-show="(expandedDivision === 'ancillary' || searchQuery.trim().length >= 2)" class="space-y-4 mb-8">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 flex items-center justify-center font-bold text-xs">3</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">Ancillary & Allied Health Division Roster</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
                
                {{-- Dental --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('Dental Sylvia Buen Marilou Galang Dentist')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-400 block mb-1">Oral Health</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Dental Clinic</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Sylvia Buen, DMD</span>
                            <span class="text-[10px] font-semibold text-cyan-700 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/60 px-1.5 py-0.2 rounded">Dentist III</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Marilou Galang</div>
                    </div>
                </div>

                {{-- Pharmacy --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('Pharmacy Hannah Mae Josue Mary Jane Anarna Elmer Belardo Noelyn Belen Mark Anthony Sebastian Kelvin Reolalas')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-400 block mb-1">Pharmacy</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Pharmacy & Supplies</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Hannah Mae Josue, RPh</span>
                            <span class="text-[10px] font-semibold text-cyan-700 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/60 px-1.5 py-0.2 rounded">Pharmacist III</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Mary Jane Anarna • Elmer Belardo</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Noelyn Belen • Mark Anthony Sebastian</div>
                    </div>
                </div>

                {{-- Laboratory & Radiology --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('Laboratory Radiology Evalyn Martin Benessie Madlangsakay Bettina Ramos Diana Angela Mae Aquino Shirleen Reyes Christina Kassandra Sesno Celergene Pellerin Ovielle Mardy Jose Stephanie Ann Estrella Kevin Saputil')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-400 block mb-1">Diagnostics</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Laboratory & X-Ray</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Evalyn Martin, RMT</span>
                            <span class="text-[10px] font-semibold text-cyan-700 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/60 px-1.5 py-0.2 rounded">MedTech III</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Benessie Madlangsakay, RMT (MedTech II)</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Bettina Ramos, RMT • Diana Aquino, RMT</div>
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Celergene Pellerin, RRT</span>
                            <span class="text-[10px] font-semibold text-cyan-700 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/60 px-1.5 py-0.2 rounded">RadTech II</span>
                        </div>
                    </div>
                </div>

                {{-- Sanitation --}}
                <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('Sanitation Environment Aileen Del Barrio Maria Florinda Gonzalez Katherine Ordonio Katherine Pallera Rhonna Rhezza Jose Tristan Voltaire Eguia Maribel Ramos')">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-400 block mb-1">Public Health</span>
                    <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">Sanitation & Environment</h5>
                    <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                            <span>Aileen Del Barrio</span>
                            <span class="text-[10px] font-semibold text-cyan-700 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/60 px-1.5 py-0.2 rounded">Inspector III</span>
                        </div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Maria Florinda Gonzalez, RN (Inspector I)</div>
                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">Rhonna Rhezza Jose, RN, MAN</div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ────────────────────────────────────────────────────────────
             ADMINISTRATIVE STAFF DIVISION DETAILS
        ──────────────────────────────────────────────────────────── --}}
        <div x-show="(expandedDivision === 'admin' || searchQuery.trim().length >= 2)" class="space-y-4 mb-4">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xs">4</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">Administrative Staff Roster</h4>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-2xl">
                @php
                    $admins = [
                        ['name' => 'Mark Anthony Sebastian', 'role' => 'Administrative Officer', 'initials' => 'MS'],
                        ['name' => 'Jacqueline Hapin', 'role' => 'Administrative Support', 'initials' => 'JH'],
                        ['name' => 'Apple Toledo', 'role' => 'Public Assistance & Records', 'initials' => 'AT']
                    ];
                @endphp
                @foreach($admins as $adm)
                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center gap-3"
                         x-show="matchesSearch('{{ addslashes($adm['name'] . ' ' . $adm['role']) }}')">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold text-xs flex items-center justify-center shrink-0 border border-amber-200/60 dark:border-amber-800/60">
                            {{ $adm['initials'] }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h5 class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $adm['name'] }}</h5>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $adm['role'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</section>
