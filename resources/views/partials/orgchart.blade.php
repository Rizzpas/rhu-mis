{{-- Modern Executive Leadership & On-Demand Departmental Directory --}}
@php
    $orgKicker = \App\Models\SiteSetting::get('org_kicker', 'Leadership & Governance');
    $orgTitle = \App\Models\SiteSetting::get('org_title', 'Organizational Structure & Leadership');
    $orgSubtitle = \App\Models\SiteSetting::get('org_subtitle', 'The dedicated healthcare administrators, medical doctors, nurses, midwives, and diagnostic specialists of Rural Health Unit — Silang, Cavite.');

    $mhoBadge = \App\Models\SiteSetting::get('mho_badge', 'Executive Head');
    $mhoSubbadge = \App\Models\SiteSetting::get('mho_subbadge', 'Head of Agency');
    $mhoName = \App\Models\SiteSetting::get('mho_name', 'Jericho Joshua E. Palay, MD');
    $mhoTitle = \App\Models\SiteSetting::get('mho_title', 'Municipal Health Officer');
    $mhoOversightTitle = \App\Models\SiteSetting::get('mho_oversight_title', 'Executive Oversight:');
    $mhoOversightDesc = \App\Models\SiteSetting::get('mho_oversight_desc', 'Clinical governance, health policy, and public healthcare across all 64 barangays.');
    $mhoImage = \App\Models\SiteSetting::get('mho_image');

    // Initials fallback for MHO
    $mhoWords = preg_split('/\s+/', trim($mhoName));
    $mhoInitials = '';
    foreach ($mhoWords as $w) {
        $cleaned = preg_replace('/[^a-zA-Z]/', '', $w);
        if (!empty($cleaned) && !in_array(strtoupper($cleaned), ['MD', 'DR', 'RN', 'DMD', 'RMT', 'RPH'])) {
            $mhoInitials .= strtoupper(substr($cleaned, 0, 1));
        }
    }
    $mhoInitials = substr($mhoInitials ?: 'JP', 0, 2);

    $medStaff = \App\Models\SiteSetting::getJson('org_medical_officers', [
        ['name' => 'Angel Casapao, MD', 'role' => 'Specialist I', 'initials' => 'AC'],
        ['name' => 'Michelle Mae Brofas, MD', 'role' => 'Medical Officer III', 'initials' => 'MB'],
        ['name' => 'Jebriel Allen Desacada, MD', 'role' => 'Medical Officer III', 'initials' => 'JD'],
        ['name' => 'Junee Elleigh Oway, MD', 'role' => 'Medical Officer II', 'initials' => 'JO']
    ]);

    $divisions = \App\Models\SiteSetting::getJson('org_divisions', [
        [
            'id' => 'primary',
            'number' => '1',
            'title' => 'Primary Health',
            'badge' => '28 Staff',
            'subtitle' => 'Immunization (NIP), Animal Bite, TB DOTS, Disease Surveillance & Emergency Transport',
            'accent' => 'emerald',
            'units' => [
                [
                    'title' => 'National Immunization (NIP)',
                    'category' => 'Immunization',
                    'lead_name' => 'Razelle Bendo, RN',
                    'lead_role' => 'Nurse II',
                    'members' => 'Merlita Leyban • Kyle Jaydee Buklatin'
                ],
                [
                    'title' => 'Animal Bite Treatment (ABTC)',
                    'category' => 'Specialized Clinic',
                    'lead_name' => 'Elaine Mae Bayacal, RN',
                    'lead_role' => 'Nurse',
                    'members' => 'Stanley Emelo • Maribel Ramos • Czar Ian Calaycay • Hafisudin Adil • Aiby Villavicencio'
                ],
                [
                    'title' => 'TB DOTS Clinic & Program',
                    'category' => 'Infectious Diseases',
                    'lead_name' => 'James Lee Ambojia, RN',
                    'lead_role' => 'Nurse II',
                    'members' => 'Edna Laureles • Nelson Malate • Neil Bryan Velando • Patricia Reyes'
                ],
                [
                    'title' => 'MESU & Disease Surveillance',
                    'category' => 'Epidemiology',
                    'lead_name' => 'Roniben Garde, RN, MAN',
                    'lead_role' => 'Nurse IV',
                    'members' => 'Elaine Mae Bayacal, RN (Nurse III) • Chaz Angelo Palumpon • Emiliano Asas'
                ],
                [
                    'title' => 'Non-Communicable Diseases',
                    'category' => 'Wellness & Lifestyle',
                    'lead_name' => 'Annaliza Marquina, RN',
                    'lead_role' => 'Nurse II',
                    'members' => 'Jenalyn De Castro, RN • Mon Christian Maneja, RN • Corazon Medina, RN • Narissa Agustin • Cecilia Amagan • Ivan Casapao'
                ],
                [
                    'title' => 'Medic Team & Transport',
                    'category' => 'Emergency Care',
                    'lead_name' => 'Edgar Bayan',
                    'lead_role' => 'Driver I',
                    'members' => 'Roniben Garde, RN, MAN • Mon Christian Maneja, RN • Redentor Mojica • Renato Loyola'
                ]
            ]
        ],
        [
            'id' => 'maternal',
            'number' => '2',
            'title' => 'Maternal & Child Health',
            'badge' => '24 Staff',
            'subtitle' => 'Family Planning, Child Nutrition, BEmONC Birthing & 19 Licensed Barangay Midwives',
            'accent' => 'teal',
            'units' => [
                [
                    'title' => 'Family Planning & Clinical Care',
                    'category' => 'Maternal Care',
                    'lead_name' => 'Tristan Voltaire Eguia, RN',
                    'lead_role' => 'Nurse II',
                    'members' => 'Razelle Bendo, RN (Maternal Health) • Maribel Ramos • Vanessa Erika Amon'
                ],
                [
                    'title' => 'Child & Adolescent Health',
                    'category' => 'Child Nutrition',
                    'lead_name' => 'Charlene Paggao, RN',
                    'lead_role' => 'Nutrition II',
                    'members' => 'Cyrus James Navarro, RN (Nurse I) • Jenalyn De Castro, RN (Adolescent Health)'
                ]
            ]
        ],
        [
            'id' => 'ancillary',
            'number' => '3',
            'title' => 'Ancillary & Allied Health',
            'badge' => '20 Staff',
            'subtitle' => 'Dental Care, Pharmacy Supplies, Medical Laboratory, X-Ray & Public Sanitation',
            'accent' => 'cyan',
            'units' => [
                [
                    'title' => 'Dental Clinic',
                    'category' => 'Oral Health',
                    'lead_name' => 'Sylvia Buen, DMD',
                    'lead_role' => 'Dentist III',
                    'members' => 'Marilou Galang'
                ],
                [
                    'title' => 'Pharmacy & Supplies',
                    'category' => 'Pharmacy',
                    'lead_name' => 'Hannah Mae Josue, RPh',
                    'lead_role' => 'Pharmacist III',
                    'members' => 'Mary Jane Anarna • Elmer Belardo • Noelyn Belen • Mark Anthony Sebastian'
                ],
                [
                    'title' => 'Laboratory & X-Ray',
                    'category' => 'Diagnostics',
                    'lead_name' => 'Evalyn Martin, RMT',
                    'lead_role' => 'MedTech III',
                    'members' => 'Benessie Madlangsakay, RMT (MedTech II) • Bettina Ramos, RMT • Diana Aquino, RMT • Celergene Pellerin, RRT (RadTech II)'
                ],
                [
                    'title' => 'Sanitation & Environment',
                    'category' => 'Public Health',
                    'lead_name' => 'Aileen Del Barrio',
                    'lead_role' => 'Inspector III',
                    'members' => 'Maria Florinda Gonzalez, RN (Inspector I) • Rhonna Rhezza Jose, RN, MAN'
                ]
            ]
        ],
        [
            'id' => 'admin',
            'number' => '4',
            'title' => 'Administrative Staff',
            'badge' => '3 Staff',
            'subtitle' => 'Institutional Governance, Records Management, Procurement & Public Assistance',
            'accent' => 'amber',
            'units' => []
        ]
    ]);

    $midwives = \App\Models\SiteSetting::getJson('org_midwives', [
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
    ]);

    $admins = \App\Models\SiteSetting::getJson('org_admins', [
        ['name' => 'Mark Anthony Sebastian', 'role' => 'Administrative Officer', 'initials' => 'MS'],
        ['name' => 'Jacqueline Hapin', 'role' => 'Administrative Support', 'initials' => 'JH'],
        ['name' => 'Apple Toledo', 'role' => 'Public Assistance & Records', 'initials' => 'AT']
    ]);

    $divMap = collect($divisions)->keyBy('id');
@endphp

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
            {{ $orgKicker }}
        </span>
        <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            {{ $orgTitle }}
        </h2>
        <p class="text-slate-600 dark:text-slate-400 mt-2 text-sm sm:text-base leading-relaxed">
            {{ $orgSubtitle }}
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
                    @if($mhoImage)
                        <img src="{{ asset($mhoImage) }}" alt="{{ $mhoName }}" 
                             class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover shadow-md shrink-0 ring-4 ring-emerald-500/10 border border-emerald-200 dark:border-emerald-800">
                    @else
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white flex items-center justify-center font-display text-xl sm:text-2xl font-extrabold shadow-md shrink-0 ring-4 ring-emerald-500/10">
                            {{ $mhoInitials }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            @if($mhoBadge)
                                <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider border border-emerald-300/50 dark:border-emerald-700/50">
                                    {{ $mhoBadge }}
                                </span>
                            @endif
                            @if($mhoSubbadge)
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $mhoSubbadge }}</span>
                            @endif
                        </div>
                        <h3 class="font-display font-extrabold text-lg sm:text-xl text-slate-900 dark:text-white">
                            {{ $mhoName }}
                        </h3>
                        <p class="text-xs sm:text-sm font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5">
                            {{ $mhoTitle }}
                        </p>
                    </div>
                </div>

                @if($mhoOversightDesc)
                    <div class="bg-white dark:bg-slate-800 rounded-xl px-3.5 py-2.5 border border-slate-200/80 dark:border-slate-700/70 text-xs text-slate-600 dark:text-slate-300 max-w-xs">
                        <span class="font-bold text-slate-900 dark:text-white block">{{ $mhoOversightTitle }}</span>
                        {{ $mhoOversightDesc }}
                    </div>
                @endif
            </div>

            {{-- Direct Clinical Reports: Doctors --}}
            <div class="pt-5">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Medical Officers & Clinical Specialists (Direct Reports)
                        </span>
                    </div>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium hidden sm:inline">{{ count($medStaff) }} Practicing Physicians</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($medStaff as $ms)
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs flex items-center gap-3"
                             x-show="matchesSearch('{{ addslashes($ms['name'] . ' ' . $ms['role']) }}')">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0 border border-emerald-200/60 dark:border-emerald-800/60">
                                {{ $ms['initials'] ?? 'DR' }}
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
            @php
                $colorStyles = [
                    'emerald' => [
                        'numBg' => 'bg-emerald-100 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300',
                        'badge' => 'text-emerald-700 dark:text-emerald-400 bg-emerald-100/60 dark:bg-emerald-950/60',
                        'hoverText' => 'group-hover:text-emerald-700 dark:group-hover:text-emerald-400',
                        'actionText' => 'text-emerald-700 dark:text-emerald-400',
                        'activeBorder' => 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/40 dark:bg-emerald-950/30'
                    ],
                    'teal' => [
                        'numBg' => 'bg-teal-100 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300',
                        'badge' => 'text-teal-700 dark:text-teal-400 bg-teal-100/60 dark:bg-teal-950/60',
                        'hoverText' => 'group-hover:text-teal-700 dark:group-hover:text-teal-400',
                        'actionText' => 'text-teal-700 dark:text-teal-400',
                        'activeBorder' => 'border-teal-500 ring-2 ring-teal-500/20 bg-teal-50/40 dark:bg-teal-950/30'
                    ],
                    'cyan' => [
                        'numBg' => 'bg-cyan-100 dark:bg-cyan-950/70 text-cyan-700 dark:text-cyan-300',
                        'badge' => 'text-cyan-700 dark:text-cyan-400 bg-cyan-100/60 dark:bg-cyan-950/60',
                        'hoverText' => 'group-hover:text-cyan-700 dark:group-hover:text-cyan-400',
                        'actionText' => 'text-cyan-700 dark:text-cyan-400',
                        'activeBorder' => 'border-cyan-500 ring-2 ring-cyan-500/20 bg-cyan-50/40 dark:bg-cyan-950/30'
                    ],
                    'amber' => [
                        'numBg' => 'bg-amber-100 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300',
                        'badge' => 'text-amber-700 dark:text-amber-400 bg-amber-100/60 dark:bg-amber-950/60',
                        'hoverText' => 'group-hover:text-amber-700 dark:group-hover:text-amber-400',
                        'actionText' => 'text-amber-700 dark:text-amber-400',
                        'activeBorder' => 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/40 dark:bg-amber-950/30'
                    ],
                ];
            @endphp

            @foreach($divisions as $dIdx => $div)
                @php
                    $acc = $div['accent'] ?? ($dIdx === 0 ? 'emerald' : ($dIdx === 1 ? 'teal' : ($dIdx === 2 ? 'cyan' : 'amber')));
                    $cs = $colorStyles[$acc] ?? $colorStyles['emerald'];

                    // Automatically count all staff in this division
                    $divStaffCount = 0;
                    if (!empty($div['units']) && is_array($div['units'])) {
                        foreach ($div['units'] as $u) {
                            if (!empty(trim($u['lead_name'] ?? ''))) $divStaffCount++;
                            if (!empty($u['members'])) {
                                $mList = is_array($u['members']) ? $u['members'] : explode('•', $u['members']);
                                $divStaffCount += count(array_filter(array_map('trim', $mList)));
                            }
                        }
                    }
                    if (($div['id'] ?? '') === 'maternal' && !empty($midwives) && is_array($midwives)) {
                        $divStaffCount += count(array_filter($midwives, fn($m) => !empty(trim($m['name'] ?? ''))));
                    }
                    if (($div['id'] ?? '') === 'admin' && !empty($admins) && is_array($admins)) {
                        $divStaffCount += count(array_filter($admins, fn($a) => !empty(trim($a['name'] ?? ''))));
                    }
                    $badgeText = $divStaffCount > 0 ? ($divStaffCount . ' Staff') : ($div['badge'] ?? '');
                @endphp
                <button @click="toggleDivision('{{ $div['id'] }}')" 
                        :class="expandedDivision === '{{ $div['id'] }}' ? '{{ $cs['activeBorder'] }}' : 'border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/60 hover:bg-slate-100/80 dark:hover:bg-slate-800'"
                        class="p-5 rounded-2xl border text-left transition-all duration-200 shadow-2xs group cursor-pointer flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <div class="w-9 h-9 rounded-xl {{ $cs['numBg'] }} flex items-center justify-center font-bold text-xs">
                                {{ $div['number'] ?? ($dIdx + 1) }}
                            </div>
                            @if(!empty($badgeText))
                                <span class="text-[10px] font-bold uppercase tracking-wider {{ $cs['badge'] }} px-2 py-0.5 rounded">
                                    {{ $badgeText }}
                                </span>
                            @endif
                        </div>
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white {{ $cs['hoverText'] }} transition-colors">
                            {{ $div['title'] }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                            {{ $div['subtitle'] }}
                        </p>
                    </div>
                    <div class="pt-4 mt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs font-semibold {{ $cs['actionText'] }}">
                        <span x-text="expandedDivision === '{{ $div['id'] }}' ? 'Hide Roster ▲' : 'View Staff Roster ▼'"></span>
                        <svg class="w-4 h-4 transform transition-transform" :class="expandedDivision === '{{ $div['id'] }}' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
            @endforeach
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
        @php
            $primaryDiv = $divMap['primary'] ?? null;
            $primaryUnits = $primaryDiv['units'] ?? [];
        @endphp
        <div x-show="(expandedDivision === 'primary' || searchQuery.trim().length >= 2)" class="space-y-4 mb-8">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs">1</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $primaryDiv['title'] ?? 'Primary Health' }} Roster</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($primaryUnits as $u)
                    <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                         x-show="matchesSearch('{{ addslashes($u['title'] . ' ' . ($u['category'] ?? '') . ' ' . ($u['lead_name'] ?? '') . ' ' . (is_array($u['members'] ?? null) ? implode(' ', $u['members']) : ($u['members'] ?? ''))) }}')">
                        @if(!empty($u['category']))
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 block mb-1">{{ $u['category'] }}</span>
                        @endif
                        <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">{{ $u['title'] }}</h5>
                        <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                            @if(!empty($u['lead_name']))
                                <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                    <span>{{ $u['lead_name'] }}</span>
                                    @if(!empty($u['lead_role']))
                                        <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.2 rounded">{{ $u['lead_role'] }}</span>
                                    @endif
                                </div>
                            @endif
                            @php
                                $unitMembers = is_array($u['members'] ?? null) ? $u['members'] : (is_string($u['members'] ?? null) ? array_filter(array_map('trim', explode('•', $u['members']))) : []);
                            @endphp
                            @if(!empty($unitMembers))
                                @foreach($unitMembers as $m)
                                    @if(trim($m) !== '')
                                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">{{ trim($m) }}</div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ────────────────────────────────────────────────────────────
             MATERNAL & CHILD HEALTH DIVISION DETAILS
        ──────────────────────────────────────────────────────────── --}}
        @php
            $maternalDiv = $divMap['maternal'] ?? null;
            $maternalUnits = $maternalDiv['units'] ?? [];
        @endphp
        <div x-show="(expandedDivision === 'maternal' || searchQuery.trim().length >= 2)" class="space-y-4 mb-8">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-xs">2</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $maternalDiv['title'] ?? 'Maternal & Child Health' }} Roster</h4>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                
                {{-- Clinical Units (Family Planning & Child Health) --}}
                <div class="lg:col-span-4 space-y-3.5">
                    @foreach($maternalUnits as $u)
                        <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                             x-show="matchesSearch('{{ addslashes($u['title'] . ' ' . ($u['category'] ?? '') . ' ' . ($u['lead_name'] ?? '') . ' ' . (is_array($u['members'] ?? null) ? implode(' ', $u['members']) : ($u['members'] ?? ''))) }}')">
                            @if(!empty($u['category']))
                                <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 block mb-1">{{ $u['category'] }}</span>
                            @endif
                            <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">{{ $u['title'] }}</h5>
                            <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                                @if(!empty($u['lead_name']))
                                    <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                        <span>{{ $u['lead_name'] }}</span>
                                        @if(!empty($u['lead_role']))
                                            <span class="text-[10px] font-semibold text-teal-700 dark:text-teal-400 bg-teal-50 dark:bg-teal-950/60 px-1.5 py-0.2 rounded">{{ $u['lead_role'] }}</span>
                                        @endif
                                    </div>
                                @endif
                                @php
                                    $unitMembers = is_array($u['members'] ?? null) ? $u['members'] : (is_string($u['members'] ?? null) ? array_filter(array_map('trim', explode('•', $u['members']))) : []);
                                @endphp
                                @if(!empty($unitMembers))
                                    @foreach($unitMembers as $m)
                                        @if(trim($m) !== '')
                                            <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">{{ trim($m) }}</div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Frontline Barangay Midwives Grid --}}
                <div class="lg:col-span-8 p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                     x-show="matchesSearch('Midwives Midwife BEmONC Birthing {{ addslashes(implode(' ', array_column($midwives, 'name'))) }}')">
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400 block">BEmONC Birthing Center</span>
                            <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">{{ count($midwives) }} Frontline Barangay Midwives</h5>
                        </div>
                    </div>

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
        @php
            $ancillaryDiv = $divMap['ancillary'] ?? null;
            $ancillaryUnits = $ancillaryDiv['units'] ?? [];
        @endphp
        <div x-show="(expandedDivision === 'ancillary' || searchQuery.trim().length >= 2)" class="space-y-4 mb-8">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 flex items-center justify-center font-bold text-xs">3</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $ancillaryDiv['title'] ?? 'Ancillary & Allied Health' }} Roster</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5">
                @foreach($ancillaryUnits as $u)
                    <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60"
                         x-show="matchesSearch('{{ addslashes($u['title'] . ' ' . ($u['category'] ?? '') . ' ' . ($u['lead_name'] ?? '') . ' ' . (is_array($u['members'] ?? null) ? implode(' ', $u['members']) : ($u['members'] ?? ''))) }}')">
                        @if(!empty($u['category']))
                            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700 dark:text-cyan-400 block mb-1">{{ $u['category'] }}</span>
                        @endif
                        <h5 class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white mb-2">{{ $u['title'] }}</h5>
                        <div class="space-y-1 text-xs text-slate-700 dark:text-slate-300">
                            @if(!empty($u['lead_name']))
                                <div class="flex items-center justify-between py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                                    <span>{{ $u['lead_name'] }}</span>
                                    @if(!empty($u['lead_role']))
                                        <span class="text-[10px] font-semibold text-cyan-700 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/60 px-1.5 py-0.2 rounded">{{ $u['lead_role'] }}</span>
                                    @endif
                                </div>
                            @endif
                            @php
                                $unitMembers = is_array($u['members'] ?? null) ? $u['members'] : (is_string($u['members'] ?? null) ? array_filter(array_map('trim', explode('•', $u['members']))) : []);
                            @endphp
                            @if(!empty($unitMembers))
                                @foreach($unitMembers as $m)
                                    @if(trim($m) !== '')
                                        <div class="py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">{{ trim($m) }}</div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ────────────────────────────────────────────────────────────
             ADMINISTRATIVE STAFF DIVISION DETAILS
        ──────────────────────────────────────────────────────────── --}}
        @php
            $adminDiv = $divMap['admin'] ?? null;
        @endphp
        <div x-show="(expandedDivision === 'admin' || searchQuery.trim().length >= 2)" class="space-y-4 mb-4">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80 dark:border-slate-800">
                <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-xs">4</div>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ $adminDiv['title'] ?? 'Administrative Staff' }} Roster</h4>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-2xl">
                @foreach($admins as $adm)
                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 flex items-center gap-3"
                         x-show="matchesSearch('{{ addslashes($adm['name'] . ' ' . $adm['role']) }}')">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold text-xs flex items-center justify-center shrink-0 border border-amber-200/60 dark:border-amber-800/60">
                            {{ $adm['initials'] ?? 'AD' }}
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
