{{-- Modern 21st.dev Org Chart Component --}}
<style>
    /* Smooth expand/collapse via max-height */
    .oc-expand { overflow: hidden; max-height: 0; opacity: 0; transition: max-height 0.4s cubic-bezier(0.16,1,0.3,1), opacity 0.3s ease; }
    .oc-expand.is-open { max-height: 5000px; opacity: 1; }
    .oc-expand-chevron { transition: transform 0.25s cubic-bezier(0.16,1,0.3,1); }
    .oc-expand-chevron.is-rotated { transform: rotate(180deg); }
    /* Search highlight glow */
    .oc-search-match { background: rgba(250, 204, 21, 0.15) !important; border-color: rgba(250, 204, 21, 0.8) !important; box-shadow: 0 0 15px rgba(250, 204, 21, 0.25) !important; }
    .dark .oc-search-match { background: rgba(250, 204, 21, 0.2) !important; border-color: rgba(234, 179, 8, 0.8) !important; }
</style>

<section id="org-chart" class="rounded-3xl p-6 sm:p-10 bg-white/70 dark:bg-gray-900/70 border border-white/80 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)]"
         x-data="{
    searchQuery: '',
    openDivisions: { 'div1': true, 'div2': false, 'div3': false, 'div4': false },
    openSections: {},

    toggleDivision(id) {
        this.openDivisions[id] = !this.openDivisions[id];
    },
    toggleSection(id) {
        this.openSections[id] = !this.openSections[id];
    },
    isDivOpen(id) { return this.openDivisions[id] === true; },
    isSecOpen(id) { return this.openSections[id] === true; },
    matchesSearch(text) {
        if (!this.searchQuery || this.searchQuery.length < 2) return false;
        return text.toLowerCase().includes(this.searchQuery.toLowerCase());
    },
    isVisible(text) {
        if (!this.searchQuery || this.searchQuery.length < 2) return true;
        return text.toLowerCase().includes(this.searchQuery.toLowerCase());
    }
}">
    {{-- Header --}}
    <div class="text-center mb-8" data-reveal>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-full mb-3 uppercase tracking-wider border border-emerald-500/20">
            Leadership & Governance
        </span>
        <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
            Organizational Structure
        </h2>
        <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm sm:text-base max-w-xl mx-auto">
            Complete institutional hierarchy and dedicated medical personnel of the Rural Health Unit — Silang, Cavite
        </p>
    </div>

    {{-- Modern Search Bar --}}
    <div class="max-w-md mx-auto mb-12" data-reveal>
        <div class="relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input x-model="searchQuery" type="text" placeholder="Search staff by name, title, or department..." 
                   class="w-full pl-11 pr-10 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/90 text-gray-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition shadow-sm backdrop-blur-md"
                   id="org-search">
            <button x-show="searchQuery.length > 0" @click="searchQuery = ''" 
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 cursor-pointer"
                    aria-label="Clear search">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- ═══ LEVEL 1: Municipal Health Officer (Executive Card) ═══ --}}
    <div class="flex flex-col items-center mb-8" data-reveal>
        <div class="relative group rounded-3xl p-6 bg-gradient-to-br from-emerald-600 to-teal-800 text-white shadow-xl border-2 border-emerald-400/30 max-w-md w-full text-center overflow-hidden card-hover"
             :class="matchesSearch('Jericho Joshua Palay Municipal Health Officer') ? 'oc-search-match ring-2 ring-yellow-400' : ''">
            
            <div class="flex items-center justify-center gap-4 mb-2">
                <div class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center font-display text-xl font-bold shadow-inner">
                    JP
                </div>
                <div class="text-left">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-400/20 text-emerald-100 text-[10px] font-bold uppercase tracking-wider border border-emerald-300/30 mb-1">
                        Executive Head
                    </span>
                    <h3 class="font-display font-extrabold text-lg sm:text-xl">Jericho Joshua E. Palay, MD</h3>
                    <p class="text-emerald-100 text-xs font-medium">Municipal Health Officer</p>
                </div>
            </div>
        </div>

        {{-- Vertical connector line --}}
        <div class="w-0.5 h-8 bg-gradient-to-b from-emerald-500 to-emerald-400"></div>
    </div>

    {{-- ═══ LEVEL 1.5: Medical Staff (Direct Reports Bento Card) ═══ --}}
    <div class="flex flex-col items-center mb-10" data-reveal>
        <div class="rounded-2xl p-5 sm:p-6 bg-white/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 shadow-sm max-w-3xl w-full text-center"
             :class="matchesSearch('Medical Staff Angel Casapao Michelle Brofas Jebriel Desacada Junee Oway') ? 'oc-search-match' : ''">
            
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <p class="font-bold text-xs uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    Medical Officers & Specialists (Direct Reports)
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-left">
                @php 
                    $medStaff = [
                        ['name' => 'Angel Casapao, MD', 'role' => 'Specialist I', 'initials' => 'AC'],
                        ['name' => 'Michelle Mae Brofas, MD', 'role' => 'Medical Officer III', 'initials' => 'MB'],
                        ['name' => 'Jebriel Allen Desacada, MD', 'role' => 'Medical Officer III', 'initials' => 'JD'],
                        ['name' => 'Junee Elleigh Oway, MD', 'role' => 'Medical Officer II', 'initials' => 'JO']
                    ]; 
                @endphp
                @foreach($medStaff as $ms)
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50/80 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700 transition-colors hover:border-blue-300 dark:hover:border-blue-700"
                         :class="matchesSearch('{{ addslashes($ms['name'] . ' ' . $ms['role']) }}') ? 'oc-search-match' : ''">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-xs shrink-0">
                            {{ $ms['initials'] }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-xs text-gray-900 dark:text-white truncate">{{ $ms['name'] }}</p>
                            <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium truncate">{{ $ms['role'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Vertical connector line --}}
        <div class="w-0.5 h-8 bg-gradient-to-b from-emerald-400 to-transparent"></div>
    </div>

    {{-- ═══ LEVEL 2: Four Divisions (Modern 21st.dev Bento Grid) ═══ --}}
    @php
        $divisions = [
            ['id' => 'div1', 'name' => 'Primary Health Division', 'sub' => 'Preventive & Emergency Care', 'color' => 'emerald', 'accent_hex' => '#10b981',
             'sections' => [
                ['id' => 's1a', 'name' => 'Preventive Health Section', 'units' => [
                    ['NIP (National Immunization Program)', ['Razelle Bendo, RN (Nurse II)', 'Merlita Leyban', 'Kyle Jaydee Buklatin']],
                    ['HEPU (Health Education)', ['Jenalyn De Castro, RN (Nurse I)']],
                    ['Infectious Diseases Surveillance', ['Elaine Mae Bayacal, RN (Nurse III)']],
                    ['Non-Communicable Diseases (NCD)', ['Annaliza Marquina, RN (Nurse II)', 'Jenalyn De Castro, RN (Nurse I)', 'Mon Christian Maneja, RN (Nurse I)', 'Corazon Medina, RN', 'Narissa Agustin', 'Cecilia Amagan', 'Ivan Casapao']],
                    ['Information & Triage', ['Emelita Vicente', 'Paoila Marie Anyayahan', 'Jazzie Mitzvah Calapati, RM', 'Judy Bayacal']],
                    ['TB Program', ['James Lee Ambojia, RN (Nurse II)', 'Edna Laureles', 'Nelson Malate', 'Neil Bryan Velando', 'Patricia Reyes']],
                    ['MESU (Epidemiology)', ['Roniben Garde, RN, MAN (Nurse IV)', 'Chaz Angelo Palumpon', 'Emiliano Asas']],
                    ['ABTC (Animal Bite Treatment)', ['Elaine Mae Bayacal, RN', 'Stanley Emelo', 'Maribel Ramos', 'Czar Ian Calaycay', 'Hafisudin Adil', 'Aiby Villavicencio']],
                    ['PhilHealth Desk', ['Elaine Mae Bayacal, RN (Nurse III)', 'Kyle Jaydee Buklatin', 'Joana Marie Guanzon', 'Chaz Angelo Palumpon', 'Czar Ian Calaycay', 'Hafisudin Adil', 'Aiby Villavicencio', 'Stanley Emelo']],
                ]],
                ['id' => 's1b', 'name' => 'Emergency First Aid Section', 'units' => [
                    ['Medic Team', ['Roniben Garde, RN, MAN (Nurse IV)', 'Mon Christian Maneja, RN (Nurse I)']],
                    ['Ambulance & Transport', ['Edgar Bayan (Driver I)', 'Redentor Mojica (Utility Worker II)', 'Renato Loyola']],
                ]],
            ]],
            ['id' => 'div2', 'name' => 'Maternal & Child Health', 'sub' => 'Family Health & Midwifery', 'color' => 'pink', 'accent_hex' => '#ec4899',
             'sections' => [
                ['id' => 's2a', 'name' => 'Family Planning & Reproductive Health', 'units' => [
                    ['Direct Clinical Staff', ['Tristan Voltaire Eguia, RN (Nurse II)', 'Maribel Ramos', 'Vanessa Erika Amon (POP COM)']],
                    ['Maternal Health', ['Razelle Bendo, RN (Nurse II)']],
                ]],
                ['id' => 's2b', 'name' => 'Child Health & Nutrition', 'units' => [
                    ['Nutrition Action', ['Cyrus James Navarro, RN (Nurse I)', 'Charlene Paggao, RN (Nutrition Officer II)']],
                    ['Adolescent Health', ['Jenalyn De Castro, RN (Nurse I)']],
                ]],
                ['id' => 's2c', 'name' => 'BEmONC & Birthing Section', 'units' => [
                    ['Barangay Midwives Team', ['Maria Mendoza, RM (Midwife III)','Zosima Aquino, RM (Midwife III)','Nena Cotoner, RM (Midwife II)','Engracia Dominguez, RM (Midwife II)','Felilia Marino, RM (Midwife II)','Evangeline Pulido, RM (Midwife II)','Emma Yaya, RM (Midwife II)','Charlene Gallardo, RM (Midwife II)','Lara Vanessa Beaton, RM (Midwife II)','Erlinda Videña, RM (Midwife II)','Anabelle Revilla, RM (Midwife I)','Anna Lissa Belardo, RM (Midwife I)','Silvestina Loyola, RM (Midwife I)','Ma. Dolores Lumagda, RM (Midwife I)','Merwinda Ignas, RM','Charo Halili, RM','Andrea Lei Javier, RM','Vanessa Erika Amon, RM','Marisa Seran']],
                ]],
            ]],
            ['id' => 'div3', 'name' => 'Ancillary & Allied Health', 'sub' => 'Clinical Diagnostics & Support', 'color' => 'sky', 'accent_hex' => '#0ea5e9',
             'sections' => [
                ['id' => 's3a', 'name' => 'Dental Section', 'units' => [
                    ['Dental Care', ['Sylvia Buen, DMD (Dentist III)', 'Marilou Galang']],
                ]],
                ['id' => 's3b', 'name' => 'Pharmacy & Supplies', 'units' => [
                    ['Pharmacy Team', ['Hannah Mae Josue, RPh (Pharmacist III)', 'Mary Jane Anarna', 'Elmer Belardo', 'Noelyn Belen']],
                    ['Medical Supplies', ['Mark Anthony Sebastian', 'Kelvin Reolalas']],
                ]],
                ['id' => 's3c', 'name' => 'Laboratory & Radiology', 'units' => [
                    ['Medical Laboratory', ['Evalyn Martin, RMT (MedTech III)', 'Benessie Madlangsakay, RMT (MedTech II)', 'Bettina Ramos, RMT (MedTech I)', 'Diana Angela Mae Aquino, RMT (MedTech I)', 'Shirleen Reyes, RRT', 'Christina Kassandra Sesno']],
                    ['X-Ray & Ultrasound', ['Celergene Pellerin, RRT (RadTech II)', 'Ovielle Mardy Jose, RRT (RadTech I)', 'Stephanie Ann Estrella, RRT (RadTech I)', 'Kevin Saputil']],
                ]],
                ['id' => 's3d', 'name' => 'Sanitation & Environment', 'units' => [
                    ['Sanitary Inspection', ['Aileen Del Barrio (Sanitary Inspector III)', 'Maria Florinda Gonzalez, RN (Sanitary Inspector I)', 'Katherine Ordonio', 'Katherine Pallera']],
                    ['Dengue Vector Control', ['Rhonna Rhezza Jose, RN, MAN (Sanitary Inspector I)']],
                    ['Health & Safety', ['Tristan Voltaire Eguia, RN (Nurse II)', 'Maribel Ramos']],
                ]],
            ]],
            ['id' => 'div4', 'name' => 'Administrative Staff', 'sub' => 'Operations & Public Service', 'color' => 'amber', 'accent_hex' => '#f59e0b',
             'sections' => [
                ['id' => 's4a', 'name' => 'Administration & Support', 'units' => [
                    ['Administrative Officers', ['Mark Anthony Sebastian', 'Jacqueline Hapin', 'Apple Toledo']],
                ]],
            ]],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5" data-reveal>
        @foreach($divisions as $div)
            <div class="flex flex-col rounded-3xl p-5 bg-white/75 dark:bg-gray-800/75 border border-gray-200/70 dark:border-gray-700/70 backdrop-blur-md shadow-xs transition-all duration-200 hover:border-emerald-500/30">
                
                {{-- Division Header Card --}}
                <button @click="toggleDivision('{{ $div['id'] }}')" 
                        class="w-full text-left p-4 rounded-2xl text-white shadow-md transition-all flex items-center justify-between gap-3 cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $div['accent_hex'] }}, #0F3D3E);"
                        :class="matchesSearch('{{ addslashes($div['name'] . ' ' . $div['sub']) }}') ? 'ring-2 ring-yellow-400' : ''"
                        aria-expanded="false" :aria-expanded="isDivOpen('{{ $div['id'] }}')"
                        id="btn-{{ $div['id'] }}">
                    <div>
                        <p class="font-display font-extrabold text-sm leading-snug">{{ $div['name'] }}</p>
                        <p class="text-white/80 text-[11px] font-medium mt-0.5">{{ $div['sub'] }}</p>
                    </div>
                    <div class="w-7 h-7 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 oc-expand-chevron text-white" :class="isDivOpen('{{ $div['id'] }}') ? 'is-rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>

                {{-- Division Sections --}}
                <div class="oc-expand mt-3 space-y-2.5"
                     :class="isDivOpen('{{ $div['id'] }}') || (searchQuery.length >= 2) ? 'is-open' : ''"
                     aria-labelledby="btn-{{ $div['id'] }}">

                    @foreach($div['sections'] as $sec)
                        <div class="rounded-xl border border-gray-100 dark:border-gray-700/60 overflow-hidden bg-gray-50/60 dark:bg-gray-800/40">
                            
                            {{-- Section Header --}}
                            <button @click="toggleSection('{{ $sec['id'] }}')"
                                    class="w-full text-left px-3.5 py-2.5 flex items-center justify-between gap-2 hover:bg-gray-100/60 dark:hover:bg-gray-700/40 transition cursor-pointer"
                                    :class="matchesSearch('{{ addslashes($sec['name']) }}') ? 'oc-search-match' : ''">
                                <span class="font-bold text-gray-800 dark:text-gray-200 text-xs">{{ $sec['name'] }}</span>
                                <svg class="w-3.5 h-3.5 text-gray-400 oc-expand-chevron" :class="isSecOpen('{{ $sec['id'] }}') || (searchQuery.length >= 2) ? 'is-rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Staff Rows --}}
                            <div class="oc-expand px-3 pb-3 space-y-2"
                                 :class="isSecOpen('{{ $sec['id'] }}') || (searchQuery.length >= 2) ? 'is-open' : ''">

                                @foreach($sec['units'] as $unit)
                                    @php $unitName = $unit[0]; $staff = $unit[1]; @endphp
                                    
                                    <div class="pt-1.5">
                                        @if($unitName !== 'Direct Clinical Staff' && $unitName !== 'Staff' && $unitName !== 'Administrative Officers')
                                            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1"
                                               :class="matchesSearch('{{ addslashes($unitName) }}') ? 'oc-search-match rounded px-1.5 py-0.5' : ''">
                                                {{ $unitName }}
                                            </p>
                                        @endif

                                        <div class="space-y-1">
                                            @foreach($staff as $person)
                                                <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 text-xs text-gray-700 dark:text-gray-300 hover:border-emerald-300 dark:hover:border-emerald-700 transition-colors"
                                                     x-show="isVisible('{{ addslashes($person . ' ' . $unitName . ' ' . $sec['name'] . ' ' . $div['name']) }}')"
                                                     :class="matchesSearch('{{ addslashes($person) }}') ? 'oc-search-match' : ''">
                                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $div['accent_hex'] }};"></span>
                                                    <span class="truncate">{{ $person }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        @endforeach
    </div>
</section>
