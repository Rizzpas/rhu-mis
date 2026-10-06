@extends('layouts.frontdesk')

@section('header', 'Patient Registration & Triage')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Breadcrumb Navigation -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 py-2 border-b border-slate-200/60 dark:border-slate-800" aria-label="Breadcrumb">
        <a href="{{ route('frontdesk.dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>
        <span class="text-slate-300 dark:text-slate-700">/</span>
        <span class="text-slate-700 dark:text-slate-300 font-semibold">Registration &amp; Triage Workstation</span>
    </nav>

    <!-- Header Section (Content Management Style) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold uppercase tracking-wider mb-2 border border-emerald-500/20">
                Frontline Clinical Intake
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </span>
                <span>Patient Registration &amp; Triage</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Search constituents, process walk-ins and appointments, auto-fill vitals, and assign patients to on-duty practitioners.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 text-xs font-semibold shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Real-time Triage Sync</span>
            </span>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 p-4 rounded-2xl shadow-xs" role="alert">
            <p class="font-bold mb-1 text-sm">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@php
    $demographicSexOptions = [
        [
            'value' => 'Male',
            'label' => 'Male',
            'sublabel' => 'Biological male',
            'badge' => '♂ Male',
            'badgeClass' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            'icon' => 'mars',
            'iconColor' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400',
        ],
        [
            'value' => 'Female',
            'label' => 'Female',
            'sublabel' => 'Biological female',
            'badge' => '♀ Female',
            'badgeClass' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'icon' => 'venus',
            'iconColor' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',
        ],
    ];

    $demographicCivilStatusOptions = [
        [
            'value' => 'Single',
            'label' => 'Single',
            'sublabel' => 'Never legally married',
            'badge' => 'Unmarried',
            'badgeClass' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border-sky-200 dark:border-sky-800',
            'icon' => 'user',
            'iconColor' => 'bg-sky-50 text-sky-600 dark:bg-sky-950/60 dark:text-sky-400',
        ],
        [
            'value' => 'Married',
            'label' => 'Married',
            'sublabel' => 'Legally married with spouse',
            'badge' => 'Coupled',
            'badgeClass' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
            'icon' => 'user',
            'iconColor' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400',
        ],
        [
            'value' => 'Separated',
            'label' => 'Separated',
            'sublabel' => 'Legally or de facto separated',
            'badge' => 'Separated',
            'badgeClass' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
            'icon' => 'user',
            'iconColor' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400',
        ],
        [
            'value' => 'Widowed',
            'label' => 'Widowed',
            'sublabel' => 'Surviving spouse',
            'badge' => 'Widowed',
            'badgeClass' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
            'icon' => 'user',
            'iconColor' => 'bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400',
        ],
    ];

    $demographicBloodTypeOptions = [
        [
            'value' => 'Unknown',
            'label' => 'Unknown / Not Tested',
            'sublabel' => 'To be determined via laboratory test',
            'badge' => 'Unverified',
            'badgeClass' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
            'icon' => 'drop',
            'iconColor' => 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
        ],
        [
            'value' => 'A+',
            'label' => 'A Positive (A+)',
            'sublabel' => 'A RhD positive',
            'badge' => 'A+',
            'badgeClass' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300 border-red-200 dark:border-red-800',
            'icon' => 'drop',
            'iconColor' => 'bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400',
        ],
        [
            'value' => 'A-',
            'label' => 'A Negative (A-)',
            'sublabel' => 'A RhD negative',
            'badge' => 'A-',
            'badgeClass' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'icon' => 'drop',
            'iconColor' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',
        ],
        [
            'value' => 'B+',
            'label' => 'B Positive (B+)',
            'sublabel' => 'B RhD positive',
            'badge' => 'B+',
            'badgeClass' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300 border-red-200 dark:border-red-800',
            'icon' => 'drop',
            'iconColor' => 'bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400',
        ],
        [
            'value' => 'B-',
            'label' => 'B Negative (B-)',
            'sublabel' => 'B RhD negative',
            'badge' => 'B-',
            'badgeClass' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'icon' => 'drop',
            'iconColor' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',
        ],
        [
            'value' => 'O+',
            'label' => 'O Positive (O+)',
            'sublabel' => 'Most common blood group',
            'badge' => 'O+',
            'badgeClass' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300 border-red-200 dark:border-red-800',
            'icon' => 'drop',
            'iconColor' => 'bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400',
        ],
        [
            'value' => 'O-',
            'label' => 'O Negative (O-)',
            'sublabel' => 'Universal donor red blood cells',
            'badge' => 'O-',
            'badgeClass' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'icon' => 'drop',
            'iconColor' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',
        ],
        [
            'value' => 'AB+',
            'label' => 'AB Positive (AB+)',
            'sublabel' => 'Universal plasma donor',
            'badge' => 'AB+',
            'badgeClass' => 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-300 border-red-200 dark:border-red-800',
            'icon' => 'drop',
            'iconColor' => 'bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400',
        ],
        [
            'value' => 'AB-',
            'label' => 'AB Negative (AB-)',
            'sublabel' => 'Rare blood phenotype',
            'badge' => 'AB-',
            'badgeClass' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'icon' => 'drop',
            'iconColor' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',
        ],
    ];

    $demographicClassificationOptions = [
        [
            'value' => 'Pediatric',
            'label' => 'Pediatric (Child / Minor)',
            'sublabel' => 'Ages 0 to 17 years old with Guardian',
            'badge' => 'Minor',
            'badgeClass' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/40 dark:text-pink-300 border-pink-200 dark:border-pink-700',
            'icon' => 'child',
            'iconColor' => 'bg-pink-50 text-pink-600 dark:bg-pink-950/60 dark:text-pink-400',
        ],
        [
            'value' => 'Regular Adult',
            'label' => 'Regular Adult',
            'sublabel' => 'Ages 18 to 59 years old',
            'badge' => 'Adult',
            'badgeClass' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            'icon' => 'user',
            'iconColor' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400',
        ],
        [
            'value' => 'Senior Citizen',
            'label' => 'Senior Citizen (60+)',
            'sublabel' => 'Express lane & priority care',
            'badge' => 'Priority',
            'badgeClass' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-700',
            'icon' => 'star',
            'iconColor' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400',
        ],
        [
            'value' => 'PWD',
            'label' => 'PWD (Person w/ Disability)',
            'sublabel' => 'Priority queue & special assistance',
            'badge' => 'Priority',
            'badgeClass' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700',
            'icon' => 'accessibility',
            'iconColor' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400',
        ],
    ];

    $demographicEducationOptions = [
        [
            'value' => 'N/A',
            'label' => 'Not Applicable (N/A)',
            'sublabel' => 'Minors or unassigned',
            'badge' => 'N/A',
            'badgeClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        ],
        [
            'value' => 'No Formal Education',
            'label' => 'No Formal Education',
            'sublabel' => 'Basic literacy attainment',
            'badge' => 'Basic',
            'badgeClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        ],
        [
            'value' => 'Primary Education (Elementary)',
            'label' => 'Primary Education (Elementary)',
            'sublabel' => 'Grade school completed or attended',
            'badge' => 'Primary',
            'badgeClass' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        ],
        [
            'value' => 'Secondary Education (High School)',
            'label' => 'Secondary Education (High School)',
            'sublabel' => 'Junior or Senior High School',
            'badge' => 'Secondary',
            'badgeClass' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
        ],
        [
            'value' => 'Vocational',
            'label' => 'Vocational / Trade Course',
            'sublabel' => 'Technical-vocational / TESDA certificate',
            'badge' => 'Tech-Voc',
            'badgeClass' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 border-teal-200 dark:border-teal-800',
        ],
        [
            'value' => 'College Undergraduate',
            'label' => 'College Undergraduate',
            'sublabel' => 'Tertiary units earned',
            'badge' => 'Undergrad',
            'badgeClass' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
        ],
        [
            'value' => 'College Graduate',
            'label' => 'College Graduate',
            'sublabel' => 'Completed Bachelor’s degree',
            'badge' => 'Graduate',
            'badgeClass' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
        ],
        [
            'value' => 'Post-Graduate',
            'label' => 'Post-Graduate (Master’s/Doctorate)',
            'sublabel' => 'Masteral, Doctorate, or Post-Doc degree',
            'badge' => 'Post-Grad',
            'badgeClass' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
        ],
    ];

    $demographicOccupationOptions = [
        [
            'value' => 'N/A',
            'label' => 'Not Applicable (N/A)',
            'sublabel' => 'Dependents, minors, or unassigned',
            'badge' => 'N/A',
            'badgeClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        ],
        [
            'value' => 'Student',
            'label' => 'Student',
            'sublabel' => 'Currently enrolled student',
            'badge' => 'Student',
            'badgeClass' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border-sky-200 dark:border-sky-800',
        ],
        [
            'value' => 'Employed',
            'label' => 'Employed',
            'sublabel' => 'Government or private sector employee',
            'badge' => 'Employed',
            'badgeClass' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
        ],
        [
            'value' => 'Self-Employed',
            'label' => 'Self-Employed',
            'sublabel' => 'Business owner, freelancer, contractor',
            'badge' => 'Business',
            'badgeClass' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
        ],
        [
            'value' => 'Unemployed',
            'label' => 'Unemployed',
            'sublabel' => 'Homemaker or currently seeking work',
            'badge' => 'Unemployed',
            'badgeClass' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        ],
        [
            'value' => 'Retired',
            'label' => 'Retired',
            'sublabel' => 'Pensioner or retired senior',
            'badge' => 'Retired',
            'badgeClass' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
        ],
        [
            'value' => 'Others',
            'label' => 'Others (Please specify)',
            'sublabel' => 'Enter custom occupation below',
            'badge' => 'Custom',
            'badgeClass' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
        ],
    ];

    $demographicReligionOptions = [
        [
            'value' => 'N/A',
            'label' => 'Not Applicable (N/A)',
            'sublabel' => 'No religious affiliation / secular',
            'badge' => 'N/A',
            'badgeClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        ],
        [
            'value' => 'Roman Catholic',
            'label' => 'Roman Catholic',
            'sublabel' => 'Catholic Church affiliation',
            'badge' => 'Catholic',
            'badgeClass' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        ],
        [
            'value' => 'Islam',
            'label' => 'Islam',
            'sublabel' => 'Islamic faith',
            'badge' => 'Muslim',
            'badgeClass' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
        ],
        [
            'value' => 'Iglesia ni Cristo',
            'label' => 'Iglesia ni Cristo',
            'sublabel' => 'INC community',
            'badge' => 'INC',
            'badgeClass' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800',
        ],
        [
            'value' => 'Born Again',
            'label' => 'Born Again Christian',
            'sublabel' => 'Evangelical / Protestant Christian',
            'badge' => 'Evangelical',
            'badgeClass' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
        ],
        [
            'value' => 'Adventist',
            'label' => 'Seventh-day Adventist',
            'sublabel' => 'Adventist Christian community',
            'badge' => 'SDA',
            'badgeClass' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 border-teal-200 dark:border-teal-800',
        ],
        [
            'value' => 'Aglipayan',
            'label' => 'Aglipayan (IFI)',
            'sublabel' => 'Iglesia Filipina Independiente',
            'badge' => 'IFI',
            'badgeClass' => 'bg-cyan-50 text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
        ],
        [
            'value' => 'Jehovah\'s Witnesses',
            'label' => 'Jehovah\'s Witnesses',
            'sublabel' => 'Watch Tower community',
            'badge' => 'JW',
            'badgeClass' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
        ],
        [
            'value' => 'Others',
            'label' => 'Others (Please specify)',
            'sublabel' => 'Enter custom religion below',
            'badge' => 'Custom',
            'badgeClass' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
        ],
    ];

    $silangBrgys = array_values(array_filter(
        array_unique(\App\Models\Patient::SILANG_BARANGAYS),
        fn($b) => !in_array($b, ['Biga 1', 'Biga 2', 'Biga Ii', 'Balite 1', 'Balite 2'])
    ));
    natcasesort($silangBrgys);
    $demographicBarangayOptions = collect($silangBrgys)->map(function ($brgy) {
        return [
            'value' => $brgy,
            'label' => $brgy,
            'sublabel' => 'Silang, Cavite',
        ];
    })->values()->all();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ mode: 'search', showDetailedForm: false }">
    <!-- Left Column: Search & Patient Selection -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Search Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden" 
             x-data="{
                 query: '{{ $search ?? '' }}',
                 results: [],
                 loading: false,
                 searched: false,
                 searchPatients() {
                     if (this.query.length < 1) {
                         this.results = [];
                         this.searched = false;
                         return;
                     }
                     this.loading = true;
                     this.searched = true;
                     fetch('{{ route('frontdesk.registration.search') }}?query=' + encodeURIComponent(this.query))
                         .then(res => res.json())
                         .then(data => {
                             this.results = data;
                             this.loading = false;
                         });
                 }
             }"
             x-init="if(query) { searchPatients(); }">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/60 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Find Patient</h3>
                </div>
                <a href="?new_patient=1" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>New Record</span>
                </a>
            </div>
            <div class="p-5">
                <div class="flex gap-2 relative">
                    <input type="text" x-model="query" @input.debounce.300ms="searchPatients()" placeholder="Name, PhilHealth, or Patient ID..." 
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950 text-slate-900 dark:text-white px-4 py-2.5 text-xs sm:text-sm focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 transition-all">
                    <div x-show="loading" class="absolute right-3.5 top-3" style="display: none;">
                        <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                <div x-show="searched" style="display: none;" class="mt-4 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <p x-show="results.length > 0" class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Search Results</p>
                        <button @click="query = ''; results = []; searched = false;" class="text-[11px] font-bold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">Clear</button>
                    </div>
                    
                    <template x-for="patient in results" :key="patient.id">
                        <div class="border border-slate-200/80 dark:border-slate-800 rounded-2xl p-3 hover:bg-emerald-50/40 dark:hover:bg-slate-800/60 hover:border-emerald-300 dark:hover:border-emerald-800 cursor-pointer transition-all flex justify-between items-center group shadow-2xs"
                             @click="window.location.href='?selected_id=' + patient.id + '&search=' + encodeURIComponent(query)">
                            <div>
                                <p class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors" x-text="patient.formatted_name"></p>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400" x-text="patient.classification"></span> 
                                    <template x-if="patient.is_follow_up">
                                        <span class="text-[9px] bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 px-1.5 py-0.2 rounded-full font-bold">FOLLOW-UP</span>
                                    </template>
                                    <span>· Age: <strong x-text="patient.age"></strong></span>
                                    <span>· DOB: <span x-text="patient.formatted_dob"></span></span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 group-hover:translate-x-0.5 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </template>
                    
                    <!-- No Results -->
                    <div x-show="results.length === 0 && !loading" style="display: none;" class="text-xs text-center py-6 text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                        <p class="font-semibold">No matching patients found.</p>
                        <button type="button" @click="window.location.href='?new_patient=1'" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline mt-1.5 inline-block">Register New Constituent →</button>
                    </div>
                </div>
                
                <div x-show="!searched" class="mt-4 text-center text-xs text-slate-500 dark:text-slate-400">
                    <p class="mb-3">Search by name, PhilHealth, or Patient ID to process a returning visit.</p>
                    <a href="?new_patient=1" class="inline-flex items-center justify-center gap-1.5 w-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/80 font-bold py-2.5 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-all text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Register New Patient</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- =====================================================
             PRE-TRIAGE WAITING POOL & APPOINTMENTS (Real-time Dynamic Auto-Refresh)
             ===================================================== --}}
        <div x-data="{
            preTriageWaiting: {{ Js::from($preTriageWaiting->map(function($pt) {
                return [
                    'id'            => $pt->id,
                    'patient_id'    => $pt->patient_id,
                    'patient_name'  => $pt->patient_name,
                    'classification'=> $pt->classification,
                    'dob'           => $pt->dob ? \Carbon\Carbon::parse($pt->dob)->format('M d, Y') : null,
                    'blood_pressure'=> $pt->blood_pressure,
                    'temperature'   => $pt->temperature,
                    'spo2'          => $pt->spo2,
                    'symptoms'      => $pt->symptoms ? \Illuminate\Support\Str::limit($pt->symptoms, 35) : null,
                    'appointment_id'=> $pt->appointment_id,
                    'is_follow_up'  => $pt->patient?->is_follow_up ?? false,
                    'created_at_human' => $pt->created_at->diffForHumans(),
                    'select_url'    => $pt->patient_id
                        ? url('/frontdesk/registration?selected_id='.$pt->patient_id.'&pre_triage_id='.$pt->id)
                        : url('/frontdesk/registration?new_from_triage='.$pt->id),
                    'cancel_url'    => route('triage.cancel', $pt->id),
                    'is_new'        => !$pt->patient_id,
                ];
            })) }},
            todayAppointments: {{ Js::from($todayAppointments->map(function($apt) {
                return [
                    'id'               => $apt->id,
                    'name'             => trim($apt->last_name.', '.$apt->first_name.' '.($apt->middle_name ?? '').' '.($apt->suffix ?? '')),
                    'dob'              => $apt->dob ? \Carbon\Carbon::parse($apt->dob)->format('M d, Y') : 'N/A',
                    'contact'          => $apt->contact_number,
                    'reference_number' => $apt->reference_number,
                    'preferred_time'   => $apt->preferred_time,
                    'status'           => $apt->status,
                    'type'             => $apt->type,
                    'is_follow_up'     => (bool) $apt->is_follow_up,
                    'cancellation_reason' => $apt->cancellation_reason,
                    'checkin_url'      => route('frontdesk.appointments.check-in', $apt),
                    'cancel_url'       => route('frontdesk.appointments.cancel', $apt),
                    'noshow_url'       => route('frontdesk.appointments.no-show', $apt),
                    'register_url'     => url('/frontdesk/registration?prefill_apt='.$apt->id.'&new_patient=1'),
                ];
            })) }},
            searchApt: '',
            isCancelModalOpen: false,
            cancelUrl: '',
            cancelName: '',
            cancelRef: '',
            cancelReason: 'Patient requested cancellation via phone/in-person',
            promptCancel(url, name, ref) {
                this.cancelUrl = url;
                this.cancelName = name;
                this.cancelRef = ref;
                this.cancelReason = 'Patient requested cancellation via phone/in-person';
                this.isCancelModalOpen = true;
            },
            closeCancelModal() {
                this.isCancelModalOpen = false;
                this.cancelUrl = '';
            },
            async fetchLiveQueue() {
                try {
                    const res = await fetch('{{ route('frontdesk.registration.queue-json') }}');
                    if (res.ok) {
                        const data = await res.json();
                        if (data.pre_triage) this.preTriageWaiting = data.pre_triage;
                        if (data.appointments) this.todayAppointments = data.appointments;
                    }
                } catch (e) {
                    console.error('Queue poll error:', e);
                }
            },
            init() {
                setInterval(() => this.fetchLiveQueue(), 3500);
            }
        }" class="space-y-6">

            <!-- Vitals Station Queue Card (Real-time Beacon) -->
            <template x-if="preTriageWaiting.length > 0">
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(16,185,129,0.08)] border-2 border-emerald-500/80 dark:border-emerald-500/60 overflow-hidden">
                    <div class="px-5 py-3.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                            </span>
                            <h3 class="text-white font-black text-xs sm:text-sm tracking-wide">Vitals Station Queue</h3>
                        </div>
                        <span class="text-[11px] font-extrabold bg-white/20 text-white px-2.5 py-0.5 rounded-full" x-text="preTriageWaiting.length + ' waiting'"></span>
                    </div>
                    <div class="px-4 py-2 bg-emerald-50/70 dark:bg-emerald-950/40 border-b border-emerald-100 dark:border-emerald-900/60 flex items-center gap-2 text-xs text-emerald-800 dark:text-emerald-300">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Vitals auto-carry to visit form. Click patient to process.</span>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto custom-scrollbar">
                        <template x-for="(pt, idx) in preTriageWaiting" :key="pt.id">
                            <div class="relative group">
                                <a :href="pt.select_url"
                                   class="block px-4 py-3 transition cursor-pointer pr-12 hover:bg-emerald-50/40 dark:hover:bg-slate-800/60">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                                <span class="text-xs font-black text-emerald-600 font-mono" x-text="'#' + (idx + 1)"></span>
                                                <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate" x-text="pt.patient_name"></span>
                                                
                                                <template x-if="pt.patient_id">
                                                    <span class="text-[10px] bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 px-2 py-0.5 rounded-full font-extrabold shrink-0">Returning</span>
                                                </template>
                                                <template x-if="!pt.patient_id">
                                                    <template x-if="pt.appointment_id">
                                                        <span class="text-[10px] bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-full font-extrabold shrink-0">Appointment</span>
                                                    </template>
                                                    <template x-if="!pt.appointment_id">
                                                        <span class="text-[10px] bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded-full font-extrabold shrink-0">New Patient</span>
                                                    </template>
                                                </template>
                                                
                                                <template x-if="pt.is_follow_up">
                                                    <span class="text-[10px] bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full font-extrabold shrink-0">Follow-up</span>
                                                </template>
                                                
                                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold shrink-0"
                                                      :class="{
                                                          'bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300': pt.classification === 'Senior',
                                                          'bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300': pt.classification === 'Pediatric',
                                                          'bg-orange-100 text-orange-700 dark:bg-orange-950 dark:text-orange-300': pt.classification === 'PWD',
                                                          'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300': !['Senior', 'Pediatric', 'PWD'].includes(pt.classification)
                                                      }"
                                                      x-text="pt.classification"></span>
                                            </div>
                                            <div class="flex items-center gap-2 flex-wrap text-[11px] text-slate-500 dark:text-slate-400">
                                                <template x-if="pt.blood_pressure"><span class="font-medium">BP: <strong class="text-slate-700 dark:text-slate-200" x-text="pt.blood_pressure"></strong></span></template>
                                                <template x-if="pt.temperature"><span class="font-medium">T: <strong class="text-slate-700 dark:text-slate-200" x-text="pt.temperature + '°C'"></strong></span></template>
                                                <template x-if="pt.spo2"><span class="font-medium">SpO₂: <strong class="text-slate-700 dark:text-slate-200" x-text="pt.spo2 + '%'"></strong></span></template>
                                                <template x-if="pt.symptoms"><span class="truncate italic text-slate-400 max-w-[160px]" x-text="pt.symptoms"></span></template>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1 font-medium">
                                                <span x-text="pt.created_at_human"></span> · 
                                                <span class="text-emerald-600 dark:text-emerald-400 font-bold group-hover:underline" x-text="pt.patient_id ? 'Process Visit →' : 'Register & Queue →'"></span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <form :action="pt.cancel_url" method="POST" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="button" 
                                            @click.prevent="window.dispatchEvent(new CustomEvent('open-confirmation', { 
                                                detail: { 
                                                    title: 'Remove Patient from Queue', 
                                                    message: 'Are you sure you want to dismiss this patient from the pre-triage queue?', 
                                                    action: pt.cancel_url, 
                                                    method: 'POST', 
                                                    confirmText: 'Yes, Remove' 
                                                } 
                                            }))"
                                            class="p-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg transition" title="Dismiss patient">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Today's Appointments Card -->
            <template x-if="todayAppointments.length > 0">
                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden">
                    <div class="px-5 py-3.5 bg-slate-50/60 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-teal-100/80 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20 shadow-2xs">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Today's Appointments</h3>
                        </div>
                        <span class="text-[10px] font-extrabold bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800 px-2 py-0.5 rounded-full" x-text="todayAppointments.length + ' total'"></span>
                    </div>
                    <div class="p-3 border-b border-slate-100 dark:border-slate-800">
                        <input type="text" x-model="searchApt" placeholder="Filter by patient name..." 
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 px-3 py-2 text-slate-800 dark:text-slate-100 focus:border-teal-500 transition-all">
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-80 overflow-y-auto custom-scrollbar">
                        <template x-for="apt in todayAppointments" :key="apt.id">
                            <div class="p-3.5 hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-all"
                                 x-show="!searchApt || apt.name.toLowerCase().includes(searchApt.toLowerCase())">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap mb-0.5">
                                            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate" x-text="apt.name"></span>
                                            <template x-if="apt.type === 'pedia'">
                                                <span class="text-[9px] bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 px-1.5 py-0.2 rounded-full font-bold shrink-0">PEDIA</span>
                                            </template>
                                            <template x-if="apt.is_follow_up">
                                                <span class="text-[9px] bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 px-1.5 py-0.2 rounded-full font-bold shrink-0">FOLLOW-UP</span>
                                            </template>
                                        </div>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                            <span x-text="'DOB: ' + apt.dob"></span>
                                            <template x-if="apt.contact">
                                                <span x-text="' · ' + apt.contact"></span>
                                            </template>
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5 font-mono">
                                            Ref: <span class="font-bold text-slate-600 dark:text-slate-300" x-text="apt.reference_number"></span>
                                            <template x-if="apt.preferred_time">
                                                <span class="ml-1 text-teal-600 dark:text-teal-400 font-sans font-bold" x-text="'· ' + apt.preferred_time"></span>
                                            </template>
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                                        <template x-if="apt.status === 'approved' || apt.status === 'rescheduled'">
                                            <div class="flex flex-col items-end gap-1">
                                                <form :action="apt.checkin_url" method="POST">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="text-[10px] font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-3 py-1.5 rounded-xl shadow-xs transition-all whitespace-nowrap cursor-pointer">
                                                        Check-In
                                                    </button>
                                                </form>
                                                <div class="flex items-center gap-1">
                                                    <form :action="apt.noshow_url" method="POST" onsubmit="return confirm('Mark this patient appointment as No-Show?');">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button type="submit" class="text-[9px] font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 px-1.5 py-0.5 rounded border border-rose-200 dark:border-rose-900 transition-all cursor-pointer">
                                                            No-Show
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="promptCancel(apt.cancel_url, apt.name, apt.reference_number)" class="text-[9px] font-bold text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="apt.status === 'arrived'">
                                            <span class="text-[10px] bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 font-extrabold px-2 py-0.5 rounded-full">Waiting Vitals</span>
                                        </template>
                                        <template x-if="apt.status === 'triaged'">
                                            <div class="flex flex-col items-end gap-1">
                                                <span class="text-[10px] bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 font-extrabold px-2 py-0.5 rounded-full">Vitals Done ✓</span>
                                                <a :href="apt.register_url" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 hover:underline whitespace-nowrap">
                                                    Queue Visit →
                                                </a>
                                            </div>
                                        </template>
                                        <template x-if="apt.status === 'registered'">
                                            <span class="text-[10px] bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-400 border border-teal-200 dark:border-teal-800 font-extrabold px-2 py-0.5 rounded-full">In Queue ✓</span>
                                        </template>
                                        <template x-if="apt.status === 'no_show'">
                                            <span class="text-[10px] bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-900 font-extrabold px-2 py-0.5 rounded-full">No-Show</span>
                                        </template>
                                        <template x-if="apt.status === 'cancelled'">
                                            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 font-extrabold px-2 py-0.5 rounded-full" :title="apt.cancellation_reason || 'Cancelled'">Cancelled</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Registration Staff Cancellation Modal -->
            <div x-show="isCancelModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-60 overflow-y-auto" 
                 style="display: none;">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeCancelModal()"></div>
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-white">Cancel Appointment</h3>
                                <p class="text-xs text-slate-400">Patient: <span class="font-bold text-slate-700 dark:text-slate-200" x-text="cancelName"></span> (<span x-text="cancelRef"></span>)</p>
                            </div>
                        </div>

                        <form :action="cancelUrl" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Reason for Cancellation</label>
                                <select x-model="cancelReason" class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2.5 text-slate-800 dark:text-slate-100 focus:border-rose-500 mb-2">
                                    <option value="Patient requested cancellation via phone/in-person">Patient requested cancellation</option>
                                    <option value="Duplicate or conflicting appointment">Duplicate / Conflicting booking</option>
                                    <option value="Patient unreachable / invalid contact">Patient unreachable</option>
                                    <option value="Facility schedule adjustment / Doctor unavailable">Facility schedule adjustment</option>
                                    <option value="custom">Other / Custom reason...</option>
                                </select>
                                <template x-if="cancelReason === 'custom'">
                                    <input type="text" name="reason" placeholder="Specify custom reason..." required
                                           class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2.5 text-slate-800 dark:text-slate-100 focus:border-rose-500">
                                </template>
                                <template x-if="cancelReason !== 'custom'">
                                    <input type="hidden" name="reason" :value="cancelReason">
                                </template>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-2">
                                <button type="button" @click="closeCancelModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                    Back
                                </button>
                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-rose-600/20 transition-all cursor-pointer">
                                    Confirm Cancellation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Queue Summary for Doctors -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden mb-6" x-data="{ qTab: 'ped' }">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/60">
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-indigo-100/80 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/20 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Active Queue Roster</h3>
                            <p class="text-[10px] text-slate-400">Patients in waiting line</p>
                        </div>
                    </div>
                    <span class="bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-[10px] px-2.5 py-0.5 rounded-full font-extrabold">{{ count($todaysPatients) }} Queued</span>
                </div>
                <!-- 4 Tabs -->
                <div class="flex flex-wrap gap-1.5 p-1 bg-slate-100 dark:bg-slate-950 rounded-2xl border border-slate-200/60 dark:border-slate-800">
                    <button @click="qTab = 'ped'" :class="qTab === 'ped' ? 'bg-white dark:bg-slate-800 text-emerald-700 dark:text-emerald-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" class="flex-1 px-2.5 py-1.5 rounded-xl text-xs transition cursor-pointer text-center">
                        Pedia
                    </button>
                    <button @click="qTab = 'sen'" :class="qTab === 'sen' ? 'bg-white dark:bg-slate-800 text-blue-700 dark:text-blue-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" class="flex-1 px-2.5 py-1.5 rounded-xl text-xs transition cursor-pointer text-center">
                        Senior
                    </button>
                    <button @click="qTab = 'pwd'" :class="qTab === 'pwd' ? 'bg-white dark:bg-slate-800 text-purple-700 dark:text-purple-400 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" class="flex-1 px-2.5 py-1.5 rounded-xl text-xs transition cursor-pointer text-center">
                        PWD
                    </button>
                    <button @click="qTab = 'reg'" :class="qTab === 'reg' ? 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 shadow-xs font-black' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'" class="flex-1 px-2.5 py-1.5 rounded-xl text-xs transition cursor-pointer text-center">
                        Regular
                    </button>
                </div>
            </div>
            <div class="p-0 overflow-x-auto max-h-96 overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
                        @forelse($todaysPatients as $consultation)
                            @php 
                                $cType = 'reg';
                                if($consultation->patient->classification === 'Pediatric') $cType = 'ped';
                                elseif($consultation->patient->classification === 'Senior Citizen') $cType = 'sen';
                                elseif($consultation->patient->classification === 'PWD') $cType = 'pwd';
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition" x-show="qTab === '{{ $cType }}'" x-cloak>
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $consultation->patient->full_name }}</p>
                                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold mb-1 font-mono">{{ $consultation->patient->patient_id }}</p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-1">Queue: <span class="font-extrabold text-slate-800 dark:text-slate-200 font-mono">{{ $consultation->queue_number ?? 'N/A' }}</span> <span class="mx-1">&bull;</span> {{ $consultation->created_at->format('h:i A') }}</p>
                                    <div class="text-[11px] flex items-center mt-1.5 gap-2">
                                        @if($consultation->doctor_id)
                                            <span class="bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-bold px-2 py-0.5 rounded-md border border-indigo-200 dark:border-indigo-800">Dr. {{ $consultation->doctor->name }}</span>
                                        @elseif($consultation->nurse_id)
                                            <span class="bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold px-2 py-0.5 rounded-md border border-blue-200 dark:border-blue-800">Nurse {{ $consultation->nurse->name }}</span>
                                        @else
                                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700">Triage Pool</span>
                                        @endif
                                        <span class="uppercase font-extrabold text-[10px] tracking-wider {{ $consultation->status === 'queued' ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $consultation->status }}</span>
                                    </div>
                                    @if($consultation->ancillaryRequests && $consultation->ancillaryRequests->count() > 0)
                                        <div class="mt-2 flex flex-wrap gap-1 items-center">
                                            @foreach($consultation->ancillaryRequests->take(2) as $anc)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $anc->status === 'Done' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $anc->status === 'Done' ? 'bg-emerald-500' : 'bg-purple-500 animate-pulse' }}"></span>
                                                    {{ $anc->test_name }}: {{ $anc->status }}
                                                </span>
                                            @endforeach
                                            @if($consultation->ancillaryRequests->count() > 2)
                                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">+{{ $consultation->ancillaryRequests->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="p-4 text-right">
                                    <a href="{{ route('frontdesk.patients.show', $consultation->patient) }}" class="inline-block bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 font-bold px-3 py-1.5 rounded-xl text-xs uppercase tracking-wider transition shadow-2xs">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="p-6 text-center text-xs text-slate-400">No patients queued yet today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Patients Card -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/60">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Recent Registrations</h3>
                        <p class="text-[10px] text-slate-400">Quickly select recent profiles</p>
                    </div>
                </div>
            </div>
            <div class="p-0 overflow-x-auto max-h-80 overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs sm:text-sm">
                        @forelse($recentPatients as $rp)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="p-3.5">
                                    <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">{{ $rp->full_name }}</p>
                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">{{ $rp->patient_id }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $rp->classification }} | {{ $rp->dob->age }} yrs</p>
                                </td>
                                <td class="p-3.5 text-right">
                                    <a href="?selected_id={{ $rp->patient_id }}" class="inline-block bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 font-bold px-3 py-1 rounded-xl text-xs transition">Select</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="p-6 text-center text-xs text-slate-400">No recent patients.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Registration / Visit Form -->
    <div class="lg:col-span-2 relative" id="right-panel-container">
        @php
            // Use controller-provided values; only override selectedPatient if query param present
            if (request('selected_id') && !$selectedPatient) {
                $selectedPatient = \App\Models\Patient::find(request('selected_id'));
            }
            $newlyRegisteredPatientId = session('new_patient_id');
            $newlyRegisteredPatient = $newlyRegisteredPatientId ? \App\Models\Patient::find($newlyRegisteredPatientId) : null;
            // $prefillApt is already set by controller via compact()
            $isNewPatient = request('new_patient') || $prefillApt || old('is_new_patient_form') || $newFromTriage ? true : false;
        @endphp

        <!-- Post-Registration Success Modal -->
        @if($newlyRegisteredPatient)
            <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm transition-opacity">
                <div @click.away="open = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform scale-100 transition-all border border-slate-200 dark:border-slate-800">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-5 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 backdrop-blur-md rounded-2xl p-2 border border-white/30">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black tracking-tight">Registration Complete</h3>
                                <p class="text-[11px] text-emerald-100 font-medium">Constituent profile registered</p>
                            </div>
                        </div>
                        <button @click="open = false" class="p-1.5 rounded-xl text-emerald-200 hover:text-white hover:bg-white/10 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-5 p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-500/20">
                                {{ $newlyRegisteredPatient->initials }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-slate-400 font-medium">Registered Constituent</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white truncate">{{ $newlyRegisteredPatient->full_name }}</p>
                                <p class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 font-bold mt-0.5">{{ $newlyRegisteredPatient->patient_id }}</p>
                            </div>
                        </div>
                        
                        <!-- Next Step Instruction -->
                        <div class="bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-2xl p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <div class="p-1.5 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-amber-900 dark:text-amber-200 uppercase tracking-wider">Mandatory Next Step</p>
                                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-1 leading-relaxed">
                                        Direct the patient to the <strong>Vitals Station</strong> to have baseline biometric readings taken. Once vitals are recorded, return to this screen to generate their consultation queue.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2.5">
                            <a href="?new_patient=1" class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md shadow-emerald-600/20 transition-all text-xs cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                <span>Register Another Patient</span>
                            </a>
                            
                            <button @click="open = false" type="button" class="w-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition text-xs cursor-pointer">
                                Dismiss Window
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Doctor Absent Modal (Follow-up Override) -->
        @if(session('doctor_absent'))
            <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
                <div @click.away="open = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform scale-100 transition-all border border-slate-200 dark:border-slate-800">
                    <!-- Header -->
                    <div class="bg-amber-600 px-6 py-5 flex items-center justify-between text-white">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white/20 backdrop-blur-md rounded-2xl border border-white/30">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black tracking-tight">Designated Doctor Absent</h3>
                                <p class="text-[11px] text-amber-100 font-medium">Follow-up re-routing required</p>
                            </div>
                        </div>
                        <button @click="open = false" class="p-1.5 rounded-xl text-amber-200 hover:text-white hover:bg-white/10 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed mb-4">
                            The designated follow-up doctor <strong class="text-slate-900 dark:text-white">Dr. {{ session('absent_doctor_name') }}</strong> is not clocked in today.
                        </p>
                        <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 mb-6">
                            Would you like to override the follow-up assignment and queue this patient to another available practitioner based on symptom severity?
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="open = false" type="button" class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold py-2.5 px-4 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition text-xs cursor-pointer">
                                Cancel
                            </button>
                            @if($selectedPatient)
                            <form action="{{ route('frontdesk.visits.store', $selectedPatient) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="pre_triage_id" value="{{ old('pre_triage_id') }}">
                                <input type="hidden" name="consultation_date" value="{{ old('consultation_date') }}">
                                <input type="hidden" name="symptom_severity" value="{{ old('symptom_severity') }}">
                                <input type="hidden" name="override_absent_doctor" value="1">
                                <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold py-2.5 px-4 rounded-xl shadow-md shadow-amber-600/20 transition text-xs cursor-pointer">
                                    Override &amp; Queue
                                </button>
                            </form>
                            @else
                            <button type="button" disabled class="flex-1 bg-slate-200 text-slate-400 font-bold py-2.5 px-4 rounded-xl text-xs cursor-not-allowed">No Patient Selected</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($selectedPatient)
            <!-- Returning Patient Visit Form -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80">
                <div class="p-5 bg-slate-50/60 dark:bg-slate-900/60 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-t-3xl">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-sm shrink-0 border border-emerald-500/20 shadow-2xs">
                            {{ $selectedPatient->initials }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">{{ $selectedPatient->full_name }}</h3>
                                <span class="text-[10px] font-mono font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded-lg">{{ $selectedPatient->patient_id }}</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                <span>{{ $selectedPatient->classification ?? 'Regular Adult' }}</span>
                                <span class="mx-1">·</span>
                                <span>{{ $selectedPatient->dob ? $selectedPatient->dob->age . ' yrs old' : 'Age N/A' }}</span>
                                <span class="mx-1">·</span>
                                <span>{{ $selectedPatient->sex }}</span>
                            </p>
                        </div>
                    </div>
                    <div>
                        @if($selectedPatient->consultations()->exists())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Returning Patient
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                First Consultation
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Separate Form for Updating Demographics -->
                <div x-data="{ 
                    editingInfo: false, 
                    classification: '{{ old('classification', $selectedPatient->classification) }}',
                    isSaving: false,
                    saveError: '',
                    saveSuccess: false,
                    submitEditForm(e) {
                        this.isSaving = true;
                        this.saveError = '';
                        this.saveSuccess = false;
                        
                        // Temporarily enable all fields inside the form so FormData can capture them
                        let form = e.target;
                        let disabledElements = form.querySelectorAll(':disabled');
                        disabledElements.forEach(el => el.disabled = false);
                        
                        let formData = new FormData(form);
                        
                        // Re-disable elements based on editingInfo
                        disabledElements.forEach(el => el.disabled = true);

                        fetch(form.action, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: formData
                        })
                        .then(async r => {
                            if(!r.ok) {
                                let res = await r.json().catch(() => ({}));
                                if(r.status === 422) {
                                    this.saveError = Object.values(res.errors || {}).flat().join('<br>');
                                } else {
                                    this.saveError = res.message || 'An error occurred while saving updates.';
                                }
                                this.isSaving = false;
                            } else {
                                // Success! Keep the updated values in the DOM and just turn off edit mode
                                this.editingInfo = false;
                                this.isSaving = false;
                                this.saveSuccess = true;
                                setTimeout(() => this.saveSuccess = false, 3500);
                            }
                        })
                        .catch(err => {
                            this.saveError = 'Network error. Could not connect to server.';
                            this.isSaving = false;
                        });
                    }
                }" class="p-6 border-b border-gray-100 dark:border-gray-700 mb-6 pb-6 relative">
                    
                    <!-- AJAX Success Toast -->
                    <div x-show="saveSuccess" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="absolute top-0 right-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50 flex items-start gap-3" role="alert">
                        <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <p class="font-bold text-sm">Updated Successfully</p>
                            <p class="text-xs">Patient information has been saved.</p>
                        </div>
                    </div>

                    <!-- AJAX Error Display -->
                    <div x-show="saveError" x-cloak class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                        <p class="font-bold">Please fix the following errors:</p>
                        <p x-html="saveError" class="text-sm mt-1"></p>
                    </div>

                    <form action="{{ route('frontdesk.patients.update', $selectedPatient) }}" method="POST" @submit.prevent="submitEditForm($event)">
                        @csrf
                        @method('PUT')
                        @if($prefillApt)
                            <input type="hidden" name="appointment_id" value="{{ $prefillApt->id }}">
                        @endif
                        <div class="flex justify-between items-center mb-5 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Patient Demographics (Permanent)</h4>
                            </div>
                            <button type="button" @click="editingInfo = !editingInfo" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer" :class="editingInfo ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950 border border-emerald-200/60 dark:border-emerald-800/60'">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                <span x-text="editingInfo ? 'Cancel Edits' : 'Edit Information'"></span>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="first_name" required value="{{ old('first_name', $selectedPatient->first_name ?? '') }}" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Middle Name (Optional)</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $selectedPatient->middle_name ?? '') }}" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Last Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="last_name" required value="{{ old('last_name', $selectedPatient->last_name ?? '') }}" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Suffix (Optional)</label>
                                <input type="text" name="suffix" value="{{ old('suffix', $selectedPatient->suffix ?? '') }}" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Sex <span class="text-rose-500">*</span></label>
                                <x-select name="sex" :options="$demographicSexOptions" :value="old('sex', $selectedPatient->sex ?? '')" placeholder="Select Sex..." x-bind:disabled="!editingInfo" />
                            </div>
                            <div x-show="classification !== 'Pediatric'" x-cloak>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Civil Status <span class="text-rose-500">*</span></label>
                                <x-select name="civil_status" :options="$demographicCivilStatusOptions" :value="old('civil_status', $selectedPatient->civil_status ?? '')" placeholder="Select Status..." x-bind:disabled="!editingInfo" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Blood Type <span class="text-rose-500">*</span></label>
                                <x-select name="blood_type" :options="$demographicBloodTypeOptions" :value="old('blood_type', $selectedPatient->blood_type ?? '')" placeholder="Select Blood Type..." x-bind:disabled="!editingInfo" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Classification <span class="text-rose-500">*</span></label>
                                <x-select name="classification" x-model="classification" :options="$demographicClassificationOptions" :value="old('classification', $selectedPatient->classification ?? '')" placeholder="Select Classification..." x-bind:disabled="!editingInfo" />
                            </div>
                            <div x-data="{
                                showDatepicker: false,
                                showMonthPicker: false,
                                showYearPicker: false,
                                yearSearch: '',
                                allYears: Array.from({length: 120}, (_, i) => new Date().getFullYear() - i),
                                get filteredYears() {
                                    if (!this.yearSearch || !this.yearSearch.trim()) return this.allYears;
                                    return this.allYears.filter(y => String(y).includes(this.yearSearch.trim()));
                                },
                                currentDate: new Date(),
                                selectedDate: '{{ old('dob', $selectedPatient->dob ? \Carbon\Carbon::parse($selectedPatient->dob)->format('Y-m-d') : '') }}',
                                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                get daysInMonth() {
                                    return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate();
                                },
                                get startDay() {
                                    return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                                },
                                setMonth(monthIndex) {
                                    this.currentDate = new Date(this.currentDate.getFullYear(), parseInt(monthIndex, 10), 1);
                                    this.showMonthPicker = false;
                                },
                                setYear(year) {
                                    this.currentDate = new Date(parseInt(year, 10), this.currentDate.getMonth(), 1);
                                    this.showYearPicker = false;
                                    this.yearSearch = '';
                                },
                                prevMonth() {
                                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                                    this.showMonthPicker = false;
                                    this.showYearPicker = false;
                                },
                                nextMonth() {
                                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                                    this.showMonthPicker = false;
                                    this.showYearPicker = false;
                                },
                                isFutureDate(day) {
                                    let dateToCheck = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                    let today = new Date();
                                    today.setHours(0,0,0,0);
                                    return dateToCheck > today;
                                },
                                selectDate(day) {
                                    if (this.isFutureDate(day)) return;
                                    const y = this.currentDate.getFullYear();
                                    const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                    const d = String(day).padStart(2, '0');
                                    this.selectedDate = `${y}-${m}-${d}`;
                                    this.showDatepicker = false;
                                    this.showMonthPicker = false;
                                    this.showYearPicker = false;
                                },
                                isSelected(day) {
                                    if(!this.selectedDate) return false;
                                    const y = this.currentDate.getFullYear();
                                    const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                    const d = String(day).padStart(2, '0');
                                    return this.selectedDate === `${y}-${m}-${d}`;
                                },
                                init() {
                                    if (this.selectedDate) {
                                        this.currentDate = new Date(this.selectedDate);
                                    }
                                }
                            }" class="relative" :class="showDatepicker ? 'z-40' : 'z-10'">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Date of Birth <span class="text-rose-500">*</span></label>
                                
                                <input type="hidden" name="dob" x-model="selectedDate">
                                
                                <div @click="if(editingInfo) { showDatepicker = !showDatepicker; showMonthPicker = false; showYearPicker = false; }" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus-within:border-emerald-500 cursor-pointer text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 cursor-not-allowed text-slate-400'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold flex justify-between items-center transition-all">
                                    <span x-text="selectedDate ? selectedDate : 'Select Date of Birth'" :class="selectedDate ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'"></span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div x-show="showDatepicker" @click.away="showDatepicker = false; showMonthPicker = false; showYearPicker = false;" style="display: none;" class="absolute z-50 mt-1 w-full min-w-[310px] max-w-sm p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl backdrop-blur-md">
                                    <!-- Header: Month and Year Selectors -->
                                    <div class="flex items-center justify-between mb-4 gap-1.5 relative">
                                        <!-- Prev Month Arrow -->
                                        <button type="button" 
                                                @click="prevMonth()" 
                                                class="p-2 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-800 dark:hover:text-emerald-400 transition cursor-pointer"
                                                title="Previous Month">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>

                                        <!-- Month & Year Custom Dropdowns -->
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <!-- Custom Month Dropdown -->
                                            <div class="relative w-3/5" @click.away="showMonthPicker = false">
                                                <button type="button" 
                                                        @click="showMonthPicker = !showMonthPicker; showYearPicker = false"
                                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                        :class="showMonthPicker 
                                                            ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/70 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-500' 
                                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:border-emerald-400 hover:bg-white dark:hover:bg-slate-750'">
                                                    <span x-text="monthNames[currentDate.getMonth()]" class="truncate font-bold"></span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200 shrink-0 ml-1"
                                                         :class="showMonthPicker ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <!-- Month Options Popover -->
                                                <div x-show="showMonthPicker" 
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                     class="absolute left-0 top-full mt-1.5 w-48 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-1.5 z-60 custom-scrollbar backdrop-blur-md"
                                                     style="display: none;">
                                                    <div class="space-y-0.5">
                                                        <template x-for="(month, index) in monthNames" :key="index">
                                                            <button type="button" 
                                                                    @click="setMonth(index)"
                                                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer transition-all text-left"
                                                                    :class="currentDate.getMonth() === index 
                                                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-300 dark:border-emerald-800 shadow-2xs' 
                                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/80 border border-transparent'">
                                                                <span x-text="month"></span>
                                                                <svg x-show="currentDate.getMonth() === index" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Custom Year Dropdown -->
                                            <div class="relative w-2/5" @click.away="showYearPicker = false">
                                                <button type="button" 
                                                        @click="showYearPicker = !showYearPicker; showMonthPicker = false; if (showYearPicker) { $nextTick(() => { $refs.editYearSearchInput?.focus(); const active = $refs.editYearList?.querySelector('[data-selected=true]'); if (active) active.scrollIntoView({ block: 'center' }); }); }"
                                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                        :class="showYearPicker 
                                                            ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/70 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-500' 
                                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:border-emerald-400 hover:bg-white dark:hover:bg-slate-750'">
                                                    <span x-text="currentDate.getFullYear()" class="truncate font-bold"></span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200 shrink-0 ml-1"
                                                         :class="showYearPicker ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <!-- Year Options Popover -->
                                                <div x-show="showYearPicker" 
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                     class="absolute right-0 top-full mt-1.5 w-40 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-2 z-60 backdrop-blur-md"
                                                     style="display: none;">
                                                    
                                                    <!-- Quick Year Search -->
                                                    <div class="relative mb-1.5">
                                                        <input type="text"
                                                               x-ref="editYearSearchInput"
                                                               x-model="yearSearch"
                                                               placeholder="Type year..."
                                                               class="w-full pl-6 pr-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                                                        <svg class="w-3 h-3 text-slate-400 absolute left-1.5 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                        </svg>
                                                    </div>

                                                    <!-- Scrollable Years List -->
                                                    <div x-ref="editYearList" class="max-h-52 overflow-y-auto space-y-0.5 custom-scrollbar pr-0.5">
                                                        <template x-for="year in filteredYears" :key="year">
                                                            <button type="button" 
                                                                    @click="setYear(year)"
                                                                    :data-selected="currentDate.getFullYear() === year ? 'true' : 'false'"
                                                                    class="w-full px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer transition-all text-left"
                                                                    :class="currentDate.getFullYear() === year 
                                                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-300 dark:border-emerald-800 shadow-2xs' 
                                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/80 border border-transparent'">
                                                                <span x-text="year"></span>
                                                                <svg x-show="currentDate.getFullYear() === year" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                        <div x-show="filteredYears.length === 0" class="py-2 text-center text-[11px] text-slate-400 font-medium">
                                                            No matching year
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Next Month Arrow -->
                                        <button type="button" 
                                                @click="nextMonth()" 
                                                class="p-2 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-800 dark:hover:text-emerald-400 transition cursor-pointer"
                                                title="Next Month">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Calendar Grid -->
                                    <div class="grid grid-cols-7 gap-1 text-center text-[11px] font-bold text-slate-400 mb-2">
                                        <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="blank in startDay">
                                            <div></div>
                                        </template>
                                        <template x-for="day in daysInMonth">
                                            <div 
                                                @click="selectDate(day)"
                                                class="h-8 md:h-9 rounded-xl flex items-center justify-center text-xs transition-all border border-transparent"
                                                :class="{
                                                    'bg-emerald-600 text-white font-bold shadow-xs cursor-pointer': isSelected(day),
                                                    'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:border-emerald-400 cursor-pointer': !isSelected(day) && !isFutureDate(day),
                                                    'bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed': isFutureDate(day)
                                                }"
                                            >
                                                <span x-text="day"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Quick Footer Status -->
                                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500" x-text="selectedDate ? 'Selected: ' + selectedDate : 'No date selected'"></span>
                                        <button type="button" 
                                                @click="showDatepicker = false; showMonthPicker = false; showYearPicker = false;" 
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition cursor-pointer">
                                            Done
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div x-data="{
                                phone: '{{ old('contact_number', $selectedPatient->contact_number ?? '') }}',
                                formatPhone() {
                                    this.phone = this.phone.replace(/[^0-9]/g, '').substring(0, 11);
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Number <span class="text-rose-500">*</span></label>
                                <input type="text" name="contact_number" required x-model="phone" @input="formatPhone" placeholder="09XXXXXXXXX" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address (Optional)</label>
                                <input type="email" name="email" value="{{ old('email', $selectedPatient->email ?? '') }}" placeholder="example@email.com" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold transition-colors">
                            </div>
                            <!-- Background fields -->
                            <div x-show="classification !== 'Pediatric'" x-cloak>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Education <span class="text-rose-500">*</span></label>
                                <x-select name="education" :options="$demographicEducationOptions" :value="old('education', $selectedPatient->education ?? '')" placeholder="Select Attainment..." x-bind:disabled="!editingInfo" />
                            </div>
                            <div x-show="classification !== 'Pediatric'" x-cloak
                                x-data="{
                                    selectedOccupation: '{{ old('occupation', $selectedPatient->occupation ?? '') }}',
                                    customOccupation: '',
                                    get finalOccupation() { return this.selectedOccupation === 'Others' ? this.customOccupation : this.selectedOccupation; },
                                    init() {
                                        let opts = ['n/a', 'student', 'employed', 'self-employed', 'unemployed', 'retired'];
                                        if (this.selectedOccupation && !opts.includes(this.selectedOccupation.toLowerCase())) {
                                            this.customOccupation = this.selectedOccupation;
                                            this.selectedOccupation = 'Others';
                                        } else if (this.selectedOccupation) {
                                            let exact = ['N/A', 'Student', 'Employed', 'Self-Employed', 'Unemployed', 'Retired'].find(o => o.toLowerCase() === this.selectedOccupation.toLowerCase());
                                            if (exact) this.selectedOccupation = exact;
                                        }
                                    }
                                }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Occupation <span class="text-rose-500">*</span></label>
                                <x-select x-model="selectedOccupation" :options="$demographicOccupationOptions" placeholder="Select Occupation..." x-bind:disabled="!editingInfo" class="mb-2" />
                                <input type="text" x-show="selectedOccupation === 'Others'" x-model="customOccupation" placeholder="Specify your occupation" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                <input type="hidden" name="occupation" :value="finalOccupation">
                            </div>
                            @php
                                $rawRel = old('religion', $selectedPatient->religion ?? '');
                                $knownRels = ['N/A', 'Roman Catholic', 'Islam', 'Iglesia ni Cristo', 'Born Again', 'Adventist', 'Aglipayan', "Jehovah's Witnesses"];
                                $matchedRel = collect($knownRels)->first(fn($r) => strtolower($r) === strtolower($rawRel));
                                $editRelVal = $matchedRel ?? ($rawRel ? 'Others' : '');
                                $editRelCust = $matchedRel ? '' : $rawRel;
                            @endphp
                            <div x-data="{
                                selectedReligion: '{{ addslashes($editRelVal) }}',
                                customReligion: '{{ addslashes($editRelCust) }}',
                                get finalReligion() {
                                    return this.selectedReligion === 'Others' ? this.customReligion : this.selectedReligion;
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Religion <span class="text-rose-500">*</span></label>
                                <x-select x-model="selectedReligion" :options="$demographicReligionOptions" placeholder="Select Religion..." x-bind:disabled="!editingInfo" class="mb-2" />
                                <input type="text" x-show="selectedReligion === 'Others'" x-model="customReligion" placeholder="Specify your religion" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                <input type="hidden" name="religion" :value="finalReligion">
                            </div>
                            <div class="md:col-span-2" x-data="{
                                addressEditing: false,
                                house_no: '{{ addslashes(old('house_no', $selectedPatient->house_no ?? '')) }}',
                                street: '{{ addslashes(old('street', $selectedPatient->street ?? '')) }}',
                                building: '{{ addslashes(old('building', $selectedPatient->building ?? '')) }}',
                                barangay: '{{ addslashes(old('barangay', $selectedPatient->barangay ?? '')) }}',
                                city_province: 'Silang, Cavite',
                                get fullAddress() {
                                    let parts = [];
                                    if(this.house_no) parts.push(this.house_no);
                                    if(this.street) parts.push(this.street);
                                    if(this.building) parts.push(this.building);
                                    if(this.barangay) parts.push(this.barangay);
                                    parts.push(this.city_province);
                                    return parts.join(', ').replace(/^, | ,/g, '').trim();
                                },
                                enableAddressEdit() {
                                    this.addressEditing = true;
                                }
                            }">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">Address <span class="text-rose-500">*</span></label>
                                    <button type="button" x-show="editingInfo && !addressEditing" @click="enableAddressEdit()" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Re-enter Address
                                    </button>
                                </div>
                                
                                <div x-show="!editingInfo || (editingInfo && !addressEditing)">
                                    <input type="text" value="{{ $selectedPatient->address }}" readonly class="w-full border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 rounded-xl px-3.5 py-2.5 sm:text-xs font-semibold uppercase cursor-not-allowed">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3" x-show="editingInfo && addressEditing" x-cloak>
                                    <div class="md:col-span-3">
                                        <input type="text" :required="addressEditing" x-model="house_no" placeholder="House No." :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-4">
                                        <input type="text" x-model="street" placeholder="Street Name (Optional)" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-5">
                                        <input type="text" x-model="building" placeholder="Building/Subd. (Optional)" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-6">
                                        <x-select 
                                            x-model="barangay" 
                                            :options="$demographicBarangayOptions" 
                                            :value="old('barangay', $selectedPatient->barangay ?? '')" 
                                            placeholder="Select Barangay..." 
                                            :searchable="true" 
                                            search-placeholder="Search barangay..." 
                                            x-bind:disabled="!editingInfo || !addressEditing" 
                                        />
                                    </div>
                                    <div class="md:col-span-6">
                                        <input type="text" x-model="city_province" readonly class="w-full rounded-xl border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 px-3.5 py-2.5 sm:text-xs font-semibold uppercase cursor-not-allowed">
                                    </div>
                                </div>
                                
                                <input type="hidden" name="address" :value="addressEditing ? fullAddress : '{{ addslashes($selectedPatient->address) }}'">
                                <input type="hidden" name="house_no" :value="house_no">
                                <input type="hidden" name="street" :value="street">
                                <input type="hidden" name="building" :value="building">
                                <input type="hidden" name="barangay" :value="barangay">
                            </div>
                            <div x-data="{
                                showPhilhealth: false,
                                philhealth: '{{ old('philhealth_number', $selectedPatient->classification === 'Pediatric' ? $selectedPatient->guardian_philhealth : $selectedPatient->philhealth_number) }}',
                                formatPhilHealth() {
                                    let val = this.philhealth.replace(/[^0-9]/g, '');
                                    if (val.length > 2) val = val.substring(0, 2) + '-' + val.substring(2);
                                    if (val.length > 12) val = val.substring(0, 12) + '-' + val.substring(12, 13);
                                    this.philhealth = val;
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    <span x-text="classification === 'Pediatric' ? `Guardian's PhilHealth No.` : `PhilHealth No.`"></span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input :type="showPhilhealth ? 'text' : 'password'" name="philhealth_number" required x-model="philhealth" @input="formatPhilHealth" placeholder="XX-XXXXXXXXX-X" maxlength="14" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold pr-10 uppercase transition-colors">
                                    <button type="button" @click="showPhilhealth = !showPhilhealth" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                        <svg x-show="!showPhilhealth" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="showPhilhealth" style="display:none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="md:col-span-2" x-data="{
                                parseName(fullName) {
                                    if(!fullName) return { first: '', middle: '', last: '', suffix: '' };
                                    let parts = fullName.split(' ');
                                    if(parts.length === 1) return { first: parts[0], middle: '', last: '', suffix: '' };
                                    if(parts.length === 2) return { first: parts[0], middle: '', last: parts[1], suffix: '' };
                                    let first = parts[0];
                                    let last = parts[parts.length - 1];
                                    let middle = parts.slice(1, parts.length - 1).join(' ');
                                    let suffixes = ['JR', 'SR', 'II', 'III', 'IV', 'V', 'JR.', 'SR.'];
                                    let suffix = '';
                                    if(suffixes.includes(last.toUpperCase())) {
                                        suffix = last;
                                        last = parts[parts.length - 2] || '';
                                        middle = parts.slice(1, parts.length - 2).join(' ');
                                    }
                                    return { first, middle, last, suffix };
                                },
                                init() {
                                    let parsed = this.parseName('{{ old('mothers_maiden_name', $selectedPatient->mothers_maiden_name ?? '') }}');
                                    this.first = parsed.first;
                                    this.middle = parsed.middle;
                                    this.last = parsed.last;
                                    this.suffix = parsed.suffix;
                                },
                                first: '', middle: '', last: '', suffix: '',
                                get fullName() {
                                    return `${this.first} ${this.middle ? this.middle + ' ' : ''}${this.last}${this.suffix ? ' ' + this.suffix : ''}`.trim().replace(/\s+/g, ' ').toUpperCase();
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mother's Maiden Name <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5">
                                    <div class="md:col-span-4">
                                        <input type="text" required x-model="first" placeholder="First Name" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-3">
                                        <input type="text" x-model="middle" placeholder="Middle Name" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-3">
                                        <input type="text" required x-model="last" placeholder="Last Name" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-2">
                                        <input type="text" x-model="suffix" placeholder="Suffix" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                </div>
                                <input type="hidden" name="mothers_maiden_name" :value="fullName">
                            </div>
                            
                            <!-- Guardian Information (Pediatric only) -->
                            <div x-show="classification === 'Pediatric'" style="display: none;" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5 mt-2 p-5 bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40 rounded-2xl">
                                <h5 class="col-span-1 md:col-span-2 text-xs font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider border-b border-amber-200/60 dark:border-amber-900/60 pb-2 mb-1 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    Guardian Information (Required for Pediatrics)
                                </h5>
                                <div class="col-span-1 md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">First Name <span class="text-rose-500">*</span></label>
                                        <input type="text" name="guardian_first_name" x-bind:required="classification === 'Pediatric'" value="{{ old('guardian_first_name', $selectedPatient->guardian_first_name ?? '') }}" placeholder="First Name" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Middle Name</label>
                                        <input type="text" name="guardian_middle_name" value="{{ old('guardian_middle_name', $selectedPatient->guardian_middle_name ?? '') }}" placeholder="Middle Name" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Last Name <span class="text-rose-500">*</span></label>
                                        <input type="text" name="guardian_last_name" x-bind:required="classification === 'Pediatric'" value="{{ old('guardian_last_name', $selectedPatient->guardian_last_name ?? '') }}" placeholder="Last Name" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                </div>
                                @php
                                    $rawGrel = old('guardian_relation', $selectedPatient->guardian_relation ?? '');
                                    $knownGrels = ['Mother', 'Father', 'Grandparent', 'Sibling'];
                                    $matchedGrel = collect($knownGrels)->first(fn($r) => strtolower($r) === strtolower($rawGrel));
                                    $editGrelVal = $matchedGrel ?? ($rawGrel ? 'Others (Please Specify)' : '');
                                    $editGrelCust = $matchedGrel ? '' : $rawGrel;
                                @endphp
                                <div x-data="{
                                    selectedGuardianRelation: '{{ addslashes($editGrelVal) }}',
                                    customGuardianRelation: '{{ addslashes($editGrelCust) }}',
                                    get finalGuardianRelation() { return this.selectedGuardianRelation === 'Others (Please Specify)' ? this.customGuardianRelation : this.selectedGuardianRelation; }
                                }">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Relationship <span class="text-rose-500">*</span></label>
                                    <select x-model="selectedGuardianRelation" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors mb-2">
                                        <option value="">Select Relationship...</option>
                                        <option value="Mother">Mother</option>
                                        <option value="Father">Father</option>
                                        <option value="Grandparent">Grandparent</option>
                                        <option value="Sibling">Sibling</option>
                                        <option value="Others (Please Specify)">Others (Please Specify)</option>
                                    </select>
                                    <input type="text" x-show="selectedGuardianRelation === 'Others (Please Specify)'" x-model="customGuardianRelation" placeholder="Specify relationship" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors" @input="customGuardianRelation = $event.target.value.toUpperCase()">
                                    <input type="hidden" name="guardian_relation" :value="finalGuardianRelation">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Guardian Contact <span class="text-rose-500">*</span></label>
                                    <input type="text" name="guardian_contact" value="{{ old('guardian_contact', $selectedPatient->guardian_contact ?? '') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" placeholder="09XXXXXXXXX" maxlength="11" :readonly="!editingInfo" :disabled="!editingInfo" :class="editingInfo ? 'border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:border-emerald-500 text-slate-900 dark:text-white' : 'border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 cursor-not-allowed'" class="w-full rounded-xl shadow-2xs px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                </div>
                            </div>
                        </div>
                    
                        <!-- Save Button for Patient Info -->
                        <div class="mt-6 flex justify-end" x-show="editingInfo" style="display: none;">
                            <button type="submit" :disabled="isSaving" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold py-2.5 px-6 rounded-xl shadow-md shadow-emerald-600/20 active:scale-[0.98] transition flex items-center gap-2 cursor-pointer text-xs">
                                <span x-show="isSaving" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                <span x-text="isSaving ? 'Saving...' : 'Save Profile Updates'"></span>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Form for creating the Visit/Vitals (Returning Patient) -->
                @php
                    $preTriageRecord = (isset($preTriageId) && $preTriageId) ? \App\Models\PreTriage::find($preTriageId) : null;
                    // NOTE: vitals-form partial defines its own copies of these, but @include scope
                    // does not leak back to this view, so compute them here for the confirm modal.
                    $isPediatric = ($selectedPatient->classification ?? '') === 'Pediatric';
                    $isFollowUp = (bool) (($selectedPatient->is_follow_up ?? false) || (isset($prefillApt) && $prefillApt && $prefillApt->is_follow_up));
                @endphp
                @if($preTriageRecord)
                <form id="returningPatientQueueForm" action="{{ route('frontdesk.visits.store', $selectedPatient) }}" method="POST" class="p-6 pt-2 space-y-6">
                    @csrf
                    @if($prefillApt)
                        <input type="hidden" name="appointment_id" value="{{ $prefillApt->id }}">
                    @endif
                    
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Vital Signs & Triaging Assignment</h4>
                    </div>
                    @include('frontdesk.registration.partials.vitals-form', ['preTriageRecord' => $preTriageRecord])
                    
                    <div x-data="{ classification: '{{ $selectedPatient->classification }}', isEmergency: {{ $preTriageRecord->is_emergency ? 'true' : 'false' }} }" x-show="classification === 'Pediatric' && !{{ $prefillApt ? 'true' : 'false' }}" x-cloak>
                        <div class="bg-rose-50/70 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40 rounded-2xl p-4 mb-2 mt-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-black text-rose-800 dark:text-rose-300 text-xs">Emergency Override</h4>
                                    <p class="text-[11px] text-rose-700/80 dark:text-rose-400 mt-0.5">Bypass the 5-patient walk-in limit for severe/emergency cases. Patient will be queued as Priority (PED-E).</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer ml-4 shrink-0">
                                    <input type="checkbox" name="emergency_override" value="1" x-model="isEmergency" class="sr-only peer">
                                    <div class="w-11 h-6 bg-rose-200 dark:bg-rose-900/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600 shadow-inner"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('frontdesk.registration.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">Cancel</a>
                        <button type="button" 
                                @click="window.queueConfirmState && window.queueConfirmState.openConfirm({
                                    formId: 'returningPatientQueueForm',
                                    patientName: '{{ addslashes($selectedPatient->full_name) }}',
                                    patientId: '{{ $selectedPatient->patient_id }}',
                                    patientClassification: '{{ $selectedPatient->classification }}',
                                    bp: '{{ $preTriageRecord->blood_pressure ?: '--' }}',
                                    temp: '{{ $preTriageRecord->temperature ? $preTriageRecord->temperature . '°C' : '--' }}',
                                    hr: '{{ $preTriageRecord->heart_rate ?: '--' }}',
                                    spo2: '{{ ($preTriageRecord->spo2 ?? $preTriageRecord->oxygen_saturation) ? ($preTriageRecord->spo2 ?? $preTriageRecord->oxygen_saturation) . '%' : '--' }}',
                                    symptoms: '{{ addslashes($preTriageRecord->symptoms ?: ($preTriageRecord->chief_complaint ?: 'None recorded')) }}',
                                    isPediatric: {{ $isPediatric ? 'true' : 'false' }},
                                    isFollowUp: {{ $isFollowUp ? 'true' : 'false' }}
                                })"
                                class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold px-6 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 active:scale-[0.98] transition flex items-center gap-2 cursor-pointer text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Generate Queue &amp; Assign
                        </button>
                    </div>
                </form>
                @else
                <div class="p-6 pt-0">
                    <div class="bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-2xl p-4 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <div>
                            <p class="font-bold">No pre-triage vitals found for this patient.</p>
                            <p class="text-[11px] mt-0.5 text-amber-700 dark:text-amber-400">This patient must go through the Vitals Station before being queued. Please direct them to the Vitals Nurse.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        @elseif($isNewPatient)
            <!-- New Patient Registration Form -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200/80 dark:border-slate-800/80">
                <div class="p-5 bg-slate-50/70 dark:bg-slate-900/70 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center rounded-t-3xl">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-sm border border-emerald-500/20 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white tracking-tight">Register New Citizen Patient</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Capture initial demographic profile and generate consultation queue ticket.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($prefillApt)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                From Appointment
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            New Record
                        </span>
                    </div>
                </div>

                @if($prefillApt && !(isset($newFromTriage) && $newFromTriage))
                <div class="mx-6 mt-5 bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl px-5 py-3.5 flex items-center gap-3.5 shadow-2xs">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center shrink-0 border border-indigo-300/40">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-indigo-900 dark:text-indigo-200">Appointment Data Pre-filled</p>
                        <p class="text-[11px] text-indigo-700 dark:text-indigo-400 mt-0.5">Ref: <span class="font-mono font-bold">{{ $prefillApt->reference_number }}</span> · {{ ucfirst($prefillApt->type) }} · {{ \Carbon\Carbon::parse($prefillApt->preferred_date)->format('M d, Y') }}{{ $prefillApt->preferred_time ? ' · ' . $prefillApt->preferred_time : '' }}</p>
                    </div>
                </div>
                @endif

                @if(isset($newFromTriage) && $newFromTriage)
                <div class="mx-6 mt-5 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl px-5 py-3.5 flex items-center gap-3.5 shadow-2xs">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 border border-emerald-300/40">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-emerald-900 dark:text-emerald-200">Vitals pre-recorded at Triage Station</p>
                        <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-0.5">BP: <span class="font-bold font-mono">{{ $newFromTriage->blood_pressure ?? '--' }}</span> &bull; Temp: <span class="font-bold font-mono">{{ $newFromTriage->temperature ?? '--' }}°C</span> &bull; SpO₂: <span class="font-bold font-mono">{{ $newFromTriage->spo2 ?? '--' }}%</span> &bull; <em>{{ $newFromTriage->patient_name }}</em></p>
                    </div>
                </div>
                <form id="newPatientQueueForm" action="{{ route('frontdesk.registerAndQueue', $newFromTriage) }}" method="POST" class="p-6 md:p-8 space-y-6">
                @else
                <form action="{{ route('frontdesk.patients.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                @endif
                    @csrf
                    @if($prefillApt || old('appointment_id'))
                        <input type="hidden" name="appointment_id" value="{{ $prefillApt ? $prefillApt->id : old('appointment_id') }}">
                    @endif
                    <input type="hidden" name="is_new_patient_form" value="1">
                    
                    @php
                        $defaultClassification = '';
                        if (isset($newFromTriage) && $newFromTriage->classification) {
                            $c = $newFromTriage->classification;
                            $defaultClassification = $c === 'Adult' ? 'Regular Adult' : ($c === 'Senior' ? 'Senior Citizen' : $c);
                        } elseif (isset($prefillApt) && $prefillApt && $prefillApt->type === 'pedia') {
                            $defaultClassification = 'Pediatric';
                        }
                    @endphp
                    <div x-data="{ classification: '{{ old('classification', $defaultClassification) }}', editingInfo: true }">
                        <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Patient Demographics (Permanent)</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="first_name" required value="{{ old('first_name', $prefillApt ? $prefillApt->first_name : (isset($newFromTriage) ? $newFromTriage->first_name : '')) }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Middle Name (Optional)</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $prefillApt ? $prefillApt->middle_name : (isset($newFromTriage) ? $newFromTriage->middle_name : '')) }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Last Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="last_name" required value="{{ old('last_name', $prefillApt ? $prefillApt->last_name : (isset($newFromTriage) ? $newFromTriage->last_name : '')) }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Suffix (Optional)</label>
                                <input type="text" name="suffix" value="{{ old('suffix', $prefillApt ? $prefillApt->suffix : (isset($newFromTriage) ? $newFromTriage->suffix : '')) }}" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Sex <span class="text-rose-500">*</span></label>
                                <x-select name="sex" :options="$demographicSexOptions" :value="old('sex', $prefillApt ? $prefillApt->sex : '')" placeholder="Select Sex..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Civil Status <span class="text-rose-500">*</span></label>
                                <x-select name="civil_status" :options="$demographicCivilStatusOptions" :value="old('civil_status', $prefillApt ? $prefillApt->civil_status : '')" placeholder="Select Status..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Blood Type <span class="text-rose-500">*</span></label>
                                <x-select name="blood_type" :options="$demographicBloodTypeOptions" :value="old('blood_type', $prefillApt ? $prefillApt->blood_type : '')" placeholder="Select Blood Type..." />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Classification <span class="text-rose-500">*</span></label>
                                <x-select name="classification" x-model="classification" :options="$demographicClassificationOptions" :value="old('classification', $defaultClassification)" placeholder="Select Classification..." />
                            </div>
                            <div x-data="{
                                showDatepicker: false,
                                showMonthPicker: false,
                                showYearPicker: false,
                                yearSearch: '',
                                allYears: Array.from({length: 120}, (_, i) => new Date().getFullYear() - i),
                                get filteredYears() {
                                    if (!this.yearSearch || !this.yearSearch.trim()) return this.allYears;
                                    return this.allYears.filter(y => String(y).includes(this.yearSearch.trim()));
                                },
                                currentDate: new Date(),
                                selectedDate: '{{ old('dob', $prefillApt && $prefillApt->dob ? \Carbon\Carbon::parse($prefillApt->dob)->format('Y-m-d') : (isset($newFromTriage) && $newFromTriage->dob ? $newFromTriage->dob : '')) }}',
                                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                get daysInMonth() {
                                    return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate();
                                },
                                get startDay() {
                                    return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                                },
                                setMonth(monthIndex) {
                                    this.currentDate = new Date(this.currentDate.getFullYear(), parseInt(monthIndex, 10), 1);
                                    this.showMonthPicker = false;
                                },
                                setYear(year) {
                                    this.currentDate = new Date(parseInt(year, 10), this.currentDate.getMonth(), 1);
                                    this.showYearPicker = false;
                                    this.yearSearch = '';
                                },
                                prevMonth() {
                                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                                    this.showMonthPicker = false;
                                    this.showYearPicker = false;
                                },
                                nextMonth() {
                                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                                    this.showMonthPicker = false;
                                    this.showYearPicker = false;
                                },
                                isFutureDate(day) {
                                    let dateToCheck = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                    let today = new Date();
                                    today.setHours(0,0,0,0);
                                    return dateToCheck > today;
                                },
                                selectDate(day) {
                                    if (this.isFutureDate(day)) return;
                                    const y = this.currentDate.getFullYear();
                                    const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                    const d = String(day).padStart(2, '0');
                                    this.selectedDate = `${y}-${m}-${d}`;
                                    this.showDatepicker = false;
                                    this.showMonthPicker = false;
                                    this.showYearPicker = false;
                                },
                                isSelected(day) {
                                    if(!this.selectedDate) return false;
                                    const y = this.currentDate.getFullYear();
                                    const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                    const d = String(day).padStart(2, '0');
                                    return this.selectedDate === `${y}-${m}-${d}`;
                                },
                                init() {
                                    if (this.selectedDate) {
                                        this.currentDate = new Date(this.selectedDate);
                                    }
                                }
                            }" class="relative" :class="showDatepicker ? 'z-40' : 'z-10'">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Date of Birth <span class="text-rose-500">*</span></label>
                                
                                <input type="hidden" name="dob" x-model="selectedDate">
                                
                                <div @click="showDatepicker = !showDatepicker; showMonthPicker = false; showYearPicker = false;" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus-within:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold cursor-pointer flex justify-between items-center transition-all"
                                     :class="showDatepicker ? 'border-emerald-500 ring-2 ring-emerald-500/20' : ''">
                                    <span x-text="selectedDate ? selectedDate : 'Select Date of Birth'" :class="selectedDate ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'"></span>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div x-show="showDatepicker" @click.away="showDatepicker = false; showMonthPicker = false; showYearPicker = false;" style="display: none;" class="absolute z-50 mt-1 w-full min-w-[310px] max-w-sm p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl backdrop-blur-md">
                                    <!-- Header: Month and Year Selectors -->
                                    <div class="flex items-center justify-between mb-4 gap-1.5 relative">
                                        <!-- Prev Month Arrow -->
                                        <button type="button" 
                                                @click="prevMonth()" 
                                                class="p-2 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-800 dark:hover:text-emerald-400 transition cursor-pointer"
                                                title="Previous Month">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>

                                        <!-- Month & Year Custom Dropdowns -->
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <!-- Custom Month Dropdown -->
                                            <div class="relative w-3/5" @click.away="showMonthPicker = false">
                                                <button type="button" 
                                                        @click="showMonthPicker = !showMonthPicker; showYearPicker = false"
                                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                        :class="showMonthPicker 
                                                            ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/70 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-500' 
                                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:border-emerald-400 hover:bg-white dark:hover:bg-slate-750'">
                                                    <span x-text="monthNames[currentDate.getMonth()]" class="truncate font-bold"></span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200 shrink-0 ml-1"
                                                         :class="showMonthPicker ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <!-- Month Options Popover -->
                                                <div x-show="showMonthPicker" 
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                     class="absolute left-0 top-full mt-1.5 w-48 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-1.5 z-60 custom-scrollbar backdrop-blur-md"
                                                     style="display: none;">
                                                    <div class="space-y-0.5">
                                                        <template x-for="(month, index) in monthNames" :key="index">
                                                            <button type="button" 
                                                                    @click="setMonth(index)"
                                                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer transition-all text-left"
                                                                    :class="currentDate.getMonth() === index 
                                                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-300 dark:border-emerald-800 shadow-2xs' 
                                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/80 border border-transparent'">
                                                                <span x-text="month"></span>
                                                                <svg x-show="currentDate.getMonth() === index" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Custom Year Dropdown -->
                                            <div class="relative w-2/5" @click.away="showYearPicker = false">
                                                <button type="button" 
                                                        @click="showYearPicker = !showYearPicker; showMonthPicker = false; if (showYearPicker) { $nextTick(() => { $refs.newYearSearchInput?.focus(); const active = $refs.newYearList?.querySelector('[data-selected=true]'); if (active) active.scrollIntoView({ block: 'center' }); }); }"
                                                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl border text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                        :class="showYearPicker 
                                                            ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/70 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-500' 
                                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:border-emerald-400 hover:bg-white dark:hover:bg-slate-750'">
                                                    <span x-text="currentDate.getFullYear()" class="truncate font-bold"></span>
                                                    <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 transition-transform duration-200 shrink-0 ml-1"
                                                         :class="showYearPicker ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <!-- Year Options Popover -->
                                                <div x-show="showYearPicker" 
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                     class="absolute right-0 top-full mt-1.5 w-40 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-2 z-60 backdrop-blur-md"
                                                     style="display: none;">
                                                    
                                                    <!-- Quick Year Search -->
                                                    <div class="relative mb-1.5">
                                                        <input type="text"
                                                               x-ref="newYearSearchInput"
                                                               x-model="yearSearch"
                                                               placeholder="Type year..."
                                                               class="w-full pl-6 pr-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                                                        <svg class="w-3 h-3 text-slate-400 absolute left-1.5 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                        </svg>
                                                    </div>

                                                    <!-- Scrollable Years List -->
                                                    <div x-ref="newYearList" class="max-h-52 overflow-y-auto space-y-0.5 custom-scrollbar pr-0.5">
                                                        <template x-for="year in filteredYears" :key="year">
                                                            <button type="button" 
                                                                    @click="setYear(year)"
                                                                    :data-selected="currentDate.getFullYear() === year ? 'true' : 'false'"
                                                                    class="w-full px-2.5 py-1.5 rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer transition-all text-left"
                                                                    :class="currentDate.getFullYear() === year 
                                                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-300 dark:border-emerald-800 shadow-2xs' 
                                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800/80 border border-transparent'">
                                                                <span x-text="year"></span>
                                                                <svg x-show="currentDate.getFullYear() === year" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                        <div x-show="filteredYears.length === 0" class="py-2 text-center text-[11px] text-slate-400 font-medium">
                                                            No matching year
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Next Month Arrow -->
                                        <button type="button" 
                                                @click="nextMonth()" 
                                                class="p-2 rounded-xl text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-slate-800 dark:hover:text-emerald-400 transition cursor-pointer"
                                                title="Next Month">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Calendar Grid -->
                                    <div class="grid grid-cols-7 gap-1 text-center text-[11px] font-bold text-slate-400 mb-2">
                                        <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="blank in startDay">
                                            <div></div>
                                        </template>
                                        <template x-for="day in daysInMonth">
                                            <div 
                                                @click="selectDate(day)"
                                                class="h-8 md:h-9 rounded-xl flex items-center justify-center text-xs transition-all border border-transparent"
                                                :class="{
                                                    'bg-emerald-600 text-white font-bold shadow-xs cursor-pointer': isSelected(day),
                                                    'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:border-emerald-400 cursor-pointer': !isSelected(day) && !isFutureDate(day),
                                                    'bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed': isFutureDate(day)
                                                }"
                                            >
                                                <span x-text="day"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Quick Footer Status -->
                                    <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500" x-text="selectedDate ? 'Selected: ' + selectedDate : 'No date selected'"></span>
                                        <button type="button" 
                                                @click="showDatepicker = false; showMonthPicker = false; showYearPicker = false;" 
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition cursor-pointer">
                                            Done
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div x-show="classification !== 'Pediatric'" x-cloak x-data="{
                                phone: '{{ old('contact_number', $prefillApt ? ($prefillApt->type === 'pedia' ? ($prefillApt->guardian_contact ?: $prefillApt->contact_number) : $prefillApt->contact_number) : '') }}',
                                formatPhone() {
                                    this.phone = this.phone.replace(/[^0-9]/g, '').substring(0, 11);
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Number <span class="text-rose-500">*</span></label>
                                <input type="text" name="contact_number" :required="classification !== 'Pediatric'" x-model="phone" @input="formatPhone" placeholder="09XXXXXXXXX" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address (Optional)</label>
                                <input type="email" name="email" value="{{ old('email', $prefillApt ? $prefillApt->email : '') }}" placeholder="example@email.com" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold transition-colors">
                            </div>
                            <!-- Background fields -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Education <span class="text-rose-500">*</span></label>
                                <x-select name="education" :options="$demographicEducationOptions" :value="old('education', $prefillApt ? $prefillApt->education : '')" placeholder="Select Attainment..." />
                            </div>
                            <div x-show="classification !== 'Pediatric'" x-cloak
                                x-data="{
                                    selectedOccupation: '{{ old('occupation', $prefillApt->occupation ?? '') }}',
                                    customOccupation: '',
                                    get finalOccupation() { return this.selectedOccupation === 'Others' ? this.customOccupation : this.selectedOccupation; },
                                    init() {
                                        let opts = ['n/a', 'student', 'employed', 'self-employed', 'unemployed', 'retired'];
                                        if (this.selectedOccupation && !opts.includes(this.selectedOccupation.toLowerCase())) {
                                            this.customOccupation = this.selectedOccupation;
                                            this.selectedOccupation = 'Others';
                                        } else if (this.selectedOccupation) {
                                            let exact = ['N/A', 'Student', 'Employed', 'Self-Employed', 'Unemployed', 'Retired'].find(o => o.toLowerCase() === this.selectedOccupation.toLowerCase());
                                            if (exact) this.selectedOccupation = exact;
                                        }
                                    }
                                }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Occupation <span class="text-rose-500">*</span></label>
                                <x-select x-model="selectedOccupation" :options="$demographicOccupationOptions" placeholder="Select Occupation..." class="mb-2" />
                                <input type="text" x-show="selectedOccupation === 'Others'" x-model="customOccupation" placeholder="Specify your occupation" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                <input type="hidden" name="occupation" :value="finalOccupation">
                            </div>
                            @php
                                $knownRels = ['N/A', 'Roman Catholic', 'Islam', 'Iglesia ni Cristo', 'Born Again', 'Adventist', 'Aglipayan', "Jehovah's Witnesses"];
                                $rawRelNew = old('religion', $prefillApt->religion ?? '');
                                $matchedRelNew = collect($knownRels)->first(fn($r) => strtolower($r) === strtolower($rawRelNew));
                                $newRelVal = $matchedRelNew ?? ($rawRelNew ? 'Others' : '');
                                $newRelCust = $matchedRelNew ? '' : $rawRelNew;
                            @endphp
                            <div x-data="{
                                selectedReligion: '{{ addslashes($newRelVal) }}',
                                customReligion: '{{ addslashes($newRelCust) }}',
                                get finalReligion() {
                                    return this.selectedReligion === 'Others' ? this.customReligion : this.selectedReligion;
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Religion <span class="text-rose-500">*</span></label>
                                <x-select x-model="selectedReligion" :options="$demographicReligionOptions" placeholder="Select Religion..." class="mb-2" />
                                <input type="text" x-show="selectedReligion === 'Others'" x-model="customReligion" placeholder="Specify your religion" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                <input type="hidden" name="religion" :value="finalReligion">
                            </div>
                            @php
                                $presetAddress = isset($prefillApt) && $prefillApt ? $prefillApt->address : '';
                                $presetBarangay = isset($prefillApt) && $prefillApt ? ($prefillApt->barangay ?? '') : '';
                                if (! $presetBarangay && $presetAddress) {
                                    $presetBarangay = \App\Models\Patient::normalizeBarangay(null, $presetAddress) ?? '';
                                }
                                $presetHouseNo = isset($prefillApt) && $prefillApt ? ($prefillApt->house_no ?? '') : '';
                                $presetStreet = isset($prefillApt) && $prefillApt ? ($prefillApt->street ?? '') : '';
                                $presetBuilding = isset($prefillApt) && $prefillApt ? ($prefillApt->building ?? '') : '';
                            @endphp
                            <div class="md:col-span-2" x-data="{
                                mode: '{{ $presetAddress ? 'readonly' : 'edit' }}',
                                combinedAddress: '{{ addslashes($presetAddress) }}',
                                house_no: '{{ addslashes(old('house_no', $presetHouseNo)) }}',
                                street: '{{ addslashes(old('street', $presetStreet)) }}',
                                building: '{{ addslashes(old('building', $presetBuilding)) }}',
                                barangay: '{{ addslashes(old('barangay', $presetBarangay)) }}',
                                city_province: 'Silang, Cavite',
                                get fullAddress() {
                                    if (this.mode === 'readonly') return this.combinedAddress;
                                    let parts = [];
                                    if(this.house_no) parts.push(this.house_no);
                                    if(this.street) parts.push(this.street);
                                    if(this.building) parts.push(this.building);
                                    if(this.barangay) parts.push(this.barangay);
                                    parts.push(this.city_province);
                                    return parts.join(', ').replace(/^, | ,/g, '').trim();
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Address <span class="text-rose-500">*</span></label>
                                
                                <input type="hidden" name="address" :value="fullAddress">
                                <input type="hidden" name="house_no" :value="house_no">
                                <input type="hidden" name="street" :value="street">
                                <input type="hidden" name="building" :value="building">
                                <input type="hidden" name="barangay" :value="barangay">
                                <input type="hidden" name="city_province" :value="city_province">

                                <div x-show="mode === 'readonly'" class="flex items-center justify-between bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-3.5 rounded-2xl">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="combinedAddress"></p>
                                    <button type="button" @click="mode = 'edit'" class="text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 border border-emerald-200 dark:border-emerald-800 px-3 py-1.5 rounded-xl transition shadow-2xs whitespace-nowrap cursor-pointer">
                                        Re-enter Address
                                    </button>
                                </div>

                                <div x-show="mode === 'edit'" class="grid grid-cols-1 md:grid-cols-12 gap-3" x-cloak>
                                    <div class="md:col-span-3">
                                        <input type="text" :required="mode === 'edit'" x-model="house_no" placeholder="House No." class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-4">
                                        <input type="text" x-model="street" placeholder="Street Name (Optional)" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-5">
                                        <input type="text" x-model="building" placeholder="Building/Subd. (Optional)" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-6">
                                        <x-select 
                                            x-model="barangay" 
                                            :options="$demographicBarangayOptions" 
                                            :value="old('barangay', $presetBarangay)" 
                                            placeholder="Select Barangay..." 
                                            :searchable="true" 
                                            search-placeholder="Search barangay..." 
                                        />
                                    </div>
                                    <div class="md:col-span-6">
                                        <input type="text" x-model="city_province" readonly class="w-full rounded-xl border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 px-3.5 py-2.5 sm:text-xs font-semibold uppercase cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                            <div x-data="{
                                showPhilhealth: false,
                                philhealth: '{{ old('philhealth_number', $prefillApt ? ($prefillApt->type === 'pedia' ? $prefillApt->guardian_philhealth : $prefillApt->philhealth_number) : '') }}',
                                formatPhilHealth() {
                                    let val = this.philhealth.replace(/[^0-9]/g, '');
                                    if (val.length > 2) val = val.substring(0, 2) + '-' + val.substring(2);
                                    if (val.length > 12) val = val.substring(0, 12) + '-' + val.substring(12, 13);
                                    this.philhealth = val;
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    <span x-text="classification === 'Pediatric' ? `Guardian's PhilHealth No.` : `PhilHealth No.`"></span>
                                    <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input :type="showPhilhealth ? 'text' : 'password'" name="philhealth_number" required x-model="philhealth" @input="formatPhilHealth" placeholder="XX-XXXXXXXXX-X" maxlength="14" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold pr-10 uppercase transition-colors">
                                    <button type="button" @click="showPhilhealth = !showPhilhealth" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                        <svg x-show="!showPhilhealth" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg x-show="showPhilhealth" style="display:none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="md:col-span-2" x-data="{
                                parseName(fullName) {
                                    if(!fullName) return { first: '', middle: '', last: '', suffix: '' };
                                    let parts = fullName.split(' ');
                                    if(parts.length === 1) return { first: parts[0], middle: '', last: '', suffix: '' };
                                    if(parts.length === 2) return { first: parts[0], middle: '', last: parts[1], suffix: '' };
                                    let first = parts[0];
                                    let last = parts[parts.length - 1];
                                    let middle = parts.slice(1, parts.length - 1).join(' ');
                                    let suffixes = ['JR', 'SR', 'II', 'III', 'IV', 'V', 'JR.', 'SR.'];
                                    let suffix = '';
                                    if(suffixes.includes(last.toUpperCase())) {
                                        suffix = last;
                                        last = parts[parts.length - 2] || '';
                                        middle = parts.slice(1, parts.length - 2).join(' ');
                                    }
                                    return { first, middle, last, suffix };
                                },
                                init() {
                                    let parsed = this.parseName('{{ old('mothers_maiden_name', $prefillApt ? $prefillApt->mothers_maiden_name : '') }}');
                                    this.first = parsed.first;
                                    this.middle = parsed.middle;
                                    this.last = parsed.last;
                                    this.suffix = parsed.suffix;
                                },
                                first: '', middle: '', last: '', suffix: '',
                                get fullName() {
                                    return `${this.first} ${this.middle ? this.middle + ' ' : ''}${this.last}${this.suffix ? ' ' + this.suffix : ''}`.trim().replace(/\s+/g, ' ').toUpperCase();
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mother's Maiden Name <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5">
                                    <div class="md:col-span-4">
                                        <input type="text" required x-model="first" placeholder="First Name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-3">
                                        <input type="text" x-model="middle" placeholder="Middle Name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-3">
                                        <input type="text" required x-model="last" placeholder="Last Name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-2">
                                        <input type="text" x-model="suffix" placeholder="Suffix" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                </div>
                                <input type="hidden" name="mothers_maiden_name" :value="fullName">
                            </div>
                            
                            <!-- Guardian Information (Pediatric only) -->
                            <div x-show="classification === 'Pediatric'" style="display: none;" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5 mt-2 p-5 bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40 rounded-2xl">
                                <h5 class="col-span-1 md:col-span-2 text-xs font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider border-b border-amber-200/60 dark:border-amber-900/60 pb-2 mb-1 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    Guardian Information (Required for Pediatrics)
                                </h5>
                                <div class="col-span-1 md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">First Name <span class="text-rose-500">*</span></label>
                                        <input type="text" name="guardian_first_name" x-bind:required="classification === 'Pediatric'" value="{{ old('guardian_first_name', $prefillApt ? $prefillApt->guardian_first_name : '') }}" placeholder="First Name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Middle Name</label>
                                        <input type="text" name="guardian_middle_name" value="{{ old('guardian_middle_name', $prefillApt ? $prefillApt->guardian_middle_name : '') }}" placeholder="Middle Name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Last Name <span class="text-rose-500">*</span></label>
                                        <input type="text" name="guardian_last_name" x-bind:required="classification === 'Pediatric'" value="{{ old('guardian_last_name', $prefillApt ? $prefillApt->guardian_last_name : '') }}" placeholder="Last Name" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                </div>
                                @php
                                    $rawGrelNew = old('guardian_relation', $prefillApt->guardian_relation ?? '');
                                    $knownGrels = ['Mother', 'Father', 'Grandparent', 'Sibling'];
                                    $matchedGrelNew = collect($knownGrels)->first(fn($r) => strtolower($r) === strtolower($rawGrelNew));
                                    $newGrelVal = $matchedGrelNew ?? ($rawGrelNew ? 'Others (Please Specify)' : '');
                                    $newGrelCust = $matchedGrelNew ? '' : $rawGrelNew;
                                @endphp
                                <div x-data="{
                                    selectedGuardianRelation: '{{ addslashes($newGrelVal) }}',
                                    customGuardianRelation: '{{ addslashes($newGrelCust) }}',
                                    get finalGuardianRelation() { return this.selectedGuardianRelation === 'Others (Please Specify)' ? this.customGuardianRelation : this.selectedGuardianRelation; }
                                }">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Relationship <span class="text-rose-500">*</span></label>
                                    <select x-model="selectedGuardianRelation" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors mb-2">
                                        <option value="">Select Relationship...</option>
                                        <option value="Mother">Mother</option>
                                        <option value="Father">Father</option>
                                        <option value="Grandparent">Grandparent</option>
                                        <option value="Sibling">Sibling</option>
                                        <option value="Others (Please Specify)">Others (Please Specify)</option>
                                    </select>
                                    <input type="text" x-show="selectedGuardianRelation === 'Others (Please Specify)'" x-model="customGuardianRelation" placeholder="Specify relationship" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors" @input="customGuardianRelation = $event.target.value.toUpperCase()">
                                    <input type="hidden" name="guardian_relation" :value="finalGuardianRelation">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Guardian Contact <span class="text-rose-500">*</span></label>
                                    <input type="text" name="guardian_contact" value="{{ old('guardian_contact', $prefillApt ? $prefillApt->guardian_contact : '') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" placeholder="09XXXXXXXXX" maxlength="11" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($newFromTriage) && $newFromTriage)
                    {{-- Vitals Verification + Severity Triage (for new patients from triage) --}}
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                        <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
                            <div class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center border border-teal-500/20">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Vital Signs & Triaging Assignment</h4>
                        </div>
                        @include('frontdesk.registration.partials.vitals-form', ['preTriageRecord' => $newFromTriage])
                        
                        <div x-data="{ isEmergency: {{ $newFromTriage->is_emergency ? 'true' : 'false' }} }" x-show="classification === 'Pediatric'" x-cloak>
                            <div class="bg-rose-50/70 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40 rounded-2xl p-4 mb-2 mt-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-black text-rose-800 dark:text-rose-300 text-xs">Emergency Override</h4>
                                        <p class="text-[11px] text-rose-700/80 dark:text-rose-400 mt-0.5">Bypass the 5-patient walk-in limit for severe/emergency cases. Patient will be queued as Priority (PED-E).</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer ml-4 shrink-0">
                                        <input type="checkbox" name="emergency_override" value="1" x-model="isEmergency" class="sr-only peer">
                                        <div class="w-11 h-6 bg-rose-200 dark:bg-rose-900/50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600 shadow-inner"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('frontdesk.registration.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 px-4 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">Cancel</a>
                        @if(isset($newFromTriage) && $newFromTriage)
                        <button type="button" 
                                @click="
                                    const form = document.getElementById('newPatientQueueForm');
                                    const fName = form?.querySelector('input[name=\'first_name\']')?.value || '';
                                    const lName = form?.querySelector('input[name=\'last_name\']')?.value || '';
                                    const fullName = (fName + ' ' + lName).trim() || '{{ addslashes($newFromTriage->patient_name) }}';
                                    const classificationEl = form?.querySelector('select[name=\'classification\'], input[name=\'classification\']');
                                    const classVal = classificationEl ? classificationEl.value : '{{ $newFromTriage->classification ?? 'Regular Adult' }}';
                                    window.queueConfirmState && window.queueConfirmState.openConfirm({
                                        formId: 'newPatientQueueForm',
                                        patientName: fullName,
                                        patientId: 'New Patient Record',
                                        patientClassification: classVal,
                                        bp: '{{ $newFromTriage->blood_pressure ?: '--' }}',
                                        temp: '{{ $newFromTriage->temperature ? $newFromTriage->temperature . '°C' : '--' }}',
                                        hr: '{{ $newFromTriage->heart_rate ?: '--' }}',
                                        spo2: '{{ ($newFromTriage->spo2 ?? $newFromTriage->oxygen_saturation) ? ($newFromTriage->spo2 ?? $newFromTriage->oxygen_saturation) . '%' : '--' }}',
                                        symptoms: '{{ addslashes($newFromTriage->symptoms ?: ($newFromTriage->chief_complaint ?: 'None recorded')) }}',
                                        isPediatric: classVal === 'Pediatric',
                                        isFollowUp: false
                                    });
                                "
                                class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold px-6 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 active:scale-[0.98] transition flex items-center gap-2 cursor-pointer text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Register &amp; Generate Queue
                        </button>
                        @else
                        <button type="submit" class="bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white font-bold px-6 py-2.5 rounded-xl shadow-md shadow-emerald-600/20 active:scale-[0.98] transition flex items-center gap-2 cursor-pointer text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Register Patient
                        </button>
                        @endif
                    </div>
                </form>
            </div>
            
        @else
            <!-- Placeholder (No Patient Selected) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-200 dark:border-slate-800 flex flex-col items-center justify-center p-12 text-center h-80 shadow-2xs">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h4 class="text-sm font-black text-slate-800 dark:text-white">Ready for Patient Triaging &amp; Intake</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm">Select an existing patient from the left panel, process a pre-triaged record, or click "New Citizen Registration" above.</p>
            </div>
        @endif

    </div> <!-- /#right-panel-container (opened at line 579) -->
</div> <!-- /.grid.grid-cols-1.lg:grid-cols-3 (opened at line 51) -->

<!-- =========================================================================
     PRE-SUBMISSION QUEUE CONFIRMATION MODAL
     Applies deliberately across ALL severities (Severe, Mild, Normal/Light)
     Guarantees clear review of triage level and assigned medical provider
     ========================================================================= -->
<div x-data="{
        // Expose the REACTIVE proxy globally. Assigning the object literal directly
        // (window.queueConfirmState = {...}) stores Alpine's raw object, so mutations
        // made from outside (e.g. isOpen = true) never trigger re-renders.
        init() { window.queueConfirmState = this; },
        isOpen: false,
        isSubmitting: false,
        formId: null,
        patientName: '',
        patientId: '',
        patientClassification: '',
        severity: 'mild',
        severityLabel: 'Mild',
        routingRole: '',
        bp: '--',
        temp: '--',
        hr: '--',
        spo2: '--',
        symptoms: '--',
        isEmergency: false,
        isPediatric: false,
        isFollowUp: false,

        openConfirm(config) {
            this.formId = config.formId;
            const form = document.getElementById(config.formId);
            if (!form) return;

            // Severity check runs BEFORE native validation: the severity radios are
            // sr-only, so the browser's validation bubble for them is effectively invisible.
            let sev = 'mild';
            if (config.isPediatric) {
                sev = 'light';
            } else if (config.isFollowUp) {
                sev = 'mild';
            } else {
                const checkedSev = form.querySelector('input[name=\'symptom_severity\']:checked') 
                    || form.querySelector('input[name=\'symptom_severity\'][type=\'hidden\']');
                if (!checkedSev || !checkedSev.value) {
                    alert('Please select a triage severity level (Light, Mild, or Severe) before generating queue.');
                    const sc = form.querySelector('.symptom-severity-container');
                    if (sc) sc.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
                sev = checkedSev.value;
            }

            // HTML5 field validation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            this.severity = sev;
            this.patientName = config.patientName || 'Unknown Patient';
            this.patientId = config.patientId || '';
            this.patientClassification = config.patientClassification || 'Regular Adult';
            this.bp = config.bp || '--';
            this.temp = config.temp || '--';
            this.hr = config.hr || '--';
            this.spo2 = config.spo2 || '--';
            this.symptoms = config.symptoms || 'None recorded';
            this.isPediatric = !!config.isPediatric;
            this.isFollowUp = !!config.isFollowUp;
            this.isEmergency = !!form.querySelector('input[name=\'emergency_override\']:checked');

            if (this.severity === 'severe') {
                this.severityLabel = 'Severe';
                this.routingRole = 'Attending Doctor (Immediate Clinical Evaluation)';
            } else if (this.severity === 'mild') {
                this.severityLabel = 'Mild';
                this.routingRole = 'Load-Balanced Practitioner (Doctor / Nurse with shortest queue)';
            } else {
                this.severityLabel = 'Light / Normal';
                this.routingRole = 'Clinical Nurse (Routine Evaluation / Refill)';
            }

            if (this.isPediatric) {
                this.routingRole = 'Pediatrician (Dedicated Pediatric Clinic)';
            } else if (this.isFollowUp) {
                this.routingRole = 'Attending Follow-Up Doctor (Continuity of Care)';
            }

            this.isSubmitting = false;
            this.isOpen = true;
        },

        submitConfirmed() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            const form = document.getElementById(this.formId);
            if (form) form.submit();
        },

        closeModal() {
            if (this.isSubmitting) return;
            this.isOpen = false;
        }
     }"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-xs transition-opacity"
     @keydown.escape.window="closeModal()">

    <div @click.away="closeModal()" 
         class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform scale-100 transition-all border border-slate-200 dark:border-slate-800"
         :class="{
             'ring-2 ring-rose-500/30 border-rose-300 dark:border-rose-900/60': severity === 'severe',
             'ring-2 ring-amber-500/30 border-amber-300 dark:border-amber-900/60': severity === 'mild',
             'ring-2 ring-emerald-500/30 border-emerald-300 dark:border-emerald-900/60': severity === 'light'
         }">

        <!-- Dynamic Header based on severity -->
        <div class="px-6 py-5 flex items-center justify-between text-white"
             :class="{
                 'bg-gradient-to-r from-rose-600 via-red-600 to-rose-700': severity === 'severe',
                 'bg-gradient-to-r from-amber-500 via-amber-600 to-amber-700': severity === 'mild',
                 'bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700': severity === 'light'
             }">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 backdrop-blur-md rounded-2xl p-2.5 border border-white/30 shrink-0">
                    <template x-if="severity === 'severe'">
                        <span class="relative flex h-5 w-5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </span>
                    </template>
                    <template x-if="severity === 'mild'">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </template>
                    <template x-if="severity === 'light'">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </template>
                </div>
                <div>
                    <h3 class="text-base font-black tracking-tight text-white flex items-center gap-2">
                        <span>Confirm Queue Generation</span>
                        <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-white/20 border border-white/30" x-text="severityLabel"></span>
                    </h3>
                    <p class="text-[11px] text-white/90 font-medium">Verify triage severity and healthcare provider assignment</p>
                </div>
            </div>
            <button @click="closeModal()" type="button" class="p-1.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <!-- Patient Summary Card -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Patient Constituent</span>
                    <h4 class="text-base font-black text-slate-900 dark:text-white truncate" x-text="patientName"></h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300" x-text="patientClassification"></span>
                        <template x-if="patientId">
                            <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400" x-text="patientId"></span>
                        </template>
                        <template x-if="isEmergency">
                            <span class="text-xs font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 border border-rose-200">EMERGENCY OVERRIDE</span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Triage Severity & Routing Assessment -->
            <div class="p-4 rounded-2xl border"
                 :class="{
                     'bg-rose-50/70 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800/60 text-rose-900 dark:text-rose-200': severity === 'severe',
                     'bg-amber-50/70 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800/60 text-amber-900 dark:text-amber-200': severity === 'mild',
                     'bg-emerald-50/70 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800/60 text-emerald-900 dark:text-emerald-200': severity === 'light'
                 }">
                <div class="flex items-start gap-3">
                    <div class="text-2xl shrink-0 mt-0.5">
                        <span x-show="severity === 'severe'">🔴</span>
                        <span x-show="severity === 'mild'">🟡</span>
                        <span x-show="severity === 'light'">🟢</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h5 class="text-xs font-black uppercase tracking-wider">Triage Acuity: <span x-text="severityLabel"></span></h5>
                        </div>
                        <p class="text-xs font-bold mt-1" :class="{
                            'text-rose-800 dark:text-rose-300': severity === 'severe',
                            'text-amber-800 dark:text-amber-300': severity === 'mild',
                            'text-emerald-800 dark:text-emerald-300': severity === 'light'
                        }">
                            Assigned Route: <span x-text="routingRole"></span>
                        </p>
                        <p class="text-[11px] mt-1 text-slate-600 dark:text-slate-400 leading-relaxed">
                            <span x-show="severity === 'severe'">High acuity patient. Will be placed directly into the attending Doctor's consultation queue for immediate medical examination.</span>
                            <span x-show="severity === 'mild'">Moderate symptoms. The system will load-balance and automatically assign the patient to whichever provider (Doctor or Nurse) has a shorter queue.</span>
                            <span x-show="severity === 'light'">Routine checkup, vital monitoring, or medication refill. Routed directly to Clinical Nurse.</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Vitals Snapshot -->
            <div class="p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80">
                <div class="text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2">Recorded Triage Biometrics</div>
                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                    <div class="bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="block text-[10px] text-slate-400 font-bold">BP</span>
                        <span class="font-black text-slate-800 dark:text-white" x-text="bp"></span>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="block text-[10px] text-slate-400 font-bold">TEMP</span>
                        <span class="font-black text-slate-800 dark:text-white" x-text="temp"></span>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="block text-[10px] text-slate-400 font-bold">HR</span>
                        <span class="font-black text-slate-800 dark:text-white" x-text="hr"></span>
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-2 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="block text-[10px] text-slate-400 font-bold">SpO₂</span>
                        <span class="font-black text-slate-800 dark:text-white" x-text="spo2"></span>
                    </div>
                </div>
                <div class="mt-2 text-[11px] text-slate-600 dark:text-slate-400">
                    <span class="font-bold">Symptoms / Chief Complaint:</span> <span class="italic" x-text="symptoms"></span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-slate-50/80 dark:bg-slate-900/80 px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
            <button @click="closeModal()" 
                    type="button" 
                    :disabled="isSubmitting"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                Review / Go Back
            </button>
            <button @click="submitConfirmed()" 
                    type="button" 
                    :disabled="isSubmitting"
                    class="px-6 py-2.5 rounded-xl font-bold text-xs text-white shadow-md active:scale-[0.98] transition flex items-center gap-2 cursor-pointer"
                    :class="{
                        'bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-700 hover:to-red-800 shadow-rose-600/20': severity === 'severe',
                        'bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 shadow-amber-600/20': severity === 'mild',
                        'bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-emerald-600/20': severity === 'light'
                    }">
                <template x-if="!isSubmitting">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Confirm &amp; Generate Queue</span>
                    </span>
                </template>
                <template x-if="isSubmitting">
                    <span class="flex items-center gap-1.5">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Generating Queue Ticket...</span>
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>

@if(session('print_queue_id'))
    @php
        $printConsultation = \App\Models\Consultation::with(['patient', 'doctor', 'nurse'])->find(session('print_queue_id'));
    @endphp
    <!-- Queue Slip Print Modal (Protected Workflow) -->
    <div x-data="{ 
            showSlipModal: true, 
            hasPrinted: false, 
            confirmedHandover: false,
            doPrint() {
                this.hasPrinted = true;
                this.confirmedHandover = true;
                printSlip();
            },
            canProceed() {
                return this.hasPrinted || this.confirmedHandover;
            },
            closeModal() {
                if (!this.canProceed()) {
                    alert('Please print the queue slip or check the confirmation box before proceeding.');
                    return;
                }
                this.showSlipModal = false;
            }
         }" 
         x-show="showSlipModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         @keydown.window="if (showSlipModal && ($event.key === 'p' || $event.key === 'P')) { doPrint(); }"
         @keydown.escape.window.prevent="">
         
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showSlipModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <!-- Modal Panel -->
            <div x-show="showSlipModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-sm overflow-hidden border border-slate-200/80 dark:border-slate-800/80">
                
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white px-5 py-4 flex justify-between items-center">
                    <h3 class="text-sm font-black flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center border border-white/30">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </span>
                        <span>Queue Slip Generated</span>
                    </h3>
                    @if($printConsultation)
                    <span class="text-xs font-black font-mono px-2.5 py-0.5 rounded-full bg-white/20 text-white border border-white/30">
                        {{ $printConsultation->queue_number }}
                    </span>
                    @endif
                </div>

                <div class="bg-slate-50/50 dark:bg-slate-950/50 p-4 flex flex-col items-center">
                    <!-- Auto-print notice badge -->
                    <div class="w-full max-w-[280px] mb-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl p-2.5 text-center text-[11px] text-emerald-800 dark:text-emerald-300 font-medium flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                        <span>Print prompt should open automatically. Click below or press <strong>P</strong> if needed.</span>
                    </div>

                    <!-- Embedded slip iframe -->
                    @if($printConsultation)
                    <iframe id="queueSlipIframe" 
                            src="{{ route('frontdesk.queue-slip', $printConsultation->id) }}" 
                            onload="onQueueSlipIframeLoad(this)"
                            class="bg-white rounded-2xl shadow-md w-[280px] h-[390px] overflow-hidden border border-slate-200 dark:border-slate-800" 
                            style="pointer-events: none;"></iframe>
                    @else
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-md w-[280px] h-[390px] flex items-center justify-center text-slate-400 text-xs font-semibold">Queue slip record not found.</div>
                    @endif

                    <!-- Safety Confirmation Checkbox -->
                    <div class="mt-3.5 w-full max-w-[280px] bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex items-start gap-2.5 text-left shadow-2xs">
                        <input type="checkbox" id="slipConfirmed" x-model="confirmedHandover" class="mt-0.5 rounded text-emerald-600 focus:ring-0 cursor-pointer">
                        <label for="slipConfirmed" class="text-xs text-slate-700 dark:text-slate-300 font-medium cursor-pointer select-none leading-tight">
                            Queue slip has been printed and handed to patient.
                        </label>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 px-5 py-4 flex flex-col sm:flex-row-reverse gap-2.5 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" 
                            @click="doPrint()" 
                            class="w-full inline-flex justify-center items-center rounded-xl px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-xs font-bold text-white shadow-md shadow-emerald-600/20 active:scale-[0.98] transition cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span x-text="hasPrinted ? 'Reprint Slip (P)' : 'Print Queue Slip (P)'"></span>
                    </button>
                    
                    <button type="button" 
                            @click="closeModal()" 
                            :disabled="!canProceed()"
                            :class="canProceed() ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 cursor-pointer' : 'bg-slate-50 text-slate-300 dark:bg-slate-800/40 dark:text-slate-600 cursor-not-allowed border-dashed'"
                            class="w-full inline-flex justify-center items-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-2.5 text-xs font-bold transition">
                        Done / Next Patient
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function printSlip() {
            var iframe = document.getElementById('queueSlipIframe');
            if (iframe && iframe.contentWindow) {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch(e) {
                    console.warn('Iframe print failed, opening print window:', e);
                    if (iframe.src) window.open(iframe.src, '_blank');
                }
            }
        }
        
        function onQueueSlipIframeLoad(iframe) {
            setTimeout(function() {
                try {
                    if (iframe && iframe.contentWindow) {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    }
                } catch(err) {
                    console.warn('Auto print was blocked by browser security policy:', err);
                }
            }, 350);
        }

        window.addEventListener('message', function(e) {
            if (e.data && e.data.type === 'QUEUE_SLIP_RENDERED') {
                var iframe = document.getElementById('queueSlipIframe');
                if (iframe) onQueueSlipIframeLoad(iframe);
            }
        });
    </script>
@endif

<script>
    // Filter fields to strictly alphanumeric uppercase without symbols/numbers
    document.addEventListener('input', function(e) {
        const nameFields = ['first_name', 'middle_name', 'last_name', 'suffix', 'guardian_name', 'mothers_maiden_name'];
        if (e.target.tagName === 'INPUT' && nameFields.includes(e.target.name)) {
            let val = e.target.value;
            let newVal = val.replace(/[^a-zA-ZÑñ\s\.\-]/g, '').toUpperCase();
            if (val !== newVal) {
                let start = e.target.selectionStart;
                let end = e.target.selectionEnd;
                e.target.value = newVal;
                if (e.target.hasAttribute('x-model') || e.target.hasAttribute('wire:model')) {
                    e.target.dispatchEvent(new Event('input', { bubbles: true }));
                }
                try { e.target.setSelectionRange(start, end); } catch(ex){}
            }
        }
    });
</script>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Intercept clicks on the triage queue links to prevent full page reload
    const initAjaxLinks = () => {
        document.querySelectorAll('a[href^="?selected_id="], a[href^="?new_from_triage="]').forEach(link => {
            // Remove existing listener if any, to avoid duplicates on re-render
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            newLink.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                const container = document.getElementById('right-panel-container');
                if(!container) return;
                
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('right-panel-container');
                    
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                        // Alpine.js automatically initializes new components
                    }
                    
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                    
                    // Update URL history without reload
                    window.history.pushState({}, '', url);
                })
                .catch(err => {
                    console.error('AJAX Load Error:', err);
                    window.location.href = url; // fallback to standard navigation
                });
            });
        });
    };
    
    initAjaxLinks();
});
</script>
@endpush
