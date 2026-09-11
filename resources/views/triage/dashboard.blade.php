@extends('layouts.nurse')

@section('header', 'Vitals Station')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16"
     x-data="{
        stats: {
            waiting: {{ $waiting->count() }},
            processed: {{ $claimed->count() }},
            totalToday: {{ $totalToday }},
            leftToday: {{ $leftToday ?? 0 }}
        },
        queueSearch: '',
        async fetchStats() {
            try {
                const res = await fetch('{{ route('triage.stats') }}');
                if (res.ok) {
                    this.stats = await res.json();
                }
            } catch (e) {
                console.error('Failed to sync stats:', e);
            }
        },
        async fetchQueues() {
            try {
                const res = await fetch('{{ route('triage.dashboard') }}');
                if (res.ok) {
                    const html = await res.text();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    const newRightPanel = tempDiv.querySelector('#right-panel-queues');
                    const currentRightPanel = document.querySelector('#right-panel-queues');
                    if (newRightPanel && currentRightPanel) {
                        currentRightPanel.innerHTML = newRightPanel.innerHTML;
                    }
                }
            } catch (e) {
                console.error('Failed to sync queues:', e);
            }
        },
        init() {
            window.addEventListener('triage-global-search', (e) => {
                this.queueSearch = e.detail;
            });
            setInterval(() => {
                this.fetchStats();
                this.fetchQueues();
            }, 3500);
        }
     }">

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="['Nurse Portal' => '', 'Vitals Station' => '']" />

    {{-- Top Bento Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 sm:gap-5">
        
        {{-- Card 1: Waiting Patients --}}
        <div class="lg:col-span-3 rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between group hover:border-amber-400 dark:hover:border-amber-600 transition-all">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-amber-500 to-orange-500"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Waiting for Vitals</span>
                <span class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white" x-text="stats.waiting"></span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">in queue</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center gap-1.5 text-[11px] font-bold text-amber-700 dark:text-amber-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Ready for pre-triage assessment</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Processed Vitals Today --}}
        <div class="lg:col-span-3 rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between group hover:border-emerald-300 dark:hover:border-emerald-700/60 transition-all">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Processed Vitals</span>
                <span class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-emerald-700 dark:text-emerald-400" x-text="stats.processed"></span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">sent to queue</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-medium text-slate-500 dark:text-slate-400 truncate">
                    Triaged & forwarded to doctors
                </div>
            </div>
        </div>

        {{-- Card 3: Total Daily Volume & Left --}}
        <div class="lg:col-span-3 rounded-3xl bg-white/90 dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 p-6 shadow-xs backdrop-blur-xs relative overflow-hidden flex flex-col justify-between group hover:border-teal-300 dark:hover:border-teal-700/60 transition-all">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-teal-500 to-cyan-600"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Station Volume</span>
                <span class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white" x-text="stats.totalToday"></span>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">patients total</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500 dark:text-slate-400">Left / Cancelled:</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400" x-text="stats.leftToday + ' patients'"></span>
                </div>
            </div>
        </div>

        {{-- Card 4: Nurse On-Duty Profile Banner --}}
        <div class="lg:col-span-3 rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-800 to-teal-900 text-white p-6 shadow-md relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-base font-black text-white">{{ auth()->user()->initials }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold text-white truncate leading-snug">{{ auth()->user()->formatted_name }}</h2>
                    <p class="text-[11px] text-emerald-200 truncate capitalize">{{ auth()->user()->role === 'vitals_nurse' ? 'Vitals Station Nurse' : str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
            </div>
            <div class="pt-3 border-t border-white/15 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.9)] animate-pulse"></span>
                    <span class="font-bold text-emerald-100 text-[11px]">{{ auth()->user()->status ?? 'Online' }}</span>
                </div>
                <span class="text-[11px] text-emerald-200/90 font-medium">{{ \Carbon\Carbon::today()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex items-center gap-3 text-xs sm:text-sm font-bold text-emerald-800 dark:text-emerald-300">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-xs sm:text-sm text-rose-800 dark:text-rose-300">
            <p class="font-bold mb-1">Please correct the following issues:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Main Two-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT COLUMN: Vitals Assessment & Input Form (Cols 1-7) --}}
        <div class="lg:col-span-7">
            <div class="rounded-3xl bg-white/95 dark:bg-slate-900/95 border border-slate-200/90 dark:border-slate-800 shadow-sm overflow-hidden"
                 @triage-cancel.window="cancelQueue($event.detail)" 
                 @triage-restore.window="restoreQueue($event.detail)"
                 @triage-arrived.window="
                    appointmentId = $event.detail.appointment_id || '';
                    searchQuery = $event.detail.name;
                    searchDOB = $event.detail.dob || '';
                    searchPatients(searchDOB);
                    setTimeout(() => { 
                        if(searchResults.length === 0) { 
                            setNew(); 
                            selectedPatient = null; 
                            const fn = document.querySelector('input[name=first_name]'); if(fn) fn.value = $event.detail.first_name || ''; 
                            const ln = document.querySelector('input[name=last_name]'); if(ln) ln.value = $event.detail.last_name || ''; 
                            const mn = document.querySelector('input[name=middle_name]'); if(mn) mn.value = $event.detail.middle_name || ''; 
                            const sfx = document.querySelector('input[name=suffix]'); if(sfx) sfx.value = $event.detail.suffix || ''; 
                            const dob = document.querySelector('input[name=dob]'); if(dob) dob.value = searchDOB;
                            classification = $event.detail.classification || $event.detail.type || 'Adult';
                            symptomsText = $event.detail.complaint || '';
                        } else if(searchResults.length === 1) {
                            selectPatient(searchResults[0]);
                            symptomsText = $event.detail.complaint || '';
                        } else {
                            symptomsText = $event.detail.complaint || '';
                            classification = $event.detail.classification || $event.detail.type || 'Adult';
                        }
                    }, 800);
                 " 
                 x-data="{
                    appointmentId: '',
                    searchQuery: '',
                    searchDOB: '',
                    searchResults: [],
                    selectedPatient: null,
                    isNew: null,
                    loading: false,
                    debounceTimer: null,
                    vitalsStartedAt: null,
                    classification: 'Adult',
                    symptomsText: '',
                    historyText: '',
                    medicineText: '',
                    allergiesText: '',
                    weightVal: '',
                    heightVal: '',
                    tempVal: '',
                    spo2Val: '',

                    get bmi() {
                        const w = parseFloat(this.weightVal);
                        const h = parseFloat(this.heightVal) / 100;
                        if (!w || !h || h <= 0) return null;
                        const val = (w / (h * h)).toFixed(1);
                        let category = 'Normal';
                        let colorClass = 'text-emerald-700 bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-300';
                        if (val < 18.5) {
                            category = 'Underweight';
                            colorClass = 'text-amber-700 bg-amber-50 dark:bg-amber-950/60 dark:text-amber-300';
                        } else if (val >= 25 && val < 30) {
                            category = 'Overweight';
                            colorClass = 'text-orange-700 bg-orange-50 dark:bg-orange-950/60 dark:text-orange-300';
                        } else if (val >= 30) {
                            category = 'Obese';
                            colorClass = 'text-rose-700 bg-rose-50 dark:bg-rose-950/60 dark:text-rose-300';
                        }
                        return { value: val, category: category, color: colorClass };
                    },

                    // Modal & Confirm State
                    showConfirmModal: false,
                    pendingFormData: null,
                    pendingDataObj: {},

                    genericConfirmOpen: false,
                    genericConfirmTitle: '',
                    genericConfirmMessage: '',
                    genericConfirmCallback: null,

                    showConfirm(title, message, callback) {
                        this.genericConfirmTitle = title;
                        this.genericConfirmMessage = message;
                        this.genericConfirmCallback = callback;
                        this.genericConfirmOpen = true;
                    },

                    showToast(message, type = 'success') {
                        window.dispatchEvent(new CustomEvent('add-toast', { 
                            detail: { message: message, type: type } 
                        }));
                    },

                    searchPatients(dob = null) {
                        clearTimeout(this.debounceTimer);
                        if (this.searchQuery.length < 1 && !dob) { this.searchResults = []; return; }
                        this.loading = true;
                        this.debounceTimer = setTimeout(() => {
                            let url = `{{ route('triage.search') }}?q=${encodeURIComponent(this.searchQuery)}`;
                            if(dob) url += `&dob=${encodeURIComponent(dob)}`;
                            fetch(url)
                                .then(r => r.json())
                                .then(data => { this.searchResults = data; this.loading = false; })
                                .catch(() => { this.loading = false; });
                        }, 100);
                    },

                    selectPatient(p) {
                        this.selectedPatient = p;
                        this.searchQuery = p.full_name;
                        this.searchResults = [];
                        this.isNew = false;
                        this.vitalsStartedAt = Date.now();

                        let mappedClass = 'Adult';
                        if (p.classification === 'Senior Citizen' || p.classification === 'Senior') mappedClass = 'Senior';
                        else if (p.classification === 'Pediatric') mappedClass = 'Pediatric';
                        else if (p.classification === 'PWD') mappedClass = 'PWD';
                        this.classification = mappedClass;
                        
                        this.historyText = p.past_medical_history || p.allergies || '';
                        this.medicineText = p.medicine_taken || p.last_medicine || '';
                        this.allergiesText = p.known_allergies || '';
                    },

                    clearHistory() {
                        this.historyText = '';
                        this.medicineText = '';
                        this.allergiesText = '';
                    },

                    setNew() {
                        this.selectedPatient = null;
                        this.isNew = true;
                        this.searchQuery = '';
                        this.searchResults = [];
                        this.vitalsStartedAt = Date.now();
                        this.classification = 'Adult';
                        this.clearHistory();
                    },

                    clearSelection() {
                        this.selectedPatient = null;
                        this.isNew = null;
                        this.searchQuery = '';
                        this.searchResults = [];
                        this.vitalsStartedAt = null;
                        this.appointmentId = '';
                        this.weightVal = '';
                        this.heightVal = '';
                        this.tempVal = '';
                        this.spo2Val = '';
                        this.clearHistory();
                        this.symptomsText = '';
                    },

                    openConfirmModal(e) {
                        this.pendingFormData = new FormData(e.target);
                        if (this.vitalsStartedAt) {
                            let elapsed = Math.floor((Date.now() - this.vitalsStartedAt) / 1000);
                            if (elapsed < 1) elapsed = 1;
                            this.pendingFormData.append('encoding_duration_seconds', elapsed);
                        }
                        this.pendingDataObj = Object.fromEntries(this.pendingFormData.entries());
                        this.showConfirmModal = true;
                    },

                    confirmSubmit() {
                        this.loading = true;
                        fetch('{{ route('triage.store') }}', {
                            method: 'POST',
                            headers: { 'Accept': 'application/json' },
                            body: this.pendingFormData
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.loading = false;
                            if (data.success) {
                                this.showConfirmModal = false;
                                this.showToast(data.message, 'success');
                                this.clearSelection();
                                document.getElementById('vitalsForm').reset();

                                fetch('{{ route('triage.dashboard') }}')
                                    .then(res => res.text())
                                    .then(html => {
                                        let tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = html;
                                        let newRightPanel = tempDiv.querySelector('#right-panel-queues');
                                        let currentRightPanel = document.querySelector('#right-panel-queues');
                                        if (newRightPanel && currentRightPanel) {
                                            currentRightPanel.innerHTML = newRightPanel.innerHTML;
                                        }
                                    });
                            } else {
                                this.showConfirmModal = false;
                                if (data.errors) {
                                    let errorMsg = Object.values(data.errors).flat().join(' ');
                                    this.showToast(errorMsg, 'error');
                                } else {
                                    this.showToast(data.message || 'Error saving vitals.', 'error');
                                }
                            }
                        })
                        .catch(err => {
                            this.loading = false;
                            this.showToast('An error occurred while connecting to the server.', 'error');
                        });
                    },

                    cancelQueue(url) {
                        this.showConfirm(
                            'Remove from Queue?', 
                            'Are you sure you want to remove this patient? They will be moved to the Archived section.',
                            () => {
                                let formData = new FormData();
                                formData.append('_token', '{{ csrf_token() }}');

                                fetch(url, {
                                    method: 'POST',
                                    headers: { 'Accept': 'application/json' },
                                    body: formData
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if(data.success) {
                                        this.showToast(data.message, 'success');
                                        fetch('{{ route('triage.dashboard') }}')
                                            .then(res => res.text())
                                            .then(html => {
                                                let tempDiv = document.createElement('div');
                                                tempDiv.innerHTML = html;
                                                const target = document.querySelector('#right-panel-queues');
                                                if (target) target.innerHTML = tempDiv.querySelector('#right-panel-queues').innerHTML;
                                            });
                                    }
                                });
                            }
                        );
                    },

                    restoreQueue(url) {
                        this.showConfirm(
                            'Restore Patient?', 
                            'Restore this patient to the active waiting queue? They will be placed at the end of the line.',
                            () => {
                                let formData = new FormData();
                                formData.append('_token', '{{ csrf_token() }}');

                                fetch(url, {
                                    method: 'POST',
                                    headers: { 'Accept': 'application/json' },
                                    body: formData
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if(data.success) {
                                        this.showToast(data.message, 'success');
                                        fetch('{{ route('triage.dashboard') }}')
                                            .then(res => res.text())
                                            .then(html => {
                                                let tempDiv = document.createElement('div');
                                                tempDiv.innerHTML = html;
                                                const target = document.querySelector('#right-panel-queues');
                                                if (target) target.innerHTML = tempDiv.querySelector('#right-panel-queues').innerHTML;
                                            });
                                    }
                                });
                            }
                        );
                    }
                 }">

                {{-- Desk Header Banner --}}
                <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 via-teal-600 to-teal-700 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base sm:text-lg text-white tracking-tight leading-tight">Patient Pre-Triage Assessment</h3>
                            <p class="text-xs text-emerald-100 font-medium">Record vital signs, triage classification, and clinical complaints</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- STEP 1: Patient Identification --}}
                    <div class="rounded-2xl p-5 border border-slate-200/90 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/40 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">1</span>
                                Patient Identification
                            </span>
                            @if(isset($arrivedAppointments) && $arrivedAppointments->count() > 0)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300">
                                    {{ $arrivedAppointments->count() }} arrived via Info Desk
                                </span>
                            @endif
                        </div>

                        {{-- Mode Selector --}}
                        <div x-show="isNew === null" class="space-y-4">
                            <div class="relative">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Search Existing Patient Record
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <input type="text" 
                                           x-model="searchQuery" 
                                           @input="searchPatients()"
                                           placeholder="Type patient name, patient ID, or PhilHealth..."
                                           class="no-uppercase w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all"
                                           style="text-transform: none !important;">
                                    <div x-show="loading" class="absolute right-3 top-3">
                                        <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Search Results Dropdown --}}
                                <div x-show="searchResults.length > 0"
                                     @click.away="searchResults = []"
                                     style="display: none;"
                                     class="absolute z-50 w-full mt-1.5 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden max-h-64 overflow-y-auto custom-scrollbar py-1">
                                    <template x-for="p in searchResults" :key="p.id">
                                        <button type="button" 
                                                @click="selectPatient(p)"
                                                class="w-full text-left px-4 py-3 hover:bg-emerald-50 dark:hover:bg-slate-800/80 border-b border-slate-100 dark:border-slate-800 last:border-0 transition-colors group cursor-pointer">
                                            <div class="flex items-center justify-between">
                                                <p class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors" x-text="p.full_name"></p>
                                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300" x-text="p.id"></span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                <span class="font-semibold text-emerald-700 dark:text-emerald-400" x-text="p.classification"></span>
                                                <span>•</span>
                                                <span>DOB: <span x-text="p.dob"></span> (<span x-text="p.age"></span> yrs)</span>
                                                <span>•</span>
                                                <span>Last visit: <span x-text="p.last_visit"></span></span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="relative flex items-center justify-center">
                                <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                                <span class="bg-slate-50 dark:bg-slate-900 px-3 text-xs font-extrabold text-slate-400 uppercase tracking-widest absolute">OR</span>
                            </div>

                            <button type="button" 
                                    @click="setNew()"
                                    class="w-full rounded-2xl border-2 border-dashed border-emerald-300 dark:border-emerald-800/80 bg-emerald-50/50 dark:bg-emerald-950/20 py-3 text-xs sm:text-sm font-extrabold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100/60 dark:hover:bg-emerald-900/40 transition-all cursor-pointer flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Register New Walk-in Patient</span>
                            </button>
                        </div>

                        {{-- Returning Patient Selected Card --}}
                        <div x-show="selectedPatient !== null" style="display:none">
                            <div class="rounded-2xl p-4 bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shrink-0">
                                            ✓
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Returning Patient</span>
                                                <span class="text-[10px] font-mono font-bold text-slate-500" x-text="selectedPatient?.id"></span>
                                            </div>
                                            <h4 class="font-extrabold text-base text-slate-900 dark:text-white" x-text="selectedPatient?.full_name"></h4>
                                            <p class="text-xs text-slate-600 dark:text-slate-300">
                                                <span x-text="selectedPatient?.classification"></span> •
                                                Age: <span x-text="selectedPatient?.age"></span> yrs •
                                                Last visit: <span x-text="selectedPatient?.last_visit"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="clearSelection()"
                                            class="text-xs text-rose-600 hover:text-rose-700 font-bold px-3 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/60 transition cursor-pointer">
                                        Change
                                    </button>
                                </div>

                                {{-- Medical History Capsule --}}
                                <div class="pt-3 border-t border-emerald-200/80 dark:border-emerald-800/50 text-xs space-y-1.5">
                                    <template x-if="selectedPatient?.allergies">
                                        <p class="text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/50 p-2 rounded-xl border border-amber-200 dark:border-amber-800/60 font-semibold">
                                            ⚠️ <strong>Known Conditions / Allergies:</strong> <span x-text="selectedPatient.allergies"></span>
                                        </p>
                                    </template>
                                    <template x-if="selectedPatient?.last_medicine">
                                        <p class="text-slate-600 dark:text-slate-400">
                                            <strong>Last Meds:</strong> <span x-text="selectedPatient.last_medicine"></span>
                                        </p>
                                    </template>
                                    <template x-if="selectedPatient?.last_symptoms">
                                        <p class="text-slate-600 dark:text-slate-400">
                                            <strong>Last Complaint:</strong> <span x-text="selectedPatient.last_symptoms"></span>
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- New Patient Selected Banner --}}
                        <div x-show="isNew === true && selectedPatient === null" style="display:none">
                            <div class="rounded-2xl p-4 bg-teal-50 dark:bg-teal-950/30 border border-teal-200 dark:border-teal-800/60 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-teal-600 text-white flex items-center justify-center font-black text-sm">
                                        +
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-sm text-teal-900 dark:text-teal-200">New Patient Entry</h4>
                                        <p class="text-xs text-teal-700 dark:text-teal-400">Fill in patient demographic & identification details below</p>
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="clearSelection()"
                                        class="text-xs text-rose-600 hover:text-rose-700 font-bold px-3 py-1 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/60 transition cursor-pointer">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Vitals Recording Form --}}
                    <div x-show="isNew !== null" style="display: none;">
                        <form @submit.prevent="openConfirmModal($event)" class="space-y-6" id="vitalsForm">
                            @csrf
                            <input type="hidden" name="patient_id" :value="selectedPatient?.id ?? ''">
                            <input type="hidden" name="appointment_id" :value="appointmentId">
                            <input type="hidden" name="vitals_started_at" :value="vitalsStartedAt">

                            {{-- Patient Name & Demographic Fields --}}
                            <div class="space-y-4">
                                {{-- Returning Patient Hidden Name Field --}}
                                <div x-show="!isNew">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                        Assigned Patient Name
                                    </label>
                                    <input type="text" 
                                           name="patient_name" 
                                           :required="!isNew"
                                           :value="selectedPatient ? selectedPatient.full_name : ''" 
                                           readonly
                                           class="w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 font-extrabold text-slate-800 dark:text-white px-4 py-2.5 text-sm focus:outline-none">
                                    <input type="hidden" name="dob" :value="selectedPatient ? selectedPatient.dob_raw : ''">
                                </div>

                                {{-- New Patient Name Fields --}}
                                <div x-show="isNew" class="space-y-3" x-cloak>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">
                                                First Name <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="text" name="first_name" :required="isNew" placeholder="Juan"
                                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-white uppercase focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">
                                                Middle Name
                                            </label>
                                            <input type="text" name="middle_name" placeholder="Dela"
                                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-white uppercase focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">
                                                Last Name <span class="text-rose-500">*</span>
                                            </label>
                                            <input type="text" name="last_name" :required="isNew" placeholder="Cruz"
                                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-white uppercase focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">
                                                Suffix
                                            </label>
                                            <input type="text" name="suffix" placeholder="Jr., III"
                                                   class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs sm:text-sm text-slate-900 dark:text-white uppercase focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                        </div>
                                    </div>
                                </div>

                                {{-- Classification & Date of Birth Row --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1.5">
                                            Classification <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-4 gap-1.5 p-1 rounded-2xl bg-slate-100 dark:bg-slate-800">
                                            <button type="button" 
                                                    @click="classification = 'Adult'"
                                                    :class="classification === 'Adult' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                                    class="py-2 text-xs font-extrabold rounded-xl transition cursor-pointer text-center">
                                                Adult
                                            </button>
                                            <button type="button" 
                                                    @click="classification = 'Senior'"
                                                    :class="classification === 'Senior' ? 'bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                                    class="py-2 text-xs font-extrabold rounded-xl transition cursor-pointer text-center">
                                                Senior
                                            </button>
                                            <button type="button" 
                                                    @click="classification = 'Pediatric'"
                                                    :class="classification === 'Pediatric' ? 'bg-white dark:bg-slate-900 text-purple-600 dark:text-purple-400 shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                                    class="py-2 text-xs font-extrabold rounded-xl transition cursor-pointer text-center">
                                                Pedia
                                            </button>
                                            <button type="button" 
                                                    @click="classification = 'PWD'"
                                                    :class="classification === 'PWD' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                                    class="py-2 text-xs font-extrabold rounded-xl transition cursor-pointer text-center">
                                                PWD
                                            </button>
                                        </div>
                                        <input type="hidden" name="classification" :value="classification">
                                    </div>

                                    {{-- DOB Picker for New Patients --}}
                                    <div x-show="isNew" 
                                         x-data="{
                                            showDatepicker: false,
                                            currentDate: new Date(),
                                            dobValue: '',
                                            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                            get daysInMonth() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate(); },
                                            get startDay() { return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay(); },
                                            setMonth(monthIndex) { this.currentDate = new Date(this.currentDate.getFullYear(), monthIndex, 1); },
                                            setYear(year) { this.currentDate = new Date(year, this.currentDate.getMonth(), 1); },
                                            isFutureDate(day) {
                                                let dateToCheck = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                                let today = new Date(); today.setHours(0,0,0,0);
                                                return dateToCheck > today;
                                            },
                                            selectDate(day) {
                                                if (this.isFutureDate(day)) return;
                                                let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                                let offset = date.getTimezoneOffset();
                                                date = new Date(date.getTime() - (offset*60*1000));
                                                this.dobValue = date.toISOString().split('T')[0];
                                                this.showDatepicker = false;
                                            },
                                            isSelected(day) {
                                                if(!this.dobValue) return false;
                                                let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                                let offset = date.getTimezoneOffset();
                                                date = new Date(date.getTime() - (offset*60*1000));
                                                return this.dobValue === date.toISOString().split('T')[0];
                                            }
                                         }" class="relative">
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1.5">
                                            Date of Birth <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="hidden" name="dob" x-model="dobValue" :required="isNew">
                                        
                                        <div @click="showDatepicker = !showDatepicker"
                                             class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2.5 text-xs sm:text-sm cursor-pointer flex justify-between items-center transition"
                                             :class="showDatepicker ? 'ring-2 ring-emerald-500/20 border-emerald-500' : ''">
                                            <span x-text="dobValue ? dobValue : 'YYYY-MM-DD'" :class="dobValue ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-400'"></span>
                                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>

                                        {{-- Popup --}}
                                        <div x-show="showDatepicker" 
                                             @click.away="showDatepicker = false" 
                                             style="display: none;"
                                             class="absolute z-50 mt-1 w-[290px] p-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl right-0">
                                            <div class="flex justify-between items-center mb-3 gap-2">
                                                <select @change="setMonth($event.target.value)" class="flex-1 rounded-lg border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 dark:bg-slate-800 p-1">
                                                    <template x-for="(month, index) in monthNames" :key="index">
                                                        <option :value="index" x-text="month" :selected="index === currentDate.getMonth()"></option>
                                                    </template>
                                                </select>
                                                <select @change="setYear($event.target.value)" class="flex-1 rounded-lg border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 dark:bg-slate-800 p-1">
                                                    <template x-for="year in Array.from({length: 120}, (_, i) => new Date().getFullYear() - i)" :key="year">
                                                        <option :value="year" x-text="year" :selected="year === currentDate.getFullYear()"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400 mb-1">
                                                <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                                            </div>
                                            <div class="grid grid-cols-7 gap-1">
                                                <template x-for="blank in startDay"><div class="p-1"></div></template>
                                                <template x-for="day in daysInMonth" :key="day">
                                                    <div @click="selectDate(day)"
                                                         class="w-7 h-7 flex items-center justify-center rounded-lg text-xs cursor-pointer transition-colors"
                                                         :class="{
                                                            'bg-emerald-600 text-white font-bold shadow-xs': isSelected(day),
                                                            'hover:bg-emerald-100 dark:hover:bg-emerald-950/60 text-slate-700 dark:text-slate-300': !isSelected(day) && !isFutureDate(day),
                                                            'text-slate-300 dark:text-slate-700 cursor-not-allowed': isFutureDate(day)
                                                         }" x-text="day">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pediatric Emergency Override --}}
                                <div x-show="classification === 'Pediatric' && !appointmentId" x-cloak class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/60 flex items-center gap-3">
                                    <input type="checkbox" name="is_emergency" value="1" id="emergencyOverride" class="rounded text-rose-600 focus:ring-rose-500 border-rose-300 w-4 h-4 cursor-pointer">
                                    <div>
                                        <label for="emergencyOverride" class="text-xs font-bold text-rose-700 dark:text-rose-400 cursor-pointer">
                                            Pediatric Emergency Override
                                        </label>
                                        <p class="text-[11px] text-rose-600/90 dark:text-rose-400/80">Bypasses slot limits to expedite immediate emergency clinical consultation.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Vital Signs Matrix --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                        Pre-Triage Vital Signs
                                    </span>
                                    <template x-if="bmi">
                                        <span class="text-xs font-black px-2.5 py-0.5 rounded-full" :class="bmi.color" x-text="'BMI: ' + bmi.value + ' (' + bmi.category + ')'"></span>
                                    </template>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    {{-- Blood Pressure --}}
                                    <div class="col-span-2 sm:col-span-1"
                                         x-data="{ 
                                            sys:'',
                                            dia:'', 
                                            checkSys(){
                                                this.sys=this.sys.replace(/\D/g,'').replace(/^0+(?!$)/, '').substring(0,3);
                                                if(this.sys.length===3)$refs.dia.focus()
                                            }, 
                                            checkDia(e){
                                                this.dia=this.dia.replace(/\D/g,'').replace(/^0+(?!$)/, '').substring(0,3);
                                                if(this.dia.length===0&&e.inputType==='deleteContentBackward')$refs.sys.focus()
                                            } 
                                         }">
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                            BP (mmHg)
                                        </label>
                                        <div class="flex items-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1.5 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20">
                                            <input type="text" x-model="sys" x-ref="sys" @input="checkSys" placeholder="120"
                                                   class="w-1/2 bg-transparent text-center border-none focus:ring-0 p-0 text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">
                                            <span class="text-slate-400 font-bold px-1">/</span>
                                            <input type="text" x-model="dia" x-ref="dia" @input="checkDia" placeholder="80"
                                                   class="w-1/2 bg-transparent text-center border-none focus:ring-0 p-0 text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white">
                                        </div>
                                        <input type="hidden" name="blood_pressure" :value="(sys||dia)?sys+'/'+dia:''">
                                    </div>

                                    {{-- Temperature --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1 flex items-center justify-between">
                                            <span>Temp (°C)</span>
                                            <span x-show="parseFloat(tempVal) > 37.5" class="text-[9px] font-black text-rose-600 uppercase">Febrile</span>
                                        </label>
                                        <input type="text" 
                                               name="temperature" 
                                               x-model="tempVal"
                                               placeholder="36.5"
                                               x-on:input="$el.value = $el.value.replace(/[^0-9.]/g, '').replace(/^0+(?!$|\.)/, '')"
                                               :class="parseFloat(tempVal) > 37.5 ? 'border-rose-400 text-rose-600 font-black ring-1 ring-rose-400' : 'border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-extrabold'"
                                               class="w-full rounded-xl border bg-white dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm focus:border-emerald-500">
                                    </div>

                                    {{-- Heart Rate --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                            HR (bpm)
                                        </label>
                                        <input type="text" name="heart_rate" placeholder="80"
                                               x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white focus:border-emerald-500">
                                    </div>

                                    {{-- Respiratory Rate --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                            RR (cpm)
                                        </label>
                                        <input type="text" name="respiratory_rate" placeholder="16"
                                               x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white focus:border-emerald-500">
                                    </div>

                                    {{-- Pulse Rate --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                            Pulse (bpm)
                                        </label>
                                        <input type="text" name="pulse_rate" placeholder="80"
                                               x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white focus:border-emerald-500">
                                    </div>

                                    {{-- SpO2 --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1 flex items-center justify-between">
                                            <span>SpO₂ (%)</span>
                                            <span x-show="parseFloat(spo2Val) < 95 && spo2Val.length > 0" class="text-[9px] font-black text-rose-600 uppercase">Low</span>
                                        </label>
                                        <input type="text" 
                                               name="spo2" 
                                               x-model="spo2Val"
                                               placeholder="98"
                                               x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                               :class="parseFloat(spo2Val) < 95 && spo2Val.length > 0 ? 'border-rose-400 text-rose-600 font-black' : 'border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-extrabold'"
                                               class="w-full rounded-xl border bg-white dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm focus:border-emerald-500">
                                    </div>

                                    {{-- Weight --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                            Weight (kg)
                                        </label>
                                        <input type="text" 
                                               name="weight" 
                                               x-model="weightVal" 
                                               placeholder="65"
                                               x-on:input="$el.value = $el.value.replace(/[^0-9.]/g, '').replace(/^0+(?!$|\.)/, '')"
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white focus:border-emerald-500">
                                    </div>

                                    {{-- Height --}}
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                            Height (cm)
                                        </label>
                                        <input type="text" 
                                               name="height" 
                                               x-model="heightVal" 
                                               placeholder="165"
                                               x-on:input="$el.value = $el.value.replace(/[^0-9.]/g, '').replace(/^0+(?!$|\.)/, '')"
                                               class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white focus:border-emerald-500">
                                    </div>
                                </div>
                            </div>

                            {{-- Clinical Complaints & Symptoms --}}
                            <div class="space-y-4 pt-2">
                                <div x-data="{
                                    toggleSymptom(symptom) {
                                        if (symptomsText.includes(symptom)) {
                                            symptomsText = symptomsText.replace(new RegExp('(?:, )?' + symptom, 'g'), '').replace(/^, /, '').trim();
                                        } else {
                                            symptomsText = symptomsText ? symptomsText + ', ' + symptom : symptom;
                                        }
                                    }
                                }">
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                        Symptoms / Chief Complaint <span class="text-rose-500">*</span>
                                    </label>

                                    {{-- Quick Select Chips --}}
                                    <div class="flex flex-wrap gap-1.5 mb-2.5">
                                        <template x-for="sym in ['Cough', 'Fever', 'Headache', 'Colds', 'Diarrhea', 'Sore Throat', 'Vomiting', 'Dizziness', 'Chest Pain', 'Abdominal Pain', 'Body Malaise']" :key="sym">
                                            <button type="button" 
                                                    @click="toggleSymptom(sym)"
                                                    :class="symptomsText.includes(sym) ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'"
                                                    class="text-xs px-3 py-1 rounded-full border border-transparent transition-all cursor-pointer"
                                                    x-text="sym">
                                            </button>
                                        </template>
                                    </div>

                                    <textarea name="symptoms" 
                                              required 
                                              x-model="symptomsText" 
                                              rows="2"
                                              placeholder="Describe patient complaint and symptoms..."
                                              class="w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-2.5 text-xs sm:text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"></textarea>
                                </div>

                                {{-- Medical History & Medications --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase">Past Medical History <span class="text-rose-500">*</span></label>
                                            <button type="button" @click="historyText = 'N/A'" class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-emerald-700 cursor-pointer">(N/A)</button>
                                        </div>
                                        <textarea name="past_medical_history" required rows="2"
                                                  x-model="historyText"
                                                  placeholder="Prior conditions, asthma, hypertension..."
                                                  class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-xs focus:border-emerald-500"></textarea>
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase">Current Medications <span class="text-rose-500">*</span></label>
                                            <button type="button" @click="medicineText = 'N/A'" class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-emerald-700 cursor-pointer">(N/A)</button>
                                        </div>
                                        <textarea name="medicine_taken" required rows="2"
                                                  x-model="medicineText"
                                                  placeholder="Maintenance drugs or recent meds..."
                                                  class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-xs focus:border-emerald-500"></textarea>
                                    </div>
                                </div>

                                {{-- Allergies --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase">Known Allergies <span class="text-rose-500">*</span></label>
                                        <button type="button" @click="allergiesText = 'N/A'" class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-emerald-700 cursor-pointer">(N/A)</button>
                                    </div>
                                    <textarea name="known_allergies" required rows="1"
                                              x-model="allergiesText"
                                              placeholder="Food or drug allergies..."
                                              class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-xs focus:border-emerald-500"></textarea>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="pt-2">
                                <button type="submit" 
                                        :disabled="loading"
                                        class="w-full bg-gradient-to-r from-emerald-600 via-teal-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-extrabold py-3.5 px-6 rounded-2xl shadow-md shadow-teal-900/10 transition-all flex items-center justify-center gap-2.5 text-sm cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Review & Commit Patient to Consultation Queue</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Prompt when no choice made yet --}}
                    <div x-show="isNew === null" class="text-center py-8 text-slate-400 space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800/80 flex items-center justify-center mx-auto text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400">Select an arrived appointment, search an existing patient, or click "Register New Walk-in".</p>
                    </div>
                </div>

                {{-- Confirm Vitals Review Modal --}}
                <div x-show="showConfirmModal"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
                     style="display: none;">
                    <div @click.away="showConfirmModal = false"
                         class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200 dark:border-slate-800">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Confirm Pre-Triage Data</h3>
                                <p class="text-xs text-slate-500">Please verify vital signs before forwarding to clinical queue.</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300" x-text="pendingDataObj.classification"></span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50">
                                <span class="text-[10px] font-black text-emerald-700 uppercase">Patient</span>
                                <p class="font-extrabold text-slate-900 dark:text-white text-base capitalize"
                                   x-text="(isNew ? pendingDataObj.first_name + ' ' + pendingDataObj.last_name : pendingDataObj.patient_name).toLowerCase()"></p>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">BP</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200" x-text="pendingDataObj.blood_pressure || '--'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Temp</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200" x-text="pendingDataObj.temperature ? pendingDataObj.temperature + '°C' : '--'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">HR</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200" x-text="pendingDataObj.heart_rate ? pendingDataObj.heart_rate + ' bpm' : '--'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/60">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">SpO2</span>
                                    <span class="text-xs font-black text-slate-800 dark:text-slate-200" x-text="pendingDataObj.spo2 ? pendingDataObj.spo2 + '%' : '--'"></span>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                                <span class="font-bold text-slate-400 uppercase text-[10px] block mb-1">Chief Complaint</span>
                                <p class="text-slate-800 dark:text-slate-200 italic leading-relaxed" x-text="'&quot;' + (pendingDataObj.symptoms || 'None recorded') + '&quot;'"></p>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="showConfirmModal = false"
                                    class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 cursor-pointer">
                                Back to Edit
                            </button>
                            <button type="button" @click="confirmSubmit()" :disabled="loading"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-2 px-5 rounded-xl shadow-xs transition flex items-center gap-2 text-xs cursor-pointer">
                                <span x-show="loading">
                                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </span>
                                <span>Confirm & Queue</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Generic Confirmation Modal --}}
                <template x-teleport="body">
                    <div x-show="genericConfirmOpen"
                         x-cloak
                         class="fixed inset-0 z-[100] overflow-y-auto"
                         role="dialog"
                         aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="genericConfirmOpen"
                                 @click="genericConfirmOpen = false"
                                 class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                            <div x-show="genericConfirmOpen"
                                 class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                                <div class="p-6 sm:p-8">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 sm:h-10 sm:w-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="genericConfirmTitle"></h3>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" x-text="genericConfirmMessage"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                                    <button type="button"
                                            @click="genericConfirmCallback(); genericConfirmOpen = false"
                                            class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-md px-5 py-2 bg-amber-600 hover:bg-amber-700 text-xs font-bold text-white transition-all cursor-pointer">
                                        Confirm
                                    </button>
                                    <button type="button"
                                            @click="genericConfirmOpen = false"
                                            class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-xs px-5 py-2 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-all cursor-pointer">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        {{-- RIGHT COLUMN: Live Waiting Queues & Station Records (Cols 8-12) --}}
        <div id="right-panel-queues" class="lg:col-span-5 space-y-5">
            
            {{-- Arrived Appointments (Checked-in by Info Desk) --}}
            @if(isset($arrivedAppointments) && $arrivedAppointments->count() > 0)
                <div class="rounded-3xl bg-blue-50/70 dark:bg-blue-950/30 border-2 border-blue-400/80 dark:border-blue-600/80 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
                        <h3 class="font-extrabold text-sm flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping inline-block"></span>
                            <span>Arrived Appointments</span>
                        </h3>
                        <span class="text-xs font-black bg-white/20 text-white px-2.5 py-0.5 rounded-full">
                            {{ $arrivedAppointments->count() }} arrived
                        </span>
                    </div>
                    <div class="px-4 py-2 bg-blue-100/60 dark:bg-blue-900/40 border-b border-blue-200 dark:border-blue-800 text-[11px] text-blue-900 dark:text-blue-200 font-medium">
                        Patients checked-in at Info Desk ready for pre-triage vitals.
                    </div>
                    <div class="divide-y divide-blue-100 dark:divide-blue-900/40 max-h-64 overflow-y-auto custom-scrollbar">
                        @foreach($arrivedAppointments as $apt)
                            <div class="p-4 hover:bg-white/60 dark:hover:bg-slate-800/60 transition flex items-center justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="font-extrabold text-slate-900 dark:text-white text-sm truncate">{{ $apt->first_name }} {{ $apt->last_name }}</p>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        <span class="font-bold px-2 py-0.2 rounded {{ $apt->type === 'pedia' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $apt->type === 'pedia' ? 'Pediatric' : 'Adult' }}
                                        </span>
                                        @if($apt->is_follow_up)
                                            <span class="text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-1.5 py-0.5 rounded font-bold uppercase">Follow-up</span>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="window.dispatchEvent(new CustomEvent('triage-arrived', { 
                                            detail: { 
                                                name: '{{ addslashes($apt->first_name . ' ' . $apt->last_name) }}', 
                                                first_name: '{{ addslashes($apt->first_name) }}',
                                                last_name: '{{ addslashes($apt->last_name) }}',
                                                middle_name: '{{ addslashes($apt->middle_name) }}',
                                                suffix: '{{ addslashes($apt->suffix) }}',
                                                classification: '{{ $apt->classification ?? ($apt->type === 'pedia' ? 'Pediatric' : 'Adult') }}',
                                                type: '{{ $apt->type === 'pedia' ? 'Pediatric' : 'Adult' }}',
                                                complaint: '{{ addslashes($apt->complaint) }}',
                                                appointment_id: '{{ $apt->id }}',
                                                dob: '{{ $apt->dob ? $apt->dob->format('Y-m-d') : '' }}'
                                            } 
                                        }))"
                                        class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-extrabold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition cursor-pointer">
                                    Start Triage
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Waiting for Info Desk (Active Queue) --}}
            <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <h3 class="font-extrabold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>Waiting for Info Desk Consultation</span>
                    </h3>
                    <span class="text-xs font-extrabold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                        {{ $waiting->count() }}
                    </span>
                </div>

                @if($waiting->isEmpty())
                    <div class="p-8 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                        <p class="text-xs font-bold">No patients waiting for vitals queue at the moment.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-[480px] overflow-y-auto custom-scrollbar">
                        @foreach($waiting as $index => $entry)
                            <div class="p-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-black text-xs flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-extrabold text-sm text-slate-900 dark:text-white truncate">{{ $entry->patient_name }}</h4>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $entry->classification === 'Senior' ? 'bg-amber-100 text-amber-800' : ($entry->classification === 'Pediatric' ? 'bg-purple-100 text-purple-800' : ($entry->classification === 'PWD' ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-700')) }}">
                                            {{ $entry->classification }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                        <span>BP: <strong>{{ $entry->blood_pressure ?: '--' }}</strong></span>
                                        <span>•</span>
                                        <span>Temp: <strong>{{ $entry->temperature ? $entry->temperature . '°C' : '--' }}</strong></span>
                                        <span>•</span>
                                        <span>SpO₂: <strong>{{ $entry->spo2 ? $entry->spo2 . '%' : '--' }}</strong></span>
                                    </div>
                                    @if($entry->symptoms)
                                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 italic truncate">"{{ $entry->symptoms }}"</p>
                                    @endif
                                    <p class="text-[10px] text-slate-400 mt-1">{{ $entry->created_at->diffForHumans() }}</p>
                                </div>
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('triage-cancel', { detail: '{{ route('triage.cancel', $entry) }}' }))"
                                        title="Remove"
                                        class="text-slate-400 hover:text-rose-500 p-1 rounded-lg hover:bg-rose-50 transition shrink-0 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Processed Today (Claimed) --}}
            @if($claimed->count() > 0)
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                        <h3 class="font-extrabold text-xs text-slate-700 dark:text-slate-300 flex items-center gap-2 uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Picked Up by Consultations ({{ $claimed->count() }})</span>
                        </h3>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-48 overflow-y-auto custom-scrollbar">
                        @foreach($claimed as $entry)
                            <div class="px-5 py-3 flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $entry->patient_name }}</span>
                                <span class="text-[11px] text-slate-400 shrink-0">{{ $entry->updated_at->format('h:i A') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Archived / Cancelled Section --}}
            @if(isset($cancelled) && $cancelled->count() > 0)
                <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                        <h3 class="font-extrabold text-xs text-rose-600 dark:text-rose-400 flex items-center gap-2 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Archived / Cancelled Today ({{ $cancelled->count() }})</span>
                        </h3>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-40 overflow-y-auto custom-scrollbar">
                        @foreach($cancelled as $entry)
                            <div class="px-5 py-2.5 flex items-center justify-between gap-3 text-xs opacity-75 hover:opacity-100 transition">
                                <span class="line-through text-slate-500 truncate">{{ $entry->patient_name }}</span>
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('triage-restore', { detail: '{{ route('triage.restore', $entry) }}' }))"
                                        class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white transition cursor-pointer">
                                    Restore
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection