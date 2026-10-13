@extends('layouts.app')

@section('content')
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
            'label' => 'Unknown / Not Sure',
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
            'badge' => 'Secular',
            'badgeClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
        ],
        [
            'value' => 'Roman Catholic',
            'label' => 'Roman Catholic',
            'sublabel' => 'Roman Catholic Christian faith',
            'badge' => 'Catholic',
            'badgeClass' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
        ],
        [
            'value' => 'Islam',
            'label' => 'Islam',
            'sublabel' => 'Muslim faith / Islamic religion',
            'badge' => 'Islam',
            'badgeClass' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300 border-teal-200 dark:border-teal-800',
        ],
        [
            'value' => 'Iglesia ni Cristo',
            'label' => 'Iglesia ni Cristo (INC)',
            'sublabel' => 'Iglesia ni Cristo denomination',
            'badge' => 'INC',
            'badgeClass' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300 border-blue-200 dark:border-blue-800',
        ],
        [
            'value' => 'Born Again',
            'label' => 'Born Again / Evangelical',
            'sublabel' => 'Protestant evangelical congregation',
            'badge' => 'Evangelical',
            'badgeClass' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border-sky-200 dark:border-sky-800',
        ],
        [
            'value' => 'Adventist',
            'label' => 'Seventh-day Adventist',
            'sublabel' => 'SDA denomination',
            'badge' => 'Adventist',
            'badgeClass' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
        ],
        [
            'value' => 'Aglipayan',
            'label' => 'Aglipayan (IFI)',
            'sublabel' => 'Philippine Independent Church',
            'badge' => 'IFI',
            'badgeClass' => 'bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-300 border-purple-200 dark:border-purple-800',
        ],
        [
            'value' => "Jehovah's Witnesses",
            'label' => "Jehovah's Witnesses",
            'sublabel' => "Jehovah's Witnesses faith",
            'badge' => 'JW',
            'badgeClass' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200 dark:border-amber-800',
        ],
        [
            'value' => 'Others',
            'label' => 'Others (Please specify)',
            'sublabel' => 'Enter specific religious affiliation below',
            'badge' => 'Custom',
            'badgeClass' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200 dark:border-rose-800',
        ],
    ];

    $guardianRelationOptions = [
        [
            'value' => 'Mother',
            'label' => 'Mother',
            'sublabel' => 'Biological or adoptive mother',
            'badge' => 'Mother',
            'badgeClass' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-700',
            'icon' => 'user',
            'iconColor' => 'bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400',
        ],
        [
            'value' => 'Father',
            'label' => 'Father',
            'sublabel' => 'Biological or adoptive father',
            'badge' => 'Father',
            'badgeClass' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-700',
            'icon' => 'user',
            'iconColor' => 'bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400',
        ],
        [
            'value' => 'Grandparent',
            'label' => 'Grandparent',
            'sublabel' => 'Grandmother or grandfather',
            'badge' => 'Senior Guardian',
            'badgeClass' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-700',
            'icon' => 'star',
            'iconColor' => 'bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400',
        ],
        [
            'value' => 'Sibling',
            'label' => 'Sibling',
            'sublabel' => 'Brother or sister of legal age',
            'badge' => 'Sibling',
            'badgeClass' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700',
            'icon' => 'child',
            'iconColor' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400',
        ],
        [
            'value' => 'Others (Please Specify)',
            'label' => 'Others (Please Specify)',
            'sublabel' => 'Aunt, uncle, legal guardian, caregiver',
            'badge' => 'Custom',
            'badgeClass' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-700',
            'icon' => 'edit',
            'iconColor' => 'bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400',
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
    <div class="py-6 sm:py-8" x-data="appointmentForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <x-breadcrumb :items="['Appointments' => route('appointment.create'), 'Book Consultation' => '']">
                <x-slot:right>
                    <a href="{{ route('appointment.manage') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors group">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Manage Existing Appointment</span>
                        <svg class="w-3 h-3 text-slate-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </x-slot:right>
            </x-breadcrumb>

            <!-- Modern Step Progress Indicator -->
            <div class="mb-6 rounded-2xl bg-white/75 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 p-4 sm:p-5 shadow-2xs backdrop-blur-xs"
                 x-data="{ stepLabels: ['Service', 'Patient Info', 'Date', 'Verification', 'Confirmation'] }">
                <div class="flex items-start justify-between relative max-w-2xl mx-auto">
                    <!-- Progress Bar Background Track -->
                    <div class="absolute left-6 right-6 top-5 transform -translate-y-1/2 h-1 bg-slate-200 dark:bg-slate-700 rounded-full z-0"></div>
                    <!-- Progress Bar Fill Track -->
                    <div class="absolute left-6 top-5 transform -translate-y-1/2 h-1 bg-emerald-700 rounded-full z-0 transition-all duration-300"
                        :style="'width: calc(' + progress + '% * 0.88)'"></div>

                    <!-- Steps 1-5 Bubbles -->
                    <template x-for="i in 5">
                        <div class="relative z-10 flex flex-col items-center w-1/5">
                            <button type="button" @click="goToStep(i)"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center font-display font-bold text-xs sm:text-sm transition-all duration-300 focus:outline-none cursor-pointer"
                                :class="step >= i ? 'bg-emerald-700 text-white shadow-xs scale-105 ring-4 ring-emerald-100 dark:ring-emerald-950/60' : 'bg-white dark:bg-slate-800 text-slate-400 border border-slate-200 dark:border-slate-700'" 
                                x-text="'0' + i"
                                :disabled="i > step && i > maxStepReached + 1"></button>
                            <span class="text-[10px] sm:text-[11px] font-bold mt-2 text-center uppercase tracking-wider transition-colors"
                                  :class="step >= i ? 'text-emerald-800 dark:text-emerald-400 font-extrabold' : 'text-slate-400 dark:text-slate-500'" 
                                  x-text="stepLabels[i-1]"></span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="rounded-2xl mb-36 p-6 sm:p-10 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-md relative z-10 overflow-hidden">
                
                <!-- Loading Overlay -->
                <div x-show="isLoading"
                    class="absolute inset-0 bg-white/85 dark:bg-slate-900/85 backdrop-blur-xs z-20 flex flex-col items-center justify-center gap-3">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-700"></div>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 tracking-wide">Processing your appointment booking...</span>
                </div>

                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Public Health Consultation <span class="text-emerald-700 dark:text-emerald-400">Booking</span>
                    </h1>
                    <p class="mt-1.5 text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto">
                        Reserve an official consultation schedule at Rural Health Unit — Silang, Cavite.
                    </p>
                </div>

                <form id="appointment-form" action="{{ route('appointment.store') }}" method="POST"
                    @submit="submitForm($event)">
                    @csrf
                    <div x-show="step === 1" x-transition>
                        <h2 class="font-display text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Step 1: Select Consultation Service
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Pediatrics Tile -->
                            <div @click="setService('pedia')"
                                class="cursor-pointer rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-200 border-2 card-hover"
                                :class="formData.type === 'pedia' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/30 shadow-md ring-2 ring-emerald-400/30' : 'border-gray-200 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60 hover:border-emerald-400/50'">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 flex items-center justify-center text-3xl mb-3 shadow-inner">
                                    👶
                                </div>
                                <span class="font-display font-extrabold text-lg text-gray-900 dark:text-white">{{ __('Pediatrics') }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1 font-medium">General consultation & check-up for infants (6 mos+) and children (up to 12 yrs)</span>
                                <span class="mt-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                                    Dedicated Pedia Doctor
                                </span>
                            </div>

                            <!-- Adult Follow-up Tile -->
                            <div @click="setService('adult')"
                                class="cursor-pointer rounded-2xl p-6 flex flex-col items-center justify-center transition-all duration-200 border-2 card-hover"
                                :class="formData.type === 'adult' ? 'border-emerald-500 bg-emerald-50/70 dark:bg-emerald-950/30 shadow-md ring-2 ring-emerald-400/30' : 'border-gray-200 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60 hover:border-emerald-400/50'">
                                <div class="w-14 h-14 rounded-2xl bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300 flex items-center justify-center text-3xl mb-3 shadow-inner">
                                    👨‍⚕️
                                </div>
                                <span class="font-display font-extrabold text-lg text-gray-900 dark:text-white">{{ __('Adult Follow-up') }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1 font-medium">Check-ups & continuous care for regular adult & senior patients</span>
                                <span class="mt-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/20">
                                    General Physician
                                </span>
                            </div>

                            <!-- Regular Adult Consultation Tile (Walk-in Only — Not Selectable) -->
                            <div class="relative rounded-2xl p-6 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50/80 dark:bg-slate-800/40 opacity-80">
                                {{-- Walk-in Badge --}}
                                <span class="absolute top-3 right-3 px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase tracking-widest bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-300/50 dark:border-amber-700/50">
                                    Walk-in Only
                                </span>
                                <div class="w-14 h-14 rounded-2xl bg-slate-200 dark:bg-slate-700/60 text-slate-500 dark:text-slate-400 flex items-center justify-center text-3xl mb-3">
                                    🏥
                                </div>
                                <span class="font-display font-extrabold text-lg text-slate-700 dark:text-slate-200">{{ __('Regular Adult') }}</span>
                                <span class="text-xs text-slate-600 dark:text-slate-300 text-center mt-1 font-medium">First-time & general consultations for adults, seniors, and PWDs</span>
                                <span class="mt-3 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600">
                                    No Appointment Needed
                                </span>
                                {{-- Explanation --}}
                                <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 w-full">
                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 text-center leading-relaxed">
                                        <svg class="w-3.5 h-3.5 inline-block mr-0.5 -mt-0.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/></svg>
                                        Regular adult consultations have <strong class="text-slate-800 dark:text-slate-100">no slot limit</strong> — simply visit the RHU during clinic hours. No prior booking required.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="type" x-model="formData.type">

                        <div class="mt-6" x-show="formData.type === 'pedia'" x-transition>
                            <div class="p-4 rounded-2xl border transition-all duration-200"
                                :class="formData.is_follow_up ? 'border-emerald-500 bg-emerald-50/80 dark:bg-emerald-950/40 shadow-xs' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50'">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" x-model="formData.is_follow_up"
                                        class="w-5 h-5 text-emerald-600 rounded-lg focus:ring-emerald-500 border-gray-300 dark:border-gray-600 cursor-pointer">
                                    <input type="hidden" name="is_follow_up" :value="formData.is_follow_up ? 1 : 0">
                                    <div>
                                        <span class="block text-sm font-bold text-gray-800 dark:text-white">Is this a follow-up visit?</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Check this if the patient was previously advised by the pediatrician to return.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Manage existing appointment callout --}}
                        <div class="mt-6 p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 text-slate-600 dark:text-slate-300">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span>Already booked an appointment? Check status, reschedule, or cancel your visit.</span>
                            </div>
                            <a href="{{ route('appointment.manage') }}"
                               class="inline-flex items-center gap-1.5 font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors whitespace-nowrap shrink-0">
                                <span>Manage Appointment</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>                    <!-- Step 2: Patient Info -->
                    <div x-show="step === 2" x-transition style="display: none;">
                        <div class="flex items-center justify-between pb-3 mb-6 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Step 2: Patient Demographic Information</span>
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Please provide accurate constituent details matching official government identification.</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span x-text="formData.type === 'pedia' ? 'Pediatric Patient' : 'Adult Follow-up'"></span>
                            </span>
                        </div>

                        {{-- Follow-up Profile Notice --}}
                        <div x-show="isFollowUp" x-cloak class="p-4 mb-6 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 border border-emerald-300/40">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="text-xs">
                                    <h4 class="font-bold text-emerald-900 dark:text-emerald-200">Registered RHU Patient Profile</h4>
                                    <p class="text-emerald-700 dark:text-emerald-300 mt-0.5 leading-relaxed">As a returning patient with existing clinical records, your permanent address and demographic files are securely saved in our system. Please confirm your identity and describe your consultation concerns.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 1: Demographics Card --}}
                        <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 sm:p-6 mb-6 border border-slate-200/80 dark:border-slate-800/80 shadow-2xs">
                            <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Patient Demographics (Permanent Record)</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('First Name') }} <span class="text-rose-500">*</span></label>
                                    <input type="text" name="first_name" x-model="formData.first_name" required
                                        @input="formData.first_name = $event.target.value.toUpperCase(); if (errors.first_name) delete errors.first_name;"
                                        placeholder="e.g. JUAN"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                        :class="errors.first_name ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                    <p x-show="errors.first_name" class="text-rose-500 text-xs mt-1" x-text="errors.first_name"></p>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Middle Name') }}</label>
                                    <input type="text" name="middle_name" x-model="formData.middle_name"
                                        @input="formData.middle_name = $event.target.value.toUpperCase()"
                                        placeholder="Optional"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Last Name') }} <span class="text-rose-500">*</span></label>
                                    <input type="text" name="last_name" x-model="formData.last_name" required
                                        @input="formData.last_name = $event.target.value.toUpperCase(); if (errors.last_name) delete errors.last_name;"
                                        placeholder="e.g. DELA CRUZ"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                        :class="errors.last_name ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                    <p x-show="errors.last_name" class="text-rose-500 text-xs mt-1" x-text="errors.last_name"></p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Suffix') }}</label>
                                    <input type="text" name="suffix" x-model="formData.suffix" placeholder="JR / SR / III"
                                        @input="formData.suffix = $event.target.value.toUpperCase()"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Sex') }} <span class="text-rose-500">*</span></label>
                                    <x-select name="sex" x-model="formData.sex" :options="$demographicSexOptions" :value="old('sex', '')" placeholder="Select Sex..." @change="if (errors.sex) delete errors.sex;" />
                                    <p x-show="errors.sex" class="text-rose-500 text-xs mt-1" x-text="errors.sex"></p>
                                </div>

                                <!-- Date of Birth with Year Search & Live Age -->
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
                                    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                    get daysInMonth() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate(); },
                                    get startDay() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay(); },
                                    setMonth(monthIndex) { 
                                        let currentDay = this.currentDate.getDate();
                                        if (formData.dob && /^\d{4}-\d{2}-\d{2}$/.test(formData.dob)) {
                                            currentDay = parseInt(formData.dob.split('-')[2], 10) || currentDay;
                                        }
                                        const maxDays = new Date(this.currentDate.getFullYear(), monthIndex + 1, 0).getDate();
                                        currentDay = Math.min(currentDay, maxDays);
                                        this.currentDate = new Date(this.currentDate.getFullYear(), monthIndex, currentDay); 
                                        this.showMonthPicker = false;
                                        const y = this.currentDate.getFullYear();
                                        const m = String(monthIndex + 1).padStart(2, '0');
                                        const d = String(currentDay).padStart(2, '0');
                                        formData.dob = `${y}-${m}-${d}`;
                                        if (errors.dob) delete errors.dob;
                                    },
                                    setYear(year) { 
                                        let currentDay = this.currentDate.getDate();
                                        if (formData.dob && /^\d{4}-\d{2}-\d{2}$/.test(formData.dob)) {
                                            currentDay = parseInt(formData.dob.split('-')[2], 10) || currentDay;
                                        }
                                        const maxDays = new Date(year, this.currentDate.getMonth() + 1, 0).getDate();
                                        currentDay = Math.min(currentDay, maxDays);
                                        this.currentDate = new Date(year, this.currentDate.getMonth(), currentDay); 
                                        this.showYearPicker = false;
                                        this.yearSearch = '';
                                        const y = year;
                                        const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                        const d = String(currentDay).padStart(2, '0');
                                        formData.dob = `${y}-${m}-${d}`;
                                        if (errors.dob) delete errors.dob;
                                    },
                                    prevMonth() {
                                        let currentDay = this.currentDate.getDate();
                                        if (formData.dob && /^\d{4}-\d{2}-\d{2}$/.test(formData.dob)) {
                                            currentDay = parseInt(formData.dob.split('-')[2], 10) || currentDay;
                                        }
                                        const targetMonth = this.currentDate.getMonth() - 1;
                                        const targetYear = this.currentDate.getFullYear();
                                        const maxDays = new Date(targetYear, targetMonth + 1, 0).getDate();
                                        currentDay = Math.min(currentDay, maxDays);
                                        this.currentDate = new Date(targetYear, targetMonth, currentDay);
                                        if (formData.dob) {
                                            const y = this.currentDate.getFullYear();
                                            const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                            const d = String(currentDay).padStart(2, '0');
                                            formData.dob = `${y}-${m}-${d}`;
                                            if (errors.dob) delete errors.dob;
                                        }
                                    },
                                    nextMonth() {
                                        let currentDay = this.currentDate.getDate();
                                        if (formData.dob && /^\d{4}-\d{2}-\d{2}$/.test(formData.dob)) {
                                            currentDay = parseInt(formData.dob.split('-')[2], 10) || currentDay;
                                        }
                                        const targetMonth = this.currentDate.getMonth() + 1;
                                        const targetYear = this.currentDate.getFullYear();
                                        const maxDays = new Date(targetYear, targetMonth + 1, 0).getDate();
                                        currentDay = Math.min(currentDay, maxDays);
                                        this.currentDate = new Date(targetYear, targetMonth, currentDay);
                                        if (formData.dob) {
                                            const y = this.currentDate.getFullYear();
                                            const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                            const d = String(currentDay).padStart(2, '0');
                                            formData.dob = `${y}-${m}-${d}`;
                                            if (errors.dob) delete errors.dob;
                                        }
                                    },
                                    isFutureDate(day) {
                                        let dateToCheck = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                        dateToCheck.setHours(0,0,0,0);
                                        let today = new Date(); today.setHours(0,0,0,0);
                                        return dateToCheck > today;
                                    },
                                    selectDate(day) {
                                        if (this.isFutureDate(day)) return;
                                        const y = this.currentDate.getFullYear();
                                        const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                        const d = String(day).padStart(2, '0');
                                        formData.dob = `${y}-${m}-${d}`;
                                        this.currentDate = new Date(y, this.currentDate.getMonth(), day);
                                        if (errors.dob) delete errors.dob;
                                        this.showDatepicker = false;
                                        this.showMonthPicker = false;
                                        this.showYearPicker = false;
                                    },
                                    isSelected(day) {
                                        if(!formData.dob) return false;
                                        const y = this.currentDate.getFullYear();
                                        const m = String(this.currentDate.getMonth() + 1).padStart(2, '0');
                                        const d = String(day).padStart(2, '0');
                                        return formData.dob === `${y}-${m}-${d}`;
                                    },
                                    syncFromDob() {
                                        if (formData.dob && /^\d{4}-\d{2}-\d{2}$/.test(formData.dob)) {
                                            const [y, m, d] = formData.dob.split('-').map(Number);
                                            if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
                                                this.currentDate = new Date(y, m - 1, d);
                                            }
                                        }
                                    },
                                    toggleCalendar() {
                                        this.syncFromDob();
                                        this.showDatepicker = !this.showDatepicker;
                                        this.showMonthPicker = false;
                                        this.showYearPicker = false;
                                        this.yearSearch = '';
                                    },
                                    init() {
                                        this.syncFromDob();
                                        this.$watch('formData.dob', () => this.syncFromDob());
                                    }
                                }" class="relative">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Date of Birth') }} <span class="text-rose-500">*</span></label>
                                        <span x-show="patientAge !== null" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider"
                                              :class="(formData.type === 'pedia' && patientAgeInMonths !== null && patientAgeInMonths < 6) ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : (patientAge <= 12 ? 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300 border border-pink-200 dark:border-pink-800' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800')"
                                              x-text="(patientAge === 0 && patientAgeInMonths !== null ? patientAgeInMonths + ' mos old' : patientAge + ' yrs old') + (formData.type === 'pedia' && patientAgeInMonths !== null && patientAgeInMonths < 6 ? ' • Under 6 Mos (Ineligible)' : (patientAge <= 12 ? ' • Pediatric' : ' • Adult'))"></span>
                                    </div>
                                    <input type="hidden" name="dob" x-model="formData.dob">
                                    <div @click="toggleCalendar()"
                                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border bg-white dark:bg-slate-800 text-xs font-semibold shadow-2xs hover:border-emerald-500 focus:outline-none transition-all cursor-pointer"
                                        :class="errors.dob ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : (showDatepicker ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-700')">
                                        <span x-text="formData.dob ? formData.dob : 'Select Date of Birth'" class="truncate" :class="formData.dob ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500'"></span>
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-500 transition-colors shrink-0 ml-2" :class="showDatepicker ? 'text-emerald-600 dark:text-emerald-400' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p x-show="errors.dob" class="text-rose-500 text-xs mt-1" x-text="errors.dob"></p>

                                    <!-- Datepicker Popup -->
                                    <div x-show="showDatepicker" @click.away="showDatepicker = false; showMonthPicker = false; showYearPicker = false;" style="display: none;"
                                        class="absolute z-50 mt-1 w-[320px] p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl backdrop-blur-md outline-none">
                                        
                                        <!-- Header: Prev, Month & Year Pickers, Next -->
                                        <div class="flex items-center justify-between mb-4 gap-1.5 relative">
                                            <!-- Prev Month Arrow -->
                                            <button type="button" @click="prevMonth()" 
                                                class="p-1.5 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 dark:text-slate-400 dark:hover:text-emerald-300 dark:hover:bg-slate-800 transition cursor-pointer"
                                                title="Previous Month">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                            </button>

                                            <!-- Month & Year Selectors -->
                                            <div class="flex items-center gap-1.5 flex-1 min-w-0">
                                                <!-- Custom Month Dropdown -->
                                                <div class="relative w-3/5" @click.away="showMonthPicker = false">
                                                    <button type="button" 
                                                        @click="showMonthPicker = !showMonthPicker; showYearPicker = false"
                                                        class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-xl border text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                        :class="showMonthPicker 
                                                            ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/70 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-500' 
                                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:border-emerald-400 hover:bg-white dark:hover:bg-slate-700'">
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
                                                        class="absolute left-0 top-full mt-1.5 w-48 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-1.5 z-60 custom-scrollbar backdrop-blur-md"
                                                        style="display: none;">
                                                        <div class="space-y-0.5">
                                                            <template x-for="(month, index) in monthNames" :key="index">
                                                                <button type="button" 
                                                                    @click="setMonth(index)"
                                                                    class="w-full px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between cursor-pointer transition-colors text-left"
                                                                    :class="currentDate.getMonth() === index 
                                                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold border border-emerald-300 dark:border-emerald-800 shadow-2xs' 
                                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                                    <span x-text="month"></span>
                                                                    <svg x-show="currentDate.getMonth() === index" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Custom Year Dropdown with Search -->
                                                <div class="relative w-2/5" @click.away="showYearPicker = false">
                                                    <button type="button" 
                                                        @click="showYearPicker = !showYearPicker; showMonthPicker = false; if (showYearPicker) { $nextTick(() => { $refs.dobYearSearchInput?.focus(); const active = $refs.dobYearList?.querySelector('[data-selected=true]'); if (active) active.scrollIntoView({ block: 'center' }); }); }"
                                                        class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-xl border text-xs font-bold transition-all shadow-2xs cursor-pointer"
                                                        :class="showYearPicker 
                                                            ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/70 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-500' 
                                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:border-emerald-400 hover:bg-white dark:hover:bg-slate-700'">
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
                                                        class="absolute right-0 top-full mt-1.5 w-40 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl p-2 z-60 backdrop-blur-md"
                                                        style="display: none;">
                                                        
                                                        <!-- Quick Year Search -->
                                                        <div class="relative mb-1.5">
                                                            <input type="text"
                                                                   x-ref="dobYearSearchInput"
                                                                   x-model="yearSearch"
                                                                   placeholder="Type year..."
                                                                   class="w-full pl-6 pr-2 py-1 text-xs font-semibold rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" />
                                                            <svg class="w-3 h-3 text-slate-400 absolute left-1.5 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                            </svg>
                                                        </div>

                                                        <!-- Scrollable Years List -->
                                                        <div x-ref="dobYearList" class="max-h-52 overflow-y-auto space-y-0.5 custom-scrollbar pr-0.5">
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
                                            <button type="button" @click="nextMonth()" 
                                                class="p-1.5 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 dark:text-slate-400 dark:hover:text-emerald-300 dark:hover:bg-slate-800 transition cursor-pointer"
                                                title="Next Month">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>

                                        <!-- Days of Week -->
                                        <div class="grid grid-cols-7 gap-1 mb-2">
                                            <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                                                <div class="text-center text-[11px] font-bold text-slate-400 dark:text-slate-500" x-text="day"></div>
                                            </template>
                                        </div>

                                        <!-- Calendar Days Grid -->
                                        <div class="grid grid-cols-7 gap-1">
                                            <template x-for="blank in startDay"><div class="p-1"></div></template>
                                            <template x-for="day in daysInMonth" :key="currentDate.getFullYear() + '-' + currentDate.getMonth() + '-' + day">
                                                <div @click="selectDate(day)"
                                                    class="h-8 md:h-9 rounded-xl flex items-center justify-center text-xs transition-all border border-transparent"
                                                    :class="{
                                                         'bg-emerald-600 text-white font-bold shadow-xs cursor-pointer': isSelected(day),
                                                         'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:border-emerald-400 cursor-pointer': !isSelected(day) && !isFutureDate(day),
                                                         'bg-slate-100 dark:bg-slate-800 text-slate-300 dark:text-slate-600 cursor-not-allowed': isFutureDate(day)
                                                     }" x-text="day">
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Footer: Selected preview and Done button -->
                                        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                            <div class="truncate mr-2">
                                                <span class="text-slate-400 dark:text-slate-500">Selected: </span>
                                                <span class="font-bold text-emerald-700 dark:text-emerald-400" x-text="formData.dob ? formData.dob : 'None'"></span>
                                            </div>
                                            <button type="button" @click="showDatepicker = false; showMonthPicker = false; showYearPicker = false;"
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition shrink-0 cursor-pointer">
                                                Done
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Age vs Service Warning Banner --}}
                                <div x-show="ageMismatchNotice" x-cloak class="md:col-span-2 p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-700/60 flex items-start gap-3 shadow-2xs">
                                    <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <div class="flex-1 text-xs">
                                        <h5 class="font-bold text-amber-900 dark:text-amber-200">Patient Age Notice</h5>
                                        <p class="text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed" x-text="ageMismatchNotice"></p>
                                    </div>
                                </div>

                                {{-- Demographics: Civil Status & Blood Type (Hidden for Follow-up) --}}
                                <div x-show="!isFollowUp" class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Civil Status') }}</label>
                                    <x-select name="civil_status" x-model="formData.civil_status" :options="$demographicCivilStatusOptions" placeholder="Select Status..." />
                                </div>

                                <div x-show="!isFollowUp" class="md:col-span-1">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Blood Type') }} <span class="text-rose-500">*</span></label>
                                    <x-select name="blood_type" x-model="formData.blood_type" :options="$demographicBloodTypeOptions" placeholder="Select Blood Type..." @change="if (errors.blood_type) delete errors.blood_type;" />
                                    <p x-show="errors.blood_type" class="text-rose-500 text-xs mt-1" x-text="errors.blood_type"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Contact & Address --}}
                        <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 sm:p-6 mb-6 border border-slate-200/80 dark:border-slate-800/80 shadow-2xs">
                            <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Contact &amp; Residence Address</h4>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        <span x-text="formData.type === 'pedia' ? 'Guardian Email Address' : 'Email Address'"></span> <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="email" name="email" x-model="formData.email" :required="true" placeholder="example@email.com"
                                        @input="formData.email = $event.target.value; if (errors.email) delete errors.email;"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 py-2.5 sm:text-xs font-semibold transition-colors"
                                        :class="errors.email ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                    <p x-show="errors.email" class="text-rose-500 text-xs mt-1" x-text="errors.email"></p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">We will send a one-time verification code and appointment updates to this address.</p>
                                </div>
                                <div x-show="formData.type !== 'pedia' && !isFollowUp">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ __('Contact Number') }} <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="contact_number" x-model="formData.contact_number" placeholder="09XXXXXXXXX" maxlength="11"
                                        x-on:input="formData.contact_number = $event.target.value.replace(/[^0-9]/g, '').slice(0, 11); if (errors.contact_number) delete errors.contact_number;"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 px-3.5 py-2.5 sm:text-xs font-semibold transition-colors"
                                        :class="errors.contact_number ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                    <p x-show="errors.contact_number" class="text-rose-500 text-xs mt-1" x-text="errors.contact_number"></p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 font-medium">Standard 11-digit Philippine mobile phone number.</p>
                                </div>
                            </div>

                            <div x-show="!isFollowUp" x-data="{
                                house_no: '{{ old('house_no', '') }}',
                                street: '{{ old('street', '') }}',
                                building: '{{ old('building', '') }}',
                                barangay: '{{ old('barangay', '') }}',
                                city_province: 'Silang, Cavite',
                                get fullAddress() {
                                    let parts = [];
                                    if (this.house_no && this.house_no.trim()) parts.push(this.house_no.trim());
                                    if (this.street && this.street.trim()) parts.push(this.street.trim());
                                    if (this.building && this.building.trim()) parts.push(this.building.trim());
                                    if (this.barangay) parts.push(this.barangay);
                                    parts.push(this.city_province);
                                    return parts.join(', ').replace(/^, | ,/g, '').trim();
                                }
                            }" x-effect="formData.address = fullAddress; formData.barangay = barangay; formData.house_no = house_no; formData.street = street; formData.building = building; formData.city_province = city_province; if (barangay && errors.address) delete errors.address;">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">{{ __('Residential Address') }} <span class="text-rose-500">*</span></label>
                                    <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">Exclusive for Silang, Cavite constituents</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                    <div class="md:col-span-3">
                                        <input type="text" x-model="house_no" :required="!isFollowUp" placeholder="House No."
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-4">
                                        <input type="text" x-model="street" placeholder="Street Name (Optional)"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-5">
                                        <input type="text" x-model="building" placeholder="Building / Subdivision (Optional)"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-6">
                                        <x-select 
                                            name="barangay"
                                            x-model="barangay" 
                                            :options="$demographicBarangayOptions" 
                                            :value="old('barangay', '')" 
                                            placeholder="Select Barangay..." 
                                            :searchable="true" 
                                            search-placeholder="Search barangay..." 
                                            @change="if (errors.address) delete errors.address;"
                                        />
                                    </div>
                                    <div class="md:col-span-6">
                                        <input type="text" x-model="city_province" readonly
                                            class="w-full rounded-xl border border-slate-200/50 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 text-slate-600 dark:text-slate-400 px-3.5 py-2.5 sm:text-xs font-semibold uppercase cursor-not-allowed">
                                    </div>
                                </div>
                                <input type="hidden" name="address" :value="fullAddress">
                                <input type="hidden" name="house_no" :value="house_no">
                                <input type="hidden" name="street" :value="street">
                                <input type="hidden" name="building" :value="building">
                                <input type="hidden" name="city_province" :value="city_province">
                                <p x-show="errors.address" class="text-rose-500 text-xs mt-1" x-text="errors.address"></p>
                            </div>
                        </div>

                        {{-- Section 3: Socio-demographic Background (Hidden for Follow-up) --}}
                        <div x-show="!isFollowUp" x-cloak class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 sm:p-6 mb-6 border border-slate-200/80 dark:border-slate-800/80 shadow-2xs">
                            <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Social &amp; Health Background</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                {{-- PhilHealth Number with Eye Toggle --}}
                                <div x-data="{ showPhilhealth: false }" class="md:col-span-3 lg:col-span-1">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        <span x-text="formData.type === 'pedia' ? `Guardian's PhilHealth No.` : `PhilHealth No.`"></span>
                                        <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showPhilhealth ? 'text' : 'password'" name="philhealth_number" :required="!isFollowUp"
                                            x-model="formData.philhealth_number" placeholder="12-123456789-0" maxlength="14"
                                            @input="formData.philhealth_number = formatPhilHealth($event.target.value); if (errors.philhealth) delete errors.philhealth;"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold pr-10 uppercase transition-colors"
                                            :class="errors.philhealth ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                        <button type="button" @click="showPhilhealth = !showPhilhealth" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                                            <svg x-show="!showPhilhealth" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <svg x-show="showPhilhealth" style="display:none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        </button>
                                    </div>
                                    <p x-show="errors.philhealth" class="text-rose-500 text-xs mt-1" x-text="errors.philhealth"></p>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Education Attainment') }}</label>
                                    <x-select name="education" x-model="formData.education" :options="$demographicEducationOptions" placeholder="Select Attainment..." />
                                </div>

                                <div x-data="{
                                    selectedReligion: '{{ old('religion', '') }}',
                                    customReligion: '',
                                    get finalReligion() { return this.selectedReligion === 'Others' ? this.customReligion : this.selectedReligion; }
                                }" x-effect="formData.religion = finalReligion; if (errors.religion && finalReligion) delete errors.religion;">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Religion') }} <span x-show="formData.type !== 'pedia'" class="text-rose-500">*</span></label>
                                    <x-select x-model="selectedReligion" :options="$demographicReligionOptions" placeholder="Select Religion..." class="mb-2" />
                                    <input type="text" x-show="selectedReligion === 'Others'" x-model="customReligion"
                                        placeholder="Specify religion..." style="display:none;"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    <input type="hidden" name="religion" :value="finalReligion">
                                    <p x-show="errors.religion" class="text-rose-500 text-xs mt-1" x-text="errors.religion"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{
                                selectedOccupation: '{{ old('occupation', '') }}',
                                customOccupation: '',
                                get finalOccupation() { return this.selectedOccupation === 'Others' ? this.customOccupation : this.selectedOccupation; }
                            }" x-effect="formData.occupation = finalOccupation">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Occupation') }}</label>
                                    <x-select x-model="selectedOccupation" :options="$demographicOccupationOptions" placeholder="Select Occupation..." class="mb-2" />
                                    <input type="text" x-show="selectedOccupation === 'Others'" x-model="customOccupation"
                                        placeholder="Specify occupation..." style="display:none;"
                                        class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    <input type="hidden" name="occupation" :value="finalOccupation">
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Family Details & Guardian Information (Hidden for Follow-up) --}}
                        <div x-show="!isFollowUp" x-cloak class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 sm:p-6 mb-6 border border-slate-200/80 dark:border-slate-800/80 shadow-2xs">
                            <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Family &amp; Guardian Information</h4>
                            </div>
                            
                            {{-- Mother's Maiden Name (Hidden for Follow-up) --}}
                            <div x-show="!isFollowUp" class="mb-5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __("Mother's Maiden Name (Birth Name)") }} <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5">
                                    <div class="md:col-span-4">
                                        <input type="text" x-model="formData.mother_first_name" :required="!isFollowUp" placeholder="Mother's First Name"
                                            @input="formData.mother_first_name = $event.target.value.toUpperCase(); if (errors.mothers_maiden_name && formData.mother_first_name && formData.mother_last_name) delete errors.mothers_maiden_name; if (motherIsGuardian) syncMotherToGuardian();"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                            :class="errors.mothers_maiden_name && !formData.mother_first_name ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                    </div>
                                    <div class="md:col-span-3">
                                        <input type="text" x-model="formData.mother_middle_name" placeholder="Mother's Middle Name"
                                            @input="formData.mother_middle_name = $event.target.value.toUpperCase(); if (motherIsGuardian) syncMotherToGuardian();"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                    <div class="md:col-span-3">
                                        <input type="text" x-model="formData.mother_last_name" :required="!isFollowUp" placeholder="Mother's Last Name"
                                            @input="formData.mother_last_name = $event.target.value.toUpperCase(); if (errors.mothers_maiden_name && formData.mother_first_name && formData.mother_last_name) delete errors.mothers_maiden_name; if (motherIsGuardian) syncMotherToGuardian();"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                            :class="errors.mothers_maiden_name && !formData.mother_last_name ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                    </div>
                                    <div class="md:col-span-2">
                                        <input type="text" x-model="formData.mother_suffix" placeholder="Suffix (JR/SR)"
                                            @input="formData.mother_suffix = $event.target.value.toUpperCase(); if (motherIsGuardian) syncMotherToGuardian();"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors">
                                    </div>
                                </div>
                                <input type="hidden" name="mothers_maiden_name" :value="`${formData.mother_first_name} ${formData.mother_middle_name ? formData.mother_middle_name + ' ' : ''}${formData.mother_last_name}${formData.mother_suffix ? ' ' + formData.mother_suffix : ''}`.trim().replace(/\s+/g, ' ').toUpperCase()">
                                <p x-show="errors.mothers_maiden_name" class="text-rose-500 text-xs mt-1" x-text="errors.mothers_maiden_name"></p>
                            </div>
                            
                            {{-- Guardian Info (Pediatric only) --}}
                            <div class="p-5 bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40 rounded-2xl" x-show="formData.type === 'pedia' && !isFollowUp" x-cloak>
                                <div class="mb-4 pb-2 border-b border-amber-200/60 dark:border-amber-900/60 flex items-center justify-between">
                                    <div>
                                        <h5 class="text-xs font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            Guardian Information (Required for Pediatrics)
                                        </h5>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Pediatric patients (12 years old and below) require a designated parent or legal guardian.</p>
                                    </div>
                                </div>

                                {{-- Mother is also Guardian Toggle Card --}}
                                <label class="group relative flex items-center justify-between gap-4 p-3.5 mb-4 rounded-xl border transition-all duration-200 cursor-pointer select-none"
                                    :class="motherIsGuardian 
                                        ? 'bg-emerald-50/90 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-700 shadow-xs ring-1 ring-emerald-500/20' 
                                        : 'bg-white dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 shadow-2xs'">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-200"
                                            :class="motherIsGuardian 
                                                ? 'bg-emerald-600 text-white shadow-xs' 
                                                : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400'">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors">
                                                    Mother is also the patient's guardian
                                                </span>
                                                <span x-show="motherIsGuardian" x-cloak class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white tracking-wide uppercase shadow-2xs">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Linked
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                                <span x-show="!motherIsGuardian">Click to auto-fill guardian name using mother's information from above</span>
                                                <span x-show="motherIsGuardian" class="text-emerald-700 dark:text-emerald-400 font-semibold">Auto-filled and synced with Mother's Name</span>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Toggle Switch -->
                                    <div class="relative inline-flex items-center shrink-0">
                                        <input type="checkbox" x-model="motherIsGuardian" @change="toggleMotherAsGuardian()" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-500/40 dark:peer-focus:ring-emerald-600/40 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-600 shadow-inner"></div>
                                    </div>
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Guardian Name') }} <span class="text-rose-500">*</span></label>
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5">
                                            <div class="md:col-span-4">
                                                <input type="text" name="guardian_first_name" x-model="formData.guardian_first_name" :required="formData.type === 'pedia' && !isFollowUp" placeholder="Guardian First Name"
                                                    :readonly="motherIsGuardian"
                                                    @input="if (!motherIsGuardian) { formData.guardian_first_name = $event.target.value.toUpperCase(); if (errors.guardian_first_name && formData.guardian_first_name && formData.guardian_last_name) delete errors.guardian_first_name; }"
                                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                                    :class="[errors.guardian_first_name && !formData.guardian_first_name ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : '', motherIsGuardian ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed' : '']">
                                            </div>
                                            <div class="md:col-span-3">
                                                <input type="text" name="guardian_middle_name" x-model="formData.guardian_middle_name" placeholder="Middle Name"
                                                    :readonly="motherIsGuardian"
                                                    @input="if (!motherIsGuardian) { formData.guardian_middle_name = $event.target.value.toUpperCase(); }"
                                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                                    :class="motherIsGuardian ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed' : ''">
                                            </div>
                                            <div class="md:col-span-3">
                                                <input type="text" name="guardian_last_name" x-model="formData.guardian_last_name" :required="formData.type === 'pedia' && !isFollowUp" placeholder="Last Name"
                                                    :readonly="motherIsGuardian"
                                                    @input="if (!motherIsGuardian) { formData.guardian_last_name = $event.target.value.toUpperCase(); if (errors.guardian_first_name && formData.guardian_first_name && formData.guardian_last_name) delete errors.guardian_first_name; }"
                                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                                    :class="[errors.guardian_first_name && !formData.guardian_last_name ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : '', motherIsGuardian ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed' : '']">
                                            </div>
                                            <div class="md:col-span-2">
                                                <input type="text" name="guardian_suffix" x-model="formData.guardian_suffix" placeholder="Suffix"
                                                    :readonly="motherIsGuardian"
                                                    @input="if (!motherIsGuardian) { formData.guardian_suffix = $event.target.value.toUpperCase(); }"
                                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors"
                                                    :class="motherIsGuardian ? 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed' : ''">
                                            </div>
                                        </div>
                                        <p x-show="errors.guardian_first_name || errors.guardian_last_name" class="text-rose-500 text-xs mt-1" x-text="errors.guardian_first_name || errors.guardian_last_name"></p>
                                    </div>
                                    <div class="md:col-span-1" x-data="{
                                        selectedGuardianRelation: '{{ old('guardian_relation', '') }}',
                                        customGuardianRelation: '',
                                        get finalGuardianRelation() { return this.selectedGuardianRelation === 'Others (Please Specify)' ? this.customGuardianRelation : this.selectedGuardianRelation; }
                                    }" x-effect="formData.guardian_relation = finalGuardianRelation; if (errors.guardian_relation && finalGuardianRelation) delete errors.guardian_relation;">
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Relationship to Patient') }} <span class="text-rose-500">*</span></label>
                                        <x-select name="guardian_relation" x-model="selectedGuardianRelation" :options="$guardianRelationOptions" placeholder="Select Relationship..." class="mb-2" />
                                        <input type="text" x-show="selectedGuardianRelation === 'Others (Please Specify)'" x-model="customGuardianRelation"
                                            placeholder="Specify relationship..." style="display:none;"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold uppercase transition-colors" @input="customGuardianRelation = $event.target.value.toUpperCase(); if (errors.guardian_relation) delete errors.guardian_relation;">
                                        <input type="hidden" name="guardian_relation" :value="finalGuardianRelation">
                                        <p x-show="errors.guardian_relation" class="text-rose-500 text-xs mt-1" x-text="errors.guardian_relation"></p>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Guardian Contact Number') }} <span class="text-rose-500">*</span></label>
                                        <input type="text" name="guardian_contact" x-model="formData.guardian_contact" placeholder="09XXXXXXXXX" maxlength="11"
                                            x-on:input="formData.guardian_contact = formData.guardian_contact.replace(/[^0-9]/g, '').slice(0, 11); if (errors.guardian_contact) delete errors.guardian_contact;"
                                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 px-3.5 py-2.5 sm:text-xs font-semibold transition-colors"
                                            :class="errors.guardian_contact ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''">
                                        <p x-show="errors.guardian_contact" class="text-rose-500 text-xs mt-1" x-text="errors.guardian_contact"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden classification auto-set by service type --}}
                        <input type="hidden" name="classification" :value="formData.type === 'pedia' ? 'Pediatric' : 'Regular Adult'">

                        {{-- Section 5: Chief Complaint --}}
                        <div class="bg-white dark:bg-slate-900/90 rounded-2xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800/80 shadow-2xs">
                            <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100 dark:border-slate-800">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h4 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider">Reason for Visit (Chief Complaint)</h4>
                            </div>

                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Select Common Symptoms') }} <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mb-4">
                                <template x-for="symptom in ['Cough', 'Fever', 'Headache', 'Abdominal Pain', 'Sore Throat', 'Difficulty Breathing', 'Dizziness', 'Skin Rash']" :key="symptom">
                                    <label class="group relative flex items-center gap-2.5 p-3 rounded-xl border transition-all duration-200 cursor-pointer select-none"
                                           :class="formData.symptoms.includes(symptom) 
                                               ? 'bg-emerald-50/90 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-700 shadow-2xs ring-1 ring-emerald-500/20' 
                                               : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-slate-50/50 dark:hover:bg-slate-750'">
                                        <input type="checkbox" :value="symptom" x-model="formData.symptoms" @change="if (errors.complaint) delete errors.complaint;" 
                                               class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-600 dark:bg-slate-700">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-700 dark:group-hover:text-emerald-300 transition-colors" x-text="symptom"></span>
                                    </label>
                                </template>
                            </div>

                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('Additional Symptoms or Consultation Reason') }}</label>
                            <textarea x-model="formData.other_symptom" rows="3" placeholder="Describe other symptoms, prior medical advisories, or specifics of your health concern..."
                                @input="if (errors.complaint) delete errors.complaint;"
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 p-3 text-xs font-semibold transition-colors"
                                :class="errors.complaint && (!combinedComplaint || combinedComplaint.trim() === '') ? 'border-rose-500! ring-1 ring-rose-500! dark:border-rose-500!' : ''"></textarea>
                            
                            <input type="hidden" name="complaint" :value="combinedComplaint">
                            <p x-show="errors.complaint" class="text-rose-500 text-xs mt-1" x-text="errors.complaint"></p>

                            <!-- Live Complaint Summary Preview -->
                            <div x-show="combinedComplaint" x-cloak class="mt-3.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 flex items-center gap-2">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Summary:</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" x-text="combinedComplaint"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Date Selection (First-Come, First-Served) -->
                    <div x-show="step === 3" x-transition style="display: none;">
                        <div class="flex items-center justify-between pb-3 mb-6 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span>Step 3: Select Consultation Date</span>
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Choose an operating clinic date for your public health consultation.</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                First-Come, First-Served
                            </span>
                        </div>
                        
                        <div x-show="isFollowUp && doctorName" x-cloak class="mb-5 bg-emerald-50/80 border border-emerald-200 dark:border-emerald-800/80 rounded-2xl p-4 dark:bg-emerald-950/30 shadow-2xs">
                            <h4 class="font-bold text-xs text-emerald-900 dark:text-emerald-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Assigned Attending Physician Follow-up
                            </h4>
                            <p class="text-xs text-emerald-800 dark:text-emerald-300 mt-1">
                                Your assigned doctor is <strong x-text="doctorName"></strong>. 
                                Please select an available date matching their schedule: <strong class="font-semibold underline" x-text="doctorSchedule"></strong>.
                            </p>
                        </div>

                        <div class="border border-slate-200 dark:border-slate-700 rounded-2xl p-5 bg-white dark:bg-slate-800/80 shadow-2xs">
                            <div class="flex justify-between items-center mb-4">
                                <button type="button" @click="prevMonth()" class="p-2 rounded-xl text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:text-slate-400 dark:hover:bg-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer" :disabled="isPrevMonthDisabled()"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg></button>
                                <h4 class="font-black text-sm sm:text-base text-slate-800 dark:text-white" x-text="monthName + ' ' + currentYear"></h4>
                                <button type="button" @click="nextMonth()" class="p-2 rounded-xl text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:text-slate-400 dark:hover:bg-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer" :disabled="isNextMonthDisabled()"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg></button>
                            </div>
                            <div class="flex gap-4 text-xs mb-4 justify-center font-bold">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Available</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Limited</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Full / Closed</span>
                            </div>
                            <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-slate-400 mb-2">
                                <span>Su</span><span>Mo</span><span>Tu</span>
                                <span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div class="grid grid-cols-7 gap-2">
                                <template x-for="blank in startDay" :key="'blank-'+blank">
                                    <div></div>
                                </template>
                                
                                <template x-for="day in daysInMonth" :key="day">
                                    <div @click="!isPastDate(day) && !isDayFull(day) && selectDate(getDateString(day))"
                                        class="h-16 md:h-20 rounded-xl flex flex-col items-center justify-center font-bold transition text-xs md:text-sm relative group border"
                                        :class="{
                                                'bg-slate-100 dark:bg-slate-800/40 text-slate-300 dark:text-slate-600 cursor-not-allowed border-transparent': isPastDate(day),
                                                'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 cursor-not-allowed border-rose-200 dark:border-rose-800': !isPastDate(day) && isDayFull(day),
                                                'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/60 cursor-pointer border-amber-200 dark:border-amber-800': !isPastDate(day) && isDayLimited(day) && !isDayFull(day),
                                                'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 cursor-pointer border-emerald-200 dark:border-emerald-800': !isPastDate(day) && !isDayLimited(day) && !isDayFull(day),
                                                'ring-2 ring-emerald-600 ring-offset-2 dark:ring-offset-slate-800': selectedDate === getDateString(day)
                                             }">
                                        <span class="text-base sm:text-lg font-black" x-text="day"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <input type="hidden" name="preferred_date" x-model="selectedDate" required>
                        <input type="hidden" name="preferred_time" value="">

                        <!-- Selected Date Info & First-Come First-Served Banner -->
                        <div x-show="selectedDate" x-cloak class="mt-6 p-5 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 border border-emerald-300/40 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 text-xs">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Selected Appointment Date</span>
                                            <h4 class="text-sm sm:text-base font-black text-slate-900 dark:text-white" x-text="formatDate(selectedDate)"></h4>
                                        </div>
                                        <span x-show="formData.type === 'pedia' && !isFollowUp" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-emerald-200 dark:border-emerald-800">
                                            Daily Slot Usage: <span class="font-extrabold text-emerald-700 dark:text-emerald-300" x-text="getSlotsUsage(selectedDate)"></span>
                                        </span>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-emerald-200/60 dark:border-emerald-800/60">
                                        <p class="font-bold text-emerald-950 dark:text-emerald-200 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            First-Come, First-Served Queue Policy
                                        </p>
                                        <p class="text-emerald-800 dark:text-emerald-300 mt-1 leading-relaxed">
                                            RHU Silang consultation queues are processed strictly in the order patients arrive at the clinic on their confirmed appointment day. No specific arrival time window needs to be selected.
                                        </p>
                                        <div class="mt-2 flex flex-wrap items-center gap-3 text-[11px] text-emerald-700 dark:text-emerald-400 font-semibold">
                                            <span>RHU Operating Hours: <strong>8:00 AM – 5:00 PM</strong></span>
                                            <span>•</span>
                                            <span>Recommended Arrival: <strong>8:00 AM – 11:00 AM</strong> for Triage Intake</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: OTP Verification -->
                    <div x-show="step === 4" x-transition style="display: none;">
                        <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Step 4: Email Verification
                        </h2>
                        <div class="text-center">
                            <div class="mb-4">
                                <span class="text-5xl">📧</span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                We have sent a verification code to <span class="font-bold text-teal-600"
                                    x-text="formData.email"></span>.
                                <br>Please enter the code below to proceed.
                            </p>

                            <div class="max-w-xs mx-auto mb-6">
                                <input type="text" x-model="otpCode" placeholder="Enter 6-digit Code" maxlength="6"
                                    class="text-center text-2xl tracking-widest block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-3">
                                <p x-show="errors.otp" class="text-red-500 text-sm mt-2" x-text="errors.otp"></p>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Didn't receive code? <button type="button" @click="sendOtp()"
                                    class="text-teal-600 hover:underline font-medium">Resend Code</button>
                            </p>
                        </div>
                    </div>

                    <!-- Step 5: Confirm -->
                    <div x-show="step === 5" x-transition style="display: none;">
                        <h2 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-4">Step 5: Review & Confirm</h2>

                        <dl
                            class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg mb-6 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4 text-sm">
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Service Type</dt>
                                <dd class="font-medium text-gray-900 dark:text-white capitalize"
                                    x-text="formData.type === 'pedia' ? 'Pediatrics' : 'Follow-up'"></dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Date</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formatDate(selectedDate)">
                                </dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Queue Policy</dt>
                                <dd class="font-medium text-emerald-700 dark:text-emerald-300">
                                    First-Come, First-Served (8:00 AM – 5:00 PM)
                                </dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Patient Name</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    <span
                                        x-text="`${formData.first_name} ${formData.middle_name ? formData.middle_name + ' ' : ''}${formData.last_name}${formData.suffix ? ' ' + formData.suffix : ''}`"></span>
                                </dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Date of Birth</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.dob"></dd>
                            </div>
                            <div class="col-span-2 md:col-span-1">
                                <dt class="text-gray-500 dark:text-gray-400">Sex</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.sex"></dd>
                            </div>
                            <div class="col-span-2" x-show="!isFollowUp">
                                <dt class="text-gray-500 dark:text-gray-400">Address</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.address"></dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400">Visit Type</dt>
                                <dd class="font-medium text-gray-900 dark:text-white"
                                    x-text="formData.is_follow_up ? 'Follow-up Consultation' : 'General Consultation'"></dd>
                            </div>
                            <div class="col-span-2" x-show="formData.type === 'pedia' && !isFollowUp">
                                <dt class="text-gray-500 dark:text-gray-400 mt-4 border-t pt-4 font-bold">Guardian Details
                                </dt>
                                <div class="grid grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400 text-xs">Name</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white"
                                            x-text="`${formData.guardian_first_name} ${formData.guardian_middle_name ? formData.guardian_middle_name + ' ' : ''}${formData.guardian_last_name}${formData.guardian_suffix ? ' ' + formData.guardian_suffix : ''}`.trim()">
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-gray-500 dark:text-gray-400 text-xs">Relation</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white"
                                            x-text="formData.guardian_relation"></dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-gray-500 dark:text-gray-400 text-xs">Contact</dt>
                                        <dd class="font-medium text-gray-900 dark:text-white"
                                            x-text="formData.guardian_contact"></dd>
                                    </div>
                                </div>
                            </div>
                            <div class="col-span-2 mt-4 border-t pt-4">
                                <dt class="text-gray-500 dark:text-gray-400">Email (For OTP & Confirmation)</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="formData.email"></dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400">Chief Complaint</dt>
                                <dd class="font-medium text-gray-900 dark:text-white" x-text="combinedComplaint || 'N/A'">
                                </dd>
                            </div>
                        </dl>

                        <div class="mb-4">
                            <label class="flex items-start space-x-3">
                                <input type="checkbox" name="data_privacy_agreed" x-model="formData.data_privacy_agreed"
                                    required
                                    class="mt-1 rounded border-gray-300 dark:border-gray-600 text-teal-600 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                <span class="text-xs text-gray-600 dark:text-gray-400">
                                    I hereby agree to the Data Privacy Act. I understand that my information will be used
                                    for medical appointment purposes only and will be handled with strict confidentiality.
                                    <button type="button" @click="showPrivacy = true"
                                        class="text-teal-600 hover:underline">{{ __('Read Data Privacy Policy') }}</button>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons (Framer Motion Spring Micro-interactions & Aligned Position) -->
                    <div class="mt-10 pt-6 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <button type="button" @click="prevStep()" x-show="step > 1" x-transition
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            <span>{{ __('Back') }}</span>
                        </button>
                        <div x-show="step === 1"></div> <!-- Spacer -->

                        <!-- Step 1-3 Next Button (Framer Motion style spring scale & arrow hover) -->
                        <button type="button" @click="nextStep()" x-show="step < 4"
                            class="group inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-xs text-white shadow-md transition-all duration-200 hover:scale-[1.03] active:scale-[0.97] hover:shadow-lg hover:shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            style="background: linear-gradient(135deg, #10b981, #0F3D3E);"
                            :disabled="(step === 3 && !selectedDate)">
                            <span x-text="step === 3 ? '{{ __('Proceed to Verification') }}' : '{{ __('Next Step') }}'"></span>
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>

                        <!-- Step 4 Verify Button -->
                        <button type="button" @click="nextStep()" x-show="step === 4"
                            class="group inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-xs text-white shadow-md transition-all duration-200 hover:scale-[1.03] active:scale-[0.97] hover:shadow-lg hover:shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            style="background: linear-gradient(135deg, #10b981, #0F3D3E);">
                            <span>{{ __('Verify & Continue') }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>

                        <!-- Step 5 Confirm Button -->
                        <button type="submit" x-show="step === 5"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-xl font-bold text-xs text-white shadow-md transition-all duration-200 hover:scale-[1.03] active:scale-[0.97] hover:shadow-lg hover:shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            style="background: linear-gradient(135deg, #10b981, #15803d);"
                            :disabled="!formData.data_privacy_agreed || isLoading">
                            <template x-if="!isLoading">
                                <span class="inline-flex items-center gap-2">
                                    <span>{{ __('Confirm Booking') }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </template>
                            <template x-if="isLoading">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ __('Submitting Booking...') }}</span>
                                </span>
                            </template>
                        </button>
                    </div>

                </form>
                <!-- Data Privacy Modal -->
                <div x-show="showPrivacy" style="display: none;"
                    class="fixed inset-0 z-70 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPrivacy = false">
                    </div>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full max-h-[80vh] flex flex-col z-80">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Data Privacy Policy</h3>
                        </div>
                        <div class="p-6 overflow-y-auto">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <strong>Compliance with Republic Act No. 10173 (Data Privacy Act of 2012)</strong>
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                The Rural Health Unit (RHU) is committed to protecting your personal information. By
                                providing your data, you agree to its collection and use for the following purposes:
                            </p>
                            <ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-400 mb-4 space-y-1">
                                <li>Appointment scheduling and management.</li>
                                <li>Medical record keeping and tracking of patient history.</li>
                                <li>Communication regarding your health concerns and appointment status.</li>
                                <li>Public health reporting requirements (anonymized where applicable).</li>
                            </ul>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Your data will be stored securely and will not be shared with unauthorized third parties
                                without your consent, except as required by law.
                            </p>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 flex justify-end">
                            <button type="button" @click="showPrivacy = false"
                                class="bg-gray-200 text-gray-800 dark:text-white px-4 py-2 rounded hover:bg-gray-300 mr-2">{{ __('Close') }}</button>
                            <button type="button" @click="formData.data_privacy_agreed = true; showPrivacy = false"
                                class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">{{ __('I Agree') }}</button>
                        </div>
                    </div>
                </div>
                <!-- Duplicate Booking Modal -->
                <div x-show="showDuplicateModal" style="display: none;"
                    class="fixed inset-0 z-70 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        @click="showDuplicateModal = false"></div>
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all max-w-md w-full z-80 p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-2">Duplicate Booking
                            Detected</h3>
                        <div class="mt-2 text-left bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                            <p class="text-sm text-yellow-800 mb-2">
                                It looks like you already have an active appointment request under this name and email.
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Please check your inbox for your <strong>Appointment Confirmation</strong> or status
                                updates. If you need to cancel or reschedule, please check the 'Manage Appointment' page.
                            </p>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button type="button" @click="showDuplicateModal = false"
                                class="inline-flex justify-center w-full rounded-full border border-transparent shadow-md px-4 py-2 bg-teal-600 text-base font-bold text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition sm:text-sm">
                                Okay, I Understand
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function appointmentForm() {
            return {
                get isFollowUp() {
                    return this.formData.type === 'adult' || (this.formData.type === 'pedia' && this.formData.is_follow_up);
                },
                get patientAge() {
                    if (!this.formData.dob) return null;
                    const d = new Date(this.formData.dob);
                    if (isNaN(d.getTime())) return null;
                    const today = new Date();
                    let age = today.getFullYear() - d.getFullYear();
                    const m = today.getMonth() - d.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < d.getDate())) age--;
                    return age;
                },
                get patientAgeInMonths() {
                    if (!this.formData.dob) return null;
                    const d = new Date(this.formData.dob);
                    if (isNaN(d.getTime())) return null;
                    const today = new Date();
                    let months = (today.getFullYear() - d.getFullYear()) * 12 + (today.getMonth() - d.getMonth());
                    if (today.getDate() < d.getDate()) months--;
                    return months;
                },
                get ageMismatchNotice() {
                    if (this.patientAge === null) return null;
                    if (this.formData.type === 'pedia') {
                        if (this.patientAgeInMonths !== null && this.patientAgeInMonths < 6) {
                            return 'Age Restriction: The Pedia Consultation service is only available for patients aged 6 months and above. Patients younger than 6 months require specialized neonatal or infant healthcare.';
                        }
                        if (this.patientAge > 12) {
                            return `Age Notice: This patient is ${this.patientAge} years old. Pediatric consultation is intended for children 12 years old and below.`;
                        }
                    }
                    if (this.formData.type !== 'pedia' && this.patientAge <= 12) {
                        return `Age Notice: This patient is ${this.patientAge} years old. Children 12 and below must use the Pediatrics consultation service.`;
                    }
                    return null;
                },
                step: 1,
                maxStepReached: 1,
                progress: 0,
                isLoading: false,
                selectedDate: '{{ old('preferred_date', '') }}',
                selectedTime: '{{ old('preferred_time', '') }}',
                timeSlots: [],
                loadingSlots: false,
                followUpDoctorId: null,
                otpCode: '',
                availability: {},
                doctorSchedule: '',
                doctorName: '',
                showPrivacy: false,
                showDuplicateModal: {{ session('duplicate_booking') ? 'true' : 'false' }},

                // Calendar Variables
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                daysInMonth: 0,
                startDay: 0,
                monthName: '',
                months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                
                initCalendar() {
                    this.updateCalendar();
                },
                updateCalendar() {
                    let d = new Date(this.currentYear, this.currentMonth, 1);
                    this.monthName = this.months[this.currentMonth];
                    this.startDay = d.getDay();
                    this.daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                },
                nextMonth() {
                    if (this.isNextMonthDisabled()) return;
                    this.currentMonth++;
                    if (this.currentMonth > 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    }
                    this.updateCalendar();
                    this.fetchAvailability();
                },
                prevMonth() {
                    if (this.isPrevMonthDisabled()) return;
                    this.currentMonth--;
                    if (this.currentMonth < 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    }
                    this.updateCalendar();
                    this.fetchAvailability();
                },
                isPrevMonthDisabled() {
                    let today = new Date();
                    return this.currentYear === today.getFullYear() && this.currentMonth === today.getMonth();
                },
                isNextMonthDisabled() {
                    let today = new Date();
                    let maxMonth = today.getMonth() + 1;
                    let maxYear = today.getFullYear();
                    if (maxMonth > 11) {
                        maxMonth = 0;
                        maxYear++;
                    }
                    return this.currentYear > maxYear || (this.currentYear === maxYear && this.currentMonth >= maxMonth);
                },
                getDateString(day) {
                    let d = new Date(this.currentYear, this.currentMonth, day);
                    let offset = d.getTimezoneOffset();
                    d = new Date(d.getTime() - (offset*60*1000));
                    return d.toISOString().split('T')[0];
                },
                isPastDate(day) {
                    let dateToCheck = new Date(this.currentYear, this.currentMonth, day);
                    let today = new Date();
                    today.setHours(0,0,0,0);
                    return dateToCheck < today;
                },
                isDayFull(day) {
                    let ds = this.getDateString(day);
                    
                    if (this.isFollowUp) {
                        // For follow-up, if the day is not in doctor's schedule, it's considered unavailable (full/disabled)
                        if (!this.validDays) return false;
                        
                        let d = new Date(this.currentYear, this.currentMonth, day);
                        let dayShort = d.toLocaleString('en-US', { weekday: 'short' }); // e.g. "Mon"
                        return !this.validDays.includes(dayShort);
                    } else {
                        // Normal Pedia
                        let slotData = this.availability[ds];
                        if (!slotData) return false; // Default to false while loading
                        if (slotData.capacity <= 0) return true; // Doctor is absent or no capacity
                        return slotData.available <= 0;
                    }
                },
                isDayLimited(day) {
                    if (this.isFollowUp) return false; // Follow-ups don't show "Limited" warning

                    let ds = this.getDateString(day);
                    let slotData = this.availability[ds];
                    if (!slotData || slotData.capacity <= 0) return false;
                    
                    let limitThreshold = slotData.capacity * 0.25; // e.g., if capacity 20, < 5 available is limited
                    return slotData.available > 0 && slotData.available <= limitThreshold;
                },

                // Form Data
                formData: {
                    first_name: '{{ old('first_name', '') }}',
                    middle_name: '{{ old('middle_name', '') }}',
                    last_name: '{{ old('last_name', '') }}',
                    suffix: '{{ old('suffix', '') }}',
                    sex: '{{ old('sex', '') }}',
                    dob: '{{ old('dob', '') }}',
                    civil_status: '{{ old('civil_status', '') }}',
                    blood_type: '{{ old('blood_type', '') }}',
                    address: '{{ old('address', '') }}',
                    barangay: '',
                    philhealth_number: '{{ old('philhealth_number', '') }}',
                    education: '{{ old('education', '') }}',
                    religion: '{{ old('religion', '') }}',
                    occupation: '{{ old('occupation', '') }}',
                    mother_first_name: '',
                    mother_middle_name: '',
                    mother_last_name: '',
                    mother_suffix: '',
                    mothers_maiden_name: '{{ old('mothers_maiden_name', '') }}',
                    classification: '{{ old('classification', '') }}',
                    email: '{{ old('email', '') }}',
                    type: 'pedia',
                    is_follow_up: false,
                    guardian_first_name: '',
                    guardian_middle_name: '',
                    guardian_last_name: '{{ old('guardian_last_name', '') }}',
                    guardian_suffix: '{{ old('guardian_suffix', '') }}',
                    guardian_relation: '{{ old('guardian_relation', '') }}',
                    guardian_contact: '{{ old('guardian_contact', '') }}',
                    guardian_email: '{{ old('guardian_email', '') }}',
                    symptoms: [],
                    other_symptom: '',
                    data_privacy_agreed: {{ old('data_privacy_agreed') ? 'true' : 'false' }}
                },

                formatPhilHealth(value) {
                    if (!value) return '';
                    let raw = value.replace(/\D/g, '').substring(0, 12);
                    let formatted = '';
                    if (raw.length > 0) {
                        formatted = raw.substring(0, 2);
                    }
                    if (raw.length > 2) {
                        formatted += '-' + raw.substring(2, 11);
                    }
                    if (raw.length > 11) {
                        formatted += '-' + raw.substring(11, 12);
                    }
                    return formatted;
                },

                get combinedComplaint() {
                    let parts = [...this.formData.symptoms];
                    if (this.formData.other_symptom && this.formData.other_symptom.trim() !== '') {
                        parts.push('Others: ' + this.formData.other_symptom.trim());
                    }
                    return parts.join(', ');
                },

                init() {
                    @if($errors->any())
                        window.dispatchEvent(new CustomEvent('add-toast', {
                            detail: {
                                type: 'error',
                                message: 'Submission Failed: {{ implode(', ', $errors->all()) }}'
                            }
                        }));

                        // Smart Step Restoration
                        if (this.selectedDate) {
                            this.step = 3; // Go to calendar to confirm date
                            this.maxStepReached = 3;
                            // If we have OTP verified in session? Hard to know. 
                            // But user can click next if date is selected.
                        } else if (this.formData.first_name) {
                            this.step = 2; // Go to info
                            this.maxStepReached = 2;
                        }
                        this.updateProgress();
                    @endif
                },

                errors: {},

                motherIsGuardian: false,

                toggleMotherAsGuardian() {
                    if (this.motherIsGuardian) {
                        this.syncMotherToGuardian();
                    }
                },

                syncMotherToGuardian() {
                    const mFirst = (this.formData.mother_first_name || document.querySelector('input[x-model="formData.mother_first_name"]')?.value || '').trim().toUpperCase();
                    const mMid = (this.formData.mother_middle_name || document.querySelector('input[x-model="formData.mother_middle_name"]')?.value || '').trim().toUpperCase();
                    const mLast = (this.formData.mother_last_name || document.querySelector('input[x-model="formData.mother_last_name"]')?.value || '').trim().toUpperCase();
                    const mSuffix = (this.formData.mother_suffix || document.querySelector('input[x-model="formData.mother_suffix"]')?.value || '').trim().toUpperCase();

                    this.formData.mother_first_name = mFirst;
                    this.formData.mother_middle_name = mMid;
                    this.formData.mother_last_name = mLast;
                    this.formData.mother_suffix = mSuffix;

                    this.formData.guardian_first_name = mFirst;
                    this.formData.guardian_middle_name = mMid;
                    this.formData.guardian_last_name = mLast;
                    this.formData.guardian_suffix = mSuffix;

                    // Immediately update DOM input values for instant visual feedback
                    const gFirst = document.querySelector('input[name="guardian_first_name"]');
                    if (gFirst) gFirst.value = mFirst;
                    const gMid = document.querySelector('input[name="guardian_middle_name"]');
                    if (gMid) gMid.value = mMid;
                    const gLast = document.querySelector('input[name="guardian_last_name"]');
                    if (gLast) gLast.value = mLast;
                    const gSuffix = document.querySelector('input[name="guardian_suffix"]');
                    if (gSuffix) gSuffix.value = mSuffix;

                    // Clear guardian name errors if names are now present
                    if (this.formData.guardian_first_name && this.formData.guardian_last_name) {
                        delete this.errors.guardian_first_name;
                        delete this.errors.guardian_last_name;
                    }
                },

                setService(type) {
                    if (type === 'pedia' && this.patientAgeInMonths !== null && this.patientAgeInMonths < 6) {
                        if (typeof window !== 'undefined') {
                            window.dispatchEvent(new CustomEvent('add-toast', {
                                detail: {
                                    type: 'error',
                                    message: 'The Pedia Consultation service is only available for patients aged 6 months and above. Patients younger than 6 months require specialized neonatal or infant healthcare.'
                                }
                            }));
                        }
                        return;
                    }
                    this.formData.type = type;
                    // Adult is always follow-up in this flow. Pedia is optional (checkbox).
                    this.formData.is_follow_up = (type === 'adult');
                    
                    // If changing service type, clarify availability again
                    this.fetchAvailability();
                },

                async nextStep() {
                    this.errors = {};
                    let canProceed = false;

                    if (this.step === 1) {
                        canProceed = true;
                    } else if (this.step === 2) {
                        this.syncFormDataFromDOM();
                        if (this.validateInfo()) {
                            // Check for duplicate booking before proceeding
                            try {
                                this.isLoading = true;
                                
                                if (this.isFollowUp) {
                                    const followUpRes = await fetch('{{ route("appointment.verify-follow-up") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({
                                            first_name: this.formData.first_name,
                                            last_name: this.formData.last_name,
                                            dob: this.formData.dob
                                        })
                                    });
                                    if (followUpRes.status === 419) {
                                        alert("Your session has expired. Please refresh the page and try again.");
                                        window.location.reload();
                                        return;
                                    }
                                    const followUpData = await followUpRes.json();
                                    if (!followUpData.valid) {
                                        window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: followUpData.message } }));
                                        this.isLoading = false;
                                        return;
                                    }
                                    
                                    // Save the doctor's schedule to restrict calendar
                                    this.doctorSchedule = followUpData.doctor_schedule;
                                    this.validDays = followUpData.valid_days;
                                    this.doctorName = followUpData.doctor_name;
                                    this.followUpDoctorId = followUpData.doctor_id || null;
                                }

                                const response = await fetch('{{ route("appointment.check-duplicate") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        first_name: this.formData.first_name,
                                        last_name: this.formData.last_name,
                                        email: this.formData.email
                                    })
                                });
                                if (response.status === 419) {
                                    alert("Your session has expired. Please refresh the page and try again.");
                                    window.location.reload();
                                    return;
                                }
                                const data = await response.json();

                                if (data.exists) {
                                    this.showDuplicateModal = true;
                                    canProceed = false;
                                } else {
                                    canProceed = true;
                                }
                            } catch (e) {
                                console.error('Error checking duplicate', e);
                                // If check fails, allow proceed or handle error? Let's allow proceed to fallback to server check
                                canProceed = true;
                            } finally {
                                this.isLoading = false;
                            }
                        } else {
                            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please complete or correct the required fields.' } }));
                            this.$nextTick(() => {
                                const errorInput = document.querySelector('.border-red-500, .ring-red-500');
                                if (errorInput) {
                                    errorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                } else {
                                    const errorTexts = Array.from(document.querySelectorAll('p.text-red-500')).filter(el => el.style.display !== 'none' && el.innerText.trim() !== '');
                                    if(errorTexts.length) errorTexts[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                        }
                    } else if (this.step === 3) {
                        if (!this.selectedDate) {
                            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please select a preferred date.' } }));
                        } else {
                            await this.sendOtp();
                            canProceed = false; // logic handled in sendOtp success
                        }
                    } else if (this.step === 4) {
                        await this.verifyOtp();
                        canProceed = false; // logic handled in verifyOtp success
                    }

                    if (canProceed) {
                        this.step++;
                        if (this.step > this.maxStepReached) this.maxStepReached = this.step;
                        if (this.step === 3) {
                            this.initCalendar();
                            this.fetchAvailability();
                        }
                        this.updateProgress();
                    }
                },

                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                        this.updateProgress();
                    }
                },

                goToStep(targetStep) {
                    // Allow going back to any previous step, or forward if reached before
                    if (targetStep < this.step || targetStep <= this.maxStepReached) {
                        this.step = targetStep;
                        if (this.step === 3) {
                            this.initCalendar();
                            this.fetchAvailability();
                        }
                        this.updateProgress();
                    }
                },

                updateProgress() {
                    this.progress = (this.step - 1) * 25;
                    this.$nextTick(() => {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                },

                selectDate(date) {
                    this.selectedDate = date;
                },

                async fetchTimeSlots(date) {
                    this.loadingSlots = true;
                    this.timeSlots = [];
                    try {
                        const params = new URLSearchParams({ date, type: this.formData.type });
                        if (this.followUpDoctorId) {
                            params.append('doctor_id', this.followUpDoctorId);
                        }
                        const response = await fetch(`{{ route('appointment.time-slots') }}?${params}`);
                        const data = await response.json();
                        this.timeSlots = data.slots || [];
                    } catch (e) {
                        console.error('Failed to fetch time slots', e);
                    } finally {
                        this.loadingSlots = false;
                    }
                },

                syncFormDataFromDOM() {
                    const syncVal = (selector, setter) => {
                        const el = document.querySelector(selector);
                        if (el && el.value !== undefined && el.value !== null && el.value.toString().trim() !== '') {
                            setter(el.value.toString().trim());
                        }
                    };

                    syncVal('input[name="first_name"]', v => { this.formData.first_name = v.toUpperCase(); });
                    syncVal('input[name="middle_name"]', v => { this.formData.middle_name = v.toUpperCase(); });
                    syncVal('input[name="last_name"]', v => { this.formData.last_name = v.toUpperCase(); });
                    syncVal('input[name="suffix"]', v => { this.formData.suffix = v.toUpperCase(); });
                    syncVal('[name="sex"]', v => { this.formData.sex = v; });
                    syncVal('input[name="dob"]', v => { this.formData.dob = v; });
                    syncVal('[name="civil_status"]', v => { this.formData.civil_status = v; });
                    syncVal('[name="blood_type"]', v => { this.formData.blood_type = v; });
                    syncVal('[name="education"]', v => { this.formData.education = v; });
                    syncVal('input[name="email"]', v => { this.formData.email = v; });
                    syncVal('input[name="contact_number"]', v => { this.formData.contact_number = v; });
                    syncVal('input[name="philhealth_number"]', v => { this.formData.philhealth_number = this.formatPhilHealth(v); });

                    // Mother's Maiden Name
                    syncVal('input[x-model="formData.mother_first_name"]', v => { this.formData.mother_first_name = v.toUpperCase(); });
                    syncVal('input[x-model="formData.mother_middle_name"]', v => { this.formData.mother_middle_name = v.toUpperCase(); });
                    syncVal('input[x-model="formData.mother_last_name"]', v => { this.formData.mother_last_name = v.toUpperCase(); });
                    syncVal('input[x-model="formData.mother_suffix"]', v => { this.formData.mother_suffix = v.toUpperCase(); });

                    // Address / Barangay
                    syncVal('[name="barangay"]', v => { this.formData.barangay = v; });
                    syncVal('input[x-model="house_no"]', v => { this.formData.house_no = v; });
                    syncVal('input[x-model="street"]', v => { this.formData.street = v; });
                    syncVal('input[x-model="building"]', v => { this.formData.building = v; });
                    syncVal('input[name="address"]', v => { this.formData.address = v; });

                    // Guardian (Pedia)
                    if (this.motherIsGuardian) {
                        // When mother is guardian, sync names from mother fields
                        this.syncMotherToGuardian();
                    } else {
                        syncVal('input[name="guardian_first_name"]', v => { this.formData.guardian_first_name = v.toUpperCase(); });
                        syncVal('input[name="guardian_middle_name"]', v => { this.formData.guardian_middle_name = v.toUpperCase(); });
                        syncVal('input[name="guardian_last_name"]', v => { this.formData.guardian_last_name = v.toUpperCase(); });
                        syncVal('input[name="guardian_suffix"]', v => { this.formData.guardian_suffix = v.toUpperCase(); });
                    }
                    syncVal('[name="guardian_relation"]', v => {
                        const custom = document.querySelector('input[x-model="customGuardianRelation"]')?.value?.trim();
                        this.formData.guardian_relation = (v === 'Others (Please Specify)' && custom) ? custom.toUpperCase() : v;
                    });
                    syncVal('input[name="guardian_contact"]', v => { this.formData.guardian_contact = v; });

                    // Religion & Occupation
                    const relEl = document.querySelector('[name="religion"]');
                    if (relEl && relEl.value) {
                        this.formData.religion = relEl.value;
                    }
                    const occEl = document.querySelector('[name="occupation"]');
                    if (occEl && occEl.value) {
                        this.formData.occupation = occEl.value;
                    }
                },

                validateInfo() {
                    this.syncFormDataFromDOM();
                    let isValid = true;
                    if (!this.formData.first_name) { this.errors.first_name = 'Required'; isValid = false; }
                    if (!this.formData.last_name) { this.errors.last_name = 'Required'; isValid = false; }
                    if (!this.formData.sex) { this.errors.sex = 'Required'; isValid = false; }
                    if (!this.formData.dob) { this.errors.dob = 'Required'; isValid = false; }
                    
                    if (this.isFollowUp) {
                        // Follow-ups only need name + DOB (already validated above)
                    } else {
                        if (!this.formData.barangay) { this.errors.address = 'Barangay is required'; isValid = false; }
                        if (!this.formData.blood_type) { this.errors.blood_type = 'Blood type is required'; isValid = false; }
                        if (this.formData.type !== 'pedia' && !this.formData.religion) { this.errors.religion = 'Religion is required'; isValid = false; }
                        
                        if (!this.formData.philhealth_number) {
                            this.errors.philhealth = 'PhilHealth Number is required'; isValid = false;
                        } else if (!/^\d{2}-\d{9}-\d{1}$/.test(this.formData.philhealth_number)) {
                            this.errors.philhealth = 'Must follow 12-123456789-0 format'; isValid = false;
                        }
                    }

                    if (!this.combinedComplaint || this.combinedComplaint.trim() === '') {
                        this.errors.complaint = 'Please specify the Reason for Visit'; isValid = false;
                    }

                    if (!this.formData.email) {
                        this.errors.email = 'Required';
                        isValid = false;
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email.toLowerCase())) {
                        this.errors.email = 'Must be a valid email address';
                        isValid = false;
                    }
                    if (this.formData.dob) {
                        let dobDate = new Date(this.formData.dob);
                        let today = new Date();
                        let age = today.getFullYear() - dobDate.getFullYear();
                        let month = today.getMonth() - dobDate.getMonth();
                        if (month < 0 || (month === 0 && today.getDate() < dobDate.getDate())) {
                            age--;
                        }
                        let ageInMonths = (today.getFullYear() - dobDate.getFullYear()) * 12 + (today.getMonth() - dobDate.getMonth());
                        if (today.getDate() < dobDate.getDate()) {
                            ageInMonths--;
                        }

                        if (this.formData.type === 'pedia') {
                            if (ageInMonths < 6) {
                                this.errors.dob = 'The Pedia Consultation service is only available for patients aged 6 months and above. Patients younger than 6 months require specialized neonatal or infant healthcare.';
                                isValid = false;
                            } else if (age > 12) {
                                this.errors.dob = 'Pediatric patients must be 12 years old or below';
                                isValid = false;
                            }
                            if (!this.isFollowUp) {
                                if (!this.formData.guardian_first_name || !this.formData.guardian_last_name) { this.errors.guardian_first_name = 'First and Last Name are required'; isValid = false; }
                                if (!this.formData.guardian_relation) { this.errors.guardian_relation = 'Required'; isValid = false; }
                                if (!this.formData.guardian_contact) {
                                    this.errors.guardian_contact = 'Required';
                                    isValid = false;
                                } else if (!/^\d{11}$/.test(this.formData.guardian_contact)) {
                                    this.errors.guardian_contact = 'Must be exactly 11 digits';
                                    isValid = false;
                                }
                            }
                        } else {
                            if (age <= 12) {
                                this.errors.dob = 'Patients 12 years old and below must use the Pediatrics booking flow';
                                isValid = false;
                            }
                        }
                    }

                    if (!this.isFollowUp) {
                        if (!this.formData.mother_first_name || !this.formData.mother_last_name) {
                            this.errors.mothers_maiden_name = "Mother's First and Last Name are required";
                            isValid = false;
                        }
                    }

                    return isValid;
                },

                async fetchAvailability() {
                    let d1 = new Date(this.currentYear, this.currentMonth, 1);
                    let d2 = new Date(this.currentYear, this.currentMonth + 1, 0);
                    let offset1 = d1.getTimezoneOffset();
                    let offset2 = d2.getTimezoneOffset();
                    d1 = new Date(d1.getTime() - (offset1*60*1000));
                    d2 = new Date(d2.getTime() - (offset2*60*1000));
                    
                    const start = d1.toISOString().split('T')[0];
                    const end = d2.toISOString().split('T')[0];

                    try {
                        const params = new URLSearchParams({ start, end });
                        const response = await fetch(`{{ route("appointment.check-availability") }}?${params}`);
                        const data = await response.json();
                        this.availability = { ...this.availability, ...data };
                    } catch (e) {
                        console.error("Failed to fetch availability", e);
                    }
                },

                async sendOtp() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route("appointment.send-otp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ email: this.formData.email })
                        });

                        if (response.status === 419) {
                            alert("Your session has expired. Please refresh the page and try again.");
                            window.location.reload();
                            return;
                        }

                        const data = await response.json();

                        if (data.success) {
                            this.step = 4;
                            if (this.step > this.maxStepReached) this.maxStepReached = this.step;
                            this.updateProgress();
                        } else {
                            window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: data.message || 'Failed to send OTP' } }));
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'An error occurred. Please try again.' } }));
                    } finally {
                        this.isLoading = false;
                    }
                },

                async verifyOtp() {
                    if (!this.otpCode || this.otpCode.length < 6) {
                        this.errors.otp = 'Please enter a valid 6-digit code';
                        return;
                    }

                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route("appointment.verify-otp") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                email: this.formData.email,
                                otp: this.otpCode
                            })
                        });

                        if (response.status === 419) {
                            alert("Your session has expired. Please refresh the page and try again.");
                            window.location.reload();
                            return;
                        }

                        const data = await response.json();

                        if (data.success) {
                            this.step = 5;
                            if (this.step > this.maxStepReached) this.maxStepReached = this.step;
                            this.updateProgress();
                        } else {
                            this.errors.otp = data.message || 'Invalid OTP';
                            window.dispatchEvent(new CustomEvent('add-toast', {
                                detail: {
                                    type: 'error',
                                    message: data.message || 'Invalid OTP'
                                }
                            }));
                        }
                    } catch (e) {
                        this.errors.otp = 'Verification failed. Try again.';
                        window.dispatchEvent(new CustomEvent('add-toast', {
                            detail: {
                                type: 'error',
                                message: 'Verification failed. Try again.'
                            }
                        }));
                    } finally {
                        this.isLoading = false;
                    }
                },

                submitForm(e) {
                    if (!this.formData.data_privacy_agreed) {
                        if (e) e.preventDefault();
                        window.dispatchEvent(new CustomEvent('add-toast', { detail: { type: 'error', message: 'Please agree to the Data Privacy Act to proceed.' } }));
                        return;
                    }

                    this.isLoading = true;
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                },

                getSlotsUsage(date) {
                    const avail = this.availability[date];
                    if (!avail) return '0/20';
                    if (typeof avail === 'object') {
                        const cap = avail.capacity || 20;
                        const booked = avail.booked !== undefined ? avail.booked : 0;
                        return booked + '/' + cap;
                    }
                    return avail + '/20';
                }
            }
        }
    </script>
    <script>
        // Filter fields to strictly alphanumeric uppercase without symbols/numbers
        document.addEventListener('input', function (e) {
            const nameFields = ['first_name', 'middle_name', 'last_name', 'suffix', 'guardian_first_name', 'guardian_last_name', 'guardian_middle_name', 'guardian_suffix', 'mothers_maiden_name'];
            if (e.target.tagName === 'INPUT' && nameFields.includes(e.target.name)) {
                let val = e.target.value;
                let newVal = val.replace(/[^a-zA-ZÑñ\s\.\-\d]/g, '').toUpperCase();
                if (val !== newVal) {
                    let start = e.target.selectionStart;
                    let end = e.target.selectionEnd;
                    e.target.value = newVal;
                    if (e.target.hasAttribute('x-model') || e.target.hasAttribute('wire:model')) {
                        e.target.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    try { e.target.setSelectionRange(start, end); } catch (ex) { }
                }
            }
        });
    </script>
@endsection