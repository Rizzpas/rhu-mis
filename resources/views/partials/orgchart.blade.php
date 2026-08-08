{{-- Org chart styles are inline/Tailwind — no external CSS dependency --}}
<style>
    /* Smooth expand/collapse via max-height */
    .oc-expand { overflow: hidden; max-height: 0; opacity: 0; transition: max-height 0.5s cubic-bezier(0.4,0,0.2,1), opacity 0.35s ease; }
    .oc-expand.is-open { max-height: 5000px; opacity: 1; }
    .oc-expand-chevron { transition: transform 0.3s ease; }
    .oc-expand-chevron.is-rotated { transform: rotate(180deg); }
    /* Connector lines */
    .oc-line-v { width: 2px; background: linear-gradient(to bottom, transparent, #16a34a 1rem, #16a34a calc(100% - 1rem), transparent); margin: 0 auto; }
    .oc-line-h { height: 2px; background: #16a34a; }
    /* Search highlight */
    .oc-search-match { background: #fef08a !important; border-color: #facc15 !important; }
    .dark .oc-search-match { background: rgba(250,204,21,0.2) !important; border-color: #ca8a04 !important; }
    /* Respect reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .oc-expand { transition: none !important; }
        .oc-expand-chevron { transition: none !important; }
    }
</style>

<section id="org-chart" x-data="{
    searchQuery: '',
    openDivisions: {},
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
    <div class="text-center mb-10" data-reveal>
        <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight" style="line-height: 1.1;">Organizational Chart</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-3 text-lg max-w-2xl mx-auto">Complete structure of the Rural Health Unit &mdash; Silang, Cavite</p>
        <div class="section-accent mx-auto mt-5"></div>
    </div>

    {{-- Search bar --}}
    <div class="max-w-md mx-auto mb-10" data-reveal>
        <div class="relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input x-model="searchQuery" type="text" placeholder="Search staff or department..." 
                   class="w-full pl-12 pr-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition shadow-sm"
                   id="org-search">
            <button x-show="searchQuery.length > 0" @click="searchQuery = ''" 
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1"
                    aria-label="Clear search">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- ═══ LEVEL 1: Municipal Health Officer ═══ --}}
    <div class="flex flex-col items-center" data-reveal>
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-2xl px-8 py-5 shadow-lg border-2 border-green-300/30 text-center max-w-sm w-full"
             :class="matchesSearch('Jericho Joshua Palay Municipal Health Officer') ? 'oc-search-match ring-2 ring-yellow-400' : ''">
            <p class="text-green-100 text-[11px] font-semibold uppercase tracking-wider mb-1">Municipal Health Officer</p>
            <p class="font-bold text-lg">Jericho Joshua E. Palay, MD</p>
        </div>
        <div class="oc-line-v h-8"></div>
    </div>

    {{-- ═══ LEVEL 1.5: Medical Staff (Direct Reports) ═══ --}}
    <div class="flex flex-col items-center mb-6" data-reveal>
        <div class="bg-white dark:bg-gray-800 rounded-xl border-l-4 border-l-blue-500 border border-gray-200 dark:border-gray-700 px-6 py-4 shadow-sm max-w-md w-full text-center"
             :class="matchesSearch('Medical Staff Angel Casapao Michelle Brofas Jebriel Desacada Junee Oway') ? 'oc-search-match' : ''">
            <p class="font-bold text-sm text-gray-900 dark:text-white mb-2">Medical Staff (Direct Reports)</p>
            <div class="flex flex-wrap justify-center gap-1.5">
                @php $medStaff = ['Angel Casapao, MD – Specialist I','Michelle Mae Brofas, MD – Officer III','Jebriel Allen Desacada, MD – Officer III','Junee Elleigh Oway, MD – Officer II']; @endphp
                @foreach($medStaff as $ms)
                    <span class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 border border-blue-100 dark:border-blue-800/30 rounded-full px-3 py-1 text-[11px] font-medium"
                          :class="matchesSearch('{{ addslashes($ms) }}') ? 'oc-search-match' : ''">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>{{ $ms }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="oc-line-v h-8"></div>
        {{-- Horizontal connector bar --}}
        <div class="hidden md:block w-full max-w-5xl h-[2px] bg-gradient-to-r from-transparent via-green-400 to-transparent"></div>
    </div>

    {{-- ═══ LEVEL 2: Four Divisions ═══ --}}
    @php
        $divisions = [
            ['id' => 'div1', 'name' => 'Primary Health Division', 'sub' => 'Preventive & Emergency', 'color' => 'green',
             'sections' => [
                ['id' => 's1a', 'name' => 'Preventive Health Section', 'units' => [
                    ['NIP (National Immunization Program)', ['Razelle Bendo, RN (Nurse II)', 'Merlita Leyban', 'Kyle Jaydee Buklatin']],
                    ['HEPU', ['Jenalyn De Castro, RN (Nurse I)']],
                    ['Infectious Diseases', ['Elaine Mae Bayacal, RN (Nurse III)']],
                    ['Non-Communicable Diseases', ['Annaliza Marquina, RN (Nurse II)', 'Jenalyn De Castro, RN (Nurse I)', 'Mon Christian Maneja, RN (Nurse I)', 'Corazon Medina, RN', 'Narissa Agustin', 'Cecilia Amagan', 'Ivan Casapao']],
                    ['Information', ['Emelita Vicente', 'Paoila Marie Anyayahan', 'Jazzie Mitzvah Calapati, RM', 'Judy Bayacal']],
                    ['TB Program', ['James Lee Ambojia, RN (Nurse II)', 'Edna Laureles', 'Nelson Malate', 'Neil Bryan Velando', 'Patricia Reyes']],
                    ['MESU', ['Roniben Garde, RN, MAN (Nurse IV)', 'Chaz Angelo Palumpon', 'Emiliano Asas']],
                    ['ABTC (Animal Bite Treatment)', ['Elaine Mae Bayacal, RN', 'Stanley Emelo', 'Maribel Ramos', 'Czar Ian Calaycay', 'Hafisudin Adil', 'Aiby Villavicencio']],
                    ['PhilHealth', ['Elaine Mae Bayacal, RN (Nurse III)', 'Kyle Jaydee Buklatin', 'Joana Marie Guanzon', 'Chaz Angelo Palumpon', 'Czar Ian Calaycay', 'Hafisudin Adil', 'Aiby Villavicencio', 'Stanley Emelo']],
                ]],
                ['id' => 's1b', 'name' => 'Emergency First Aide Section', 'units' => [
                    ['Medic', ['Roniben Garde, RN, MAN (Nurse IV)', 'Mon Christian Maneja, RN (Nurse I)']],
                    ['Driver', ['Edgar Bayan (Driver I)', 'Redentor Mojica (Utility Worker II)', 'Renato Loyola']],
                ]],
            ]],
            ['id' => 'div2', 'name' => 'Maternal & Child Health', 'sub' => 'Family Health & Midwifery', 'color' => 'pink',
             'sections' => [
                ['id' => 's2a', 'name' => 'Family Planning & Reproductive Health', 'units' => [
                    ['Direct Staff', ['Tristan Voltaire Eguia, RN (Nurse II)', 'Maribel Ramos', 'Vanessa Erika Amon (POP COM)']],
                    ['Maternal & Child', ['Razelle Bendo, RN (Nurse II)']],
                ]],
                ['id' => 's2b', 'name' => 'Child Health & Nutrition', 'units' => [
                    ['Nutrition', ['Cyrus James Navarro, RN (Nurse I)', 'Charlene Paggao, RN (Nutrition Officer II)']],
                    ['Adolescent', ['Jenalyn De Castro, RN (Nurse I)']],
                ]],
                ['id' => 's2c', 'name' => 'BEmONC & Birthing Section', 'units' => [
                    ['Barangay Health Station Midwives', ['Maria Mendoza, RM (Midwife III)','Zosima Aquino, RM (Midwife III)','Nena Cotoner, RM (Midwife II)','Engracia Dominguez, RM (Midwife II)','Felilia Marino, RM (Midwife II)','Evangeline Pulido, RM (Midwife II)','Emma Yaya, RM (Midwife II)','Charlene Gallardo, RM (Midwife II)','Lara Vanessa Beaton, RM (Midwife II)','Erlinda Videña, RM (Midwife II)','Anabelle Revilla, RM (Midwife I)','Anna Lissa Belardo, RM (Midwife I)','Silvestina Loyola, RM (Midwife I)','Ma. Dolores Lumagda, RM (Midwife I)','Merwinda Ignas, RM','Charo Halili, RM','Andrea Lei Javier, RM','Vanessa Erika Amon, RM','Marisa Seran']],
                ]],
            ]],
            ['id' => 'div3', 'name' => 'Ancillary & Allied Health', 'sub' => 'Clinical Support', 'color' => 'sky',
             'sections' => [
                ['id' => 's3a', 'name' => 'Dental Section', 'units' => [
                    ['Dental', ['Sylvia Buen, DMD (Dentist III)', 'Marilou Galang']],
                ]],
                ['id' => 's3b', 'name' => 'Pharmacy & Supplies', 'units' => [
                    ['Pharmacy', ['Hannah Mae Josue, RPh (Pharmacist III)', 'Mary Jane Anarna', 'Elmer Belardo', 'Noelyn Belen']],
                    ['Supplies', ['Mark Anthony Sebastian', 'Kelvin Reolalas']],
                ]],
                ['id' => 's3c', 'name' => 'Laboratory Section', 'units' => [
                    ['Laboratory', ['Evalyn Martin, RMT (MedTech III)', 'Benessie Madlangsakay, RMT (MedTech II)', 'Bettina Ramos, RMT (MedTech I)', 'Diana Angela Mae Aquino, RMT (MedTech I)', 'Shirleen Reyes, RRT', 'Christina Kassandra Sesno']],
                    ['X-Ray & Ultrasound', ['Celergene Pellerin, RRT (RadTech II)', 'Ovielle Mardy Jose, RRT (RadTech I)', 'Stephanie Ann Estrella, RRT (RadTech I)', 'Kevin Saputil']],
                ]],
                ['id' => 's3d', 'name' => 'Sanitation & Environment', 'units' => [
                    ['Sanitation', ['Aileen Del Barrio (Sanitary Inspector III)', 'Maria Florinda Gonzalez, RN (Sanitary Inspector I)', 'Katherine Ordonio', 'Katherine Pallera']],
                    ['Dengue Prevention', ['Rhonna Rhezza Jose, RN, MAN (Sanitary Inspector I)']],
                    ['Peace & Order', ['Tristan Voltaire Eguia, RN (Nurse II)', 'Maribel Ramos']],
                ]],
            ]],
            ['id' => 'div4', 'name' => 'Administrative Staff', 'sub' => 'Support & Operations', 'color' => 'amber',
             'sections' => [
                ['id' => 's4a', 'name' => 'Admin Team', 'units' => [
                    ['Staff', ['Mark Anthony Sebastian', 'Jacqueline Hapin', 'Apple Toledo']],
                ]],
            ]],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5" data-reveal>
        @foreach($divisions as $div)
            <div class="flex flex-col">
                {{-- Division header (Level 2) — clickable --}}
                <button @click="toggleDivision('{{ $div['id'] }}')" 
                        class="w-full text-left bg-gradient-to-r from-{{ $div['color'] }}-600 to-{{ $div['color'] }}-700 dark:from-{{ $div['color'] }}-700 dark:to-{{ $div['color'] }}-800 text-white rounded-xl px-5 py-4 shadow-md hover:shadow-lg transition-all flex items-center justify-between gap-3 min-h-[64px] group"
                        :class="matchesSearch('{{ addslashes($div['name'] . ' ' . $div['sub']) }}') ? 'ring-2 ring-yellow-400' : ''"
                        aria-expanded="false" :aria-expanded="isDivOpen('{{ $div['id'] }}')"
                        id="btn-{{ $div['id'] }}">
                    <div>
                        <p class="font-bold text-sm leading-tight">{{ $div['name'] }}</p>
                        <p class="text-{{ $div['color'] }}-200 text-[11px] font-medium mt-0.5">{{ $div['sub'] }}</p>
                    </div>
                    <svg class="w-5 h-5 shrink-0 oc-expand-chevron text-white/70" :class="isDivOpen('{{ $div['id'] }}') ? 'is-rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                {{-- Division children — collapsed by default --}}
                <div class="oc-expand mt-2 space-y-2 pl-3 border-l-2 border-{{ $div['color'] }}-200 dark:border-{{ $div['color'] }}-800/40"
                     :class="isDivOpen('{{ $div['id'] }}') || (searchQuery.length >= 2) ? 'is-open' : ''"
                     aria-labelledby="btn-{{ $div['id'] }}">

                    @foreach($div['sections'] as $sec)
                        {{-- Section header (Level 3) --}}
                        <div>
                            <button @click="toggleSection('{{ $sec['id'] }}')"
                                    class="w-full text-left bg-{{ $div['color'] }}-50 dark:bg-{{ $div['color'] }}-900/20 border border-{{ $div['color'] }}-100 dark:border-{{ $div['color'] }}-800/30 rounded-lg px-4 py-3 flex items-center justify-between gap-2 min-h-[48px] hover:bg-{{ $div['color'] }}-100 dark:hover:bg-{{ $div['color'] }}-900/30 transition-colors"
                                    :class="matchesSearch('{{ addslashes($sec['name']) }}') ? 'oc-search-match' : ''">
                                <span class="font-semibold text-{{ $div['color'] }}-800 dark:text-{{ $div['color'] }}-300 text-xs">{{ $sec['name'] }}</span>
                                <svg class="w-4 h-4 shrink-0 text-{{ $div['color'] }}-400 oc-expand-chevron" :class="isSecOpen('{{ $sec['id'] }}') || (searchQuery.length >= 2) ? 'is-rotated' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Section children (Level 4: staff rows) --}}
                            <div class="oc-expand pl-3 mt-1 space-y-1.5"
                                 :class="isSecOpen('{{ $sec['id'] }}') || (searchQuery.length >= 2) ? 'is-open' : ''">

                                @foreach($sec['units'] as $unit)
                                    @php $unitName = $unit[0]; $staff = $unit[1]; @endphp
                                    @if($unitName !== 'Direct Staff' && $unitName !== 'Staff')
                                        <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pt-2 pl-1"
                                           :class="matchesSearch('{{ addslashes($unitName) }}') ? 'oc-search-match rounded px-2 py-0.5' : ''">{{ $unitName }}</p>
                                    @endif
                                    @foreach($staff as $person)
                                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700/50 text-xs text-gray-700 dark:text-gray-300 hover:border-{{ $div['color'] }}-200 dark:hover:border-{{ $div['color'] }}-700/50 transition-colors"
                                             x-show="isVisible('{{ addslashes($person . ' ' . $unitName . ' ' . $sec['name'] . ' ' . $div['name']) }}')"
                                             :class="matchesSearch('{{ addslashes($person) }}') ? 'oc-search-match' : ''">
                                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $div['color'] }}-400 shrink-0"></span>
                                            {{ $person }}
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
