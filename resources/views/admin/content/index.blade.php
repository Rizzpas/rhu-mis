@extends('layouts.admin')

@section('header', 'Content Management')

@section('content')
<div x-data="{ activeTab: 'hero' }" x-on:switch-tab.window="activeTab = $event.detail" class="max-w-6xl mx-auto space-y-6">

    {{-- Top Action & Header Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </span>
                <span>Landing Page Content</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage public homepage text, hero banner, facilities, and municipal healthcare advisories.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('welcome') }}" target="_blank" 
               class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold shadow-2xs flex items-center gap-1.5 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Live Landing Page</span>
            </a>
        </div>
    </div>

    {{-- Modern Segmented Navigation Tabs (8 Dedicated Content Areas) --}}
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar sm:grid sm:grid-cols-3 lg:grid-cols-9 p-0.5">
            <template x-for="tab in [
                { id: 'hero', name: 'Hero Banner', icon: 'hero' },
                { id: 'topbar', name: 'Top Bar', icon: 'topbar' },
                { id: 'facilities', name: 'Facilities', icon: 'facilities' },
                { id: 'about', name: 'Mission & Charter', icon: 'about' },
                { id: 'organization', name: 'Org Structure', icon: 'organization' },
                { id: 'steps', name: 'Process Steps', icon: 'steps' },
                { id: 'faq', name: 'FAQs', icon: 'faq' },
                { id: 'privacy', name: 'Privacy Policy', icon: 'privacy' },
                { id: 'footer', name: 'Footer Info', icon: 'footer' }
            ]" :key="tab.id">
                <button @click.prevent="activeTab = tab.id"
                    :class="{
                        'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-bold': activeTab === tab.id,
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold hover:bg-slate-100/80 dark:hover:bg-slate-800/80': activeTab !== tab.id
                    }"
                    class="shrink-0 sm:shrink px-3 sm:px-2.5 py-2 sm:py-2.5 rounded-xl text-xs transition-all duration-200 flex items-center justify-center gap-1.5 cursor-pointer text-center select-none active:scale-[0.98]">
                    
                    {{-- Dynamic Tab Icons --}}
                    <span :class="activeTab === tab.id ? 'text-white' : 'text-slate-400 dark:text-slate-500'">
                        <template x-if="tab.icon === 'hero'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'topbar'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'facilities'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </template>
                        <template x-if="tab.icon === 'about'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        </template>
                        <template x-if="tab.icon === 'organization'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'steps'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </template>
                        <template x-if="tab.icon === 'faq'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'privacy'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'footer'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </template>
                    </span>

                    <span class="whitespace-nowrap font-medium" x-text="tab.name"></span>
                </button>
            </template>
        </div>
    </div>

    <script>
        function landingContentManager() {
            return {
                showConfirmModal: false,
                showResetModal: false,
                isDirty: false,
                isSaving: false,
                init() {
                    this.$nextTick(() => {
                        const form = document.getElementById('landingContentForm');
                        if (form) {
                            form.addEventListener('input', () => { this.isDirty = true; });
                            form.addEventListener('change', () => { this.isDirty = true; });
                        }
                    });
                    window.addEventListener('beforeunload', (e) => {
                        if (this.isDirty && !this.isSaving) {
                            e.preventDefault();
                            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                            return e.returnValue;
                        }
                    });
                },
                submitForm() {
                    this.isSaving = true;
                    this.showConfirmModal = false;
                    if (window.showSkeleton) { window.showSkeleton(); }
                    document.getElementById('landingContentForm').submit();
                },
                validateAndConfirm() {
                    const form = document.getElementById('landingContentForm');
                    const titleInputs = form ? form.querySelectorAll('input[name*="guiding_principles"][name$="[title]"]') : [];
                    const descInputs = form ? form.querySelectorAll('textarea[name*="guiding_principles"][name$="[description]"]') : [];
                    let hasEmpty = false;
                    let firstInvalid = null;

                    titleInputs.forEach(input => {
                        if (!input.value || input.value.trim() === '') {
                            hasEmpty = true;
                            if (!firstInvalid) firstInvalid = input;
                            input.classList.add('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        } else {
                            input.classList.remove('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        }
                    });

                    descInputs.forEach(input => {
                        if (!input.value || input.value.trim() === '') {
                            hasEmpty = true;
                            if (!firstInvalid) firstInvalid = input;
                            input.classList.add('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        } else {
                            input.classList.remove('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        }
                    });

                    if (hasEmpty) {
                        window.dispatchEvent(new CustomEvent('add-toast', {
                            detail: {
                                type: 'error',
                                message: 'Please fill in all required Principle Title and Charter Description fields before saving.',
                                duration: 5000
                            }
                        }));
                        window.dispatchEvent(new CustomEvent('switch-tab', { detail: 'about' }));
                        window.dispatchEvent(new CustomEvent('validate-principles'));
                        if (firstInvalid) {
                            setTimeout(() => {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 100);
                        }
                        return;
                    }

                    this.showConfirmModal = true;
                }
            };
        }

        function createFacilityState(initial = {}) {
            let mode = 'weekdays';
            let openTime = '08:00';
            let closeTime = '17:00';
            let customText = '';
            let is24h = false;

            if (initial.operating_hours_structured) {
                mode = initial.operating_hours_structured.mode || 'weekdays';
                openTime = initial.operating_hours_structured.open_time || '08:00';
                closeTime = initial.operating_hours_structured.close_time || '17:00';
                customText = initial.operating_hours_structured.custom_text || '';
                is24h = Boolean(initial.operating_hours_structured.is_24h);
            } else if (initial.operating_hours) {
                if (/24\s*\/?\s*7/i.test(initial.operating_hours)) {
                    mode = 'weekdays';
                    is24h = true;
                }
            }

            let contacts = [{ label: 'Main Line', number: initial.contact_number || '(046) 414-0000' }];
            if (initial.contacts_structured && Array.isArray(initial.contacts_structured) && initial.contacts_structured.length > 0) {
                contacts = initial.contacts_structured.map(c => ({ label: c.label || 'Main Line', number: c.number || '' }));
            }

            let services = [];
            if (Array.isArray(initial.services_offered)) {
                services = [...initial.services_offered];
            } else if (typeof initial.services_offered === 'string' && initial.services_offered.trim() !== '') {
                services = initial.services_offered.split('\n').map(s => s.trim()).filter(Boolean);
            }

            return {
                id: initial.id || null,
                slug: initial.slug || '',
                name: initial.name || '',
                category: initial.category || 'General Medicine',
                description: initial.description || '',
                location: initial.location || 'RHU Main Complex',
                sort_order: initial.sort_order ?? 10,
                is_active: initial.id !== undefined && initial.id !== null ? Boolean(initial.is_active) : true,
                image_url: initial.image_url || '',
                imagePreview: null,
                imageError: null,
                activeTab: 'editor',
                is24h: is24h,
                hoursMode: mode,
                openTime: openTime,
                closeTime: closeTime,
                customText: customText,
                timeError: null,
                contacts: contacts,
                services: services,
                serviceSearch: '',
                customServiceInput: '',
                addCustomService() {
                    const val = this.customServiceInput.trim();
                    if (val && !this.services.includes(val)) {
                        this.services.push(val);
                        this.customServiceInput = '';
                    }
                },
                toggleService(svc) {
                    const idx = this.services.indexOf(svc);
                    if (idx > -1) {
                        this.services.splice(idx, 1);
                    } else {
                        this.services.push(svc);
                    }
                },
                removeService(idx) {
                    this.services.splice(idx, 1);
                },
                addContact() {
                    this.contacts.push({ label: 'Mobile Hotline', number: '' });
                },
                removeContact(idx) {
                    if (this.contacts.length > 1) {
                        this.contacts.splice(idx, 1);
                    }
                },
                formatPhone(event, index) {
                    let val = event.target.value;
                    let digits = val.replace(/\D/g, '');
                    if (digits.startsWith('09') && digits.length <= 11) {
                        if (digits.length > 7) {
                            val = digits.slice(0, 4) + ' ' + digits.slice(4, 7) + ' ' + digits.slice(7);
                        } else if (digits.length > 4) {
                            val = digits.slice(0, 4) + ' ' + digits.slice(4);
                        } else {
                            val = digits;
                        }
                    }
                    this.contacts[index].number = val;
                },
                validateTimes() {
                    if (this.is24h || this.hoursMode === 'custom') {
                        this.timeError = null;
                        return true;
                    }
                    if (this.openTime && this.closeTime && this.closeTime <= this.openTime) {
                        this.timeError = 'Closing time must be after opening time.';
                        return false;
                    }
                    this.timeError = null;
                    return true;
                },
                setPreset(open, close) {
                    this.is24h = false;
                    this.hoursMode = 'weekdays';
                    this.openTime = open;
                    this.closeTime = close;
                    this.validateTimes();
                },
                computedHoursSummary() {
                    if (this.is24h) {
                        return '24 Hours / 7 Days a Week';
                    }
                    if (this.hoursMode === 'custom' && this.customText.trim()) {
                        return this.customText.trim();
                    }
                    const fmt = (t) => {
                        if (!t) return '';
                        const parts = t.split(':');
                        let h = parseInt(parts[0], 10);
                        const m = parts[1] || '00';
                        const ampm = h >= 12 ? 'PM' : 'AM';
                        h = h % 12 || 12;
                        return `${h}:${m} ${ampm}`;
                    };
                    const prefix = this.hoursMode === 'daily' ? 'Mon - Sun' : 'Mon - Fri';
                    return `${prefix} | ${fmt(this.openTime)} - ${fmt(this.closeTime)}`;
                }
            };
        }

        function facilityManager() {
            return {
                showAddModal: false,
                showEditModal: false,
                showDeleteModal: false,
                addState: createFacilityState(),
                editState: createFacilityState(),
                openAdd() {
                    this.addState = createFacilityState();
                    this.showAddModal = true;
                },
                openEdit(unit) {
                    this.editState = createFacilityState(unit);
                    this.showEditModal = true;
                }
            };
        }
    </script>

    {{-- Main Content Settings Form --}}
    <form id="landingContentForm" data-no-loader="true" x-data="landingContentManager()" @submit.prevent="validateAndConfirm()" action="{{ route('admin.content.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 pb-28 sm:pb-20">
        @csrf
        @method('PUT')

        {{-- Top Server-Side Validation Errors Banner --}}
        @if($errors->any())
            <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 shadow-sm space-y-2">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Please review and correct the following items:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-2 text-rose-700 dark:text-rose-300">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Save Changes Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="showConfirmModal"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="save-modal-title"
                 role="dialog"
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showConfirmModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                         @click="showConfirmModal = false"
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showConfirmModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                        <div class="p-6 sm:p-8">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 sm:h-10 sm:w-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="save-modal-title">
                                        Save Landing Content?
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Updated text and images will immediately go live on the public website. Are you sure you want to apply these changes?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="button"
                                    @click="submitForm()"
                                    :disabled="isSaving"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-transparent shadow-lg px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-sm font-bold text-white transition-all cursor-pointer disabled:opacity-50">
                                <template x-if="isSaving">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </template>
                                <span x-text="isSaving ? 'Applying Changes...' : 'Yes, Apply Changes'">Yes, Apply Changes</span>
                            </button>
                            <button type="button"
                                    @click="showConfirmModal = false"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Reset Fields Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="showResetModal"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="reset-modal-title"
                 role="dialog"
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showResetModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                         @click="showResetModal = false"
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showResetModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                        <div class="p-6 sm:p-8">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 sm:h-10 sm:w-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="reset-modal-title">
                                        Reset All Content Fields?
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Are you sure you want to reset all fields back to normal? Any unsaved changes made across all tabs will be discarded.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="button"
                                    @click="showResetModal = false; window.location.reload();"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-amber-600 hover:bg-amber-700 text-sm font-bold text-white transition-all cursor-pointer">
                                Yes, Reset to Normal
                            </button>
                            <button type="button"
                                    @click="showResetModal = false"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 1: HERO SECTION
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'hero'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 initLine1: '{{ old('settings.hero_title_line1', $settings['hero']['hero_title_line1']->value ?? 'Accessible') }}',
                 initHighlight: '{{ old('settings.hero_title_highlight', $settings['hero']['hero_title_highlight']->value ?? 'Public Healthcare') }}',
                 initLine2: '{{ old('settings.hero_title_line2', $settings['hero']['hero_title_line2']->value ?? 'for Every Silang Constituent.') }}',
                 initDesc: '{{ addslashes(old('settings.hero_description', $settings['hero']['hero_description']->value ?? 'The Rural Health Unit is the official municipal healthcare gateway of Silang, Cavite. We provide online appointments, digital triage, doctor consultations, and primary diagnostic referrals.')) }}',
                 initPreview: '{{ isset($settings['hero']['hero_image']->value) ? asset($settings['hero']['hero_image']->value) : '' }}',
                 imagePreview: '{{ isset($settings['hero']['hero_image']->value) ? asset($settings['hero']['hero_image']->value) : '' }}',
                 isDragging: false,
                 line1: '',
                 highlight: '',
                 line2: '',
                 desc: '',
                 init() {
                     this.resetHero();
                 },
                 resetHero() {
                     this.line1 = this.initLine1;
                     this.highlight = this.initHighlight;
                     this.line2 = this.initLine2;
                     this.desc = this.initDesc;
                     this.imagePreview = this.initPreview;
                     const input = document.getElementById('hero_image_file_input');
                     if (input) input.value = '';
                     this.heroError = null;
                 },
                 heroError: null,
                 async handleHeroFile(file) {
                     const input = document.getElementById('hero_image_file_input');
                     if (!file) return;

                     if (window.SecureImageValidator) {
                         const res = await window.SecureImageValidator.validateFile(file);
                         if (!res.valid) {
                             this.heroError = res.message;
                             if (input) input.value = '';
                             return;
                         }
                     }
                     this.heroError = null;

                     $store.imageCropper.open(file, {
                         aspectRatio: 1,
                         subtitle: 'Square crop (1:1) — Hero image banner',
                         onApply: (blob, previewUrl) => {
                             this.imagePreview = previewUrl;
                             this.heroError = null;
                             setCroppedFile(input, blob, file.name || ((blob && blob.type === 'image/webp') ? 'hero.webp' : 'hero.jpg'));
                         }
                     });
                 }
             }"
             @content-reset.window="resetHero()">
            
            {{-- Live Visual Preview Wireframe Card with Browser Chrome Mockup --}}
            <div x-data="{ previewDark: true }"
                 :class="previewDark
                    ? 'bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 border-slate-800'
                    : 'bg-gradient-to-br from-green-50/80 via-white to-emerald-50/60 border-slate-200'"
                 class="text-white rounded-3xl p-6 sm:p-8 border shadow-xl relative overflow-hidden transition-all duration-500">

                {{-- Browser Chrome Bar --}}
                <div :class="previewDark ? 'border-white/10' : 'border-slate-200/80'"
                     class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b transition-colors duration-300">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 inline-block shadow-xs"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80 inline-block shadow-xs"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 inline-block shadow-xs"></span>
                        </div>
                        <div :class="previewDark ? 'bg-white/10 text-slate-300 border-white/5' : 'bg-slate-100 text-slate-500 border-slate-200'"
                             class="px-3 py-1 rounded-lg text-[11px] font-mono flex items-center gap-1.5 border transition-colors duration-300">
                            <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>silang.gov.ph / Rural Health Unit</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5">
                        {{-- Dark / Light Mode Toggle (scoped to preview only) --}}
                        <button type="button"
                                @click.prevent.stop="previewDark = !previewDark"
                                :aria-checked="previewDark.toString()"
                                role="switch"
                                :title="previewDark ? 'Switch preview to Light Mode' : 'Switch preview to Dark Mode'"
                                :class="previewDark
                                    ? 'bg-slate-800/90 border-slate-700 hover:border-slate-600'
                                    : 'bg-slate-200/90 border-slate-300 hover:border-slate-400'"
                                class="relative inline-flex items-center h-7 w-14 shrink-0 cursor-pointer rounded-full border p-0.5 transition-colors duration-300 focus:outline-none select-none">
                            {{-- Ambient Sun Icon on Track (visible when in dark mode) --}}
                            <span :class="previewDark ? 'opacity-70' : 'opacity-0'"
                                  class="absolute left-2 inset-y-0 flex items-center justify-center pointer-events-none transition-opacity duration-200">
                                <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.06 1.06l1.06 1.06z"/>
                                </svg>
                            </span>

                            {{-- Ambient Moon Icon on Track (visible when in light mode) --}}
                            <span :class="previewDark ? 'opacity-0' : 'opacity-70'"
                                  class="absolute right-2 inset-y-0 flex items-center justify-center pointer-events-none transition-opacity duration-200">
                                <svg class="w-3 h-3 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.766.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z" clip-rule="evenodd"/>
                                </svg>
                            </span>

                            {{-- Sliding Knob --}}
                            <span :class="previewDark
                                    ? 'translate-x-7 bg-slate-900 text-indigo-300 shadow-md border border-slate-700/80'
                                    : 'translate-x-0 bg-white text-amber-500 shadow-sm border border-slate-200'"
                                  class="pointer-events-none inline-flex h-[22px] w-[22px] transform items-center justify-center rounded-full transition-transform duration-200 ease-in-out z-10">
                                {{-- Moon Icon on Knob (Dark Mode) --}}
                                <svg x-show="previewDark" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.455 2.004a.75.75 0 01.26.77 7 7 0 009.958 7.766.75.75 0 011.067.853A8.5 8.5 0 116.647 1.921a.75.75 0 01.808.083z" clip-rule="evenodd"/>
                                </svg>
                                {{-- Sun Icon on Knob (Light Mode) --}}
                                <svg x-show="!previewDark" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                            </span>
                        </button>

                        {{-- Live Preview Badge --}}
                        <span :class="previewDark ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 'text-emerald-700 bg-emerald-100 border-emerald-300/50'"
                              class="h-7 inline-flex items-center gap-1.5 px-3 rounded-full text-[11px] font-bold uppercase tracking-wider border transition-colors duration-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live Preview
                        </span>
                    </div>
                </div>

                {{-- Hero Content Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">
                    {{-- Left Column — Hero Image Box (matches the public landing page carousel) --}}
                    <div class="lg:col-span-6 order-1">
                        <div :class="previewDark ? 'border-white/10 shadow-emerald-500/10' : 'border-slate-200/90 shadow-slate-300/30'"
                             class="relative w-full aspect-[4/3] rounded-2xl sm:rounded-3xl overflow-hidden border shadow-2xl bg-slate-800 transition-colors duration-300">
                            {{-- Hero Image --}}
                            <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover" x-cloak>
                            {{-- Placeholder when no image --}}
                            <div x-show="!imagePreview" class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 p-6">
                                <div class="w-16 h-16 rounded-2xl bg-slate-700/60 flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-400">Hero Facility Photo</span>
                                <span class="text-[10px] text-slate-500 mt-1">Upload an image below</span>
                            </div>

                            {{-- Gradient Overlay (like the real landing page) --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent pointer-events-none"></div>

                            {{-- Bottom Overlay Info (carousel title/subtitle) --}}
                            <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-5 text-white z-10">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-600/90 text-white text-[9px] font-bold uppercase tracking-wider rounded-sm mb-1.5 backdrop-blur-sm">
                                    Municipal Facility
                                </span>
                                <h3 class="font-extrabold text-sm sm:text-lg leading-tight mb-0.5 text-white">
                                    {{ old('settings.carousel_hero_title', $settings['hero']['carousel_hero_title']->value ?? 'RHU Silang, Cavite') }}
                                </h3>
                                <p class="text-slate-200/80 text-[10px] sm:text-xs font-normal leading-snug">
                                    {{ old('settings.carousel_hero_subtitle', $settings['hero']['carousel_hero_subtitle']->value ?? 'Providing Quality Healthcare for All Citizens') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column — Badge, Title, Description, CTAs (matches the landing page) --}}
                    <div class="lg:col-span-6 order-2 space-y-3 sm:space-y-4">
                        {{-- Municipal Standards & Coverage Badge Preview --}}
                        <div>
                            <div :class="previewDark
                                    ? 'bg-slate-900/80 border-slate-800 text-slate-200'
                                    : 'bg-white/90 border-slate-200/90 text-slate-700 shadow-2xs'"
                                 class="inline-flex items-center gap-2 p-1 pr-3 rounded-full border text-[11px] font-medium transition-colors duration-300">
                                <span :class="previewDark
                                        ? 'bg-emerald-950/80 text-emerald-300 border-emerald-700/50'
                                        : 'bg-emerald-100/90 text-emerald-800 border-emerald-300/50'"
                                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider border shrink-0">
                                    <svg class="w-2.5 h-2.5 text-emerald-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12 1.5l8.5 3.5v7c0 6-4.5 10.5-8.5 12-4-1.5-8.5-6-8.5-12V5L12 1.5zm3.7 7.3a1 1 0 00-1.4-1.4L10.5 11.2 8.7 9.4a1 1 0 10-1.4 1.4l2.5 2.5a1 1 0 001.4 0l4.5-4.5z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>DOH-Accredited</span>
                                </span>
                                <span class="flex items-center gap-1 text-[10px]">
                                    <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <span>Serving <strong :class="previewDark ? 'text-white' : 'text-slate-900'" class="font-bold">64 Barangays</strong> of Silang, Cavite</span>
                                </span>
                            </div>
                        </div>

                        {{-- Primary Headline --}}
                        <h1 :class="previewDark ? 'text-white' : 'text-slate-900'"
                            class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight leading-[1.15] transition-colors duration-300">
                            <span x-text="line1 || 'Accessible'"></span>
                            <span :class="previewDark ? 'text-emerald-400' : 'text-emerald-700'" class="transition-colors duration-300" x-text="highlight || 'Healthcare'"></span><br>
                            <span x-text="line2 || 'for Every Citizen.'"></span>
                        </h1>

                        {{-- Description --}}
                        <p :class="previewDark ? 'text-slate-300/90' : 'text-slate-600'"
                           class="text-xs sm:text-sm leading-relaxed font-normal max-w-md transition-colors duration-300" x-text="desc"></p>

                        {{-- CTA Buttons --}}
                        <div class="pt-1 flex flex-wrap gap-2.5">
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold shadow-md shadow-emerald-500/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Book an Appointment
                            </span>
                            <span :class="previewDark
                                    ? 'bg-white/10 text-white border-white/15'
                                    : 'bg-white text-slate-700 border-slate-300 shadow-sm'"
                                  class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border transition-colors duration-300">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                Manage Booking
                            </span>
                        </div>

                        {{-- Subtle helper labels under CTAs --}}
                        <div :class="previewDark ? 'text-slate-500' : 'text-slate-400'"
                             class="flex flex-wrap gap-6 text-[10px] pt-0.5 transition-colors duration-300">
                            <span>Online appointment scheduling</span>
                            <span>Check status or reschedule</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hero Form Inputs Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Primary Headline & Typography</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure the primary public headline, accent keywords, and introduction text.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">

                    {{-- Title 3-part grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Title Line 1</label>
                            <input type="text" name="settings[hero_title_line1]" x-model="line1"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Title Highlight (Green Accent)</label>
                            <input type="text" name="settings[hero_title_highlight]" x-model="highlight"
                                   class="w-full h-11 px-4 rounded-xl border border-emerald-400 dark:border-emerald-600 bg-emerald-50/40 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 text-sm font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Title Line 2</label>
                            <input type="text" name="settings[hero_title_line2]" x-model="line2"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                    </div>

                    {{-- Hero Description --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Hero Description Paragraph</label>
                        <textarea name="settings[hero_description]" x-model="desc" rows="3"
                                  class="w-full p-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                    </div>
                </div>
            </div>

            {{-- Carousel Banner Text Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/20 to-emerald-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Announcement Carousel Overlay</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Default headline and subtitle shown on the first carousel slide when no cover post is selected.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Slide Headline</label>
                        <input type="text" name="settings[carousel_hero_title]" value="{{ old('settings.carousel_hero_title', $settings['hero']['carousel_hero_title']->value ?? 'RHU Silang, Cavite') }}"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Slide Subtitle</label>
                        <input type="text" name="settings[carousel_hero_subtitle]" value="{{ old('settings.carousel_hero_subtitle', $settings['hero']['carousel_hero_subtitle']->value ?? 'Providing Responsive & Quality Healthcare for All Constituents') }}"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                </div>
            </div>

            {{-- Hero Image Card with Interactive Cropper --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Hero Facility Banner</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Primary photograph of the RHU facility featured in the hero banner on the public website.</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-start gap-6">
                    <div class="flex-1 space-y-3 w-full">
                        <input type="file" id="hero_image_file_input" name="hero_image_file" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden"
                               @change="if ($event.target.files.length) handleHeroFile($event.target.files[0])">
                        
                        <div @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) handleHeroFile($event.dataTransfer.files[0])"
                             @click="document.getElementById('hero_image_file_input').click()"
                             :class="isDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800/70'"
                             class="w-full border-2 border-dashed rounded-2xl p-7 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2 group">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Drag & drop image here, or <span class="text-emerald-600 dark:text-emerald-400 underline underline-offset-2">browse files</span>
                            </p>
                            <p class="text-xs text-slate-400">Supports JPG, PNG, WEBP · 1:1 square crop</p>
                        </div>
                        <template x-if="heroError">
                            <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-2 flex items-center justify-center gap-1.5" role="alert">
                                <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span x-text="heroError"></span>
                            </p>
                        </template>
                    </div>

                    <div class="w-full sm:w-48 h-48 bg-slate-100 dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm shrink-0 flex items-center justify-center relative group">
                        <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                        <span x-show="!imagePreview" class="text-xs font-semibold text-slate-400">No Image</span>
                        <template x-if="imagePreview">
                            <button type="button" @click="document.getElementById('hero_image_file_input').click()" 
                                    class="absolute inset-0 bg-slate-900/60 text-white flex flex-col items-center justify-center text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity gap-1.5 backdrop-blur-xs cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Change Image</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 2: TOP BAR
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'topbar'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 republic: '{{ old('settings.topbar_republic', $settings['topbar']['topbar_republic']->value ?? 'Republic of the Philippines') }}',
                 province: '{{ old('settings.topbar_province', $settings['topbar']['topbar_province']->value ?? 'Province of Cavite') }}',
                 municipality: '{{ old('settings.topbar_municipality', $settings['topbar']['topbar_municipality']->value ?? 'Municipality of Silang') }}',
                 hours: '{{ old('settings.clinic_hours', $settings['topbar']['clinic_hours']->value ?? 'Mon - Fri | 8:00 AM - 5:00 PM') }}',
                 hotlines: '{{ old('settings.emergency_hotlines', $settings['topbar']['emergency_hotlines']->value ?? '(046) 432-1234') }}'
             }">

            {{-- Live Top Bar Preview --}}
            <div class="bg-gradient-to-r from-emerald-950 via-slate-950 to-teal-950 text-white rounded-3xl p-6 sm:p-7 border border-emerald-900/60 shadow-xl relative overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-white/10">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Top Bar Preview
                    </span>
                    <span class="text-[11px] text-slate-400">Exact live preview rendered at the very top of all public pages</span>
                </div>

                <div class="bg-emerald-900/90 rounded-2xl p-4 sm:p-4.5 border border-emerald-800/80 shadow-inner flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-white">
                        <span class="px-2 py-0.5 rounded bg-emerald-800/80 text-[10px] font-black uppercase tracking-wider text-emerald-300 border border-emerald-700/60">Official</span>
                        <span class="font-bold tracking-wide" x-text="republic || 'Republic of the Philippines'"></span>
                        <span class="text-emerald-400 opacity-60">•</span>
                        <span class="text-emerald-200" x-text="province || 'Province of Cavite'"></span>
                        <span class="text-emerald-400 opacity-60">•</span>
                        <span class="text-emerald-200 font-semibold" x-text="municipality || 'Municipality of Silang'"></span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 text-emerald-100/90 text-xs">
                        <div class="flex items-center gap-1.5 bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-800/60">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="hours || 'Mon - Fri | 8:00 AM - 5:00 PM'"></span>
                        </div>
                        <div class="flex items-center gap-1.5 bg-emerald-950/60 text-emerald-200 px-2.5 py-1 rounded-lg border border-emerald-800/60 font-semibold">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span x-text="hotlines || '(046) 432-1234'"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Banner Text Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Institutional Government Banner</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">The official municipal hierarchy labels displayed in the public header.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Republic Label</label>
                        <input type="text" name="settings[topbar_republic]" x-model="republic"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">e.g. "Republic of the Philippines"</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Province Label</label>
                        <input type="text" name="settings[topbar_province]" x-model="province"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">e.g. "Province of Cavite"</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Municipality Label</label>
                        <input type="text" name="settings[topbar_municipality]" x-model="municipality"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">e.g. "Municipality of Silang"</p>
                    </div>
                </div>
            </div>

            {{-- Clinic Hours & Contact Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/20 to-emerald-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Operating Hours & Emergency Contact</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Shown across the entire portal — top header bar, footer, about page, unit facilities, announcements, and booking portals.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Clinic Hours</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <input type="text" name="settings[clinic_hours]" x-model="hours"
                                   class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Shown on site header, announcements, and clinic info cards.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">RHU Contact / Landline</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="text" name="settings[emergency_hotlines]" x-model="hotlines"
                                   class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Universal RHU contact/landline synced across Top Bar, Footer, About Page, Facilities, and Appointments.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 3: FACILITIES & UNITS
        ───────────────────────────────────────────────────────────── --}}
        @php
            $facilityCategoriesMeta = [
                'General Medicine' => [
                    'value' => 'General Medicine',
                    'label' => 'General Medicine',
                    'iconBg' => 'bg-emerald-100/90 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                    'desc' => 'Primary outpatient consultation, routine adult care & vitals',
                    'iconSvg' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                ],
                'Maternity & Child Health' => [
                    'value' => 'Maternity & Child Health',
                    'label' => 'Maternity & Child Health',
                    'iconBg' => 'bg-rose-100/90 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                    'desc' => 'Prenatal, postnatal checkups, normal delivery & pediatric care',
                    'iconSvg' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="7" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 21a6.5 6.5 0 0113 0M17 11a4 4 0 014 4m-18-4a4 4 0 00-4 4"/></svg>',
                ],
                'Women\'s Health' => [
                    'value' => 'Women\'s Health',
                    'label' => 'Women\'s Health',
                    'iconBg' => 'bg-purple-100/90 text-purple-700 dark:bg-purple-900/60 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                    'desc' => 'Gynecology, reproductive wellness & family planning services',
                    'iconSvg' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="8" r="5" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13v8m-3.5-3.5h7"/></svg>',
                ],
                'Dental Care' => [
                    'value' => 'Dental Care',
                    'label' => 'Dental Care',
                    'iconBg' => 'bg-cyan-100/90 text-cyan-700 dark:bg-cyan-900/60 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800/60',
                    'desc' => 'Dental consultations, oral cleaning, prophylaxis & tooth extraction',
                    'iconSvg' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                ],
                'Infectious Diseases' => [
                    'value' => 'Infectious Diseases',
                    'label' => 'Infectious Diseases',
                    'iconBg' => 'bg-amber-100/90 text-amber-700 dark:bg-amber-900/60 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
                    'desc' => 'TB-DOTS screening, GeneXpert testing & communicable disease care',
                    'iconSvg' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
                ],
                'Emergency & Immunization' => [
                    'value' => 'Emergency & Immunization',
                    'label' => 'Emergency & Immunization',
                    'iconBg' => 'bg-red-100/90 text-red-700 dark:bg-red-900/60 dark:text-red-300 border-red-200 dark:border-red-800/60',
                    'desc' => 'Animal bite rabies assessments, tetanus toxoid & acute immunizations',
                    'iconSvg' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                ],
                'Diagnostic & Laboratory' => [
                    'value' => 'Diagnostic & Laboratory',
                    'label' => 'Diagnostic & Laboratory',
                    'iconBg' => 'bg-indigo-100/90 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/60',
                    'desc' => 'Routine blood chemistry, urinalysis, microscopy & diagnostic assays',
                    'iconSvg' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
                ],
            ];

            $contactLabelsMeta = [
                'Main Line' => [
                    'value' => 'Main Line',
                    'label' => 'Main Line',
                    'desc' => 'Primary facility contact',
                    'iconBg' => 'bg-emerald-100/90 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
                    'iconSvg' => '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>',
                ],
                'Mobile Hotline' => [
                    'value' => 'Mobile Hotline',
                    'label' => 'Mobile Hotline',
                    'desc' => 'Mobile & SMS inquiries',
                    'iconBg' => 'bg-teal-100/90 text-teal-700 dark:bg-teal-900/60 dark:text-teal-300 border-teal-200 dark:border-teal-800/60',
                    'iconSvg' => '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
                ],
                'Emergency Line' => [
                    'value' => 'Emergency Line',
                    'label' => 'Emergency Line',
                    'desc' => '24/7 urgent helpline',
                    'iconBg' => 'bg-rose-100/90 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
                    'iconSvg' => '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
                ],
                'Landline' => [
                    'value' => 'Landline',
                    'label' => 'Landline',
                    'desc' => 'Local desk telephone',
                    'iconBg' => 'bg-blue-100/90 text-blue-700 dark:bg-blue-900/60 dark:text-blue-300 border-blue-200 dark:border-blue-800/60',
                    'iconSvg' => '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                ],
                'Appointment Desk' => [
                    'value' => 'Appointment Desk',
                    'label' => 'Appointment Desk',
                    'desc' => 'Booking & consultations',
                    'iconBg' => 'bg-purple-100/90 text-purple-700 dark:bg-purple-900/60 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
                    'iconSvg' => '<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                ],
            ];
        @endphp
        <div x-show="activeTab === 'facilities'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="facilityManager()">

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Municipal Health Facilities & Specialized Clinics</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage public clinical departments, operating schedules, contact lines, and published services.</p>
                        </div>
                    </div>
                    @can('manage-facilities')
                    <button type="button" @click="openAdd()"
                            class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer shrink-0 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Facility Unit</span>
                    </button>
                    @endcan
                </div>

                {{-- Modernized Facilities Table --}}
                <div class="border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/90 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                                <th class="p-4 w-14 text-center">Order</th>
                                <th class="p-4">Facility & Discipline</th>
                                <th class="p-4">Operating Hours & Contact</th>
                                <th class="p-4">Services Count</th>
                                <th class="p-4">Public Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                            @forelse($facilityUnits as $fUnit)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="p-4 text-center font-mono font-bold text-slate-400">{{ $fUnit->sort_order }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3.5">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xs">
                                                <img src="{{ $fUnit->image_url }}" alt="{{ $fUnit->name }}" class="w-full h-full object-cover">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white text-sm tracking-tight truncate flex items-center gap-2">
                                                    <span>{{ $fUnit->name }}</span>
                                                    @if($fUnit->slug === 'main-health-center')
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                            Core Facility
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">
                                                        {{ $fUnit->category ?: 'General Medicine' }}
                                                    </span>
                                                    <span class="text-[11px] text-slate-400 font-mono">/units/{{ $fUnit->slug }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-slate-800 dark:text-slate-200 font-semibold flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ $fUnit->operating_hours ?: 'Mon - Fri | 8:00 AM - 5:00 PM' }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>{{ $fUnit->contact_number ?: $fUnit->location }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px] border border-slate-200/60 dark:border-slate-700/60">
                                            {{ is_array($fUnit->services_offered) ? count($fUnit->services_offered) : 0 }} services
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if($fUnit->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Active</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700">
                                                <span>Hidden</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @can('manage-facilities')
                                            <button type="button" @click="openEdit({{ json_encode($fUnit) }})" 
                                                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs" 
                                                    title="Edit Facility">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            @endcan
                                            <a href="{{ route('units.show', $fUnit->slug) }}" target="_blank" 
                                               class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-blue-300 transition-all cursor-pointer shadow-2xs" 
                                               title="View Public Unit Page">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 italic">No facility units configured. Click "Add Facility Unit" above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────────
                 ADD FACILITY MODAL
            ───────────────────────────────────────────────────────────── --}}
            <template x-teleport="body">
                <div x-show="showAddModal" style="display: none;" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 sm:p-6 overflow-y-auto">
                    <div @click.away="showAddModal = false" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-3xl w-full my-8 border border-slate-200/90 dark:border-slate-800 flex flex-col max-h-[92vh]">
                        
                        {{-- Modal Header --}}
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/90 flex items-center justify-between shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-t-3xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-white leading-tight">Add New Health Facility / Clinic</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Register a municipal clinic, diagnostic unit, or specialized healthcare service.</p>
                                </div>
                            </div>
                            <button type="button" @click="showAddModal = false" 
                                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                    title="Close modal">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Mode Selector Sub-Tabs: Form Editor vs Live Card Preview --}}
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-2.5 bg-slate-50/70 dark:bg-slate-800/40">
                            <div class="flex items-center gap-1.5 p-1 bg-slate-200/70 dark:bg-slate-700/60 rounded-xl">
                                <button type="button" @click="addState.activeTab = 'editor'" 
                                        :class="addState.activeTab === 'editor' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Form Editor</span>
                                </button>
                                <button type="button" @click="addState.activeTab = 'preview'" 
                                        :class="addState.activeTab === 'preview' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Live Card Preview</span>
                                </button>
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline" x-text="addState.activeTab === 'preview' ? 'Card preview mirrors public /units directory' : 'Configure structured hours, contacts, and services'"></span>
                        </div>

                        {{-- Modal Body Form --}}
                        <form action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data" 
                              class="flex flex-col flex-1 overflow-hidden" 
                              @submit="if (!addState.validateTimes()) { $event.preventDefault(); return false; }">
                            @csrf

                            {{-- Hidden Structured Hours, Contacts, Services Payload --}}
                            <input type="hidden" name="operating_hours" :value="addState.computedHoursSummary()">
                            <input type="hidden" name="operating_hours_structured[mode]" :value="addState.hoursMode">
                            <input type="hidden" name="operating_hours_structured[is_24h]" :value="addState.is24h ? 1 : 0">
                            <input type="hidden" name="operating_hours_structured[open_time]" :value="addState.openTime">
                            <input type="hidden" name="operating_hours_structured[close_time]" :value="addState.closeTime">
                            <input type="hidden" name="operating_hours_structured[custom_text]" :value="addState.customText">

                            <input type="hidden" name="contact_number" :value="addState.contacts[0] ? addState.contacts[0].number : ''">
                            <template x-for="(c, cIdx) in addState.contacts" :key="cIdx">
                                <div>
                                    <input type="hidden" :name="'contacts_structured[' + cIdx + '][label]'" :value="c.label">
                                    <input type="hidden" :name="'contacts_structured[' + cIdx + '][number]'" :value="c.number">
                                </div>
                            </template>

                            <template x-for="(s, sIdx) in addState.services" :key="sIdx">
                                <input type="hidden" :name="'services_offered[' + sIdx + ']'" :value="s">
                            </template>

                            {{-- TAB 1: FORM EDITOR --}}
                            <div x-show="addState.activeTab === 'editor'" class="p-6 sm:p-7 space-y-6 overflow-y-auto flex-1">
                                
                                {{-- Facility Photo Upload with Live Preview --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Facility Photo / Banner Image
                                    </label>
                                    <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 hover:border-emerald-500/50 transition-colors">
                                        <div class="relative w-32 h-20 sm:w-36 sm:h-24 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200 dark:border-slate-700 shadow-2xs flex items-center justify-center">
                                            <template x-if="addState.imagePreview">
                                                <img :src="addState.imagePreview" class="w-full h-full object-cover" alt="Preview">
                                            </template>
                                            <template x-if="!addState.imagePreview">
                                                <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                                    <svg class="w-6 h-6 mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[10px] font-medium">Select photo</span>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex-1 text-center sm:text-left space-y-1.5">
                                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                                <label class="cursor-pointer px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-2xs transition-all inline-flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    <span>Choose Photo</span>
                                                    <input type="file" name="image" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden"
                                                           @change="const file = $event.target.files[0]; if (file) { if (window.SecureImageValidator) { window.SecureImageValidator.validateFile(file).then(res => { if (!res.valid) { addState.imageError = res.message; addState.imagePreview = null; $event.target.value = ''; } else { addState.imageError = null; addState.imagePreview = URL.createObjectURL(file); } }); } else { addState.imagePreview = URL.createObjectURL(file); } }">
                                                </label>
                                                <button type="button" x-show="addState.imagePreview" 
                                                        @click="addState.imagePreview = null; addState.imageError = null; $el.closest('form').querySelector('input[name=image]').value = ''" 
                                                        class="px-2.5 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400">
                                                    Clear
                                                </button>
                                            </div>
                                            <template x-if="addState.imageError">
                                                <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5" role="alert">
                                                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span x-text="addState.imageError"></span>
                                                </p>
                                            </template>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                                Recommended: 16:9 ratio (JPG, PNG, WebP up to 5MB). Used for unit banner & card thumbnail.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Row 1: Name and Standardized Category Dropdown --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                            Facility Name <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <input type="text" name="name" x-model="addState.name" required placeholder="e.g. OB-GYN Clinical Unit" 
                                                   class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1">Official unit or clinical department name.</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                            Clinical Category / Discipline <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative z-30" x-data="{ openCategory: false }" @click.outside="openCategory = false" @keydown.escape.window="openCategory = false">
                                            {{-- Hidden Form Input for Validation & Submission --}}
                                            <input type="hidden" name="category" :value="addState.category" required>

                                            {{-- Styled Trigger Button --}}
                                            <button type="button" 
                                                    @click="openCategory = !openCategory"
                                                    class="w-full h-11 px-3.5 rounded-xl border bg-slate-50/70 dark:bg-slate-800/60 text-left transition-all duration-200 cursor-pointer shadow-2xs flex items-center justify-between gap-2.5 focus:outline-none group"
                                                    :class="openCategory 
                                                        ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-white dark:bg-slate-800 shadow-sm' 
                                                        : 'border-slate-300 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-500/60'">
                                                
                                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                                    @foreach($facilityCategoriesMeta as $catKey => $cat)
                                                        <div x-show="addState.category === '{{ addslashes($catKey) }}'" class="flex items-center gap-2.5 min-w-0 flex-1">
                                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border {{ $cat['iconBg'] }} transition-transform duration-200 group-hover:scale-105">
                                                                {!! $cat['iconSvg'] !!}
                                                            </div>
                                                            <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                                                {{ $cat['label'] }}
                                                            </span>
                                                        </div>
                                                    @endforeach

                                                    {{-- Fallback if custom/unknown category --}}
                                                    <div x-show="!{{ json_encode(array_keys($facilityCategoriesMeta)) }}.includes(addState.category)" class="flex items-center gap-2.5 min-w-0 flex-1">
                                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border bg-emerald-100/90 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                        </div>
                                                        <span class="text-sm font-semibold text-slate-900 dark:text-white truncate" x-text="addState.category || 'Select Category'"></span>
                                                    </div>
                                                </div>

                                                {{-- Rotating Chevron Arrow --}}
                                                <div class="shrink-0 flex items-center text-slate-400 dark:text-slate-400 transition-colors group-hover:text-slate-600 dark:group-hover:text-slate-200">
                                                    <svg class="w-4 h-4 transition-transform duration-200"
                                                         :class="openCategory ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </div>
                                            </button>

                                            {{-- Custom Elevated Dropdown Panel --}}
                                            <div x-show="openCategory" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 style="display: none;"
                                                 class="absolute left-0 right-0 z-50 mt-1.5 max-h-72 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-2 custom-scrollbar backdrop-blur-xl space-y-1">
                                                
                                                <div class="px-2.5 py-1.5 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 mb-1">
                                                    <span>Select Clinical Discipline</span>
                                                </div>

                                                @foreach($facilityCategoriesMeta as $catKey => $cat)
                                                    <button type="button"
                                                            @click="addState.category = '{{ addslashes($catKey) }}'; openCategory = false"
                                                            class="w-full text-left p-2.5 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between gap-3 cursor-pointer transition-all duration-150 group border"
                                                            :class="addState.category === '{{ addslashes($catKey) }}'
                                                                ? 'bg-emerald-50/90 dark:bg-emerald-950/50 border-emerald-200/90 dark:border-emerald-800/60 shadow-2xs' 
                                                                : 'bg-transparent border-transparent hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:border-slate-200/60 dark:hover:border-slate-700/60'">
                                                        
                                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border {{ $cat['iconBg'] }} transition-transform duration-150 group-hover:scale-105">
                                                                {!! $cat['iconSvg'] !!}
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white block"
                                                                      :class="addState.category === '{{ addslashes($catKey) }}' ? 'text-emerald-700 dark:text-emerald-300' : ''">
                                                                    {{ $cat['label'] }}
                                                                </span>
                                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $cat['desc'] }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="shrink-0 pl-1" x-show="addState.category === '{{ addslashes($catKey) }}'">
                                                            <div class="w-6 h-6 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1">Primary healthcare discipline for this facility unit.</p>
                                    </div>
                                </div>

                                {{-- Row 2: Clinical Overview --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Clinical Overview & Description
                                    </label>
                                    <textarea name="description" x-model="addState.description" rows="3" placeholder="Provide a summary of medical services, capabilities, and target patient group..." 
                                              class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                    <p class="text-[11px] text-slate-400 mt-1">Displayed on the public unit details page.</p>
                                </div>

                                {{-- Row 3: Structured Operating Hours --}}
                                <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/30 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Operating Hours Configuration</span>
                                        </label>
                                        <span class="text-[11px] font-mono font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800/60" x-text="addState.computedHoursSummary()"></span>
                                    </div>

                                    {{-- Mode Selector --}}
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        <button type="button" @click="addState.is24h = false; addState.hoursMode = 'weekdays'; addState.validateTimes()"
                                                :class="!addState.is24h && addState.hoursMode === 'weekdays' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            Weekdays (Mon-Fri)
                                        </button>
                                        <button type="button" @click="addState.is24h = false; addState.hoursMode = 'daily'; addState.validateTimes()"
                                                :class="!addState.is24h && addState.hoursMode === 'daily' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            Daily (Mon-Sun)
                                        </button>
                                        <button type="button" @click="addState.is24h = true; addState.hoursMode = 'weekdays'; addState.validateTimes()"
                                                :class="addState.is24h ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            24/7 Continuous
                                        </button>
                                        <button type="button" @click="addState.is24h = false; addState.hoursMode = 'custom'; addState.validateTimes()"
                                                :class="!addState.is24h && addState.hoursMode === 'custom' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            Custom Schedule
                                        </button>
                                    </div>

                                    {{-- Standard Time Pickers --}}
                                    <div x-show="!addState.is24h && addState.hoursMode !== 'custom'" class="space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Opens At</label>
                                                <input type="time" x-model="addState.openTime" @input="addState.validateTimes()"
                                                       class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Closes At</label>
                                                <input type="time" x-model="addState.closeTime" @input="addState.validateTimes()"
                                                       class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                            </div>
                                        </div>
                                        <template x-if="addState.timeError">
                                            <p class="text-xs text-rose-500 font-semibold" x-text="addState.timeError"></p>
                                        </template>

                                        {{-- Quick Shortcuts --}}
                                        <div class="flex items-center gap-2 pt-1">
                                            <span class="text-[11px] text-slate-400">Presets:</span>
                                            <button type="button" @click="addState.setPreset('08:00', '17:00')" class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300">8:00 AM - 5:00 PM</button>
                                            <button type="button" @click="addState.setPreset('07:00', '16:00')" class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300">7:00 AM - 4:00 PM</button>
                                            <button type="button" @click="addState.setPreset('08:00', '12:00')" class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300">Morning (8 AM - 12 PM)</button>
                                        </div>
                                    </div>

                                    {{-- 24/7 Notice --}}
                                    <div x-show="addState.is24h" class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300">
                                        ✓ Facility operates 24 hours a day, 7 days a week (continuous clinical/maternal emergency care).
                                    </div>

                                    {{-- Custom Schedule Input --}}
                                    <div x-show="!addState.is24h && addState.hoursMode === 'custom'">
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Custom Operating Schedule Description</label>
                                        <input type="text" x-model="addState.customText" placeholder="e.g. Mon, Wed, Fri | 8:00 AM - 2:00 PM"
                                               class="w-full h-10 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                    </div>
                                </div>

                                {{-- Row 4: Structured Philippine Contacts Repeater & Location --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                    {{-- Multi-Contact Repeater --}}
                                    <div class="space-y-2.5 min-w-0">
                                        <div class="flex items-center justify-between h-5 mb-2">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                                Contact Numbers
                                            </label>
                                            <button type="button" @click="addState.addContact()" 
                                                    class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                                + Add Line
                                            </button>
                                        </div>

                                        <template x-for="(contact, cIdx) in addState.contacts" :key="cIdx">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="relative shrink-0 w-32 sm:w-36" x-data="{ openLabel: false }" @click.outside="openLabel = false" @keydown.escape.window="openLabel = false">
                                                    {{-- Styled Trigger Button --}}
                                                    <button type="button" 
                                                            @click="openLabel = !openLabel"
                                                            class="w-full h-10 px-2.5 rounded-xl border bg-slate-50/70 dark:bg-slate-800/60 text-left transition-all duration-200 cursor-pointer shadow-2xs flex items-center justify-between gap-1.5 focus:outline-none group"
                                                            :class="openLabel 
                                                                ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-white dark:bg-slate-800 shadow-sm' 
                                                                : 'border-slate-300 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-500/60'">
                                                        
                                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                                            @foreach($contactLabelsMeta as $lKey => $lbl)
                                                                <div x-show="contact.label === '{{ addslashes($lKey) }}'" class="flex items-center gap-1.5 min-w-0 flex-1">
                                                                    <div class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 border {{ $lbl['iconBg'] }}">
                                                                        {!! $lbl['iconSvg'] !!}
                                                                    </div>
                                                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                                        {{ $lbl['label'] }}
                                                                    </span>
                                                                </div>
                                                            @endforeach

                                                            {{-- Fallback for custom/unmatched label --}}
                                                            <div x-show="!{{ json_encode(array_keys($contactLabelsMeta)) }}.includes(contact.label)" class="flex items-center gap-1.5 min-w-0 flex-1">
                                                                <div class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 border bg-emerald-100/90 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                                </div>
                                                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" x-text="contact.label || 'Select Line'"></span>
                                                            </div>
                                                        </div>

                                                        {{-- Chevron Arrow --}}
                                                        <div class="shrink-0 flex items-center text-slate-400 dark:text-slate-400">
                                                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                                                 :class="openLabel ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </div>
                                                    </button>

                                                    {{-- Floating Dropdown Menu --}}
                                                    <div x-show="openLabel" 
                                                         x-transition:enter="transition ease-out duration-150"
                                                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                         x-transition:leave="transition ease-in duration-100"
                                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                         style="display: none;"
                                                         class="absolute left-0 z-50 mt-1.5 w-52 sm:w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-1.5 custom-scrollbar backdrop-blur-xl space-y-1">
                                                        
                                                        <div class="px-2 py-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 mb-0.5">
                                                            <span>Contact Line Type</span>
                                                        </div>

                                                        @foreach($contactLabelsMeta as $lKey => $lbl)
                                                            <button type="button"
                                                                    @click="contact.label = '{{ addslashes($lKey) }}'; openLabel = false"
                                                                    class="w-full text-left p-2 rounded-xl text-xs font-medium flex items-center justify-between gap-2.5 cursor-pointer transition-all duration-150 group border"
                                                                    :class="contact.label === '{{ addslashes($lKey) }}'
                                                                        ? 'bg-emerald-50/90 dark:bg-emerald-950/50 border-emerald-200/90 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-200 shadow-2xs' 
                                                                        : 'bg-transparent border-transparent hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:border-slate-200/60 dark:hover:border-slate-700/60 text-slate-700 dark:text-slate-300'">
                                                                
                                                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 border {{ $lbl['iconBg'] }} transition-transform duration-150 group-hover:scale-105">
                                                                        {!! $lbl['iconSvg'] !!}
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <span class="text-xs font-bold block"
                                                                              :class="contact.label === '{{ addslashes($lKey) }}' ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-800 dark:text-white'">
                                                                            {{ $lbl['label'] }}
                                                                        </span>
                                                                        <p class="text-[10px] text-slate-400 dark:text-slate-400 truncate">{{ $lbl['desc'] }}</p>
                                                                    </div>
                                                                </div>

                                                                <div class="shrink-0" x-show="contact.label === '{{ addslashes($lKey) }}'">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <input type="text" :value="contact.number" @input="addState.formatPhone($event, cIdx)" placeholder="09XX XXX XXXX or (046) 414-XXXX"
                                                       class="min-w-0 flex-1 h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                                <button type="button" x-show="addState.contacts.length > 1" @click="addState.removeContact(cIdx)" 
                                                        class="shrink-0 w-8 h-8 rounded-lg text-slate-400 hover:text-rose-500 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                        <p class="text-[11px] text-slate-400 mt-1">First line is used as the primary clinic contact.</p>
                                    </div>

                                    {{-- Location --}}
                                    <div class="min-w-0">
                                        <div class="flex items-center justify-between h-5 mb-2">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                                Facility Location / Wing
                                            </label>
                                        </div>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </div>
                                            <input type="text" name="location" x-model="addState.location" placeholder="e.g. Ground Floor, Wing B" 
                                                   class="w-full h-10 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1">Physical location within the municipal health complex.</p>
                                    </div>
                                </div>

                                {{-- Row 5: Predefined Services Chip Selector & Custom Adder --}}
                                <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/30 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block">
                                                Predefined Clinical Services
                                            </label>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Click chips to toggle offered medical services, or add custom services below.</p>
                                        </div>
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300" x-text="addState.services.length + ' Selected'"></span>
                                    </div>

                                    {{-- Search Filter --}}
                                    <div class="relative">
                                        <input type="text" x-model="addState.serviceSearch" placeholder="Filter predefined services..."
                                               class="w-full h-9 pl-9 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                    </div>

                                    {{-- Predefined Services Chips Grid --}}
                                    <div class="flex flex-wrap gap-1.5 max-h-40 overflow-y-auto p-1">
                                        @foreach(\App\Models\FacilityUnit::PREDEFINED_SERVICES as $svc)
                                            <button type="button" 
                                                    x-show="!addState.serviceSearch || '{{ strtolower($svc) }}'.includes(addState.serviceSearch.toLowerCase())"
                                                    @click="addState.toggleService('{{ $svc }}')"
                                                    :class="addState.services.includes('{{ $svc }}') ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-300'"
                                                    class="px-2.5 py-1 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                                <span x-show="addState.services.includes('{{ $svc }}')">✓</span>
                                                <span>{{ $svc }}</span>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Custom Service Adder --}}
                                    <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                        <input type="text" x-model="addState.customServiceInput" @keydown.enter.prevent="addState.addCustomService()" placeholder="Add a custom service not listed above..."
                                               class="flex-1 h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white">
                                        <button type="button" @click="addState.addCustomService()" 
                                                class="px-3.5 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold hover:bg-slate-900 transition">
                                            + Add
                                        </button>
                                    </div>

                                    {{-- Selected Services Tags Summary --}}
                                    <div x-show="addState.services.length > 0" class="pt-2">
                                        <div class="text-[11px] font-bold text-slate-400 mb-1.5">Selected Services List:</div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="(s, sIdx) in addState.services" :key="sIdx">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    <span x-text="s"></span>
                                                    <button type="button" @click="addState.removeService(sIdx)" class="text-emerald-600 hover:text-rose-500">×</button>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Row 6: Visibility & Sorting --}}
                                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <label class="flex items-center gap-3 cursor-pointer select-none">
                                        <input type="checkbox" name="is_active" value="1" x-model="addState.is_active" 
                                               class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition">
                                        <div>
                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Active Public Status</span>
                                            <span class="text-[11px] text-slate-400 block">Make this facility immediately visible on the public landing page</span>
                                        </div>
                                    </label>
                                    <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Display Order:</label>
                                        <input type="number" name="sort_order" x-model="addState.sort_order"
                                               class="w-20 h-10 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-center text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                    </div>
                                </div>

                            </div>

                            {{-- TAB 2: LIVE CARD PREVIEW --}}
                            <div x-show="addState.activeTab === 'preview'" class="p-6 sm:p-8 flex justify-center bg-slate-50 dark:bg-slate-950/60 overflow-y-auto flex-1">
                                <div class="w-full max-w-md bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-md overflow-hidden flex flex-col">
                                    {{-- Facility Image Banner with Category Badge --}}
                                    <div class="relative h-44 bg-slate-900 overflow-hidden">
                                        <template x-if="addState.imagePreview">
                                            <img :src="addState.imagePreview" class="w-full h-full object-cover" alt="Preview">
                                        </template>
                                        <template x-if="!addState.imagePreview">
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950 text-slate-500">
                                                <svg class="w-10 h-10 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span class="text-xs font-medium">Facility Photo Preview</span>
                                            </div>
                                        </template>
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
                                        <div class="absolute top-3 left-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-white/95 dark:bg-slate-900/95 text-emerald-800 dark:text-emerald-300 shadow-xs border border-white/20 backdrop-blur-xs" x-text="addState.category"></span>
                                        </div>
                                        {{-- Appointment Badge: Core constraint --}}
                                        <div class="absolute top-3 right-3">
                                            <template x-if="addState.name.toLowerCase().includes('main health')">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-600 text-white shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Appointments Available</span>
                                                </span>
                                            </template>
                                            <template x-if="!addState.name.toLowerCase().includes('main health')">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Walk-in Care Only</span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Facility Card Body --}}
                                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                        <div>
                                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="addState.name || 'Facility Name'"></h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed" x-text="addState.description || 'Facility clinical overview and medical mission statement...'"></p>
                                            
                                            {{-- Meta Information --}}
                                            <div class="mt-4 space-y-2 text-xs">
                                                <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium">
                                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span x-text="addState.computedHoursSummary()"></span>
                                                </div>
                                                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span x-text="addState.contacts[0] ? addState.contacts[0].number : '(046) 414-XXXX'"></span>
                                                </div>
                                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span x-text="addState.location || 'RHU Ground Floor'"></span>
                                                </div>
                                            </div>

                                            {{-- Services Chips Preview --}}
                                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center justify-between">
                                                    <span>Services</span>
                                                    <span x-text="addState.services.length + ' offered'"></span>
                                                </div>
                                                <div class="flex flex-wrap gap-1">
                                                    <template x-for="(s, idx) in addState.services.slice(0, 4)" :key="idx">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300" x-text="s"></span>
                                                    </template>
                                                    <template x-if="addState.services.length > 4">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300" x-text="'+' + (addState.services.length - 4) + ' more'"></span>
                                                    </template>
                                                    <template x-if="addState.services.length === 0">
                                                        <span class="text-xs text-slate-400 italic">No services selected yet</span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Action Button Preview (Strict booking constraint visual) --}}
                                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60">
                                            <template x-if="addState.name.toLowerCase().includes('main health')">
                                                <div class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-emerald-600 text-white text-center flex items-center justify-center gap-1.5 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span>Book Online Appointment</span>
                                                </div>
                                            </template>
                                            <template x-if="!addState.name.toLowerCase().includes('main health')">
                                                <div class="space-y-1.5">
                                                    <div class="w-full py-2.5 px-4 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-center flex items-center justify-center gap-1.5 bg-slate-50 dark:bg-slate-800/50">
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                        <span>Call Clinic / Walk-in Details</span>
                                                    </div>
                                                    <p class="text-[10px] text-amber-600 dark:text-amber-400 text-center font-medium">Walk-in care only — no online booking buttons are shown publicly.</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/90 flex items-center justify-end gap-3 shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-b-3xl">
                                <button type="button" @click="showAddModal = false" 
                                        class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition-all shadow-xs hover:shadow flex items-center gap-2 cursor-pointer active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Save Facility</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- ─────────────────────────────────────────────────────────────
                 EDIT FACILITY MODAL
            ───────────────────────────────────────────────────────────── --}}
            <template x-teleport="body">
                <div x-show="showEditModal" style="display: none;" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 sm:p-6 overflow-y-auto">
                    <div @click.away="showEditModal = false" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                         class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-3xl w-full my-8 border border-slate-200/90 dark:border-slate-800 flex flex-col max-h-[92vh]">
                        
                        {{-- Modal Header --}}
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/90 flex items-center justify-between shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-t-3xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100/80 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-white leading-tight">Edit Health Facility</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Modify clinic details, operating schedule, contacts, or public status.</p>
                                </div>
                            </div>
                            <button type="button" @click="showEditModal = false" 
                                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                    title="Close modal">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Mode Selector Sub-Tabs: Form Editor vs Live Card Preview --}}
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-2.5 bg-slate-50/70 dark:bg-slate-800/40">
                            <div class="flex items-center gap-1.5 p-1 bg-slate-200/70 dark:bg-slate-700/60 rounded-xl">
                                <button type="button" @click="editState.activeTab = 'editor'" 
                                        :class="editState.activeTab === 'editor' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Form Editor</span>
                                </button>
                                <button type="button" @click="editState.activeTab = 'preview'" 
                                        :class="editState.activeTab === 'preview' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                                        class="px-3 py-1.5 text-xs rounded-lg transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Live Card Preview</span>
                                </button>
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline" x-text="editState.activeTab === 'preview' ? 'Card preview mirrors public /units directory' : 'Edit details and structured information'"></span>
                        </div>

                        {{-- Modal Body Form --}}
                        <form :action="'{{ url('admin/facilities') }}/' + editState.slug" method="POST" enctype="multipart/form-data" 
                              class="flex flex-col flex-1 overflow-hidden" 
                              @submit="if (!editState.validateTimes()) { $event.preventDefault(); return false; }">
                            @csrf
                            @method('PUT')

                            {{-- Hidden Structured Hours, Contacts, Services Payload --}}
                            <input type="hidden" name="operating_hours" :value="editState.computedHoursSummary()">
                            <input type="hidden" name="operating_hours_structured[mode]" :value="editState.hoursMode">
                            <input type="hidden" name="operating_hours_structured[is_24h]" :value="editState.is24h ? 1 : 0">
                            <input type="hidden" name="operating_hours_structured[open_time]" :value="editState.openTime">
                            <input type="hidden" name="operating_hours_structured[close_time]" :value="editState.closeTime">
                            <input type="hidden" name="operating_hours_structured[custom_text]" :value="editState.customText">

                            <input type="hidden" name="contact_number" :value="editState.contacts[0] ? editState.contacts[0].number : ''">
                            <template x-for="(c, cIdx) in editState.contacts" :key="cIdx">
                                <div>
                                    <input type="hidden" :name="'contacts_structured[' + cIdx + '][label]'" :value="c.label">
                                    <input type="hidden" :name="'contacts_structured[' + cIdx + '][number]'" :value="c.number">
                                </div>
                            </template>

                            <template x-for="(s, sIdx) in editState.services" :key="sIdx">
                                <input type="hidden" :name="'services_offered[' + sIdx + ']'" :value="s">
                            </template>

                            {{-- TAB 1: FORM EDITOR --}}
                            <div x-show="editState.activeTab === 'editor'" class="p-6 sm:p-7 space-y-6 overflow-y-auto flex-1">
                                
                                {{-- Facility Photo Upload / Replace with Live Preview --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Facility Photo / Banner Image
                                    </label>
                                    <div class="flex flex-col sm:flex-row items-center gap-4 p-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 hover:border-emerald-500/50 transition-colors">
                                        <div class="relative w-32 h-20 sm:w-36 sm:h-24 rounded-xl overflow-hidden bg-slate-900 shrink-0 border border-slate-200 dark:border-slate-700 shadow-2xs flex items-center justify-center">
                                            <template x-if="editState.imagePreview">
                                                <img :src="editState.imagePreview" class="w-full h-full object-cover" alt="New Preview">
                                            </template>
                                            <template x-if="!editState.imagePreview && editState.image_url">
                                                <img :src="editState.image_url" class="w-full h-full object-cover" alt="Current Photo">
                                            </template>
                                            <template x-if="!editState.imagePreview && !editState.image_url">
                                                <div class="flex flex-col items-center justify-center text-slate-400 p-2 text-center">
                                                    <svg class="w-6 h-6 mb-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[10px] font-medium">Default image</span>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="flex-1 text-center sm:text-left space-y-1.5">
                                            <div class="flex items-center justify-center sm:justify-start gap-2">
                                                <label class="cursor-pointer px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-2xs transition-all inline-flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    <span x-text="editState.imagePreview ? 'Change Selection' : 'Replace Photo'">Replace Photo</span>
                                                    <input type="file" name="image" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden"
                                                           @change="const file = $event.target.files[0]; if (file) { if (window.SecureImageValidator) { window.SecureImageValidator.validateFile(file).then(res => { if (!res.valid) { editState.imageError = res.message; editState.imagePreview = null; $event.target.value = ''; } else { editState.imageError = null; editState.imagePreview = URL.createObjectURL(file); } }); } else { editState.imagePreview = URL.createObjectURL(file); } }">
                                                </label>
                                                <button type="button" x-show="editState.imagePreview" 
                                                        @click="editState.imagePreview = null; editState.imageError = null; $el.closest('form').querySelector('input[name=image]').value = ''" 
                                                        class="px-2.5 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400">
                                                    Revert
                                                </button>
                                            </div>
                                            <template x-if="editState.imageError">
                                                <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5" role="alert">
                                                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span x-text="editState.imageError"></span>
                                                </p>
                                            </template>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                                Upload a new photo to replace the current picture. Recommended: 16:9 ratio (JPG, PNG, WebP up to 5MB).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Row 1: Name and Category --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                            Facility Name <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <input type="text" name="name" x-model="editState.name" required 
                                                   class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1">Official unit or clinical department name.</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                            Clinical Category / Discipline <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative z-30" x-data="{ openCategory: false }" @click.outside="openCategory = false" @keydown.escape.window="openCategory = false">
                                            {{-- Hidden Form Input for Validation & Submission --}}
                                            <input type="hidden" name="category" :value="editState.category" required>

                                            {{-- Styled Trigger Button --}}
                                            <button type="button" 
                                                    @click="openCategory = !openCategory"
                                                    class="w-full h-11 px-3.5 rounded-xl border bg-slate-50/70 dark:bg-slate-800/60 text-left transition-all duration-200 cursor-pointer shadow-2xs flex items-center justify-between gap-2.5 focus:outline-none group"
                                                    :class="openCategory 
                                                        ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-white dark:bg-slate-800 shadow-sm' 
                                                        : 'border-slate-300 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-500/60'">
                                                
                                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                                    @foreach($facilityCategoriesMeta as $catKey => $cat)
                                                        <div x-show="editState.category === '{{ addslashes($catKey) }}'" class="flex items-center gap-2.5 min-w-0 flex-1">
                                                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border {{ $cat['iconBg'] }} transition-transform duration-200 group-hover:scale-105">
                                                                {!! $cat['iconSvg'] !!}
                                                            </div>
                                                            <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                                                {{ $cat['label'] }}
                                                            </span>
                                                        </div>
                                                    @endforeach

                                                    {{-- Fallback if custom/unknown category --}}
                                                    <div x-show="!{{ json_encode(array_keys($facilityCategoriesMeta)) }}.includes(editState.category)" class="flex items-center gap-2.5 min-w-0 flex-1">
                                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border bg-emerald-100/90 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                        </div>
                                                        <span class="text-sm font-semibold text-slate-900 dark:text-white truncate" x-text="editState.category || 'Select Category'"></span>
                                                    </div>
                                                </div>

                                                {{-- Rotating Chevron Arrow --}}
                                                <div class="shrink-0 flex items-center text-slate-400 dark:text-slate-400 transition-colors group-hover:text-slate-600 dark:group-hover:text-slate-200">
                                                    <svg class="w-4 h-4 transition-transform duration-200"
                                                         :class="openCategory ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </div>
                                            </button>

                                            {{-- Custom Elevated Dropdown Panel --}}
                                            <div x-show="openCategory" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 style="display: none;"
                                                 class="absolute left-0 right-0 z-50 mt-1.5 max-h-72 overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-2 custom-scrollbar backdrop-blur-xl space-y-1">
                                                
                                                <div class="px-2.5 py-1.5 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 mb-1">
                                                    <span>Select Clinical Discipline</span>
                                                </div>

                                                @foreach($facilityCategoriesMeta as $catKey => $cat)
                                                    <button type="button"
                                                            @click="editState.category = '{{ addslashes($catKey) }}'; openCategory = false"
                                                            class="w-full text-left p-2.5 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between gap-3 cursor-pointer transition-all duration-150 group border"
                                                            :class="editState.category === '{{ addslashes($catKey) }}'
                                                                ? 'bg-emerald-50/90 dark:bg-emerald-950/50 border-emerald-200/90 dark:border-emerald-800/60 shadow-2xs' 
                                                                : 'bg-transparent border-transparent hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:border-slate-200/60 dark:hover:border-slate-700/60'">
                                                        
                                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border {{ $cat['iconBg'] }} transition-transform duration-150 group-hover:scale-105">
                                                                {!! $cat['iconSvg'] !!}
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <span class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white block"
                                                                      :class="editState.category === '{{ addslashes($catKey) }}' ? 'text-emerald-700 dark:text-emerald-300' : ''">
                                                                    {{ $cat['label'] }}
                                                                </span>
                                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">{{ $cat['desc'] }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="shrink-0 pl-1" x-show="editState.category === '{{ addslashes($catKey) }}'">
                                                            <div class="w-6 h-6 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1">Primary healthcare discipline for this facility unit.</p>
                                    </div>
                                </div>

                                {{-- Row 2: Overview --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Clinical Overview & Description
                                    </label>
                                    <textarea name="description" x-model="editState.description" rows="3" 
                                              class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                    <p class="text-[11px] text-slate-400 mt-1">Displayed on the public unit details page.</p>
                                </div>

                                {{-- Row 3: Structured Operating Hours --}}
                                <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/30 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Operating Hours Configuration</span>
                                        </label>
                                        <span class="text-[11px] font-mono font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-2.5 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800/60" x-text="editState.computedHoursSummary()"></span>
                                    </div>

                                    {{-- Mode Selector --}}
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                        <button type="button" @click="editState.is24h = false; editState.hoursMode = 'weekdays'; editState.validateTimes()"
                                                :class="!editState.is24h && editState.hoursMode === 'weekdays' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            Weekdays (Mon-Fri)
                                        </button>
                                        <button type="button" @click="editState.is24h = false; editState.hoursMode = 'daily'; editState.validateTimes()"
                                                :class="!editState.is24h && editState.hoursMode === 'daily' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            Daily (Mon-Sun)
                                        </button>
                                        <button type="button" @click="editState.is24h = true; editState.hoursMode = 'weekdays'; editState.validateTimes()"
                                                :class="editState.is24h ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            24/7 Continuous
                                        </button>
                                        <button type="button" @click="editState.is24h = false; editState.hoursMode = 'custom'; editState.validateTimes()"
                                                :class="!editState.is24h && editState.hoursMode === 'custom' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                                                class="px-3 py-2 rounded-xl text-xs transition-all text-center">
                                            Custom Schedule
                                        </button>
                                    </div>

                                    {{-- Standard Time Pickers --}}
                                    <div x-show="!editState.is24h && editState.hoursMode !== 'custom'" class="space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Opens At</label>
                                                <input type="time" x-model="editState.openTime" @input="editState.validateTimes()"
                                                       class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                            </div>
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Closes At</label>
                                                <input type="time" x-model="editState.closeTime" @input="editState.validateTimes()"
                                                       class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                            </div>
                                        </div>
                                        <template x-if="editState.timeError">
                                            <p class="text-xs text-rose-500 font-semibold" x-text="editState.timeError"></p>
                                        </template>

                                        {{-- Quick Shortcuts --}}
                                        <div class="flex items-center gap-2 pt-1">
                                            <span class="text-[11px] text-slate-400">Presets:</span>
                                            <button type="button" @click="editState.setPreset('08:00', '17:00')" class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300">8:00 AM - 5:00 PM</button>
                                            <button type="button" @click="editState.setPreset('07:00', '16:00')" class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300">7:00 AM - 4:00 PM</button>
                                            <button type="button" @click="editState.setPreset('08:00', '12:00')" class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300">Morning (8 AM - 12 PM)</button>
                                        </div>
                                    </div>

                                    {{-- 24/7 Notice --}}
                                    <div x-show="editState.is24h" class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300">
                                        ✓ Facility operates 24 hours a day, 7 days a week (continuous clinical/maternal emergency care).
                                    </div>

                                    {{-- Custom Schedule Input --}}
                                    <div x-show="!editState.is24h && editState.hoursMode === 'custom'">
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Custom Operating Schedule Description</label>
                                        <input type="text" x-model="editState.customText" placeholder="e.g. Mon, Wed, Fri | 8:00 AM - 2:00 PM"
                                               class="w-full h-10 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                    </div>
                                </div>

                                {{-- Row 4: Structured Contacts & Location --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                    {{-- Multi-Contact Repeater --}}
                                    <div class="space-y-2.5 min-w-0">
                                        <div class="flex items-center justify-between h-5 mb-2">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                                Contact Numbers
                                            </label>
                                            <button type="button" @click="editState.addContact()" 
                                                    class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                                + Add Line
                                            </button>
                                        </div>

                                        <template x-for="(contact, cIdx) in editState.contacts" :key="cIdx">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="relative shrink-0 w-32 sm:w-36" x-data="{ openLabel: false }" @click.outside="openLabel = false" @keydown.escape.window="openLabel = false">
                                                    {{-- Styled Trigger Button --}}
                                                    <button type="button" 
                                                            @click="openLabel = !openLabel"
                                                            class="w-full h-10 px-2.5 rounded-xl border bg-slate-50/70 dark:bg-slate-800/60 text-left transition-all duration-200 cursor-pointer shadow-2xs flex items-center justify-between gap-1.5 focus:outline-none group"
                                                            :class="openLabel 
                                                                ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-white dark:bg-slate-800 shadow-sm' 
                                                                : 'border-slate-300 dark:border-slate-700 hover:border-emerald-400 dark:hover:border-emerald-500/60'">
                                                        
                                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                                            @foreach($contactLabelsMeta as $lKey => $lbl)
                                                                <div x-show="contact.label === '{{ addslashes($lKey) }}'" class="flex items-center gap-1.5 min-w-0 flex-1">
                                                                    <div class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 border {{ $lbl['iconBg'] }}">
                                                                        {!! $lbl['iconSvg'] !!}
                                                                    </div>
                                                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                                                                        {{ $lbl['label'] }}
                                                                    </span>
                                                                </div>
                                                            @endforeach

                                                            {{-- Fallback for custom/unmatched label --}}
                                                            <div x-show="!{{ json_encode(array_keys($contactLabelsMeta)) }}.includes(contact.label)" class="flex items-center gap-1.5 min-w-0 flex-1">
                                                                <div class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 border bg-emerald-100/90 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                                </div>
                                                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" x-text="contact.label || 'Select Line'"></span>
                                                            </div>
                                                        </div>

                                                        {{-- Chevron Arrow --}}
                                                        <div class="shrink-0 flex items-center text-slate-400 dark:text-slate-400">
                                                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                                                 :class="openLabel ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </div>
                                                    </button>

                                                    {{-- Floating Dropdown Menu --}}
                                                    <div x-show="openLabel" 
                                                         x-transition:enter="transition ease-out duration-150"
                                                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                         x-transition:leave="transition ease-in duration-100"
                                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                         style="display: none;"
                                                         class="absolute left-0 z-50 mt-1.5 w-52 sm:w-56 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700 shadow-2xl shadow-slate-900/20 p-1.5 custom-scrollbar backdrop-blur-xl space-y-1">
                                                        
                                                        <div class="px-2 py-1 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 mb-0.5">
                                                            <span>Contact Line Type</span>
                                                        </div>

                                                        @foreach($contactLabelsMeta as $lKey => $lbl)
                                                            <button type="button"
                                                                    @click="contact.label = '{{ addslashes($lKey) }}'; openLabel = false"
                                                                    class="w-full text-left p-2 rounded-xl text-xs font-medium flex items-center justify-between gap-2.5 cursor-pointer transition-all duration-150 group border"
                                                                    :class="contact.label === '{{ addslashes($lKey) }}'
                                                                        ? 'bg-emerald-50/90 dark:bg-emerald-950/50 border-emerald-200/90 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-200 shadow-2xs' 
                                                                        : 'bg-transparent border-transparent hover:bg-slate-100/80 dark:hover:bg-slate-800/70 hover:border-slate-200/60 dark:hover:border-slate-700/60 text-slate-700 dark:text-slate-300'">
                                                                
                                                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 border {{ $lbl['iconBg'] }} transition-transform duration-150 group-hover:scale-105">
                                                                        {!! $lbl['iconSvg'] !!}
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <span class="text-xs font-bold block"
                                                                              :class="contact.label === '{{ addslashes($lKey) }}' ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-800 dark:text-white'">
                                                                            {{ $lbl['label'] }}
                                                                        </span>
                                                                        <p class="text-[10px] text-slate-400 dark:text-slate-400 truncate">{{ $lbl['desc'] }}</p>
                                                                    </div>
                                                                </div>

                                                                <div class="shrink-0" x-show="contact.label === '{{ addslashes($lKey) }}'">
                                                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                    </svg>
                                                                </div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <input type="text" :value="contact.number" @input="editState.formatPhone($event, cIdx)" placeholder="09XX XXX XXXX or (046) 414-XXXX"
                                                       class="min-w-0 flex-1 h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                                <button type="button" x-show="editState.contacts.length > 1" @click="editState.removeContact(cIdx)" 
                                                        class="shrink-0 w-8 h-8 rounded-lg text-slate-400 hover:text-rose-500 flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                        <p class="text-[11px] text-slate-400 mt-1">First line is used as the primary clinic contact.</p>
                                    </div>

                                    {{-- Location --}}
                                    <div class="min-w-0">
                                        <div class="flex items-center justify-between h-5 mb-2">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                                Facility Location / Wing
                                            </label>
                                        </div>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </div>
                                            <input type="text" name="location" x-model="editState.location" placeholder="e.g. Ground Floor, Wing B" 
                                                   class="w-full h-10 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1">Physical location within the municipal health complex.</p>
                                    </div>
                                </div>

                                {{-- Row 5: Predefined Services Chip Selector & Custom Adder --}}
                                <div class="p-4 sm:p-5 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/30 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block">
                                                Predefined Clinical Services
                                            </label>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Click chips to toggle offered medical services, or add custom services below.</p>
                                        </div>
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300" x-text="editState.services.length + ' Selected'"></span>
                                    </div>

                                    {{-- Search Filter --}}
                                    <div class="relative">
                                        <input type="text" x-model="editState.serviceSearch" placeholder="Filter predefined services..."
                                               class="w-full h-9 pl-9 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                    </div>

                                    {{-- Predefined Services Chips Grid --}}
                                    <div class="flex flex-wrap gap-1.5 max-h-40 overflow-y-auto p-1">
                                        @foreach(\App\Models\FacilityUnit::PREDEFINED_SERVICES as $svc)
                                            <button type="button" 
                                                    x-show="!editState.serviceSearch || '{{ strtolower($svc) }}'.includes(editState.serviceSearch.toLowerCase())"
                                                    @click="editState.toggleService('{{ $svc }}')"
                                                    :class="editState.services.includes('{{ $svc }}') ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-300'"
                                                    class="px-2.5 py-1 rounded-lg text-xs transition-all flex items-center gap-1.5 cursor-pointer">
                                                <span x-show="editState.services.includes('{{ $svc }}')">✓</span>
                                                <span>{{ $svc }}</span>
                                            </button>
                                        @endforeach
                                    </div>

                                    {{-- Custom Service Adder --}}
                                    <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                        <input type="text" x-model="editState.customServiceInput" @keydown.enter.prevent="editState.addCustomService()" placeholder="Add a custom service not listed above..."
                                               class="flex-1 h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white">
                                        <button type="button" @click="editState.addCustomService()" 
                                                class="px-3.5 py-2 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold hover:bg-slate-900 transition">
                                            + Add
                                        </button>
                                    </div>

                                    {{-- Selected Services Tags Summary --}}
                                    <div x-show="editState.services.length > 0" class="pt-2">
                                        <div class="text-[11px] font-bold text-slate-400 mb-1.5">Selected Services List:</div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="(s, sIdx) in editState.services" :key="sIdx">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                    <span x-text="s"></span>
                                                    <button type="button" @click="editState.removeService(sIdx)" class="text-emerald-600 hover:text-rose-500">×</button>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- Row 6: Visibility & Sorting (Protected for main-health-center) --}}
                                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <template x-if="editState.slug === 'main-health-center'">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" checked disabled 
                                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-emerald-600 opacity-60 cursor-not-allowed">
                                                <input type="hidden" name="is_active" value="1">
                                                <div>
                                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Active Status (Permanently Active)</span>
                                                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium block">Core municipal healthcare facility cannot be hidden or deactivated.</span>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="editState.slug !== 'main-health-center'">
                                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                                <input type="checkbox" name="is_active" value="1" x-model="editState.is_active" 
                                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition">
                                                <div>
                                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Active Status</span>
                                                    <span class="text-[11px] text-slate-400 block">Make this facility visible on the public landing page</span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>

                                    <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Display Order:</label>
                                        <input type="number" name="sort_order" x-model="editState.sort_order" 
                                               class="w-20 h-10 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-center text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                    </div>
                                </div>

                            </div>

                            {{-- TAB 2: LIVE CARD PREVIEW --}}
                            <div x-show="editState.activeTab === 'preview'" class="p-6 sm:p-8 flex justify-center bg-slate-50 dark:bg-slate-950/60 overflow-y-auto flex-1">
                                <div class="w-full max-w-md bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-md overflow-hidden flex flex-col">
                                    {{-- Facility Image Banner with Category Badge --}}
                                    <div class="relative h-44 bg-slate-900 overflow-hidden">
                                        <template x-if="editState.imagePreview">
                                            <img :src="editState.imagePreview" class="w-full h-full object-cover" alt="Preview">
                                        </template>
                                        <template x-if="!editState.imagePreview && editState.image_url">
                                            <img :src="editState.image_url" class="w-full h-full object-cover" alt="Current Photo">
                                        </template>
                                        <template x-if="!editState.imagePreview && !editState.image_url">
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950 text-slate-500">
                                                <svg class="w-10 h-10 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span class="text-xs font-medium">Facility Photo Preview</span>
                                            </div>
                                        </template>
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent"></div>
                                        <div class="absolute top-3 left-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase tracking-wider bg-white/95 dark:bg-slate-900/95 text-emerald-800 dark:text-emerald-300 shadow-xs border border-white/20 backdrop-blur-xs" x-text="editState.category"></span>
                                        </div>
                                        {{-- Appointment Badge: Core constraint --}}
                                        <div class="absolute top-3 right-3">
                                            <template x-if="editState.slug === 'main-health-center'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-600 text-white shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Appointments Available</span>
                                                </span>
                                            </template>
                                            <template x-if="editState.slug !== 'main-health-center'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500 text-white shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Walk-in Care Only</span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Facility Card Body --}}
                                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                        <div>
                                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white" x-text="editState.name || 'Facility Name'"></h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed" x-text="editState.description || 'Facility clinical overview and medical mission statement...'"></p>
                                            
                                            {{-- Meta Information --}}
                                            <div class="mt-4 space-y-2 text-xs">
                                                <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 font-medium">
                                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span x-text="editState.computedHoursSummary()"></span>
                                                </div>
                                                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span x-text="editState.contacts[0] ? editState.contacts[0].number : '(046) 414-XXXX'"></span>
                                                </div>
                                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    <span x-text="editState.location || 'RHU Ground Floor'"></span>
                                                </div>
                                            </div>

                                            {{-- Services Chips Preview --}}
                                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                                                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center justify-between">
                                                    <span>Services</span>
                                                    <span x-text="editState.services.length + ' offered'"></span>
                                                </div>
                                                <div class="flex flex-wrap gap-1">
                                                    <template x-for="(s, idx) in editState.services.slice(0, 4)" :key="idx">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300" x-text="s"></span>
                                                    </template>
                                                    <template x-if="editState.services.length > 4">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300" x-text="'+' + (editState.services.length - 4) + ' more'"></span>
                                                    </template>
                                                    <template x-if="editState.services.length === 0">
                                                        <span class="text-xs text-slate-400 italic">No services selected yet</span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Action Button Preview (Strict booking constraint visual) --}}
                                        <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60">
                                            <template x-if="editState.slug === 'main-health-center'">
                                                <div class="w-full py-2.5 px-4 rounded-xl text-xs font-bold bg-emerald-600 text-white text-center flex items-center justify-center gap-1.5 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span>Book Online Appointment</span>
                                                </div>
                                            </template>
                                            <template x-if="editState.slug !== 'main-health-center'">
                                                <div class="space-y-1.5">
                                                    <div class="w-full py-2.5 px-4 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-center flex items-center justify-center gap-1.5 bg-slate-50 dark:bg-slate-800/50">
                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                        <span>Call Clinic / Walk-in Details</span>
                                                    </div>
                                                    <p class="text-[10px] text-amber-600 dark:text-amber-400 text-center font-medium">Walk-in care only — no online booking buttons are shown publicly.</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/90 flex items-center justify-between gap-3 shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-b-3xl">
                                <div>
                                    <template x-if="editState.slug !== 'main-health-center'">
                                        <button type="button" @click="showDeleteModal = true" 
                                                class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-transparent hover:border-rose-200 dark:hover:border-rose-800/50 transition-all flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Delete Facility</span>
                                        </button>
                                    </template>
                                    <template x-if="editState.slug === 'main-health-center'">
                                        <span class="text-[11px] font-semibold text-slate-400 italic">Protected Main Facility</span>
                                    </template>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <button type="button" @click="showEditModal = false" 
                                            class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition-all shadow-xs hover:shadow flex items-center gap-2 cursor-pointer active:scale-95">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Update Facility</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form x-ref="deleteForm" :action="'{{ url('admin/facilities') }}/' + editState.slug" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </template>

            {{-- ─────────────────────────────────────────────────────────────
                 DELETE FACILITY CONFIRMATION MODAL
            ───────────────────────────────────────────────────────────── --}}
            <template x-teleport="body">
                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm px-4">
                    <div @click.away="showDeleteModal = false" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 scale-100" 
                         x-transition:leave-end="opacity-0 scale-95" 
                         class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800">
                        <div class="flex items-center gap-3.5 mb-3">
                            <div class="w-11 h-11 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Delete Facility?</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Remove clinical unit from public directory.</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mt-2.5">
                            Are you sure you want to delete <span class="font-bold text-slate-900 dark:text-white" x-text="editState.name"></span>? This will permanently remove the facility from the public healthcare portal.
                        </p>
                        <div class="mt-6 flex justify-end gap-2.5">
                            <button type="button" @click="showDeleteModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                            <button type="button" @click="showDeleteModal = false; $refs.deleteForm.submit();" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition shadow-sm cursor-pointer active:scale-95 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Yes, Delete Facility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 4: MISSION, VISION & PUBLIC SERVICE CHARTER
        ───────────────────────────────────────────────────────────── --}}
        @php
            $aboutHeadline = old('settings.mission_headline', $settings['about']['mission_headline']->value ?? 'Advancing Municipal Health with Integrity & Care');
            $aboutSubheadline = old('settings.mission_subheadline', $settings['about']['mission_subheadline']->value ?? 'The primary public healthcare authority of the Municipality of Silang, Cavite — dedicated to providing responsive, equitable, and professional medical services to every constituent across all 64 barangays.');

            $rawMetrics = null;
            if(isset($settings['about']['about_metrics'])) {
                $rawMetrics = json_decode($settings['about']['about_metrics']->value, true);
            }
            if(empty($rawMetrics) || !is_array($rawMetrics)) {
                $rawMetrics = [
                    ['value' => '5 Units', 'title' => 'Specialized Facilities', 'subtitle' => 'Primary care, maternal, dental & labs'],
                    ['value' => '24/7 Care', 'title' => 'Maternal Birthing', 'subtitle' => 'Continuous newborn & maternal service'],
                    ['value' => '100k+', 'title' => 'Citizens Served', 'subtitle' => 'Across all Silang barangays'],
                    ['value' => '100% Free', 'title' => 'Primary Triage', 'subtitle' => 'No user fee for basic consultation'],
                ];
            }

            $missionTitle = old('settings.mission_title', $settings['about']['mission_title']->value ?? 'Delivering Responsive, Equitable Public Healthcare');
            $missionStatement = old('settings.mission_statement', $settings['about']['mission_statement']->value ?? 'To provide responsive, equitable, and quality primary healthcare services to all citizens of Silang. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs.');
            $missionCaption = old('settings.mission_image_caption', $settings['about']['mission_image_caption']->value ?? 'RHU Silang Medical Consultation Wing');

            $rawMissionPoints = null;
            if(isset($settings['about']['mission_points'])) {
                $rawMissionPoints = json_decode($settings['about']['mission_points']->value, true);
            }
            if(empty($rawMissionPoints) || !is_array($rawMissionPoints)) {
                $rawMissionPoints = [
                    'Equitable access across 64 Silang barangays',
                    'Digital queue & clinical record tracking',
                    'Continuous inventory & pharmacy transparency',
                    '24/7 dedicated maternal emergency care',
                ];
            }

            $missionImgSetting = $settings['about']['mission_image']->value ?? null;
            $currentMissionImageUrl = $missionImgSetting 
                ? (\Illuminate\Support\Str::startsWith($missionImgSetting, ['uploads/', 'http']) ? asset($missionImgSetting) : asset('uploads/' . $missionImgSetting))
                : asset('assets/images/rhu-consultation.jpg');

            $visionHeadline = old('settings.vision_headline', $settings['about']['vision_headline']->value ?? 'A healthy, resilient, and empowered Silang served by modern healthcare integrity.');
            $visionStatement = old('settings.vision_statement', $settings['about']['vision_statement']->value ?? 'A healthy, resilient, and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable right for every citizen.');

            $rawVisionPillars = null;
            if(isset($settings['about']['vision_pillars'])) {
                $rawVisionPillars = json_decode($settings['about']['vision_pillars']->value, true);
            }
            if(empty($rawVisionPillars) || !is_array($rawVisionPillars)) {
                $rawVisionPillars = [
                    ['title' => 'Universal Care', 'description' => 'A reliable, dignified right for every family in Silang'],
                    ['title' => 'Technological Leap', 'description' => 'Integrated health information MIS and online queues'],
                    ['title' => 'Medical Integrity', 'description' => 'Ethical diagnostics, certified medicine dispensing'],
                ];
            }

            $charterTitle = old('settings.charter_title', $settings['about']['charter_title']->value ?? 'Public Service Charter');
            $charterSubtitle = old('settings.charter_subtitle', $settings['about']['charter_subtitle']->value ?? 'Guiding Principles');

            $rawPrinciples = null;
            if(isset($settings['about']['guiding_principles'])) {
                $rawPrinciples = json_decode($settings['about']['guiding_principles']->value, true);
            }
            if(empty($rawPrinciples) || !is_array($rawPrinciples)) {
                $rawPrinciples = [
                    ['number' => '01', 'title' => 'Compassionate Care', 'description' => 'Treating every patient with dignity, empathy, and dedicated professional attention.'],
                    ['number' => '02', 'title' => 'Digital Innovation', 'description' => 'Streamlining triage, clinical schedules, and patient records with modern MIS solutions.'],
                    ['number' => '03', 'title' => 'Transparency & Ethics', 'description' => 'Upholding absolute accountability in pharmacy inventories and healthcare governance.'],
                    ['number' => '04', 'title' => 'Universal Inclusivity', 'description' => 'Guaranteeing barrier-free medical access for all constituents regardless of status.']
                ];
            }
        @endphp

        <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 activeView: 'editor',
                 missionHeadline: {{ json_encode($aboutHeadline) }},
                 missionSubheadline: {{ json_encode($aboutSubheadline) }},
                 metrics: {{ json_encode($rawMetrics) }},
                 missionTitle: {{ json_encode($missionTitle) }},
                 missionStatement: {{ json_encode($missionStatement) }},
                 missionCaption: {{ json_encode($missionCaption) }},
                 missionImgUrl: {{ json_encode($currentMissionImageUrl) }},
                 missionImgPreview: null,
                 missionImgError: null,
                 missionPoints: {{ json_encode($rawMissionPoints) }},
                 newPointInput: '',
                 visionHeadline: {{ json_encode($visionHeadline) }},
                 visionStatement: {{ json_encode($visionStatement) }},
                 visionPillars: {{ json_encode($rawVisionPillars) }},
                 charterTitle: {{ json_encode($charterTitle) }},
                 charterSubtitle: {{ json_encode($charterSubtitle) }},
                 initPrinciples: {{ json_encode($rawPrinciples) }},
                 principles: {{ json_encode($rawPrinciples) }},
                 principleErrors: [],
                 addPoint() {
                     const val = this.newPointInput.trim();
                     if (val && !this.missionPoints.includes(val)) {
                         this.missionPoints.push(val);
                         this.newPointInput = '';
                     }
                 },
                 removePoint(index) {
                     this.missionPoints.splice(index, 1);
                 },
                 addPrinciple() {
                     const nextNum = String(this.principles.length + 1).padStart(2, '0');
                     this.principles.push({ number: nextNum, title: '', description: '' });
                 },
                 removePrinciple(index) {
                     this.principles.splice(index, 1);
                     this.principleErrors.splice(index, 1);
                     if (this.principles.length === 0) {
                         this.addPrinciple();
                     }
                 },
                 resetPrinciples() {
                     this.principles = JSON.parse(JSON.stringify(this.initPrinciples));
                     this.principleErrors = [];
                 },
                 validatePrinciples() {
                     this.principleErrors = [];
                     let valid = true;
                     this.principles.forEach((p, i) => {
                         let errs = { title: false, description: false };
                         if (!p.title || p.title.trim() === '') { errs.title = true; valid = false; }
                         if (!p.description || p.description.trim() === '') { errs.description = true; valid = false; }
                         this.principleErrors.push(errs);
                     });
                     return valid;
                 }
             }"
             @content-reset.window="resetPrinciples()"
             @validate-principles.window="validatePrinciples()">

            {{-- Top Mode Switcher: Form Editor vs Live About Page Preview --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-3 sm:p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl self-start sm:self-auto">
                    <button type="button" @click="activeView = 'editor'" 
                            :class="activeView === 'editor' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                            class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Content Editor</span>
                    </button>
                    <button type="button" @click="activeView = 'preview'" 
                            :class="activeView === 'preview' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                            class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Live Page Preview</span>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400">All updates reflect immediately on the public <a href="{{ route('about') }}" target="_blank" class="text-emerald-600 font-bold hover:underline">/about</a> page.</span>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 VIEW 1: FORM EDITOR
            ───────────────────────────────────────────────────────── --}}
            <div x-show="activeView === 'editor'" class="space-y-6">

                {{-- Section 1: Institutional Header & Headline --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Institutional Header & Civic Mandate</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Top-level institutional headline and mission narrative on the About Us page.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            Header
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Institutional Headline <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="settings[mission_headline]" x-model="missionHeadline" required placeholder="e.g. Advancing Municipal Health with Integrity & Care"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                            <p class="text-[11px] text-slate-400 mt-1">Rendered as the primary H1 title on the public /about page.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Mandate Subheadline Description
                            </label>
                            <textarea name="settings[mission_subheadline]" x-model="missionSubheadline" rows="3" placeholder="Provide municipal health mandate context..."
                                      class="w-full p-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                            <p class="text-[11px] text-slate-400 mt-1">Institutional overview positioned alongside the headline.</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Institutional Metric Strip (4 Cards) --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Institutional Metric Cards</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Four dynamic statistical highlights displayed in the prominent metric strip.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            Metrics Strip
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="(m, mIdx) in metrics" :key="mIdx">
                            <div class="p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400" x-text="'Metric ' + (mIdx + 1)"></span>
                                    <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-xs" x-text="mIdx + 1"></span>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Statistic / Value</label>
                                    <input type="text" :name="`about_metrics[${mIdx}][value]`" x-model="m.value" placeholder="e.g. 5 Units"
                                           class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-extrabold text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Metric Title</label>
                                    <input type="text" :name="`about_metrics[${mIdx}][title]`" x-model="m.title" placeholder="e.g. Specialized Facilities"
                                           class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Subtitle / Note</label>
                                    <input type="text" :name="`about_metrics[${mIdx}][subtitle]`" x-model="m.subtitle" placeholder="e.g. Primary care, maternal, dental & labs"
                                           class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Section 3: Healthcare Mission & Documentary Photo --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Healthcare Mission Narrative & Photography</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Day-to-day healthcare service mandate, documentary photo, and structured key points.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            Mission
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        {{-- Photo Upload & Caption Column --}}
                        <div class="lg:col-span-5 space-y-4">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Documentary Photography
                            </label>
                            
                            <div class="relative rounded-2xl overflow-hidden aspect-[16/11] bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-md flex items-center justify-center">
                                <template x-if="missionImgPreview">
                                    <img :src="missionImgPreview" class="w-full h-full object-cover" alt="New Mission Preview">
                                </template>
                                <template x-if="!missionImgPreview">
                                    <img :src="missionImgUrl" class="w-full h-full object-cover" alt="Current Mission Photo">
                                </template>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <label class="cursor-pointer px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-2xs transition-all inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span x-text="missionImgPreview ? 'Change Photo' : 'Replace Photo'">Replace Photo</span>
                                        <input type="file" name="mission_image_file" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden"
                                               @change="const file = $event.target.files[0]; if (file) { if (window.SecureImageValidator) { window.SecureImageValidator.validateFile(file).then(res => { if (!res.valid) { missionImgError = res.message; missionImgPreview = null; $event.target.value = ''; } else { missionImgError = null; missionImgPreview = URL.createObjectURL(file); } }); } else { missionImgPreview = URL.createObjectURL(file); } }">
                                    </label>
                                    <button type="button" x-show="missionImgPreview" 
                                            @click="missionImgPreview = null; missionImgError = null; $el.closest('.space-y-4').querySelector('input[type=file]').value = ''"
                                            class="px-2.5 py-2 text-xs font-semibold text-rose-600 hover:text-rose-700">
                                        Revert
                                    </button>
                                </div>
                                <template x-if="missionImgError">
                                    <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5 mt-1.5" role="alert">
                                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span x-text="missionImgError"></span>
                                    </p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Photo Caption</label>
                                <input type="text" name="settings[mission_image_caption]" x-model="missionCaption" placeholder="e.g. RHU Silang Medical Consultation Wing"
                                       class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold">
                            </div>
                        </div>

                        {{-- Mission Content & Key Points Column --}}
                        <div class="lg:col-span-7 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Mission Section Headline Title <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="settings[mission_title]" x-model="missionTitle" required placeholder="e.g. Delivering Responsive, Equitable Public Healthcare"
                                       class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Official Mission Statement <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="settings[mission_statement]" x-model="missionStatement" rows="4" required placeholder="Enter the official municipal mission statement..."
                                          class="w-full p-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                            </div>

                            {{-- Mission Key Points Repeater --}}
                            <div class="p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        Mission Focus Points
                                    </label>
                                    <span class="text-[11px] text-slate-400" x-text="missionPoints.length + ' points configured'"></span>
                                </div>

                                <div class="space-y-2">
                                    <template x-for="(point, pIdx) in missionPoints" :key="pIdx">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <input type="text" :name="`mission_points[${pIdx}]`" x-model="missionPoints[pIdx]" 
                                                   class="flex-1 h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white font-medium">
                                            <button type="button" @click="removePoint(pIdx)" class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-500 flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex items-center gap-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                                    <input type="text" x-model="newPointInput" @keydown.enter.prevent="addPoint()" placeholder="Add a new mission focal point..."
                                           class="flex-1 h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white">
                                    <button type="button" @click="addPoint()" class="px-3.5 py-1.5 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold hover:bg-slate-900">
                                        + Add Point
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Community Vision & Strategic Pillars --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/20 to-cyan-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Community Vision & Strategic Pillars</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Aspiration block, manifesto quote, and three foundational pillars.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800/60">
                            Vision
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Vision Headline Manifesto Quote <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="settings[vision_headline]" x-model="visionHeadline" required placeholder="e.g. A healthy, resilient, and empowered Silang..."
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Official Vision Statement <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="settings[vision_statement]" x-model="visionStatement" rows="4" required placeholder="Enter official vision statement..."
                                      class="w-full p-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
                        </div>

                        {{-- 3 Vision Pillars Grid --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Three Strategic Pillars
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <template x-for="(pillar, piIdx) in visionPillars" :key="piIdx">
                                    <div class="p-4 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400" x-text="'Pillar ' + (piIdx + 1)"></span>
                                            <span class="w-6 h-6 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 flex items-center justify-center font-bold text-xs" x-text="piIdx + 1"></span>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Pillar Title</label>
                                            <input type="text" :name="`vision_pillars[${piIdx}][title]`" x-model="pillar.title" placeholder="e.g. Universal Care"
                                                   class="w-full h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-bold">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase mb-1">Description</label>
                                            <textarea :name="`vision_pillars[${piIdx}][description]`" x-model="pillar.description" rows="2" placeholder="Brief explanation..."
                                                      class="w-full p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs leading-relaxed"></textarea>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 5: Public Service Charter — Guiding Principles --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Public Service Charter — Guiding Principles</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure ethical healthcare standards and numbered pillars featured on the public About Us page.</p>
                            </div>
                        </div>

                        <button type="button" @click="addPrinciple()"
                                class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto shrink-0 active:scale-95">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Principle</span>
                        </button>
                    </div>

                    {{-- Titles --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Charter Section Title</label>
                            <input type="text" name="settings[charter_title]" x-model="charterTitle" placeholder="Public Service Charter"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Charter Subtitle</label>
                            <input type="text" name="settings[charter_subtitle]" x-model="charterSubtitle" placeholder="Guiding Principles"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold">
                        </div>
                    </div>

                    {{-- Interactive Principles Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <template x-for="(principle, index) in principles" :key="index">
                            <div class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-4 relative shadow-2xs group hover:border-emerald-300 dark:hover:border-emerald-700/70 transition-all">
                                
                                {{-- Principle Card Header --}}
                                <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 flex items-center justify-center font-black text-xs font-mono shadow-2xs" x-text="principle.number || (index + 1)"></span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Charter Standard</span>
                                    </div>
                                    
                                    <button type="button" @click="removePrinciple(index)" 
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" 
                                            title="Remove Principle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>

                                {{-- Number Tag & Title Inputs --}}
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
                                    <div class="sm:col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Number Tag</label>
                                        <input type="text" :name="`guiding_principles[${index}][number]`" x-model="principle.number" placeholder="01"
                                               class="w-full h-11 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-bold text-center font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Principle Title <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="`guiding_principles[${index}][title]`" x-model="principle.title" placeholder="e.g. Compassionate Care" required
                                               :class="principleErrors[index] && principleErrors[index].title ? 'border-rose-400 dark:border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-300 dark:border-slate-700'"
                                               @input="if (principleErrors[index]) principleErrors[index].title = false"
                                               class="w-full h-11 px-3.5 rounded-xl border bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                        <p x-show="principleErrors[index] && principleErrors[index].title" class="text-[11px] text-rose-500 font-medium mt-1">Principle title is required.</p>
                                    </div>
                                </div>

                                {{-- Description Input --}}
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Charter Description <span class="text-rose-500">*</span></label>
                                    <textarea :name="`guiding_principles[${index}][description]`" x-model="principle.description" rows="2"
                                              placeholder="Explain the patient care standard..." required
                                              :class="principleErrors[index] && principleErrors[index].description ? 'border-rose-400 dark:border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-300 dark:border-slate-700'"
                                              @input="if (principleErrors[index]) principleErrors[index].description = false"
                                              class="w-full p-3.5 rounded-xl border bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                    <p x-show="principleErrors[index] && principleErrors[index].description" class="text-[11px] text-rose-500 font-medium mt-1">Charter description is required.</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            {{-- ─────────────────────────────────────────────────────────
                 VIEW 2: LIVE ABOUT PAGE PREVIEW
            ───────────────────────────────────────────────────────── --}}
            <div x-show="activeView === 'preview'" class="bg-slate-100 dark:bg-slate-950 p-6 sm:p-10 rounded-3xl border border-slate-200/90 dark:border-slate-800 space-y-12 overflow-hidden shadow-inner">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-600">Real-Time Public Page Rendering</span>
                    <span class="text-[11px] text-slate-400">Previewing live unsaved changes</span>
                </div>

                {{-- Header Preview --}}
                <div class="max-w-6xl mx-auto space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-md border border-emerald-300/50">
                        Municipality of Silang
                    </span>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-end">
                        <div class="lg:col-span-7">
                            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight" x-text="missionHeadline || 'Advancing Municipal Health with Integrity & Care'"></h1>
                        </div>
                        <div class="lg:col-span-5">
                            <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed" x-text="missionSubheadline"></p>
                        </div>
                    </div>
                </div>

                {{-- Metrics Strip Preview --}}
                <div class="max-w-6xl mx-auto">
                    <div class="bg-white dark:bg-slate-800/95 rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-xs overflow-hidden">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-slate-700">
                            <template x-for="(m, idx) in metrics" :key="idx">
                                <div class="p-6 flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-xs font-bold text-base" x-text="idx + 1"></div>
                                    <div>
                                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white" x-text="m.value"></div>
                                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mt-1" x-text="m.title"></div>
                                        <p class="text-[11px] text-slate-400 mt-0.5" x-text="m.subtitle"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Mission Section Preview --}}
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6">
                        <div class="bg-slate-100 dark:bg-slate-900 p-3 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-md">
                            <div class="relative rounded-xl overflow-hidden aspect-[16/11] bg-slate-900">
                                <img :src="missionImgPreview || missionImgUrl" class="w-full h-full object-cover" alt="Mission Preview">
                            </div>
                            <div class="pt-2 px-1 text-[11px] text-slate-500 font-medium" x-text="missionCaption"></div>
                        </div>
                    </div>
                    <div class="lg:col-span-6 space-y-4">
                        <div class="inline-flex items-center gap-2">
                            <span class="w-6 h-0.5 bg-emerald-600"></span>
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest">Our Mission</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight" x-text="missionTitle"></h2>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed" x-text="missionStatement"></p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                            <template x-for="(pt, idx) in missionPoints" :key="idx">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-5 h-5 rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-medium" x-text="pt"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Vision Section Preview --}}
                <div class="max-w-6xl mx-auto rounded-3xl bg-gradient-to-br from-emerald-900 via-emerald-950 to-slate-900 text-white p-8 sm:p-12 border border-emerald-800/50 shadow-xl space-y-6">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-0.5 bg-emerald-400"></span>
                        <span class="text-xs font-bold text-emerald-300 uppercase tracking-widest">Our Vision</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white" x-text="'&ldquo;' + visionHeadline + '&rdquo;'"></h2>
                    <p class="text-emerald-100/90 text-sm leading-relaxed max-w-3xl" x-text="visionStatement"></p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-6 border-t border-white/10">
                        <template x-for="(pillar, idx) in visionPillars" :key="idx">
                            <div class="bg-white/10 backdrop-blur-xs rounded-xl p-4 border border-white/10">
                                <div class="text-xs font-bold text-white" x-text="pillar.title"></div>
                                <div class="text-[11px] text-emerald-200/80 mt-1" x-text="pillar.description"></div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Charter Section Preview --}}
                <div class="max-w-6xl mx-auto space-y-6">
                    <div>
                        <span class="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-widest" x-text="charterTitle"></span>
                        <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mt-1" x-text="charterSubtitle"></h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="(principle, idx) in principles" :key="idx">
                            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs space-y-2">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-black text-xs font-mono" x-text="principle.number || (idx + 1)"></span>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white" x-text="principle.title"></h4>
                                <p class="text-xs text-slate-500 leading-relaxed" x-text="principle.description"></p>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB: ORGANIZATIONAL STRUCTURE & LEADERSHIP
        ───────────────────────────────────────────────────────────── --}}
        @php
            $orgKicker = old('settings.org_kicker', $settings['organization']['org_kicker']->value ?? 'Leadership & Governance');
            $orgTitle = old('settings.org_title', $settings['organization']['org_title']->value ?? 'Organizational Structure & Leadership');
            $orgSubtitle = old('settings.org_subtitle', $settings['organization']['org_subtitle']->value ?? 'The dedicated healthcare administrators, medical doctors, nurses, midwives, and diagnostic specialists of Rural Health Unit — Silang, Cavite.');

            $mhoBadge = old('settings.mho_badge', $settings['organization']['mho_badge']->value ?? 'Executive Head');
            $mhoSubbadge = old('settings.mho_subbadge', $settings['organization']['mho_subbadge']->value ?? 'Head of Agency');
            $mhoName = old('settings.mho_name', $settings['organization']['mho_name']->value ?? 'Jericho Joshua E. Palay, MD');
            $mhoTitle = old('settings.mho_title', $settings['organization']['mho_title']->value ?? 'Municipal Health Officer');
            $mhoOversightTitle = old('settings.mho_oversight_title', $settings['organization']['mho_oversight_title']->value ?? 'Executive Oversight:');
            $mhoOversightDesc = old('settings.mho_oversight_desc', $settings['organization']['mho_oversight_desc']->value ?? 'Clinical governance, health policy, and public healthcare across all 64 barangays.');
            $mhoImageVal = $settings['organization']['mho_image']->value ?? '';
            $currentMhoImageUrl = $mhoImageVal ? asset($mhoImageVal) : null;

            $rawMedOfficers = null;
            if(isset($settings['organization']['org_medical_officers'])) {
                $rawMedOfficers = json_decode($settings['organization']['org_medical_officers']->value, true);
            }
            if(empty($rawMedOfficers) || !is_array($rawMedOfficers)) {
                $rawMedOfficers = [
                    ['name' => 'Angel Casapao, MD', 'role' => 'Specialist I', 'initials' => 'AC'],
                    ['name' => 'Michelle Mae Brofas, MD', 'role' => 'Medical Officer III', 'initials' => 'MB'],
                    ['name' => 'Jebriel Allen Desacada, MD', 'role' => 'Medical Officer III', 'initials' => 'JD'],
                    ['name' => 'Junee Elleigh Oway, MD', 'role' => 'Medical Officer II', 'initials' => 'JO']
                ];
            }

            $rawDivisions = null;
            if(isset($settings['organization']['org_divisions'])) {
                $rawDivisions = json_decode($settings['organization']['org_divisions']->value, true);
            }
            if(empty($rawDivisions) || !is_array($rawDivisions)) {
                $rawDivisions = [
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
                ];
            }

            $rawMidwives = null;
            if(isset($settings['organization']['org_midwives'])) {
                $rawMidwives = json_decode($settings['organization']['org_midwives']->value, true);
            }
            if(empty($rawMidwives) || !is_array($rawMidwives)) {
                $rawMidwives = [
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
            }

            $rawAdmins = null;
            if(isset($settings['organization']['org_admins'])) {
                $rawAdmins = json_decode($settings['organization']['org_admins']->value, true);
            }
            if(empty($rawAdmins) || !is_array($rawAdmins)) {
                $rawAdmins = [
                    ['name' => 'Mark Anthony Sebastian', 'role' => 'Administrative Officer', 'initials' => 'MS'],
                    ['name' => 'Jacqueline Hapin', 'role' => 'Administrative Support', 'initials' => 'JH'],
                    ['name' => 'Apple Toledo', 'role' => 'Public Assistance & Records', 'initials' => 'AT']
                ];
            }

            // Normalize members in rawDivisions to array of strings
            foreach ($rawDivisions as &$divItem) {
                if (isset($divItem['units']) && is_array($divItem['units'])) {
                    foreach ($divItem['units'] as &$unitItem) {
                        if (isset($unitItem['members'])) {
                            if (is_string($unitItem['members'])) {
                                $unitItem['members'] = array_values(array_filter(array_map('trim', explode('•', $unitItem['members'])), fn($m) => $m !== ''));
                            } elseif (is_array($unitItem['members'])) {
                                $unitItem['members'] = array_values(array_filter(array_map('trim', $unitItem['members']), fn($m) => $m !== ''));
                            } else {
                                $unitItem['members'] = [];
                            }
                        } else {
                            $unitItem['members'] = [];
                        }
                    }
                }
            }
            unset($divItem, $unitItem);
        @endphp

        <div x-show="activeTab === 'organization'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 activeView: 'editor',
                 activeDivisionTab: 'primary',
                 previewExpandedDivision: null,
                 previewSearch: '',
                 orgKicker: {{ json_encode($orgKicker) }},
                 orgTitle: {{ json_encode($orgTitle) }},
                 orgSubtitle: {{ json_encode($orgSubtitle) }},
                 mhoBadge: {{ json_encode($mhoBadge) }},
                 mhoSubbadge: {{ json_encode($mhoSubbadge) }},
                 mhoName: {{ json_encode($mhoName) }},
                 mhoTitle: {{ json_encode($mhoTitle) }},
                 mhoOversightTitle: {{ json_encode($mhoOversightTitle) }},
                 mhoOversightDesc: {{ json_encode($mhoOversightDesc) }},
                 mhoImgUrl: {{ json_encode($currentMhoImageUrl) }},
                 mhoImgPreview: null,
                 removeMhoImg: false,
                 systemStaff: {{ json_encode($systemStaff ?? []) }},
                 doctorStaffSelect: '',
                 adminStaffSelect: '',
                 medOfficers: {{ json_encode($rawMedOfficers) }},
                 divisions: {{ json_encode($rawDivisions) }},
                 midwives: {{ json_encode($rawMidwives) }},
                 admins: {{ json_encode($rawAdmins) }},

                 moveItemUp(list, index) {
                     if (index > 0 && index < list.length) {
                         const item = list.splice(index, 1)[0];
                         list.splice(index - 1, 0, item);
                     }
                 },
                 moveItemDown(list, index) {
                     if (index >= 0 && index < list.length - 1) {
                         const item = list.splice(index, 1)[0];
                         list.splice(index + 1, 0, item);
                     }
                 },
                 getInitialsFromName(name) {
                     if (!name) return 'ST';
                     const words = name.trim().split(/\s+/);
                     let ini = '';
                     for (const w of words) {
                         const clean = w.replace(/[^a-zA-Z]/g, '');
                         if (clean && !['MD', 'DR', 'RN', 'DMD', 'RMT', 'RPH', 'RTRP'].includes(clean.toUpperCase())) {
                             ini += clean[0].toUpperCase();
                         }
                     }
                     return ini.slice(0, 2) || 'ST';
                 },
                 addMedicalOfficerFromStaff(staffId) {
                     if (!staffId) return;
                     const staff = this.systemStaff.find(s => s.id == staffId);
                     if (!staff) return;
                     const roleTitle = staff.role === 'pedia_doctor' ? 'Pediatric Specialist' : 'Medical Officer III';
                     const initials = this.getInitialsFromName(staff.name);
                     this.medOfficers.push({
                         name: staff.name,
                         role: roleTitle,
                         initials: initials
                     });
                     this.doctorStaffSelect = '';
                 },
                 addAdminFromStaff(staffId) {
                     if (!staffId) return;
                     const staff = this.systemStaff.find(s => s.id == staffId);
                     if (!staff) return;
                     let position = 'Administrative Staff';
                     if (staff.role === 'super_admin' || staff.role === 'admin') position = 'Administrative Officer';
                     else if (staff.role === 'information_desk') position = 'Public Assistance & Frontline Officer';
                     else if (staff.role === 'pharmacy') position = 'Pharmacy & Inventory Specialist';
                     const initials = this.getInitialsFromName(staff.name);
                     this.admins.push({
                         name: staff.name,
                         role: position,
                         initials: initials
                     });
                     this.adminStaffSelect = '';
                 },
                 addMedicalOfficer() {
                     this.medOfficers.push({ name: '', role: 'Medical Officer II', initials: 'MD' });
                 },
                 removeMedicalOfficer(index) {
                     this.medOfficers.splice(index, 1);
                 },
                 addMidwife() {
                     this.midwives.push({ name: '', rank: 'Midwife' });
                 },
                 removeMidwife(index) {
                     this.midwives.splice(index, 1);
                 },
                 addAdmin() {
                     this.admins.push({ name: '', role: 'Administrative Staff', initials: 'AS' });
                 },
                 removeAdmin(index) {
                     this.admins.splice(index, 1);
                 },
                 addDivisionUnit(divIdx) {
                     if (!this.divisions[divIdx].units) {
                         this.divisions[divIdx].units = [];
                     }
                     this.divisions[divIdx].units.push({
                         title: 'New Clinical Unit',
                         category: 'Specialized Care',
                         lead_name: '',
                         lead_role: '',
                         members: []
                     });
                 },
                 removeDivisionUnit(divIdx, unitIdx) {
                     this.divisions[divIdx].units.splice(unitIdx, 1);
                 },
                 addUnitMember(divIdx, unitIdx, memberName = '') {
                     if (!this.divisions[divIdx].units[unitIdx].members) {
                         this.divisions[divIdx].units[unitIdx].members = [];
                     }
                     this.divisions[divIdx].units[unitIdx].members.push(memberName);
                 },
                 removeUnitMember(divIdx, unitIdx, memberIdx) {
                     if (this.divisions[divIdx].units[unitIdx].members) {
                         this.divisions[divIdx].units[unitIdx].members.splice(memberIdx, 1);
                     }
                 },
                 getUnitMembersCount(unit) {
                     if (!unit || !unit.members) return 0;
                     if (Array.isArray(unit.members)) return unit.members.length;
                     if (typeof unit.members === 'string' && unit.members.trim().length > 0) {
                         return unit.members.split(/[•\n]/).filter(m => m.trim().length > 0).length;
                     }
                     return 0;
                 },
                 getUnitTotalStaffCount(unit) {
                     if (!unit) return 0;
                     let count = 0;
                     if (unit.lead_name && typeof unit.lead_name === 'string' && unit.lead_name.trim().length > 0) {
                         count++;
                     }
                     count += this.getUnitMembersCount(unit);
                     return count;
                 },
                 getDivisionStaffCount(div) {
                     if (!div) return 0;
                     let count = 0;
                     if (div.units && Array.isArray(div.units)) {
                         for (const u of div.units) {
                             count += this.getUnitTotalStaffCount(u);
                         }
                     }
                     if (div.id === 'maternal' && this.midwives && Array.isArray(this.midwives)) {
                         count += this.midwives.filter(m => m.name && m.name.trim().length > 0).length;
                     }
                     if (div.id === 'admin' && this.admins && Array.isArray(this.admins)) {
                         count += this.admins.filter(a => a.name && a.name.trim().length > 0).length;
                     }
                     return count;
                 },
                 previewMhoImage(event) {
                     const file = event.target.files[0];
                     if (file) {
                         this.removeMhoImg = false;
                         const reader = new FileReader();
                         reader.onload = (e) => { this.mhoImgPreview = e.target.result; };
                         reader.readAsDataURL(file);
                     }
                 },
                 clearMhoImage() {
                     this.mhoImgPreview = null;
                     this.mhoImgUrl = null;
                     this.removeMhoImg = true;
                     const input = document.getElementById('mho_image_file_input');
                     if (input) input.value = '';
                 },
                 getMhoInitials() {
                     if (!this.mhoName) return 'JP';
                     const words = this.mhoName.trim().split(/\s+/);
                     let ini = '';
                     for (const w of words) {
                         const clean = w.replace(/[^a-zA-Z]/g, '');
                         if (clean && !['MD', 'DR', 'RN', 'DMD', 'RMT', 'RPH'].includes(clean.toUpperCase())) {
                             ini += clean[0].toUpperCase();
                         }
                     }
                     return ini.slice(0, 2) || 'JP';
                 },
                 togglePreviewDivision(divId) {
                     this.previewExpandedDivision = this.previewExpandedDivision === divId ? null : divId;
                 },
                 matchesPreviewSearch(text) {
                     if (!this.previewSearch || this.previewSearch.trim().length < 2) return true;
                     return text.toLowerCase().includes(this.previewSearch.toLowerCase().trim());
                 }
             }">

            {{-- Top Mode Switcher: Form Editor vs Live Preview --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-3 sm:p-4 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl self-start sm:self-auto">
                    <button type="button" @click="activeView = 'editor'" 
                            :class="activeView === 'editor' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                            class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Content Editor</span>
                    </button>
                    <button type="button" @click="activeView = 'preview'" 
                            :class="activeView === 'preview' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 font-medium'"
                            class="px-4 py-2 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Live Org Chart Preview</span>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400">All updates reflect immediately on the public <a href="{{ route('about') }}#org-chart" target="_blank" class="text-emerald-600 font-bold hover:underline">/about#org-chart</a> section.</span>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 VIEW 1: FORM EDITOR
            ───────────────────────────────────────────────────────── --}}
            <div x-show="activeView === 'editor'" class="space-y-6">

                {{-- Hidden input for removing MHO image --}}
                <input type="hidden" name="remove_mho_image" :value="removeMhoImg ? '1' : '0'">

                {{-- Section 1: Section Header & Civic Narrative --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Section Presentation & Narrative</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Top-level section kicker, title, and descriptive mission narrative.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            Header
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Section Kicker / Badge Text
                            </label>
                            <input type="text" name="settings[org_kicker]" x-model="orgKicker" placeholder="e.g. Leadership & Governance"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Section Main Headline <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="settings[org_title]" x-model="orgTitle" required placeholder="e.g. Organizational Structure & Leadership"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Section Subtitle & Clinical Overview
                            </label>
                            <textarea name="settings[org_subtitle]" x-model="orgSubtitle" rows="2" placeholder="Describe the dedicated administrators, medical officers, nurses and staff..."
                                      class="w-full p-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Executive Head (Municipal Health Officer) --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-2xs">
                                MHO
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Executive Head (Municipal Health Officer)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Top agency leader details, executive oversight mandate, and official portrait.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                            Executive Spotlight
                        </span>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        
                        {{-- Photo / Portrait Upload --}}
                        <div class="lg:col-span-4 flex flex-col items-center p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/70 text-center">
                            <div class="relative group mb-3">
                                <template x-if="mhoImgPreview">
                                    <img :src="mhoImgPreview" alt="MHO Preview" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md ring-4 ring-emerald-500/20 border border-emerald-200 dark:border-emerald-800">
                                </template>
                                <template x-if="!mhoImgPreview && mhoImgUrl">
                                    <img :src="mhoImgUrl" alt="MHO" class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md ring-4 ring-emerald-500/20 border border-emerald-200 dark:border-emerald-800">
                                </template>
                                <template x-if="!mhoImgPreview && !mhoImgUrl">
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white flex items-center justify-center font-display text-2xl sm:text-3xl font-extrabold shadow-md ring-4 ring-emerald-500/10"
                                         x-text="getMhoInitials()">
                                    </div>
                                </template>

                                <button type="button" x-show="mhoImgPreview || mhoImgUrl" @click="clearMhoImage()" 
                                        class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-rose-500 text-white hover:bg-rose-600 flex items-center justify-center text-xs shadow-md cursor-pointer transition-transform hover:scale-110"
                                        title="Remove portrait">
                                    ✕
                                </button>
                            </div>

                            <label for="mho_image_file_input" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:border-emerald-500 shadow-2xs transition-colors">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Upload Portrait</span>
                            </label>
                            <input id="mho_image_file_input" type="file" name="mho_image_file" accept="image/*" @change="previewMhoImage($event)" class="hidden">
                            <p class="text-[10px] text-slate-400 mt-2">Recommended: 1:1 square photo, JPG/PNG/WebP, max 10MB. Fallbacks to initials.</p>
                        </div>

                        {{-- Details Form Fields --}}
                        <div class="lg:col-span-8 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Executive Head Full Name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="settings[mho_name]" x-model="mhoName" required placeholder="e.g. Jericho Joshua E. Palay, MD"
                                           class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Executive Designation / Role <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="settings[mho_title]" x-model="mhoTitle" required placeholder="e.g. Municipal Health Officer"
                                           class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Primary Badge Label
                                    </label>
                                    <input type="text" name="settings[mho_badge]" x-model="mhoBadge" placeholder="e.g. Executive Head"
                                           class="w-full h-10 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Secondary Sub-Badge Label
                                    </label>
                                    <input type="text" name="settings[mho_subbadge]" x-model="mhoSubbadge" placeholder="e.g. Head of Agency"
                                           class="w-full h-10 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Oversight Title
                                    </label>
                                    <input type="text" name="settings[mho_oversight_title]" x-model="mhoOversightTitle" placeholder="e.g. Executive Oversight:"
                                           class="w-full h-10 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Executive Oversight Narrative
                                    </label>
                                    <input type="text" name="settings[mho_oversight_desc]" x-model="mhoOversightDesc" placeholder="e.g. Clinical governance, health policy, and public healthcare across all 64 barangays."
                                           class="w-full h-10 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Section 3: Medical Officers & Clinical Specialists (Direct Reports) --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/20 to-cyan-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Medical Officers & Clinical Specialists (Direct Reports)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Management table for practicing clinical physicians reporting directly to the Municipal Health Officer.</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800/60"
                                  x-text="medOfficers.length + ' Physicians'">
                            </span>
                            {{-- Dropdown to import from registered staff --}}
                            <div class="relative">
                                <select x-model="doctorStaffSelect" @change="addMedicalOfficerFromStaff($event.target.value)"
                                        class="h-9 pl-3 pr-8 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-700 dark:text-slate-300 shadow-2xs focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 cursor-pointer">
                                    <option value="">+ Import from Staff Roster...</option>
                                    <template x-for="st in systemStaff.filter(s => ['regular_doctor', 'pedia_doctor'].includes(s.role))" :key="st.id">
                                        <option :value="st.id" x-text="st.name + ' (' + (st.role === 'pedia_doctor' ? 'Pediatric' : 'Doctor') + ')'"></option>
                                    </template>
                                </select>
                            </div>
                            <button type="button" @click="addMedicalOfficer()" 
                                    class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Doctor</span>
                            </button>
                        </div>
                    </div>

                    {{-- Tabular Roster Table for Physicians --}}
                    <div class="border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-x-auto shadow-2xs">
                        <table class="w-full min-w-[540px] text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/90 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                                    <th class="p-3.5 w-20 text-center">Order</th>
                                    <th class="p-3.5 w-20 text-center">Initials</th>
                                    <th class="p-3.5">Physician Full Name & Credentials</th>
                                    <th class="p-3.5">Clinical Rank / Role</th>
                                    <th class="p-3.5 w-20 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                <template x-for="(doc, dIdx) in medOfficers" :key="dIdx">
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                        <td class="p-3.5 text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                <span class="font-mono font-bold text-slate-400 w-5 text-center" x-text="dIdx + 1"></span>
                                                <div class="flex flex-col gap-0.5">
                                                    <button type="button" @click="moveItemUp(medOfficers, dIdx)" :disabled="dIdx === 0"
                                                            class="p-0.5 rounded text-slate-400 hover:text-teal-600 disabled:opacity-20 disabled:hover:text-slate-400 cursor-pointer"
                                                            title="Move Up">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                                    </button>
                                                    <button type="button" @click="moveItemDown(medOfficers, dIdx)" :disabled="dIdx === medOfficers.length - 1"
                                                            class="p-0.5 rounded text-slate-400 hover:text-teal-600 disabled:opacity-20 disabled:hover:text-slate-400 cursor-pointer"
                                                            title="Move Down">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3.5 text-center">
                                            <div class="w-9 h-9 mx-auto rounded-xl bg-teal-100 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300 font-bold text-xs flex items-center justify-center border border-teal-200/60 dark:border-teal-800/60 shadow-2xs">
                                                <input type="text" :name="'org_medical_officers[' + dIdx + '][initials]'" x-model="doc.initials" maxlength="2"
                                                       class="w-full text-center bg-transparent border-0 font-bold text-xs uppercase p-0 focus:ring-0 text-teal-800 dark:text-teal-200">
                                            </div>
                                        </td>
                                        <td class="p-3.5">
                                            <input type="text" :name="'org_medical_officers[' + dIdx + '][name]'" x-model="doc.name" placeholder="e.g. Angel Casapao, MD" required
                                                   class="w-full h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                                        </td>
                                        <td class="p-3.5">
                                            <input type="text" :name="'org_medical_officers[' + dIdx + '][role]'" x-model="doc.role" placeholder="e.g. Medical Officer III" required
                                                   class="w-full h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                                        </td>
                                        <td class="p-3.5 text-right">
                                            <button type="button" @click="removeMedicalOfficer(dIdx)" 
                                                    class="w-8 h-8 ml-auto rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center text-xs transition cursor-pointer"
                                                    title="Remove Doctor">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="medOfficers.length === 0">
                                    <td colspan="5" class="p-8 text-center text-slate-400">
                                        <p class="font-semibold text-xs">No practicing physicians configured yet.</p>
                                        <p class="text-[11px] mt-1">Click "Add Doctor" or select from the staff roster dropdown above.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Section 4: Operational Divisions & Clinical Programs --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0 border border-cyan-500/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Operational Divisions & Departmental Rosters</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure division descriptions, clinical units, staff rosters, midwives, and administrative teams.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Horizontal Division Tab Bar --}}
                    <div class="flex items-center gap-2 p-1.5 bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-x-auto">
                        <button type="button" @click="activeDivisionTab = 'primary'"
                                :class="activeDivisionTab === 'primary' ? 'bg-white dark:bg-slate-700 text-emerald-700 dark:text-emerald-300 shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-semibold'"
                                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2 shrink-0 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>1. Primary Health</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200/60 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono"
                                  x-text="getDivisionStaffCount(divisions.find(d => d.id === 'primary')) + ' staff'"></span>
                        </button>
                        <button type="button" @click="activeDivisionTab = 'maternal'"
                                :class="activeDivisionTab === 'maternal' ? 'bg-white dark:bg-slate-700 text-teal-700 dark:text-teal-300 shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-semibold'"
                                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2 shrink-0 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                            <span>2. Maternal & Child Health</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200/60 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono"
                                  x-text="getDivisionStaffCount(divisions.find(d => d.id === 'maternal')) + ' staff'"></span>
                        </button>
                        <button type="button" @click="activeDivisionTab = 'ancillary'"
                                :class="activeDivisionTab === 'ancillary' ? 'bg-white dark:bg-slate-700 text-cyan-700 dark:text-cyan-300 shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-semibold'"
                                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2 shrink-0 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            <span>3. Ancillary & Allied Health</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200/60 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono"
                                  x-text="getDivisionStaffCount(divisions.find(d => d.id === 'ancillary')) + ' staff'"></span>
                        </button>
                        <button type="button" @click="activeDivisionTab = 'admin'"
                                :class="activeDivisionTab === 'admin' ? 'bg-white dark:bg-slate-700 text-amber-700 dark:text-amber-300 shadow-2xs font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 font-semibold'"
                                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2 shrink-0 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>4. Administrative Staff</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-200/60 dark:bg-slate-800 text-slate-500 dark:text-slate-400 font-mono"
                                  x-text="getDivisionStaffCount(divisions.find(d => d.id === 'admin')) + ' staff'"></span>
                        </button>
                    </div>

                    {{-- Loop through the 4 divisions --}}
                    <template x-for="(div, dIdx) in divisions" :key="div.id">
                        <div x-show="activeDivisionTab === div.id" class="space-y-6">
                            
                            {{-- Hidden Metadata inputs --}}
                            <input type="hidden" :name="'org_divisions[' + dIdx + '][id]'" :value="div.id">
                            <input type="hidden" :name="'org_divisions[' + dIdx + '][number]'" :value="div.number">
                            <input type="hidden" :name="'org_divisions[' + dIdx + '][accent]'" :value="div.accent">

                            {{-- Division Overview Card --}}
                            <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/70 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-700/70">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 h-8 rounded-xl bg-slate-200/80 dark:bg-slate-700/80 text-slate-800 dark:text-slate-200 font-bold text-xs flex items-center justify-center border border-slate-300/60 dark:border-slate-600/60 shadow-2xs shrink-0"
                                              x-text="'Div ' + (div.number || (dIdx + 1))"></span>
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                                Division Information & Scope
                                            </h4>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Top-level division branding and operational scope displayed on department cards.</p>
                                        </div>
                                    </div>

                                    {{-- Auto-calculated Staff Count Badge in Card Header --}}
                                    <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-700/80 shadow-2xs">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="getDivisionStaffCount(div) + ' Total Personnel'"></span>
                                            <span class="text-[9px] uppercase tracking-wider font-extrabold text-emerald-700 dark:text-emerald-300 bg-emerald-100/80 dark:bg-emerald-950/80 px-1.5 py-0.5 rounded-md">Auto-Counted</span>
                                        </div>
                                        <input type="hidden" :name="'org_divisions[' + dIdx + '][badge]'" :value="getDivisionStaffCount(div) + ' Staff'">
                                    </div>
                                </div>

                                {{-- Spacious Responsive Grid (4-col / 8-col split on desktop, full-width on mobile) --}}
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 sm:gap-4">
                                    <div class="md:col-span-4 min-w-0">
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 sm:mb-2">
                                            Division Display Title <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" :name="'org_divisions[' + dIdx + '][title]'" x-model="div.title" required
                                               placeholder="e.g. Primary Health"
                                               class="w-full min-w-0 h-10 sm:h-11 px-3.5 sm:px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                    </div>
                                    <div class="md:col-span-8 min-w-0">
                                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5 sm:mb-2">
                                            Division Scope Summary <span class="text-slate-400 text-[10px] font-normal lowercase">(displayed on cards)</span>
                                        </label>
                                        <input type="text" :name="'org_divisions[' + dIdx + '][subtitle]'" x-model="div.subtitle" placeholder="e.g. Immunization (NIP), Animal Bite, TB DOTS, Disease Surveillance & Emergency Transport"
                                               class="w-full min-w-0 h-10 sm:h-11 px-3.5 sm:px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                    </div>
                                </div>
                            </div>

                            <div x-show="div.id !== 'admin'" class="space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-1">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                                            <span>Clinical Units & Programs</span>
                                            <span class="text-slate-400 text-[11px] font-normal" x-text="'(' + (div.units ? div.units.length : 0) + ' configured)'"></span>
                                        </h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Specialized clinics, diagnostic sections, and health programs operating within this division.</p>
                                    </div>
                                    <button type="button" @click="addDivisionUnit(dIdx)" 
                                            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs active:scale-95 self-start sm:self-auto shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        <span>Add Clinical Unit</span>
                                    </button>
                                </div>

                                {{-- Modular Clinical Unit Cards (Fully responsive, fluid on all screens) --}}
                                <div class="space-y-4">
                                    <template x-for="(unit, uIdx) in (div.units || [])" :key="uIdx">
                                        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition-all overflow-hidden">
                                            
                                            {{-- Unit Header Bar --}}
                                            <div class="p-3.5 sm:p-5 bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800 space-y-3">
                                                
                                                {{-- Top Meta Row: Order badge, Staff Count, and Delete Unit button --}}
                                                <div class="flex items-center justify-between gap-2">
                                                    <div class="flex items-center gap-2 flex-wrap min-w-0">
                                                        {{-- Order Control --}}
                                                        <div class="flex items-center gap-1 bg-white dark:bg-slate-900 px-2 py-1 rounded-xl border border-slate-200/80 dark:border-slate-700/80 shadow-2xs shrink-0">
                                                            <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400 w-5 text-center" x-text="'#' + (uIdx + 1)"></span>
                                                            <div class="flex flex-col">
                                                                <button type="button" @click="moveItemUp(div.units, uIdx)" :disabled="uIdx === 0"
                                                                        class="p-0.5 rounded text-slate-400 hover:text-emerald-600 disabled:opacity-20 cursor-pointer" title="Move Up">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                                </button>
                                                                <button type="button" @click="moveItemDown(div.units, uIdx)" :disabled="uIdx === div.units.length - 1"
                                                                        class="p-0.5 rounded text-slate-400 hover:text-emerald-600 disabled:opacity-20 cursor-pointer" title="Move Down">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        {{-- Staff Count Badge --}}
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/70 dark:border-emerald-800/70 text-[11px] font-bold shadow-2xs shrink-0 whitespace-nowrap">
                                                            <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            <span x-text="getUnitTotalStaffCount(unit) + ' Staff'"></span>
                                                        </span>
                                                    </div>

                                                    {{-- Delete Unit Button --}}
                                                    <button type="button" @click="removeDivisionUnit(dIdx, uIdx)" 
                                                            class="px-2.5 py-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/80 dark:border-slate-700/80 transition flex items-center gap-1 text-xs font-semibold cursor-pointer shadow-2xs shrink-0"
                                                            title="Remove Unit">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        <span class="text-[11px]">Remove</span>
                                                    </button>
                                                </div>

                                                {{-- Inputs Row: Program Title and Category Tag (Fluid grid, full width on mobile) --}}
                                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">
                                                    <div class="sm:col-span-8 min-w-0">
                                                        <input type="text" 
                                                               :name="'org_divisions[' + dIdx + '][units][' + uIdx + '][title]'" 
                                                               x-model="unit.title" 
                                                               placeholder="Clinical Program / Unit Title (e.g. National Immunization)" 
                                                               required
                                                               class="w-full min-w-0 h-9 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-bold text-slate-900 dark:text-white placeholder:font-normal focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                                    </div>

                                                    <div class="sm:col-span-4 min-w-0">
                                                        <div class="relative">
                                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] font-bold uppercase tracking-wider pointer-events-none">Tag:</span>
                                                            <input type="text" 
                                                                   :name="'org_divisions[' + dIdx + '][units][' + uIdx + '][category]'" 
                                                                   x-model="unit.category" 
                                                                   placeholder="e.g. Immunization"
                                                                   class="w-full min-w-0 h-9 pl-11 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Unit Body: 2-Column Responsive Layout (Lead Clinician & Assigned Members) --}}
                                            <div class="p-3.5 sm:p-5 grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5">
                                                
                                                {{-- Left Column: Lead Clinician & Rank (5 Cols on large, full-width on mobile/tablet) --}}
                                                <div class="lg:col-span-5 bg-slate-50/70 dark:bg-slate-800/40 rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 dark:border-slate-700/70 flex flex-col justify-between space-y-3.5 min-w-0">
                                                    <div class="space-y-3">
                                                        <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                                                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                                <span>Lead Clinician / Unit Head</span>
                                                            </span>
                                                            <span class="text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-100/70 dark:bg-emerald-950/70 px-2 py-0.5 rounded-md">Head</span>
                                                        </div>

                                                        <div>
                                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                                                Full Name & Credentials
                                                            </label>
                                                            <input type="text" 
                                                                   :name="'org_divisions[' + dIdx + '][units][' + uIdx + '][lead_name]'" 
                                                                   x-model="unit.lead_name" 
                                                                   placeholder="e.g. Razelle Bendo, RN"
                                                                   class="w-full min-w-0 h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                                        </div>

                                                        <div>
                                                            <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                                                Position / Official Rank
                                                            </label>
                                                            <input type="text" 
                                                                   :name="'org_divisions[' + dIdx + '][units][' + uIdx + '][lead_role]'" 
                                                                   x-model="unit.lead_role" 
                                                                   placeholder="e.g. Nurse II / Section Head"
                                                                   class="w-full min-w-0 h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-emerald-700 dark:text-emerald-400 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                                        </div>
                                                    </div>

                                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 italic pt-1">
                                                        Appears as the lead supervisor and clinic head in the public directory.
                                                    </p>
                                                </div>

                                                {{-- Right Column: Assigned Team Members (7 Cols on large, full-width on mobile/tablet) --}}
                                                <div class="lg:col-span-7 bg-slate-50/70 dark:bg-slate-800/40 rounded-2xl p-3.5 sm:p-4 border border-slate-200/70 dark:border-slate-700/70 space-y-3 min-w-0">
                                                    <div class="flex flex-wrap items-center justify-between gap-2.5 pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                                                        <div class="flex items-center gap-2 min-w-0 shrink-0">
                                                            <svg class="w-3.5 h-3.5 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider whitespace-nowrap">Assigned Team Members</span>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200/80 dark:bg-slate-700/80 text-slate-600 dark:text-slate-300 whitespace-nowrap shrink-0"
                                                                  x-text="getUnitMembersCount(unit) + ' ' + (getUnitMembersCount(unit) === 1 ? 'Member' : 'Members')"></span>
                                                        </div>

                                                        {{-- Actions: Quick Add and Pick from System Staff --}}
                                                        <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                                                            <button type="button" 
                                                                    @click="addUnitMember(dIdx, uIdx)"
                                                                    class="flex-1 sm:flex-none justify-center px-2.5 py-1.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-slate-300 dark:border-slate-700 hover:border-emerald-400 transition flex items-center gap-1 cursor-pointer shadow-2xs shrink-0 whitespace-nowrap">
                                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                                <span>Add Member</span>
                                                            </button>

                                                            <div class="relative flex-1 sm:w-44 min-w-0" x-data="{ staffPicker: '' }">
                                                                <select x-model="staffPicker" 
                                                                        @change="if ($event.target.value) { addUnitMember(dIdx, uIdx, $event.target.value); staffPicker = ''; }"
                                                                        class="w-full h-8 pl-2 pr-7 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-[11px] font-semibold text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-emerald-500 cursor-pointer shadow-2xs truncate">
                                                                    <option value="">+ Pick Staff...</option>
                                                                    <template x-for="st in systemStaff" :key="st.id">
                                                                        <option :value="st.name" x-text="st.name"></option>
                                                                    </template>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Member List Items (Spacious, clean rows) --}}
                                                    <div class="space-y-2">
                                                        <template x-for="(mName, mIdx) in (unit.members || [])" :key="mIdx">
                                                            <div class="flex items-center gap-2 p-1.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-2xs group hover:border-emerald-500/50 transition-all">
                                                                <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 text-[11px] font-mono font-bold text-slate-500 dark:text-slate-400 flex items-center justify-center shrink-0" x-text="mIdx + 1"></span>
                                                                <input type="text"
                                                                       :name="'org_divisions[' + dIdx + '][units][' + uIdx + '][members][' + mIdx + ']'"
                                                                       x-model="unit.members[mIdx]"
                                                                       placeholder="Staff name / credentials..."
                                                                       required
                                                                       class="flex-1 min-w-0 h-8 px-2 text-xs rounded-lg bg-transparent border-0 text-slate-900 dark:text-white focus:ring-1 focus:ring-emerald-500 font-semibold placeholder:font-normal">
                                                                <button type="button" 
                                                                        @click="removeUnitMember(dIdx, uIdx, mIdx)"
                                                                        class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition cursor-pointer shrink-0"
                                                                        title="Remove Member">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>
                                                        </template>

                                                        {{-- Empty state when no members --}}
                                                        <div x-show="!unit.members || unit.members.length === 0" 
                                                             class="p-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-900/40 text-center">
                                                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">No additional team members assigned yet.</p>
                                                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Click "Add Member" or pick from the staff roster to assign personnel to this unit.</p>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                                    </template>

                                    {{-- Empty state if division has no units --}}
                                    <div x-show="!div.units || div.units.length === 0" 
                                         class="p-6 sm:p-8 rounded-2xl sm:rounded-3xl border-2 border-dashed border-slate-300 dark:border-slate-700 text-center bg-slate-50/50 dark:bg-slate-800/30">
                                        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">No clinical units configured in this division</h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">Add health programs, specialized clinics, or testing sections to this operational division.</p>
                                        <button type="button" @click="addDivisionUnit(dIdx)" 
                                                class="mt-4 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            <span>Add First Clinical Unit</span>
                                        </button>
                                    </div>

                                    {{-- Bottom "+ Add Another Unit" friendly card button --}}
                                    <div x-show="div.units && div.units.length > 0" class="pt-1">
                                        <button type="button" @click="addDivisionUnit(dIdx)" 
                                                class="w-full py-3.5 rounded-2xl border-2 border-dashed border-slate-300 hover:border-emerald-500 dark:border-slate-700 dark:hover:border-emerald-500 text-slate-600 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold text-xs transition flex items-center justify-center gap-2 cursor-pointer bg-white/40 dark:bg-slate-900/40 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            <span>Add Another Clinical Unit or Program</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Frontline Barangay Midwives Section (Inside Maternal & Child Health) --}}
                            <div x-show="div.id === 'maternal'" class="p-5 sm:p-6 rounded-3xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/70 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-700/70">
                                    <div>
                                        <h4 class="text-xs font-bold text-teal-800 dark:text-teal-300 uppercase tracking-wider flex items-center gap-2">
                                            <span>Frontline Barangay Midwives Roster</span>
                                            <span class="text-slate-400 text-[10px] lowercase">(bemonc birthing center)</span>
                                        </h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Management table for licensed barangay midwives deployed across municipal health stations.</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-100 text-teal-800 dark:bg-teal-950/70 dark:text-teal-300"
                                              x-text="midwives.length + ' Midwives'"></span>
                                        <button type="button" @click="addMidwife()" 
                                                class="px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            <span>Add Midwife</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabular Midwives Roster --}}
                                <div class="border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-x-auto shadow-2xs">
                                    <table class="w-full min-w-[500px] text-left border-collapse">
                                        <thead>
                                            <tr class="bg-slate-100/80 dark:bg-slate-800/80 border-b border-slate-200/90 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                                                <th class="p-3.5 w-20 text-center">Order</th>
                                                <th class="p-3.5">Midwife Full Name & Credentials</th>
                                                <th class="p-3.5 w-48">Designation / Rank</th>
                                                <th class="p-3.5 w-20 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                            <template x-for="(mw, mwIdx) in midwives" :key="mwIdx">
                                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td class="p-3.5 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <span class="font-mono font-bold text-slate-400 w-5 text-center" x-text="mwIdx + 1"></span>
                                                            <div class="flex flex-col gap-0.5">
                                                                <button type="button" @click="moveItemUp(midwives, mwIdx)" :disabled="mwIdx === 0"
                                                                        class="p-0.5 rounded text-slate-400 hover:text-teal-600 disabled:opacity-20 cursor-pointer" title="Move Up">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                                                </button>
                                                                <button type="button" @click="moveItemDown(midwives, mwIdx)" :disabled="mwIdx === midwives.length - 1"
                                                                        class="p-0.5 rounded text-slate-400 hover:text-teal-600 disabled:opacity-20 cursor-pointer" title="Move Down">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="p-3.5">
                                                        <input type="text" :name="'org_midwives[' + mwIdx + '][name]'" x-model="mw.name" placeholder="e.g. Maria Mendoza, RM" required
                                                               class="w-full h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                                                    </td>
                                                    <td class="p-3.5">
                                                        <input type="text" :name="'org_midwives[' + mwIdx + '][rank]'" x-model="mw.rank" placeholder="e.g. Midwife III"
                                                               class="w-full h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-bold text-teal-700 dark:text-teal-300 focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                                                    </td>
                                                    <td class="p-3.5 text-right">
                                                        <button type="button" @click="removeMidwife(mwIdx)" 
                                                                class="w-8 h-8 ml-auto rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center text-xs transition cursor-pointer"
                                                                title="Remove Midwife">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr x-show="midwives.length === 0">
                                                <td colspan="4" class="p-6 text-center text-slate-400">
                                                    <p class="font-semibold text-xs">No barangay midwives listed.</p>
                                                    <p class="text-[11px] mt-1">Click "+ Add Midwife" above to add personnel.</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Administrative Personnel Section (Inside Administrative Division) --}}
                            <div x-show="div.id === 'admin'" class="p-5 sm:p-6 rounded-3xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/70 space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80 dark:border-slate-700/70">
                                    <div>
                                        <h4 class="text-xs font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider">
                                            Administrative Staff Personnel Roster
                                        </h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Management table for officers and support staff managing governance, records, and procurement.</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300"
                                              x-text="admins.length + ' Personnel'"></span>
                                        {{-- Dropdown to import from registered staff --}}
                                        <div class="relative">
                                            <select x-model="adminStaffSelect" @change="addAdminFromStaff($event.target.value)"
                                                    class="h-9 pl-3 pr-8 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-700 dark:text-slate-300 shadow-2xs focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 cursor-pointer">
                                                <option value="">+ Import from Staff Roster...</option>
                                                <template x-for="st in systemStaff.filter(s => ['super_admin', 'admin', 'information_desk', 'pharmacy'].includes(s.role))" :key="st.id">
                                                    <option :value="st.id" x-text="st.name + ' (' + (st.role === 'information_desk' ? 'Front Desk' : (st.role === 'pharmacy' ? 'Pharmacy' : 'Admin')) + ')'"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <button type="button" @click="addAdmin()" 
                                                class="px-3.5 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            <span>Add Personnel</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabular Admin Roster --}}
                                <div class="border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-x-auto shadow-2xs">
                                    <table class="w-full min-w-[500px] text-left border-collapse">
                                        <thead>
                                            <tr class="bg-slate-100/80 dark:bg-slate-800/80 border-b border-slate-200/90 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                                                <th class="p-3.5 w-20 text-center">Order</th>
                                                <th class="p-3.5 w-20 text-center">Initials</th>
                                                <th class="p-3.5">Staff Full Name</th>
                                                <th class="p-3.5">Position / Designation</th>
                                                <th class="p-3.5 w-20 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                                            <template x-for="(adm, aIdx) in admins" :key="aIdx">
                                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                                    <td class="p-3.5 text-center">
                                                        <div class="flex items-center justify-center gap-1">
                                                            <span class="font-mono font-bold text-slate-400 w-5 text-center" x-text="aIdx + 1"></span>
                                                            <div class="flex flex-col gap-0.5">
                                                                <button type="button" @click="moveItemUp(admins, aIdx)" :disabled="aIdx === 0"
                                                                        class="p-0.5 rounded text-slate-400 hover:text-amber-600 disabled:opacity-20 cursor-pointer" title="Move Up">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"/></svg>
                                                                </button>
                                                                <button type="button" @click="moveItemDown(admins, aIdx)" :disabled="aIdx === admins.length - 1"
                                                                        class="p-0.5 rounded text-slate-400 hover:text-amber-600 disabled:opacity-20 cursor-pointer" title="Move Down">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="p-3.5 text-center">
                                                        <div class="w-9 h-9 mx-auto rounded-xl bg-amber-100 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 font-bold text-xs flex items-center justify-center border border-amber-200/60 dark:border-amber-800/60 shadow-2xs">
                                                            <input type="text" :name="'org_admins[' + aIdx + '][initials]'" x-model="adm.initials" maxlength="2"
                                                                   class="w-full text-center bg-transparent border-0 font-bold text-xs uppercase p-0 focus:ring-0 text-amber-800 dark:text-amber-200">
                                                        </div>
                                                    </td>
                                                    <td class="p-3.5">
                                                        <input type="text" :name="'org_admins[' + aIdx + '][name]'" x-model="adm.name" placeholder="e.g. Mark Anthony Sebastian" required
                                                               class="w-full h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                                    </td>
                                                    <td class="p-3.5">
                                                        <input type="text" :name="'org_admins[' + aIdx + '][role]'" x-model="adm.role" placeholder="e.g. Administrative Officer" required
                                                               class="w-full h-9 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                                                    </td>
                                                    <td class="p-3.5 text-right">
                                                        <button type="button" @click="removeAdmin(aIdx)" 
                                                                class="w-8 h-8 ml-auto rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/60 dark:border-slate-700/60 flex items-center justify-center text-xs transition cursor-pointer"
                                                                title="Remove Personnel">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                            <tr x-show="admins.length === 0">
                                                <td colspan="5" class="p-6 text-center text-slate-400">
                                                    <p class="font-semibold text-xs">No administrative personnel listed.</p>
                                                    <p class="text-[11px] mt-1">Click "+ Add Personnel" or import from the staff roster above.</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            {{-- ─────────────────────────────────────────────────────────
                 VIEW 2: LIVE ORG CHART PREVIEW
            ───────────────────────────────────────────────────────── --}}
            <div x-show="activeView === 'preview'" class="space-y-8 p-4 sm:p-8 rounded-3xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/90 dark:border-slate-800">
                <div class="text-center max-w-3xl mx-auto mb-8">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-md border border-emerald-300/50 dark:border-emerald-700/50 mb-3"
                          x-text="orgKicker"></span>
                    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight"
                        x-text="orgTitle"></h2>
                    <p class="text-slate-600 dark:text-slate-400 mt-2 text-xs sm:text-sm leading-relaxed"
                       x-text="orgSubtitle"></p>
                </div>

                {{-- Executive Spotlight Preview --}}
                <div class="rounded-3xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xs">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 pb-6 border-b border-slate-200/80 dark:border-slate-800">
                        <div class="flex items-center gap-4 sm:gap-5">
                            <template x-if="mhoImgPreview">
                                <img :src="mhoImgPreview" class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover shadow-md ring-4 ring-emerald-500/10">
                            </template>
                            <template x-if="!mhoImgPreview && mhoImgUrl">
                                <img :src="mhoImgUrl" class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl object-cover shadow-md ring-4 ring-emerald-500/10">
                            </template>
                            <template x-if="!mhoImgPreview && !mhoImgUrl">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white flex items-center justify-center font-display text-xl sm:text-2xl font-extrabold shadow-md shrink-0"
                                     x-text="getMhoInitials()"></div>
                            </template>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 text-[10px] font-bold uppercase tracking-wider"
                                          x-text="mhoBadge"></span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400" x-text="mhoSubbadge"></span>
                                </div>
                                <h3 class="font-display font-extrabold text-lg sm:text-xl text-slate-900 dark:text-white"
                                    x-text="mhoName"></h3>
                                <p class="text-xs sm:text-sm font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5"
                                   x-text="mhoTitle"></p>
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-800 rounded-xl px-3.5 py-2.5 border border-slate-200/80 dark:border-slate-700 text-xs text-slate-600 dark:text-slate-300 max-w-xs">
                            <span class="font-bold text-slate-900 dark:text-white block" x-text="mhoOversightTitle"></span>
                            <span x-text="mhoOversightDesc"></span>
                        </div>
                    </div>

                    {{-- Doctors Grid Preview --}}
                    <div class="pt-5">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Medical Officers & Clinical Specialists (Direct Reports)
                            </span>
                            <span class="text-[11px] text-slate-400 font-medium" x-text="medOfficers.length + ' Physicians'"></span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <template x-for="(doc, dIdx) in medOfficers" :key="dIdx">
                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold text-xs flex items-center justify-center shrink-0"
                                         x-text="doc.initials || 'DR'"></div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="doc.name"></h4>
                                        <p class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 mt-0.5 truncate" x-text="doc.role"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- 4 Division Selection Cards Preview --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-display font-extrabold text-base sm:text-lg text-slate-900 dark:text-white">
                            Operational Divisions & Departmental Units
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <template x-for="(div, dIdx) in divisions" :key="div.id">
                            <div class="p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 font-bold text-xs flex items-center justify-center"
                                             x-text="div.number || (dIdx + 1)"></div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300"
                                              x-text="getDivisionStaffCount(div) + ' Staff'"></span>
                                    </div>
                                    <h4 class="font-bold text-sm text-slate-900 dark:text-white" x-text="div.title"></h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2" x-text="div.subtitle"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 5: PROCESS STEPS
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'steps'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Facility Process & Patient Flow Steps</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Step-by-step patient pathway displayed along the alternating timeline on facility unit pages.</p>
                    </div>
                </div>
            </div>

            @php
                $unitsList = $facilityUnits->pluck('name', 'slug')->toArray();
                if (empty($unitsList)) {
                    $unitsList = [
                        'main-health-center' => 'Main Health Center',
                        'lying-in-clinic' => 'Lying-in Clinic',
                        'ob-gyn-unit' => 'OB-GYN Unit',
                        'dental-clinic' => 'Dental Clinic',
                        'tb-dots-facility' => 'TB DOTS Facility',
                        'animal-bite-center' => 'Animal Bite Center'
                    ];
                }

                $unitSteps = [];
                foreach($unitsList as $slug => $name) {
                    $key = 'steps_data_' . $slug;
                    if(isset($settings['steps'][$key])) {
                        $unitSteps[$slug] = json_decode($settings['steps'][$key]->value, true) ?: [];
                    } else {
                        $unitSteps[$slug] = [];
                    }
                    if(empty($unitSteps[$slug])) {
                        $unitSteps[$slug] = [
                            ['title' => 'Admission & Triage Check-in', 'description' => 'Present your records or valid ID at the admission desk.', 'image' => ''],
                            ['title' => 'Clinical Monitoring & Vitals', 'description' => 'Staff will record vital signs and perform preliminary screening.', 'image' => '']
                        ];
                    }
                    foreach($unitSteps[$slug] as &$s) {
                        $s['image'] = $s['image'] ?? '';
                    }
                    unset($s);
                }
                $firstSlug = array_key_first($unitsList);
            @endphp

            <div x-data="{ 
                activeUnit: '{{ $firstSlug }}',
                initUnitSteps: {{ json_encode($unitSteps) }},
                unitSteps: {{ json_encode($unitSteps) }},
                resetSteps() {
                    this.unitSteps = JSON.parse(JSON.stringify(this.initUnitSteps));
                },
                addStep(slug) {
                    this.unitSteps[slug].push({ title: '', description: '', image: '' });
                },
                removeStep(slug, index) {
                    this.unitSteps[slug].splice(index, 1);
                    if(this.unitSteps[slug].length === 0) this.addStep(slug);
                }
            }"
            @content-reset.window="resetSteps()">
                {{-- Modern Segmented Unit Switcher Pills --}}
                <div class="flex flex-wrap gap-2 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    @foreach($unitsList as $slug => $name)
                        <button type="button" @click.prevent="activeUnit = '{{ $slug }}'"
                            :class="activeUnit === '{{ $slug }}' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-sm' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/60 dark:border-slate-700/60'"
                            class="px-4 py-2.5 text-xs rounded-xl transition-all cursor-pointer select-none">
                            {{ $name }}
                        </button>
                    @endforeach
                </div>

                {{-- Unit Step Builders --}}
                @foreach($unitsList as $slug => $name)
                    <div x-show="activeUnit === '{{ $slug }}'" x-transition:enter="transition ease-out duration-150" class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Workflow Timeline for: <span class="text-emerald-600 dark:text-emerald-400">{{ $name }}</span></h4>
                            </div>
                            <button type="button" @click="addStep('{{ $slug }}')" 
                                    class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Step</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(step, index) in unitSteps['{{ $slug }}']" :key="index">
                                <div class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 relative space-y-4 shadow-2xs group hover:border-emerald-300 dark:hover:border-emerald-700/70 transition-all">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 font-black text-xs font-mono flex items-center justify-center shadow-2xs" x-text="index + 1"></span>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" x-text="'Patient Flow Step #' + (index + 1)"></span>
                                        </div>
                                        <button type="button" @click="removeStep('{{ $slug }}', index)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" title="Remove Step">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="space-y-3.5">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Step Title</label>
                                                <input type="text" :name="`steps[{{ $slug }}][${index}][title]`" x-model="step.title" placeholder="e.g. Check-in & Triage"
                                                       class="w-full h-11 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Instructions & Clinical Details</label>
                                                <textarea :name="`steps[{{ $slug }}][${index}][description]`" x-model="step.description" rows="3" placeholder="Explain what the patient should do in this phase..."
                                                          class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                            </div>
                                        </div>

                                        {{-- Step Image Upload --}}
                                        <div class="space-y-2"
                                             x-data="{
                                                 isStepDragging: false,
                                                 stepError: null,
                                                 async handleStepFile(file, slug, idx) {
                                                     const input = document.getElementById(`step_image_input_${slug}_${idx}`);
                                                     if (!file) return;

                                                     if (window.SecureImageValidator) {
                                                         const res = await window.SecureImageValidator.validateFile(file);
                                                         if (!res.valid) {
                                                             this.stepError = res.message;
                                                             if (input) input.value = '';
                                                             return;
                                                         }
                                                     }
                                                     this.stepError = null;

                                                     $store.imageCropper.open(file, {
                                                         aspectRatio: 16/9,
                                                         subtitle: 'Landscape crop (16:9) — Process Step Image',
                                                         onApply: (blob, previewUrl) => {
                                                             step._preview = previewUrl;
                                                             this.stepError = null;
                                                             setCroppedFile(input, blob, file.name || ((blob && blob.type === 'image/webp') ? 'step.webp' : 'step.jpg'));
                                                         }
                                                     });
                                                 }
                                             }">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Step Visual (Optional 16:9 Banner)</label>
                                            <input type="hidden" :name="`steps[{{ $slug }}][${index}][image]`" x-model="step.image">
                                            <input type="file" :id="`step_image_input_{{ $slug }}_${index}`" :name="`step_images[{{ $slug }}][${index}]`" 
                                                   accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden"
                                                   @change="if ($event.target.files.length) handleStepFile($event.target.files[0], '{{ $slug }}', index)">

                                            <div class="flex items-center gap-3">
                                                <div @dragover.prevent="isStepDragging = true"
                                                     @dragleave.prevent="isStepDragging = false"
                                                     @drop.prevent="isStepDragging = false; if ($event.dataTransfer.files.length) handleStepFile($event.dataTransfer.files[0], '{{ $slug }}', index)"
                                                     @click="document.getElementById(`step_image_input_{{ $slug }}_${index}`).click()"
                                                     :class="isStepDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800/60'"
                                                     class="flex-1 border-2 border-dashed rounded-2xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5">
                                                    <div class="w-9 h-9 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-2xs">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Choose or drop step image</span>
                                                    <span class="text-[10px] text-slate-400">JPG, PNG, WEBP · 16:9 ratio</span>
                                                </div>
                                                <template x-if="stepError">
                                                    <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-1.5" role="alert">
                                                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        <span x-text="stepError"></span>
                                                    </p>
                                                </template>

                                                <template x-if="step.image || step._preview">
                                                    <div class="relative w-28 h-20 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-2xs shrink-0 group/img">
                                                        <img :src="step._preview || '/uploads/' + step.image" class="w-full h-full object-cover">
                                                        <button type="button" @click="step.image = ''; step._preview = null; const inp = document.getElementById(`step_image_input_{{ $slug }}_${index}`); if(inp) inp.value = '';" 
                                                                class="absolute top-1.5 right-1.5 bg-rose-600 text-white rounded-lg p-1 opacity-0 group-hover/img:opacity-100 transition shadow-xs cursor-pointer" title="Remove image">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 6: FAQs
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'faq'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Frequently Asked Questions</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Common public inquiries and authoritative responses displayed in the public FAQ modal.</p>
                    </div>
                </div>
            </div>

            @php
                $faqs = [];
                if(isset($settings['faq']['faq_items'])) {
                    $faqs = json_decode($settings['faq']['faq_items']->value, true) ?: [];
                }
                if(empty($faqs)) $faqs = [['question' => '', 'answer' => '']];
            @endphp

            <div x-data="{ 
                initFaqs: {{ json_encode($faqs) }},
                faqs: {{ json_encode($faqs) }},
                resetFaqs() {
                    this.faqs = JSON.parse(JSON.stringify(this.initFaqs));
                },
                addFaq() {
                    this.faqs.push({ question: '', answer: '' });
                },
                removeFaq(index) {
                    this.faqs.splice(index, 1);
                    if(this.faqs.length === 0) this.addFaq();
                }
            }"
            @content-reset.window="resetFaqs()" class="space-y-4">
                <template x-for="(faq, index) in faqs" :key="index">
                    <div class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-4 relative shadow-2xs group hover:border-emerald-300 dark:hover:border-emerald-700/70 transition-all">
                        <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 flex items-center justify-center font-black text-xs font-mono shadow-2xs" x-text="'Q' + (index + 1)"></span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" x-text="'Question Item #' + (index + 1)"></span>
                            </div>
                            <button type="button" @click="removeFaq(index)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" title="Remove FAQ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Question Title</label>
                            <input type="text" :name="`faq[${index}][question]`" x-model="faq.question" placeholder="e.g. What are the clinic operating hours and emergency policies?"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Official Response</label>
                            <textarea :name="`faq[${index}][answer]`" x-model="faq.answer" rows="3" placeholder="Provide a helpful, precise answer to constituents..."
                                      class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                        </div>
                    </div>
                </template>

                <div class="pt-2">
                    <button type="button" @click="addFaq()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-2 shadow-sm hover:shadow cursor-pointer active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Question</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 7: PRIVACY POLICY
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'privacy'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Data Privacy Policy (RA 10173 Compliance)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure the official health data privacy governance notice displayed to citizens and patients.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Introduction Text</label>
                    <textarea name="settings[privacy_intro]" rows="3"
                              placeholder="Enter data privacy statement introduction..."
                              class="w-full p-4 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">{{ old('settings.privacy_intro', $settings['privacy']['privacy_intro']->value ?? '') }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Introductory declaration citing compliance with Republic Act No. 10173.</p>
                </div>

                @php
                    $privacyItems = [];
                    if(isset($settings['privacy']['privacy_items'])) {
                        $privacyItems = json_decode($settings['privacy']['privacy_items']->value, true) ?: [];
                    }
                    if(empty($privacyItems)) $privacyItems = [''];
                @endphp

                <div x-data="{ 
                    initItems: {{ json_encode($privacyItems) }},
                    items: {{ json_encode($privacyItems) }},
                    resetItems() {
                        this.items = JSON.parse(JSON.stringify(this.initItems));
                    },
                    addItem() { this.items.push(''); },
                    removeItem(index) {
                        this.items.splice(index, 1);
                        if(this.items.length === 0) this.addItem();
                    }
                }"
                @content-reset.window="resetItems()" class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-4 shadow-2xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Policy Commitments & Core Protections</label>
                        <span class="text-[11px] text-slate-400">Bulleted statutory commitments</span>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex gap-3 items-center">
                                <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 flex items-center justify-center font-black text-xs font-mono shrink-0 shadow-2xs" x-text="index + 1"></span>
                                <input type="text" :name="`privacy_list[]`" x-model="items[index]" placeholder="e.g. Personal information is processed exclusively for medical triage and clinical scheduling."
                                       class="flex-1 h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                <button type="button" @click="removeItem(index)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" title="Remove point">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="addItem()" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Commitment Point</span>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Footer Contact Statement</label>
                    <input type="text" name="settings[privacy_footer]" value="{{ old('settings.privacy_footer', $settings['privacy']['privacy_footer']->value ?? '') }}"
                           placeholder="e.g. Inquiries regarding health records may be directed to our Data Protection Officer at privacy@silang.gov.ph"
                           class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Conclusive statement rendered at the foot of the privacy charter modal.</p>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 8: FOOTER & CONTACT
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'footer'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Footer Address & Municipal Directory</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Official physical location, municipal email, and telephone lines displayed on the portal footer.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Street Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <input type="text" name="settings[footer_address_line1]" value="{{ old('settings.footer_address_line1', $settings['footer']['footer_address_line1']->value ?? 'M.H del Pilar St.') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Street address and building designation.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Municipality & Province</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <input type="text" name="settings[footer_address_line2]" value="{{ old('settings.footer_address_line2', $settings['footer']['footer_address_line2']->value ?? 'Silang, Cavite') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">City or Municipality, Province, and Postal code.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Public Telephone Line</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <input type="text" name="settings[footer_phone]" value="{{ old('settings.footer_phone', $settings['footer']['footer_phone']->value ?? $settings['topbar']['emergency_hotlines']->value ?? '') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Landline telephone or direct health office line.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Official Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input type="email" name="settings[footer_email]" value="{{ old('settings.footer_email', $settings['footer']['footer_email']->value ?? 'contact@silang.gov.ph') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Municipal portal contact or RHU health desk email.</p>
                </div>
            </div>

            {{-- Connected RHU Contact Notice --}}
            <div class="mt-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 dark:text-white">RHU Contact / Landline Displayed in Footer:</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400 ml-1">{{ \App\Models\SiteSetting::get('emergency_hotlines', '(046) 432-1234') }}</span>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">RHU contact/landline is universally synced across the Top Bar, Footer, About Page, and Facility Cards.</p>
                    </div>
                </div>
                <button type="button" @click="activeTab = 'topbar'" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:hover:bg-emerald-900/60 dark:text-emerald-300 font-bold text-xs border border-emerald-200 dark:border-emerald-800 transition cursor-pointer">
                    Edit Contact &rarr;
                </button>
            </div>
        </div>


        {{-- Unified Sticky Save Changes Bar --}}
        <div class="sticky bottom-2 sm:bottom-6 z-30 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl sm:rounded-3xl border border-slate-200/90 dark:border-slate-800 p-3 sm:px-8 sm:py-4 shadow-xl flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center justify-between sm:justify-start gap-2.5 text-xs text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="hidden sm:inline font-medium">Review your adjustments across tabs before publishing updates live.</span>
                    <span class="sm:hidden font-semibold text-slate-700 dark:text-slate-300 text-xs">CMS Actions</span>
                </div>
                <span x-show="isDirty" x-cloak class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 text-[11px] sm:text-xs font-bold border border-amber-300 dark:border-amber-800 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Unsaved Changes</span>
                </span>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-end">
                {{-- Reset / Discard Changes Button --}}
                <button type="button" 
                        @click="showResetModal = true"
                        class="flex-1 sm:flex-none justify-center px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs sm:text-sm shadow-2xs hover:shadow-xs transition-all active:scale-95 flex items-center gap-1.5 sm:gap-2 cursor-pointer"
                        title="Reset all fields back to saved normal values">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-500 dark:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Discard</span>
                </button>

                <button type="button" 
                        @click="validateAndConfirm()"
                        :disabled="isSaving"
                        :class="isSaving ? 'opacity-60 cursor-not-allowed' : ''"
                        class="flex-1 sm:flex-none justify-center px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs sm:text-sm shadow-sm hover:shadow transition-all active:scale-95 flex items-center gap-1.5 sm:gap-2 cursor-pointer disabled:opacity-60">
                    <template x-if="!isSaving">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="isSaving">
                        <svg class="animate-spin w-3.5 h-3.5 sm:w-4 sm:h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span x-text="isSaving ? 'Saving...' : 'Save Changes'">Save Changes</span>
                </button>
            </div>
        </div>

    </form>
</div>

@include('partials.image-cropper')
@endsection

