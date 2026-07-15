<link rel="stylesheet" href="{{ asset('css/orgchart.css') }}">

<section class="mb-12 org-chart" id="org-chart">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight sm:text-4xl">
            Organizational Chart
        </h2>
        <p class="mt-4 text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
            RHU Main &mdash; Complete organizational structure of the Rural Health Unit.
        </p>
        <div class="mt-4 flex justify-center gap-3">
            <button class="oc-toggle-all" onclick="orgToggleAll(false)">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                Expand All
            </button>
            <button class="oc-toggle-all" onclick="orgToggleAll(true)">
                <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                Collapse All
            </button>
        </div>
    </div>

    {{-- ═══ HEAD ═══ --}}
    <div class="oc-connector-v">
        <div class="oc-card oc-head mx-auto">
            <div class="oc-title">Municipal Health Officer</div>
            <div class="oc-name" style="font-size:1.1rem">Jericho Joshua E. Palay, MD</div>
        </div>
        <div class="oc-vline" style="height:32px"></div>
    </div>

    {{-- ═══ MEDICAL STAFF ═══ --}}
    <div class="oc-connector-v mb-6">
        <div class="oc-card oc-medical mx-auto" style="min-width:280px">
            <div class="oc-title">Medical Staff (Direct Reports)</div>
            <div class="oc-med-list">
                <span class="oc-staff"><span class="dot" style="background:#3b82f6"></span>Angel Casapao, MD &ndash; Medical Specialist I</span>
                <span class="oc-staff"><span class="dot" style="background:#3b82f6"></span>Michelle Mae Brofas, MD &ndash; Medical Officer III</span>
                <span class="oc-staff"><span class="dot" style="background:#3b82f6"></span>Jebriel Allen Desacada, MD &ndash; Medical Officer III</span>
                <span class="oc-staff"><span class="dot" style="background:#3b82f6"></span>Junee Elleigh Oway, MD &ndash; Medical Officer II</span>
            </div>
        </div>
        <div class="oc-vline" style="height:32px"></div>
        {{-- Horizontal bar --}}
        <div style="width:100%;max-width:1100px;height:2px;background:#22c55e;margin:0 auto"></div>
    </div>

    {{-- ═══ FOUR DIVISIONS ═══ --}}
    <div class="oc-divisions-row px-4">

        {{-- ──── 1. PRIMARY HEALTH DIVISION ──── --}}
        <div class="oc-div-col">
            <div class="oc-card oc-division" onclick="orgToggle(this)">
                <div class="oc-name">1. Primary Health Division</div>
                <div class="oc-title">Preventive &amp; Emergency</div>
            </div>
            <div class="oc-children">
                {{-- Preventive Health Section --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Preventive Health Section</div>
                    </div>
                    <div class="oc-children">
                        @php
                        $prevUnits = [
                            ['NIP (National Immunization Program)', ['Razelle Bendo, RN (Nurse II)', 'Merlita Leyban', 'Kyle Jaydee Buklatin']],
                            ['HEPU', ['Jenalyn De Castro, RN (Nurse I)']],
                            ['Infectious Diseases', ['Elaine Mae Bayacal, RN (Nurse III)']],
                            ['Non-Communicable Diseases', ['Annaliza Marquina, RN (Nurse II)', 'Jenalyn De Castro, RN (Nurse I)', 'Mon Christian Maneja, RN (Nurse I)', 'Corazon Medina, RN (Honorarium)', 'Narissa Agustin', 'Cecilia Amagan', 'Ivan Casapao']],
                            ['Information', ['Emelita Vicente', 'Paoila Marie Anyayahan', 'Jazzie Mitzvah Calapati, RM', 'Judy Bayacal']],
                            ['TB Program', ['James Lee Ambojia, RN (Nurse II)', 'Edna Laureles', 'Nelson Malate', 'Neil Bryan Velando', 'Patricia Reyes']],
                            ['MESU', ['Roniben Garde, RN, MAN (Nurse IV)', 'Chaz Angelo Palumpon', 'Emiliano Asas']],
                            ['ABTC (Animal Bite Treatment)', ['Elaine Mae Bayacal, RN', 'Stanley Emelo', 'Maribel Ramos', 'Czar Ian Calaycay', 'Hafisudin Adil', 'Aiby Villavicencio']],
                            ['PhilHealth', ['Elaine Mae Bayacal, RN (Nurse III)', 'Kyle Jaydee Buklatin', 'Joana Marie Guanzon', 'Chaz Angelo Palumpon', 'Czar Ian Calaycay', 'Hafisudin Adil', 'Aiby Villavicencio', 'Stanley Emelo']],
                        ];
                        @endphp
                        @foreach($prevUnits as $u)
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">{{ $u[0] }}</div></div>
                            <div class="oc-staff-grid">
                                @foreach($u[1] as $s)<span class="oc-staff"><span class="dot"></span>{{ $s }}</span>@endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                {{-- Emergency First Aide Section --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Emergency First Aide Section</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Medic</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Roniben Garde, RN, MAN (Nurse IV)</span>
                                <span class="oc-staff"><span class="dot"></span>Mon Christian Maneja, RN (Nurse I)</span>
                            </div>
                        </div>
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Driver</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Edgar Bayan (Driver I)</span>
                                <span class="oc-staff"><span class="dot"></span>Redentor Mojica (Utility Worker II)</span>
                                <span class="oc-staff"><span class="dot"></span>Renato Loyola</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──── 2. MATERNAL & CHILD HEALTH CARE ──── --}}
        <div class="oc-div-col">
            <div class="oc-card oc-division" onclick="orgToggle(this)">
                <div class="oc-name">2. Maternal &amp; Child Health</div>
                <div class="oc-title">Family Health &amp; Midwifery</div>
            </div>
            <div class="oc-children">
                {{-- Family Planning --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Family Planning &amp; Reproductive Health</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-staff-grid">
                            <span class="oc-staff"><span class="dot"></span>Tristan Voltaire Eguia, RN (Nurse II)</span>
                            <span class="oc-staff"><span class="dot"></span>Maribel Ramos</span>
                            <span class="oc-staff"><span class="dot"></span>Vanessa Erika Amon (POP COM)</span>
                        </div>
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Maternal &amp; Child</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Razelle Bendo, RN (Nurse II)</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Child Health & Nutrition --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Child Health &amp; Nutrition</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Nutrition</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Cyrus James Navarro, RN (Nurse I)</span>
                                <span class="oc-staff"><span class="dot"></span>Charlene Paggao, RN (Nutrition Officer II)</span>
                            </div>
                        </div>
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Adolescent</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Jenalyn De Castro, RN (Nurse I)</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- BEmONC & Birthing --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">BEmONC &amp; Birthing Section</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Barangay Health Station Midwives</div></div>
                            <div class="oc-staff-grid">
                                @php
                                $midwives = ['Maria Mendoza, RM (Midwife III)','Zosima Aquino, RM (Midwife III)','Nena Cotoner, RM (Midwife II)','Engracia Dominguez, RM (Midwife II)','Felilia Marino, RM (Midwife II)','Evangeline Pulido, RM (Midwife II)','Emma Yaya, RM (Midwife II)','Charlene Gallardo, RM (Midwife II)','Lara Vanessa Beaton, RM (Midwife II)','Erlinda Videña, RM (Midwife II)','Anabelle Revilla, RM (Midwife I)','Anna Lissa Belardo, RM (Midwife I)','Silvestina Loyola, RM (Midwife I)','Ma. Dolores Lumagda, RM (Midwife I)','Merwinda Ignas, RM (Casual)','Charo Halili, RM (Casual)','Andrea Lei Javier, RM (Casual)','Vanessa Erika Amon, RM (Pop Com)','Marisa Seran'];
                                @endphp
                                @foreach($midwives as $m)<span class="oc-staff"><span class="dot"></span>{{ $m }}</span>@endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──── 3. ANCILLARY & ALLIED HEALTH ──── --}}
        <div class="oc-div-col">
            <div class="oc-card oc-division" onclick="orgToggle(this)">
                <div class="oc-name">3. Ancillary &amp; Allied Health</div>
                <div class="oc-title">Clinical Support</div>
            </div>
            <div class="oc-children">
                {{-- Dental --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Dental Section</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-staff-grid">
                            <span class="oc-staff"><span class="dot"></span>Sylvia Buen, DMD (Dentist III)</span>
                            <span class="oc-staff"><span class="dot"></span>Marilou Galang</span>
                        </div>
                    </div>
                </div>
                {{-- Pharmacy & Supplies --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Pharmacy &amp; Supplies</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Pharmacy</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Hannah Mae Josue, RPh (Pharmacist III)</span>
                                <span class="oc-staff"><span class="dot"></span>Mary Jane Anarna</span>
                                <span class="oc-staff"><span class="dot"></span>Elmer Belardo</span>
                                <span class="oc-staff"><span class="dot"></span>Noelyn Belen</span>
                            </div>
                        </div>
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Supplies</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Mark Anthony Sebastian</span>
                                <span class="oc-staff"><span class="dot"></span>Kelvin Reolalas</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Laboratory --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Laboratory Section</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Laboratory</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Evalyn Martin, RMT (MedTech III)</span>
                                <span class="oc-staff"><span class="dot"></span>Benessie Madlangsakay, RMT (MedTech II)</span>
                                <span class="oc-staff"><span class="dot"></span>Bettina Ramos, RMT (MedTech I)</span>
                                <span class="oc-staff"><span class="dot"></span>Diana Angela Mae Aquino, RMT (MedTech I)</span>
                                <span class="oc-staff"><span class="dot"></span>Shirleen Reyes, RRT</span>
                                <span class="oc-staff"><span class="dot"></span>Christina Kassandra Sesno</span>
                            </div>
                        </div>
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">X-Ray &amp; Ultrasound</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Celergene Pellerin, RRT (RadTech II)</span>
                                <span class="oc-staff"><span class="dot"></span>Ovielle Mardy Jose, RRT (RadTech I)</span>
                                <span class="oc-staff"><span class="dot"></span>Stephanie Ann Estrella, RRT (RadTech I)</span>
                                <span class="oc-staff"><span class="dot"></span>Kevin Saputil</span>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Sanitation --}}
                <div class="oc-section-block">
                    <div class="oc-card oc-section" onclick="orgToggle(this)">
                        <div class="oc-name">Sanitation &amp; Environment</div>
                    </div>
                    <div class="oc-children">
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Sanitation</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Aileen Del Barrio (Sanitary Inspector III)</span>
                                <span class="oc-staff"><span class="dot"></span>Maria Florinda Gonzalez, RN (Sanitary Inspector I)</span>
                                <span class="oc-staff"><span class="dot"></span>Katherine Ordonio</span>
                                <span class="oc-staff"><span class="dot"></span>Katherine Pallera</span>
                            </div>
                        </div>
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Dengue Prevention</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Rhonna Rhezza Jose, RN, MAN (Sanitary Inspector I)</span>
                            </div>
                        </div>
                        <div class="oc-unit-block">
                            <div class="oc-card oc-unit"><div class="oc-name">Peace &amp; Order</div></div>
                            <div class="oc-staff-grid">
                                <span class="oc-staff"><span class="dot"></span>Tristan Voltaire Eguia, RN (Nurse II)</span>
                                <span class="oc-staff"><span class="dot"></span>Maribel Ramos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ──── 4. ADMINISTRATIVE STAFF ──── --}}
        <div class="oc-div-col">
            <div class="oc-card oc-division" onclick="orgToggle(this)">
                <div class="oc-name">4. Administrative Staff</div>
                <div class="oc-title">Support &amp; Operations</div>
            </div>
            <div class="oc-children">
                <div class="oc-section-block">
                    <div class="oc-staff-grid" style="padding-top:12px">
                        <span class="oc-staff"><span class="dot"></span>Mark Anthony Sebastian</span>
                        <span class="oc-staff"><span class="dot"></span>Jacqueline Hapin</span>
                        <span class="oc-staff"><span class="dot"></span>Apple Toledo</span>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end divisions-row --}}
</section>

<script>
function orgToggle(el) {
    const children = el.nextElementSibling || el.parentElement.querySelector('.oc-children');
    if (!children || !children.classList.contains('oc-children')) {
        // try sibling
        let sib = el.nextElementSibling;
        while(sib) { if(sib.classList.contains('oc-children')){children=sib;break;} sib=sib.nextElementSibling; }
    }
    if (children && children.classList.contains('oc-children')) {
        children.classList.toggle('collapsed');
        el.classList.toggle('collapsed');
    }
}
function orgToggleAll(collapse) {
    document.querySelectorAll('#org-chart .oc-children').forEach(c => {
        collapse ? c.classList.add('collapsed') : c.classList.remove('collapsed');
    });
    document.querySelectorAll('#org-chart .oc-division, #org-chart .oc-section').forEach(el => {
        collapse ? el.classList.add('collapsed') : el.classList.remove('collapsed');
    });
}
</script>
