@extends('layouts.nurse')

@section('header', 'Vitals Station')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6"
         x-data="{
            stats: {
                waiting: {{ $waiting->count() }},
                processed: {{ $claimed->count() }},
                totalToday: {{ $totalToday }},
                leftToday: {{ $leftToday ?? 0 }}
            },
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
                setInterval(() => {
                    this.fetchStats();
                    this.fetchQueues();
                }, 3500);
            }
         }">

        {{-- Flash --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4">
                <p class="font-bold mb-1">Please fix the following:</p>
                <ul class="list-disc list-inside text-sm space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Welcome Header --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-teal-600 to-emerald-700 dark:from-teal-800 dark:to-emerald-900 rounded-2xl shadow-sm border border-teal-700 dark:border-teal-900 p-6 flex items-center justify-between text-white transition-colors duration-300">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-white opacity-5 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-emerald-400 opacity-10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 flex items-center gap-5">
                <div class="shrink-0">
                    <div class="w-16 h-16 rounded-xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center overflow-hidden shadow-lg">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-black text-white">
                                {{ auth()->user()->initials }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold mb-1 drop-shadow-sm tracking-tight">Welcome back, {{ auth()->user()->formatted_name }}</h2>
                    <p class="text-teal-50 dark:text-teal-100/80 text-sm font-medium">Record patient vitals and manage the pre-triage queue.</p>
                </div>
            </div>
            <div class="hidden md:block relative z-10 bg-white/10 p-3 rounded-xl backdrop-blur-sm border border-white/10">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>

        {{-- Header Stats --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pre-Triage Stats Today</h3>
                <p class="text-sm text-gray-500 mt-1">Real-time overview of vitals station activity.</p>
            </div>
            <div class="flex items-center gap-3">
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-center shadow-sm cursor-help">
                    <div x-show="showTooltip" class="absolute z-50 bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl text-center" style="display: none;">Patients currently in the queue waiting for their vitals to be taken.</div>
                    <div class="text-2xl font-bold text-yellow-600" x-text="stats.waiting"></div>
                    <div class="text-xs text-gray-500">Waiting</div>
                </div>
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-center shadow-sm cursor-help">
                    <div x-show="showTooltip" class="absolute z-50 bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl text-center" style="display: none;">Patients whose vitals have been recorded today.</div>
                    <div class="text-2xl font-bold text-green-600" x-text="stats.processed"></div>
                    <div class="text-xs text-gray-500">Processed</div>
                </div>
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-center shadow-sm cursor-help">
                    <div x-show="showTooltip" class="absolute z-50 bottom-full mb-2 left-1/2 -translate-x-1/2 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl text-center" style="display: none;">Total number of patients handled by this station today.</div>
                    <div class="text-2xl font-bold text-gray-700 dark:text-gray-200" x-text="stats.totalToday"></div>
                    <div class="text-xs text-gray-500">Total Today</div>
                </div>
                <div x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false" class="relative bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 text-center shadow-sm cursor-help">
                    <div x-show="showTooltip" class="absolute z-50 bottom-full mb-2 right-0 w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl text-center" style="display: none;">Patients who left or were cancelled after vitals were taken.</div>
                    <div class="text-2xl font-bold text-red-500" x-text="stats.leftToday"></div>
                    <div class="text-xs text-red-500">Left After Vitals</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">

            {{-- LEFT: Vitals Input Form --}}
            <div class="md:col-span-3">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden"
                    @triage-cancel.window="cancelQueue($event.detail)" @triage-restore.window="restoreQueue($event.detail)"
                    @triage-arrived.window="
                        appointmentId = $event.detail.appointment_id || '';
                        searchQuery = $event.detail.name;
                        searchDOB = $event.detail.dob || '';
                        searchPatients(searchDOB);
                        setTimeout(() => { 
                            if(searchResults.length === 0) { 
                                setNew(); 
                                selectedPatient = null; 
                                document.querySelector('input[name=first_name]').value = $event.detail.first_name || ''; 
                                document.querySelector('input[name=last_name]').value = $event.detail.last_name || ''; 
                                document.querySelector('input[name=middle_name]').value = $event.detail.middle_name || ''; 
                                document.querySelector('input[name=suffix]').value = $event.detail.suffix || ''; 
                                document.querySelector('input[name=dob]').value = searchDOB;
                                classification = $event.detail.classification || $event.detail.type || 'Adult';
                                symptomsText = $event.detail.complaint || '';
                            } else if(searchResults.length === 1) {
                                // AUTO-SELECT if single exact match
                                selectPatient(searchResults[0]);
                                symptomsText = $event.detail.complaint || '';
                            } else {
                                symptomsText = $event.detail.complaint || '';
                                classification = $event.detail.classification || $event.detail.type || 'Adult';
                            }
                        }, 800);
                     " x-data="{
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

                        // Toast State
                        toastVisible: false,
                        toastMessage: '',
                        toastType: 'success',
                        toastTimer: null,

                        // Modal State
                        showConfirmModal: false,
                        pendingFormData: null,
                        pendingDataObj: {},

                        // Generic Confirmation Modal
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
                            // Dispatch to Global Notification System (partials/toast.blade.php)
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
                            
                            // Auto-populate health history
                            this.historyText = p.past_medical_history || '';
                            this.medicineText = p.medicine_taken || '';
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
                            this.clearHistory();
                            this.symptomsText = '';
                        },

                        openConfirmModal(e) {
                            // Capture data from the form event
                            this.pendingFormData = new FormData(e.target);
                            
                            // Calculate actual elapsed seconds locally to prevent clock-skew negative values
                            if (this.vitalsStartedAt) {
                                let elapsed = Math.floor((Date.now() - this.vitalsStartedAt) / 1000);
                                // Fallback for instant submits
                                if (elapsed < 1) elapsed = 1;
                                this.pendingFormData.append('encoding_duration_seconds', elapsed);
                            }

                            this.pendingDataObj = Object.fromEntries(this.pendingFormData.entries());

                            // Show modal
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

                                    // Reset the form explicitly since we don't have the original event reference easily
                                    document.getElementById('vitalsForm').reset();

                                    // Dynamically fetch new list to update right panel without reloading
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
                                    // Close modal on error so user can see and fix the issue
                                    this.showConfirmModal = false;

                                    if (data.errors) {
                                        // Format validation errors (e.g. from the new vitals plausibility checks)
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
                                                    document.querySelector('#right-panel-queues').innerHTML = tempDiv.querySelector('#right-panel-queues').innerHTML;
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
                                                    document.querySelector('#right-panel-queues').innerHTML = tempDiv.querySelector('#right-panel-queues').innerHTML;
                                                });
                                        }
                                    });
                                }
                            );
                        }
                     }">


                    <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-6 py-4">
                        <h3 class="text-white font-bold text-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Record Patient Vitals
                        </h3>
                        <p class="text-teal-100 text-xs mt-1">Step 1: Identify if returning or new. Step 2: Fill vitals.</p>
                    </div>

                    <div class="p-6 space-y-5">

                        {{-- STEP 1: Patient Identification --}}
                        <div
                            class="bg-slate-50 dark:bg-gray-900/50 rounded-xl p-4 border border-slate-200 dark:border-gray-700">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Step 1 — Identify
                                Patient</p>

                            {{-- Initial choice --}}
                            <div x-show="isNew === null" class="space-y-3">
                                <div class="relative">
                                    <label
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Search
                                        existing patient by name or PhilHealth ID</label>
                                    <div class="relative">
                                        <input type="text" x-model="searchQuery" @input="searchPatients()"
                                            placeholder="Type name to search..."
                                            class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm focus:border-teal-500 pr-10">
                                        <div x-show="loading" class="absolute right-3 top-3">
                                            <svg class="animate-spin w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    {{-- Search Results Dropdown --}}
                                    <div x-show="searchResults.length > 0"
                                        class="absolute z-20 w-full mt-1 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden max-h-64 overflow-y-auto">
                                        <template x-for="p in searchResults" :key="p.id">
                                            <button type="button" @click="selectPatient(p)"
                                                class="w-full text-left px-4 py-3 hover:bg-teal-50 dark:hover:bg-teal-900/30 border-b border-gray-100 dark:border-gray-700 last:border-0 transition">
                                                <p class="font-bold text-gray-900 dark:text-white text-sm"
                                                    x-text="p.full_name"></p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    <span x-text="p.classification"></span> &bull;
                                                    DOB: <span x-text="p.dob"></span> &bull;
                                                    Last visit: <span x-text="p.last_visit"></span>
                                                </p>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 border-t border-gray-200"></div>
                                    <span class="text-xs text-gray-400 font-medium">OR</span>
                                    <div class="flex-1 border-t border-gray-200"></div>
                                </div>
                                <button type="button" @click="setNew()"
                                    class="w-full bg-white dark:bg-gray-700 border-2 border-dashed border-slate-300 dark:border-gray-500 rounded-lg py-2.5 text-sm font-bold text-gray-600 dark:text-gray-300 hover:border-teal-400 hover:text-teal-600 transition">
                                    + This is a New Patient
                                </button>
                            </div>

                            {{-- Returning Patient Selected --}}
                            <div x-show="selectedPatient !== null" style="display:none">
                                <div
                                    class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-4 border border-teal-200 dark:border-teal-700">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <svg class="w-4 h-4 text-teal-600 shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span
                                                    class="text-xs font-bold text-teal-700 dark:text-teal-300 uppercase tracking-wide">Returning
                                                    Patient</span>
                                            </div>
                                            <p class="font-bold text-gray-900 dark:text-white"
                                                x-text="selectedPatient?.full_name"></p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                <span x-text="selectedPatient?.classification"></span> &bull;
                                                Age: <span x-text="selectedPatient?.age"></span> &bull;
                                                Last visit: <span x-text="selectedPatient?.last_visit"></span>
                                            </p>
                                        </div>
                                        <button type="button" @click="clearSelection()"
                                            class="text-xs text-red-500 hover:text-red-700 border border-red-200 bg-red-50 px-2 py-1 rounded font-bold shrink-0">✕
                                            Clear</button>
                                    </div>

                                    {{-- Patient History Panel --}}
                                    <div class="mt-3 pt-3 border-t border-teal-200 dark:border-teal-700 space-y-1.5">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Medical History
                                        </p>
                                        <template x-if="selectedPatient?.allergies">
                                            <p
                                                class="text-xs text-gray-700 dark:text-gray-300 bg-orange-50 border border-orange-200 rounded px-2 py-1.5">
                                                <span class="font-bold text-orange-700">⚠ Known Conditions/Allergies:</span>
                                                <span x-text="selectedPatient.allergies"></span>
                                            </p>
                                        </template>
                                        <template x-if="selectedPatient?.last_medicine">
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                <span class="font-bold">Last Medicines:</span>
                                                <span x-text="selectedPatient.last_medicine"></span>
                                            </p>
                                        </template>
                                        <template x-if="selectedPatient?.last_symptoms">
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                <span class="font-bold">Last Complaint:</span>
                                                <span x-text="selectedPatient.last_symptoms"></span>
                                            </p>
                                        </template>
                                        <template x-if="selectedPatient?.last_diagnosis">
                                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                                <span class="font-bold">Last Diagnosis:</span>
                                                <span x-text="selectedPatient.last_diagnosis"></span>
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- New Patient --}}
                            <div x-show="isNew === true && selectedPatient === null" style="display:none">
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-sm font-bold text-blue-800 dark:text-blue-200">New Patient — Name
                                            will be collected below</span>
                                    </div>
                                    <button type="button" @click="clearSelection()"
                                        class="text-xs text-red-500 hover:text-red-700 border border-red-200 bg-red-50 px-2 py-1 rounded font-bold">✕</button>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 2: Vitals Form --}}
                        <div x-show="isNew !== null">
                            <form @submit.prevent="openConfirmModal($event)" class="space-y-5" id="vitalsForm">
                                @csrf
                                {{-- Hidden fields --}}
                                <input type="hidden" name="patient_id" :value="selectedPatient?.id ?? ''">
                                <input type="hidden" name="appointment_id" :value="appointmentId">
                                <input type="hidden" name="vitals_started_at" :value="vitalsStartedAt">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Patient Name (Returning Patient) --}}
                                    <div x-show="!isNew" class="md:col-span-1">
                                        <label
                                            class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">
                                            Patient Name <span class="text-red-500">*</span>
                                            <span class="ml-1 normal-case text-teal-600 font-normal">(auto-filled)</span>
                                        </label>
                                        <input type="text" name="patient_name" :required="!isNew"
                                            :value="selectedPatient ? selectedPatient.full_name : ''" readonly
                                            class="w-full rounded-lg border-2 border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 px-3 py-2.5 text-sm focus:border-teal-500">
                                        <input type="hidden" name="dob" :value="selectedPatient ? selectedPatient.dob_raw : ''">
                                    </div>

                                    {{-- Split Name Fields (New Patient) --}}
                                    <div x-show="isNew" class="md:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4" x-cloak>
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">First
                                                Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="first_name" :required="isNew" placeholder="Juan"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm focus:border-teal-500 uppercase">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">Middle
                                                Name</label>
                                            <input type="text" name="middle_name" placeholder="Dela"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm focus:border-teal-500 uppercase">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">Last
                                                Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="last_name" :required="isNew" placeholder="Cruz"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm focus:border-teal-500 uppercase">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">Suffix</label>
                                            <input type="text" name="suffix" placeholder="Jr., III"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm focus:border-teal-500 uppercase">
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">Classification
                                            <span class="text-red-500">*</span></label>
                                        <select name="classification" required x-model="classification"
                                            class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2.5 text-sm focus:border-teal-500">
                                            <option value="Adult">Adult</option>
                                            <option value="Senior">Senior Citizen</option>
                                            <option value="Pediatric">Pediatric</option>
                                            <option value="PWD">PWD</option>
                                        </select>
                                    </div>
                                    <div x-show="isNew" x-data="{
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
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">Date of Birth <span class="text-red-500">*</span></label>
                                        <input type="hidden" name="dob" x-model="dobValue" :required="isNew">
                                        
                                        <div @click="showDatepicker = !showDatepicker"
                                            class="w-full rounded-lg border-2 bg-white dark:bg-gray-700 dark:text-white px-3 py-2.5 text-sm cursor-pointer flex justify-between items-center transition duration-150"
                                            :class="showDatepicker ? 'border-teal-500' : 'border-slate-300 dark:border-gray-600'">
                                            <span x-text="dobValue ? dobValue : 'YYYY-MM-DD'" :class="dobValue ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-300'"></span>
                                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>

                                        <!-- Datepicker Popup -->
                                        <div x-show="showDatepicker" @click.away="showDatepicker = false" style="display: none;"
                                            class="absolute z-50 mt-1 w-[300px] p-4 bg-white dark:bg-gray-800 dark:border-gray-600 border border-gray-200 rounded-lg shadow-xl outline-none right-0 sm:right-auto">
                                            <div class="flex justify-between items-center mb-4 gap-2">
                                                <select @change="setMonth($event.target.value)" class="w-1/2 flex-1 rounded-md border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 dark:bg-gray-700 focus:border-teal-500 p-1">
                                                    <template x-for="(month, index) in monthNames" :key="index">
                                                        <option :value="index" x-text="month" :selected="index === currentDate.getMonth()"></option>
                                                    </template>
                                                </select>
                                                <select @change="setYear($event.target.value)" class="w-1/2 flex-1 rounded-md border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 dark:bg-gray-700 focus:border-teal-500 p-1">
                                                    <template x-for="year in Array.from({length: 120}, (_, i) => new Date().getFullYear() - i)" :key="year">
                                                        <option :value="year" x-text="year" :selected="year === currentDate.getFullYear()"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-7 gap-1 mb-2">
                                                <template x-for="day in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']">
                                                    <div class="text-center text-xs font-bold text-gray-400 dark:text-gray-500" x-text="day"></div>
                                                </template>
                                            </div>
                                            <div class="grid grid-cols-7 gap-1">
                                                <template x-for="blank in startDay"><div class="p-1"></div></template>
                                                <template x-for="day in daysInMonth" :key="day">
                                                    <div @click="selectDate(day)"
                                                        class="w-8 h-8 flex items-center justify-center rounded-full text-sm cursor-pointer transition-colors"
                                                        :class="{
                                                                'bg-teal-600 text-white font-bold shadow-md': isSelected(day),
                                                                'hover:bg-teal-100 dark:hover:bg-teal-900/50 text-gray-700 dark:text-gray-300': !isSelected(day) && !isFutureDate(day),
                                                                'text-gray-300 dark:text-gray-600 cursor-not-allowed': isFutureDate(day),
                                                                'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-medium': !isSelected(day) && new Date().getDate() === day && new Date().getMonth() === currentDate.getMonth() && new Date().getFullYear() === currentDate.getFullYear()
                                                            }" x-text="day">
                                                    </div>
                                                </template>
                                            </div>
                                            
                                            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                                <button type="button" @click="dobValue = ''; showDatepicker = false" class="text-xs text-red-500 hover:text-red-700 font-medium">Clear</button>
                                                <button type="button" @click="currentDate = new Date(); selectDate(new Date().getDate())" class="text-xs text-teal-600 hover:text-teal-700 font-medium">Today</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div x-show="classification === 'Pediatric' && !appointmentId" x-cloak class="md:col-span-2 mt-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" name="is_emergency" value="1" class="rounded text-red-600 focus:ring-red-500 border-red-300">
                                            <span class="text-sm font-bold text-red-700 dark:text-red-400">Emergency Override</span>
                                        </label>
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1 pl-6">Check this to bypass the 5-slot Pediatric walk-in limit for severe or emergency cases.</p>
                                    </div>

                                </div>

                                {{-- Vital Signs --}}
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Vital Signs</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Temp
                                                (°C)</label>
                                            <input type="text" name="temperature" value="{{ old('temperature') }}"
                                                placeholder="36.5"
                                                x-on:input="$el.value = $el.value.replace(/[^0-9.]/g, '').replace(/^0+(?!$|\.)/, '')"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">
                                        </div>
                                        <div x-data="{ 
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
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">BP
                                                (mmHg)</label>
                                            <div
                                                class="flex items-center rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 focus-within:border-teal-500 px-1 py-1">
                                                <input type="text" x-model="sys" x-ref="sys" @input="checkSys"
                                                    @keydown.right="if($refs.sys.selectionStart>=sys.length)$refs.dia.focus()"
                                                    @keydown.slash.prevent="$refs.dia.focus()" placeholder="120"
                                                    class="w-1/2 bg-transparent text-center border-none focus:ring-0 p-1 text-sm text-gray-900 dark:text-white">
                                                <span class="text-gray-400 font-bold">/</span>
                                                <input type="text" x-model="dia" x-ref="dia" @input="checkDia"
                                                    @keydown.left="if($refs.dia.selectionStart===0)$refs.sys.focus()"
                                                    placeholder="80"
                                                    class="w-1/2 bg-transparent text-center border-none focus:ring-0 p-1 text-sm text-gray-900 dark:text-white">
                                            </div>
                                            <input type="hidden" name="blood_pressure" :value="(sys||dia)?sys+'/'+dia:''">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">HR
                                                (bpm)</label>
                                            <input type="text" name="heart_rate" value="{{ old('heart_rate') }}"
                                                placeholder="80"
                                                x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">RR
                                                (cpm)</label>
                                            <input type="text" name="respiratory_rate" value="{{ old('respiratory_rate') }}"
                                                placeholder="16"
                                                x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">PR
                                                (bpm)</label>
                                            <input type="text" name="pulse_rate" value="{{ old('pulse_rate') }}"
                                                placeholder="80"
                                                x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">SpO₂
                                                (%)</label>
                                            <input type="text" name="spo2" value="{{ old('spo2') }}" placeholder="98"
                                                x-on:input="$el.value = $el.value.replace(/\D/g, '').replace(/^0+(?!$)/, '')"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Wt
                                                (kg)</label>
                                            <input type="text" name="weight" value="{{ old('weight') }}" placeholder="65"
                                                x-on:input="$el.value = $el.value.replace(/[^0-9.]/g, '').replace(/^0+(?!$|\.)/, '')"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Ht
                                                (cm)</label>
                                            <input type="text" name="height" value="{{ old('height') }}" placeholder="165"
                                                x-on:input="$el.value = $el.value.replace(/[^0-9.]/g, '').replace(/^0+(?!$|\.)/, '')"
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">
                                        </div>
                                    </div>
                                </div>

                                {{-- Clinical Notes --}}
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Clinical Notes</p>
                                    
                                    {{-- Row 1: Past Medical History & Medicine Taken --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <div class="flex justify-between items-center mb-1.5">
                                                <label class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Past Medical History <span class="text-red-500">*</span></label>
                                                <button type="button" @click="historyText = 'N/A'" class="text-xs text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800 bg-teal-50 dark:bg-teal-900/40 px-2 py-0.5 rounded font-bold hover:bg-teal-100 dark:hover:bg-teal-800 transition">(N/A)</button>
                                            </div>
                                            <textarea name="past_medical_history" required rows="2"
                                                x-model="historyText"
                                                placeholder="Past surgeries, conditions..."
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">{{ old('past_medical_history') }}</textarea>
                                        </div>
                                        <div>
                                            <div class="flex justify-between items-center mb-1.5">
                                                <label class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Medicine Taken <span class="text-red-500">*</span></label>
                                                <button type="button" @click="medicineText = 'N/A'" class="text-xs text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800 bg-teal-50 dark:bg-teal-900/40 px-2 py-0.5 rounded font-bold hover:bg-teal-100 dark:hover:bg-teal-800 transition">(N/A)</button>
                                            </div>
                                            <textarea name="medicine_taken" required rows="2"
                                                x-model="medicineText"
                                                placeholder="Medication currently being taken..."
                                                class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">{{ old('medicine_taken') }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Row 2: Known Allergies --}}
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <label class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Known Allergies <span class="text-red-500">*</span></label>
                                            <button type="button" @click="allergiesText = 'N/A'" class="text-xs text-teal-600 dark:text-teal-400 border border-teal-200 dark:border-teal-800 bg-teal-50 dark:bg-teal-900/40 px-2 py-0.5 rounded font-bold hover:bg-teal-100 dark:hover:bg-teal-800 transition">(N/A)</button>
                                        </div>
                                        <textarea name="known_allergies" required rows="2"
                                            x-model="allergiesText"
                                            placeholder="Food or drug allergies..."
                                            class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">{{ old('known_allergies') }}</textarea>
                                    </div>

                                    {{-- Row 3: Symptoms / Chief Complaint --}}
                                    <div x-data="{
                                        toggleSymptom(symptom) {
                                            if (symptomsText.includes(symptom)) {
                                                symptomsText = symptomsText.replace(new RegExp('(?:, )?' + symptom, 'g'), '').replace(/^, /, '').trim();
                                            } else {
                                                symptomsText = symptomsText ? symptomsText + ', ' + symptom : symptom;
                                            }
                                        }
                                    }">
                                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-1.5">Symptoms / Chief Complaint <span class="text-red-500">*</span></label>

                                        {{-- Common Symptoms Checkboxes --}}
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium py-1">Quick select:</span>
                                            <button type="button" @click="toggleSymptom('Cough')" :class="symptomsText.includes('Cough') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Cough</button>
                                            <button type="button" @click="toggleSymptom('Colds')" :class="symptomsText.includes('Colds') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Colds</button>
                                            <button type="button" @click="toggleSymptom('Fever')" :class="symptomsText.includes('Fever') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Fever</button>
                                            <button type="button" @click="toggleSymptom('Headache')" :class="symptomsText.includes('Headache') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Headache</button>
                                            <button type="button" @click="toggleSymptom('Diarrhea')" :class="symptomsText.includes('Diarrhea') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Diarrhea</button>
                                            <button type="button" @click="toggleSymptom('Sore Throat')" :class="symptomsText.includes('Sore Throat') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Sore Throat</button>
                                            <button type="button" @click="toggleSymptom('Vomiting')" :class="symptomsText.includes('Vomiting') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Vomiting</button>
                                            <button type="button" @click="toggleSymptom('Dizziness')" :class="symptomsText.includes('Dizziness') ? 'bg-teal-100 dark:bg-teal-900/50 border-teal-500 dark:border-teal-400 text-teal-800 dark:text-teal-300' : 'bg-white dark:bg-gray-800 border-slate-300 dark:border-gray-600 text-gray-600 dark:text-gray-400'" class="text-xs px-2.5 py-1 border rounded-md hover:bg-teal-50 dark:hover:bg-teal-900/30 transition font-bold">Dizziness</button>
                                        </div>
                                        <textarea name="symptoms" required x-model="symptomsText" rows="3"
                                            placeholder="Patient complains of..."
                                            class="w-full rounded-lg border-2 border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2 text-sm focus:border-teal-500">{{ old('symptoms') }}</textarea>
                                    </div>
                                </div>

                                <button type="submit" :disabled="loading"
                                    class="w-full bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white font-bold py-3 rounded-xl transition shadow-sm flex items-center justify-center gap-2 text-sm">
                                    <span x-show="loading">
                                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </span>
                                    <span x-show="!loading">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                    </span>
                                    Review &amp; Submit Vitals
                                </button>
                            </form>
                        </div>

                        {{-- Confirm Modal Overlay --}}
                        <div x-show="showConfirmModal"
                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                            style="display: none;">
                            <div @click.away="showConfirmModal = false"
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                <div
                                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Pre-Triage Data</h3>
                                    <p class="text-xs text-gray-500">Please review the recorded vitals before sending to
                                        queue.</p>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div
                                        class="bg-teal-50 dark:bg-teal-900/20 p-3 rounded-lg border border-teal-100 dark:border-teal-800 flex justify-between items-center">
                                        <div>
                                            <p class="text-xs text-teal-600 font-bold uppercase">Patient</p>
                                            <p class="font-bold text-gray-900 dark:text-white capitalize"
                                                x-text="(isNew ? pendingDataObj.first_name + ' ' + pendingDataObj.last_name : pendingDataObj.patient_name).toLowerCase()">
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs font-bold rounded-full"
                                            x-text="pendingDataObj.classification"></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div><span class="text-gray-500">BP:</span> <span class="font-bold"
                                                x-text="pendingDataObj.blood_pressure || '—'"></span></div>
                                        <div><span class="text-gray-500">Temp:</span> <span class="font-bold"
                                                x-text="pendingDataObj.temperature ? pendingDataObj.temperature + '°C' : '—'"></span>
                                        </div>
                                        <div><span class="text-gray-500">HR:</span> <span class="font-bold"
                                                x-text="pendingDataObj.heart_rate ? pendingDataObj.heart_rate + ' bpm' : '—'"></span>
                                        </div>
                                        <div><span class="text-gray-500">SpO2:</span> <span class="font-bold"
                                                x-text="pendingDataObj.spo2 ? pendingDataObj.spo2 + '%' : '—'"></span></div>
                                        <div><span class="text-gray-500">Weight:</span> <span class="font-bold"
                                                x-text="pendingDataObj.weight ? pendingDataObj.weight + ' kg' : '—'"></span>
                                        </div>
                                        <div><span class="text-gray-500">Height:</span> <span class="font-bold"
                                                x-text="pendingDataObj.height ? pendingDataObj.height + ' cm' : '—'"></span>
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                                        <p class="text-xs text-gray-500 font-bold uppercase mb-1">Symptoms/Complaint</p>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200"
                                            x-text="pendingDataObj.symptoms || pendingDataObj.chief_complaint || 'None recorded'">
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                                    <button type="button" @click="showConfirmModal = false"
                                        class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-800 transition">←
                                        Edit</button>
                                    <button type="button" @click="confirmSubmit()" :disabled="loading"
                                        class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-5 rounded-lg shadow-sm transition flex items-center gap-2 text-sm">
                                        <span x-show="loading">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </span>
                                        ✓ Confirm &amp; Submit
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Generic Confirmation Modal --}}
                        <div x-show="genericConfirmOpen" x-cloak
                            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <div @click.away="genericConfirmOpen = false"
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 dark:border-gray-700 transform transition-all"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                                <div class="p-6">
                                    <div class="flex items-center gap-4 mb-4">
                                        <div
                                            class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white"
                                            x-text="genericConfirmTitle"></h3>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed"
                                        x-text="genericConfirmMessage"></p>
                                </div>
                                <div
                                    class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-end gap-3 border-t border-gray-100 dark:border-gray-700">
                                    <button type="button" @click="genericConfirmOpen = false"
                                        class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition uppercase tracking-wider">
                                        Cancel
                                    </button>
                                    <button type="button" @click="genericConfirmCallback(); genericConfirmOpen = false"
                                        class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition flex items-center gap-2 text-sm uppercase tracking-wider">
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Prompt when no choice made yet --}}
                        <div x-show="isNew === null" class="text-center py-4 text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            Search for a returning patient or select "New Patient" above to begin.
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Waiting Queue --}}
            <div id="right-panel-queues" class="md:col-span-2 space-y-4">
                {{-- Arrived Appointments (Checked-in by Info Desk) --}}
                @if(isset($arrivedAppointments) && $arrivedAppointments->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 border-blue-400 overflow-hidden">
                        <div class="px-5 py-3 bg-blue-500 flex items-center justify-between">
                            <h3 class="font-bold text-white flex items-center gap-2 text-sm">
                                <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse inline-block"></span>
                                Arrived Appointments
                            </h3>
                            <span
                                class="text-xs font-bold bg-white/20 text-white px-2.5 py-1 rounded-full">{{ $arrivedAppointments->count() }}
                                waiting</span>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 px-4 py-2 border-b border-blue-100">
                            <p class="text-xs text-blue-700 dark:text-blue-300">These patients were checked in at the info desk.
                                Click triage to start.</p>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[300px] overflow-y-auto">
                            @foreach($arrivedAppointments as $apt)
                                <div class="px-5 py-3 hover:bg-blue-50/50 transition flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $apt->first_name }}
                                            {{ $apt->last_name }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span
                                                class="text-xs px-1.5 py-0.5 rounded-full font-bold {{ $apt->type === 'pedia' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">{{ $apt->type === 'pedia' ? 'Pediatric' : 'Adult' }}</span>
                                            @if($apt->is_follow_up)
                                                <span
                                                    class="text-[10px] bg-gray-200 text-gray-700 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">Follow-up</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('triage-arrived', { 
                                                detail: { 
                                                    name: '{{ addslashes($apt->first_name . ' ' . $apt->last_name) }}', 
                                                    first_name: '{{ addslashes($apt->first_name) }}',
                                                    last_name: '{{ addslashes($apt->last_name) }}',
                                                    middle_name: '{{ addslashes($apt->middle_name) }}',
                                                    suffix: '{{ addslashes($apt->suffix) }}',
                                                    classification: '{{ $apt->classification ?? ($apt->type === 'pedia' ? 'Pediatric' : 'Adult') }}',
                                                    classification: '{{ $apt->classification ?? ($apt->type === 'pedia' ? 'Pediatric' : 'Adult') }}',
                                                    type: '{{ $apt->type === 'pedia' ? 'Pediatric' : 'Adult' }}',
                                                    complaint: '{{ addslashes($apt->complaint) }}',
                                                    appointment_id: '{{ $apt->id }}',
                                                    dob: '{{ $apt->dob ? $apt->dob->format('Y-m-d') : '' }}'
                                                } 
                                            }))"
                                        class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded shadow-sm transition">
                                        Triage
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mt-6">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-pulse inline-block"></span>
                            Waiting for Info Desk
                        </h3>
                        <span
                            class="text-xs font-bold bg-yellow-100 text-yellow-800 px-2.5 py-1 rounded-full">{{ $waiting->count() }}</span>
                    </div>
                    @if($waiting->isEmpty())
                        <div class="px-5 py-10 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                            </svg>
                            <p class="text-sm">No patients waiting.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
                            @foreach($waiting as $index => $entry)
                                <div class="px-5 py-4 flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center shrink-0 text-teal-700 font-bold text-sm">
                                        {{ $index + 1 }}</div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $entry->patient_name }}
                                            </p>
                                            @if($entry->dob)
                                                <span class="text-[10px] font-bold text-gray-400">DOB: {{ \Carbon\Carbon::parse($entry->dob)->format('M d, Y') }}</span>
                                            @endif
                                            @if($entry->patient_id)
                                                <span
                                                    class="text-xs bg-teal-100 text-teal-700 px-1.5 py-0.5 rounded-full font-bold">Returning</span>
                                            @else
                                                <span
                                                    class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full font-bold">New</span>
                                            @endif
                                            <span
                                                class="text-xs px-1.5 py-0.5 rounded-full font-semibold {{ $entry->classification === 'Senior' ? 'bg-blue-100 text-blue-700' : ($entry->classification === 'Pediatric' ? 'bg-purple-100 text-purple-700' : ($entry->classification === 'PWD' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600')) }}">{{ $entry->classification }}</span>
                                        </div>
                                        @if($entry->symptoms)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::limit($entry->symptoms, 55) }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-x-3 mt-1">
                                            @if($entry->blood_pressure)<span class="text-xs text-gray-500"><b>BP:</b>
                                            {{ $entry->blood_pressure }}</span>@endif
                                            @if($entry->temperature)<span class="text-xs text-gray-500"><b>T:</b>
                                            {{ $entry->temperature }}°C</span>@endif
                                            @if($entry->spo2)<span class="text-xs text-gray-500"><b>SpO₂:</b>
                                            {{ $entry->spo2 }}%</span>@endif
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">{{ $entry->created_at->diffForHumans() }}</p>
                                    </div>
                                    <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('triage-cancel', { detail: '{{ route('triage.cancel', $entry) }}' }))"
                                        title="Remove"
                                        class="text-gray-400 hover:text-red-500 p-1 rounded hover:bg-red-50 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($claimed->count() > 0)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-bold text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                Picked Up Today ({{ $claimed->count() }})
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-40 overflow-y-auto">
                            @foreach($claimed as $entry)
                                <div class="px-5 py-2.5 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex-1 truncate">
                                        {{ $entry->patient_name }}</p>
                                    <span class="text-xs text-gray-400 shrink-0">{{ $entry->updated_at->format('h:i A') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Archived / Cancelled List --}}
                @if(isset($cancelled) && $cancelled->count() > 0)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Archived / Cancelled Today ({{ $cancelled->count() }})
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[300px] overflow-y-auto">
                            @foreach($cancelled as $entry)
                                <div class="px-5 py-3 flex items-start gap-3 opacity-80 hover:opacity-100 transition">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 dark:text-white text-sm line-through decoration-red-400">
                                            {{ $entry->patient_name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Removed at {{ $entry->updated_at->format('h:i A') }}
                                        </p>
                                    </div>
                                    <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('triage-restore', { detail: '{{ route('triage.restore', $entry) }}' }))"
                                        title="Restore to queue"
                                        class="text-xs bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white border border-teal-200 font-bold py-1 px-2 rounded shadow-sm transition shrink-0">
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