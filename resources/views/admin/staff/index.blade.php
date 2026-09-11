@extends('layouts.admin')

@section('header', 'Staff Management')

@section('content')
    <div class="bg-slate-50 dark:bg-gray-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-600 overflow-hidden"
        x-data="{
            showAddDoctor: {{ old('_form') == 'add_staff' && $errors->any() ? 'true' : 'false' }},
                    showEditStaff: false,
                    editMember: {},
                    selectedIds: [],
                    selectAll: false,
                    showBulkModal: false,
                    toggleAll() {
                        if (this.selectAll) {
                            this.selectedIds = [...document.querySelectorAll('.rowCheckbox')].map(cb => cb.value);
                        } else {
                            this.selectedIds = [];
                        }
                    },
                    openEdit(member) {
                        this.editMember = member;
                        this.showEditStaff = true;
                    }
                }" @open-edit-staff.window="openEdit($event.detail)">
        <div
            class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white dark:bg-slate-900 relative overflow-hidden">
            <!-- Subtle background pattern -->
            <div class="absolute inset-0 opacity-5 dark:opacity-10 pointer-events-none">
                <svg class="h-full w-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 L100 0" stroke="currentColor" class="text-slate-900 dark:text-white"
                        stroke-width="0.1" />
                    <path d="M0 0 L100 100" stroke="currentColor" class="text-slate-900 dark:text-white"
                        stroke-width="0.1" />
                </svg>
            </div>

            <div class="relative z-10 w-full lg:w-auto">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Staff Roster</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage personnel, roles, and system access
                    levels.</p>

                <!-- Premium Legend -->
                <div class="flex flex-wrap gap-2 mt-4">
                    <div
                        class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Doctor</span>
                    </div>
                    <div
                        class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Pedia</span>
                    </div>
                    <div
                        class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 shadow-[0_0_8px_rgba(250,204,21,0.5)]"></span>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Nurse</span>
                    </div>
                    <div
                        class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Lab</span>
                    </div>
                    <div
                        class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]"></span>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Radio</span>
                    </div>
                    <div
                        class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 px-3 py-1.5 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-[0_0_8px_rgba(148,163,184,0.5)]"></span>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-300">Admin</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div x-show="selectedIds.length > 0" x-cloak>
                    <button @click="showBulkModal = true"
                        class="h-11 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        <span>Archive Selected (<span x-text="selectedIds.length"></span>)</span>
                    </button>
                </div>
                <button @click="showAddDoctor = true"
                    class="h-11 px-5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold text-sm shadow-md shadow-teal-600/25 hover:shadow-teal-600/35 transition-all flex items-center gap-2 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add Staff</span>
                </button>
            </div>
        </div>

        <!-- Advanced Search and Filter Bar -->
        <div class="px-6 py-4 bg-slate-50/80 dark:bg-slate-800/40 border-b border-slate-200/80 dark:border-slate-800">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
                <!-- Search Input -->
                <div class="sm:col-span-2 lg:col-span-5">
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Search Staff Members
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-teal-600 dark:group-focus-within:text-teal-400 transition-colors">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or email..."
                            class="h-11 pl-4 pr-4 block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 text-sm font-medium transition-all placeholder:text-slate-400">
                    </div>
                </div>

                <!-- Role Filter -->
                <div class="sm:col-span-1 lg:col-span-3">
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Role & Department
                    </label>
                    @php
                        $roleOptions = ['all' => 'All Roles'];
                        if (auth()->user()->can('promote-admin')) {
                            $roleOptions['super_admin'] = 'Super Admin';
                            $roleOptions['admin'] = 'Admin';
                        }
                        $roleOptions += [
                            'regular_doctor' => 'Regular Doctor',
                            'pedia_doctor' => 'Pedia Doctor',
                            'laboratory' => 'Laboratory',
                            'radiology' => 'Radiology',
                            'clinical_nurse' => 'Clinical Nurse',
                            'vitals_nurse' => 'Vitals Nurse',
                            'pharmacy' => 'Pharmacist',
                            'information_desk' => 'Front Desk',
                        ];
                    @endphp
                    <x-select 
                        name="role" 
                        :options="$roleOptions" 
                        :value="request('role', 'all')"
                        class="!h-11 !py-0 flex items-center font-semibold"
                    />
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-1 lg:col-span-2">
                    <label class="block text-[11px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Availability Status
                    </label>
                    <x-select 
                        name="status" 
                        :options="[
                            'all' => 'All Status',
                            'Online' => 'Online',
                            'Offline' => 'Offline',
                            'Occupied' => 'Occupied',
                        ]" 
                        :value="request('status', 'all')"
                        class="!h-11 !py-0 flex items-center font-semibold"
                    />
                </div>

                <!-- Actions -->
                <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
                    @if(request()->anyFilled(['q', 'role', 'status']) && (request('q') || request('role') != 'all' || request('status') != 'all'))
                        <a href="{{ route('admin.staff.index') }}"
                            title="Reset Filters"
                            class="h-11 px-3.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl font-bold text-sm border border-slate-300 dark:border-slate-600 transition-all flex items-center justify-center shrink-0 shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                    <button type="submit"
                        class="flex-1 h-11 px-4 bg-slate-900 dark:bg-teal-600 hover:bg-slate-800 dark:hover:bg-teal-700 text-white rounded-xl font-bold text-sm shadow-sm hover:shadow-md transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Apply Filters</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Add Staff Modal -->
        <div x-show="showAddDoctor" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAddDoctor = false">
                </div>
                <div x-data="{
                            step: 1,
                            firstName: @js(old('first_name', '')),
                            middleName: @js(old('middle_name', '')),
                            lastName: @js(old('last_name', '')),
                            suffix: @js(old('suffix', '')),
                            email: @js(old('email', '')),
                            role: @js(old('role', '')),
                            status: @js(old('status', 'Online')),
                            openRoleDropdown: false,
                            openStatusDropdown: false,
                            availableRoles: [
                                @can('promote-admin')
                                { value: 'super_admin', label: 'Super Admin' },
                                { value: 'admin', label: 'Admin' },
                                @endcan
                                { value: 'regular_doctor', label: 'Regular Doctor' },
                                { value: 'pedia_doctor', label: 'Pedia Doctor' },
                                { value: 'laboratory', label: 'Laboratory' },
                                { value: 'radiology', label: 'Radiology' },
                                { value: 'clinical_nurse', label: 'Clinical Nurse' },
                                { value: 'vitals_nurse', label: 'Vitals Nurse (Triage)' },
                                { value: 'pharmacy', label: 'Pharmacist' },
                                { value: 'information_desk', label: 'Front Desk / Information Desk' }
                            ],
                            availableStatuses: [
                                { value: 'Online', label: 'Online', dot: 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' },
                                { value: 'Offline', label: 'Offline', dot: 'bg-slate-400 shadow-[0_0_8px_rgba(148,163,184,0.5)]' },
                                { value: 'Occupied', label: 'Occupied', dot: 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' }
                            ],
                            getRoleLabel(val) {
                                const found = this.availableRoles.find(r => r.value === val);
                                return found ? found.label : (val ? val.replace(/_/g, ' ') : 'Select Role');
                            },
                            getStatusLabel(val) {
                                const found = this.availableStatuses.find(s => s.value === val);
                                return found ? found.label : (val || 'Select Status');
                            },
                            get fullName() {
                                return `${this.firstName} ${this.middleName} ${this.lastName} ${this.suffix}`.replace(/\s+/g, ' ').trim();
                            },
                            avatarFileName: '',
                            avatarPreview: null,
                            isDragging: false,
                            dragCounter: 0,
                            isProcessingAvatar: false,
                            avatarUploadSuccess: false,
                            handleDragEnter(e) {
                                if (e.dataTransfer && e.dataTransfer.types && Array.from(e.dataTransfer.types).includes('Files')) {
                                    this.dragCounter++;
                                    this.isDragging = true;
                                }
                            },
                            handleDragLeave(e) {
                                if (e.dataTransfer && e.dataTransfer.types && Array.from(e.dataTransfer.types).includes('Files')) {
                                    this.dragCounter--;
                                    if (this.dragCounter <= 0) {
                                        this.isDragging = false;
                                        this.dragCounter = 0;
                                    }
                                }
                            },
                            handleDrop(e) {
                                this.isDragging = false;
                                this.dragCounter = 0;
                                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                                    const file = e.dataTransfer.files[0];
                                    if (file && file.type.startsWith('image/')) {
                                        this.handleStaffAvatar(file);
                                    }
                                }
                            },
                            handleStaffAvatar(file) {
                                if (!file || !file.type.startsWith('image/')) return;
                                this.isProcessingAvatar = true;
                                this.avatarUploadSuccess = false;
                                const input = document.getElementById('staff_create_avatar_input');
                                $store.imageCropper.open(file, {
                                    aspectRatio: 1,
                                    circular: true,
                                    subtitle: 'Square crop (1:1) — Staff Avatar',
                                    onApply: (blob, previewUrl) => {
                                        this.isProcessingAvatar = true;
                                        setTimeout(() => {
                                            this.avatarPreview = previewUrl;
                                            this.avatarFileName = file.name;
                                            setCroppedFile(input, blob, file.name || 'avatar.jpg');
                                            this.isProcessingAvatar = false;
                                            this.avatarUploadSuccess = true;
                                            setTimeout(() => { this.avatarUploadSuccess = false; }, 4000);
                                        }, 350);
                                    },
                                    onCancel: () => {
                                        this.isProcessingAvatar = false;
                                    }
                                });
                            },
                            password: '', 
                            passwordConfirmation: '',
                            get strength() {
                                let score = 0;
                                if (this.password.length > 7) score++;
                                if (/[A-Z]/.test(this.password)) score++;
                                if (/[0-9]/.test(this.password)) score++;
                                if (/[^A-Za-z0-9]/.test(this.password)) score++;
                                return score;
                            },
                            get strengthColor() {
                                if (this.strength === 0) return 'bg-slate-200 dark:bg-slate-700';
                                if (this.strength === 1) return 'bg-red-500';
                                if (this.strength === 2) return 'bg-yellow-500';
                                if (this.strength === 3) return 'bg-teal-500';
                                return 'bg-green-500';
                            },
                            get strengthText() {
                                if (this.password.length === 0) return '';
                                if (this.strength <= 1) return 'Weak';
                                if (this.strength === 2) return 'Fair';
                                if (this.strength === 3) return 'Good';
                                return 'Strong';
                            },
                            days: {Mon: false, Tue: false, Wed: false, Thu: false, Fri: false, Sat: false, Sun: false},
                            timeIn: '08:00',
                            timeOut: '17:00',
                            get formattedSchedule() {
                                const selected = Object.keys(this.days).filter(d => this.days[d]);
                                if (selected.length === 0) return '';
                                const formatTime = (time24) => {
                                    if(!time24) return '';
                                    let [hours, minutes] = time24.split(':');
                                    let ampm = hours >= 12 ? 'PM' : 'AM';
                                    hours = hours % 12 || 12;
                                    return `${hours}:${minutes} ${ampm}`;
                                };
                                return `${selected.join(', ')} (${formatTime(this.timeIn)} - ${formatTime(this.timeOut)})`;
                            },
                            get schedulePayload() {
                                const selected = Object.keys(this.days).filter(d => this.days[d]);
                                if (selected.length === 0) return '';
                                return JSON.stringify(selected.map(day => ({
                                    day: day,
                                    time_in: this.timeIn,
                                    time_out: this.timeOut
                                })));
                            },
                            nextStep() {
                                if (this.step === 1 && (!this.firstName || !this.lastName || !this.email)) {
                                    alert('Please fill out all required fields in Step 1.');
                                    return;
                                }
                                if (this.step === 2 && !this.role) {
                                    alert('Please select a role.');
                                    return;
                                }
                                if (this.step < 3) this.step++;
                            },
                            prevStep() {
                                if (this.step > 1) this.step--;
                            }
                        }" class="relative z-10 w-full max-w-2xl lg:max-w-3xl xl:max-w-4xl mx-auto flex flex-col pointer-events-auto">
                    <div
                        class="relative bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl transform transition-all w-full min-h-[580px] md:min-h-[620px] max-h-[92vh] flex flex-col border border-slate-200/80 dark:border-slate-800"
                        @dragenter.prevent="handleDragEnter($event)"
                        @dragleave.prevent="handleDragLeave($event)"
                        @dragover.prevent
                        @drop.prevent="handleDrop($event)">
                        
                        <!-- Full Modal Drag & Drop Overlay (Whole Box Dropzone) -->
                        <div x-show="isDragging"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-98"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-98"
                             style="display: none;"
                             class="absolute inset-0 z-50 rounded-3xl bg-slate-900/90 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center pointer-events-none border-4 border-dashed border-teal-400">
                            <div class="w-20 h-20 rounded-full bg-teal-500/20 ring-8 ring-teal-500/30 flex items-center justify-center text-teal-400 animate-bounce mb-4">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-black text-white tracking-tight mb-1">Drop Image to Add Photo</h4>
                            <p class="text-sm text-teal-200/90 max-w-sm font-medium">
                                Release your photo anywhere inside this box to open the 1:1 image cropper
                            </p>
                            <div class="mt-4 px-4 py-1.5 rounded-full bg-teal-500/20 text-xs font-bold text-teal-300 border border-teal-400/30 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Supports JPG, PNG, WEBP, GIF</span>
                            </div>
                        </div>

                        <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data"
                            class="flex flex-col h-full flex-1"
                            @submit="if(step < 3) { $event.preventDefault(); nextStep(); } else if (password !== passwordConfirmation) { $event.preventDefault(); alert('Passwords do not match'); }">
                            @csrf
                            <input type="hidden" name="_form" value="add_staff">
                            <input type="hidden" name="name" :value="fullName">
                            <input type="hidden" name="schedule" :value="schedulePayload">

                            <div
                                class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex justify-between items-center relative overflow-hidden shrink-0">
                                <!-- Progress Bar -->
                                <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-teal-500 to-emerald-500 transition-all duration-300"
                                    :style="`width: ${(step / 3) * 100}%`"></div>
                                <div class="relative z-10">
                                    <h3 class="text-xl leading-6 font-bold text-slate-900 dark:text-white tracking-tight"
                                        x-text="step === 1 ? 'Step 1: Personal Info' : (step === 2 ? 'Step 2: Role & Schedule' : 'Step 3: Security & Finish')">
                                        Add New Staff Member</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Adding Staff Member as an
                                        Administrator.</p>
                                </div>

                                <button type="button" @click="showAddDoctor = false"
                                    class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer relative z-10">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="px-8 sm:px-10 py-7 overflow-y-auto custom-scrollbar flex-1 space-y-6 min-h-[420px]">
                                <!-- Step 1: Personal Info -->
                                <div x-show="step === 1" class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">First
                                                Name <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="firstName" name="first_name" :required="step === 1"
                                                class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Middle
                                                Name</label>
                                            <input type="text" x-model="middleName" name="middle_name"
                                                class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Last
                                                 Name <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="lastName" name="last_name" :required="step === 1"
                                                class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Suffix
                                                (e.g. Jr., Sr.)</label>
                                            <input type="text" x-model="suffix" name="suffix" placeholder="Jr., Sr., III"
                                                class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Email
                                            Address <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" x-model="email" :required="step === 1"
                                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Profile Photo (Max 2MB)</label>
                                        <input type="file" id="staff_create_avatar_input" name="avatar" accept="image/*" class="hidden"
                                            @change="if ($event.target.files.length) handleStaffAvatar($event.target.files[0])">
                                        
                                        <div class="flex items-center gap-4">
                                            <!-- Preview avatar circle with hover action -->
                                            <div @click="document.getElementById('staff_create_avatar_input').click()"
                                                 class="h-16 w-16 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-800 relative border-2 border-teal-500 shadow-md cursor-pointer group flex-shrink-0 flex items-center justify-center">
                                                <template x-if="avatarPreview">
                                                    <img :src="avatarPreview" class="h-full w-full object-cover" alt="Staff Avatar">
                                                </template>
                                                <template x-if="!avatarPreview">
                                                    <div class="h-full w-full flex items-center justify-center bg-teal-50 dark:bg-teal-950/40 text-teal-600 dark:text-teal-400 font-bold text-xl">
                                                        <span x-text="((firstName ? firstName[0] : '') + (lastName ? lastName[0] : '')).toUpperCase() || 'ST'"></span>
                                                    </div>
                                                </template>
                                                <div class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                </div>
                                                <!-- Processing spinner -->
                                                <div x-show="isProcessingAvatar" style="display: none;" class="absolute inset-0 bg-slate-900/80 flex items-center justify-center text-teal-400">
                                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                </div>
                                            </div>

                                            <!-- Dropzone / Browse banner -->
                                            <div @click="document.getElementById('staff_create_avatar_input').click()"
                                                 class="flex-1 border-2 border-dashed rounded-xl px-4 py-3 text-center cursor-pointer transition-all flex items-center justify-between gap-3 border-slate-300 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 group">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-9 h-9 rounded-lg bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800/60 text-teal-600 dark:text-teal-400 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                    <div class="text-left truncate">
                                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate" x-text="avatarFileName || 'Drag & drop image anywhere or browse'"></p>
                                                        <p class="text-[11px] text-slate-400">1:1 square crop • Max 2MB</p>
                                                    </div>
                                                </div>
                                                <template x-if="avatarUploadSuccess">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 shrink-0">
                                                        Ready
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Role & Schedule -->
                                <div x-show="step === 2" style="display: none;" class="space-y-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <!-- Custom Floating Role Dropdown -->
                                        <div class="relative" @click.outside="openRoleDropdown = false">
                                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                Role & Access Level <span class="text-red-500">*</span>
                                            </label>
                                            <input type="hidden" name="role" :value="role">
                                            
                                            <button type="button" 
                                                    @click="openRoleDropdown = !openRoleDropdown; openStatusDropdown = false"
                                                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                    :class="openRoleDropdown ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                <span class="truncate" :class="!role ? 'text-slate-400 dark:text-slate-500 font-normal' : ''" x-text="getRoleLabel(role)"></span>
                                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0 ml-2"
                                                     :class="openRoleDropdown ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <!-- Custom Floating Menu with rounded-2xl -->
                                            <div x-show="openRoleDropdown" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 style="display: none;"
                                                 class="absolute left-0 right-0 z-50 mt-1.5 max-h-56 overflow-y-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/15 p-1.5 custom-scrollbar backdrop-blur-md">
                                                <template x-for="item in availableRoles" :key="item.value">
                                                    <div @click="role = item.value; openRoleDropdown = false"
                                                         class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between cursor-pointer transition-colors"
                                                         :class="role === item.value 
                                                            ? 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold' 
                                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                        <span x-text="item.label"></span>
                                                        <svg x-show="role === item.value" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Custom Floating Status Dropdown -->
                                        <div class="relative" @click.outside="openStatusDropdown = false">
                                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                                Initial Status <span class="text-red-500">*</span>
                                            </label>
                                            <input type="hidden" name="status" :value="status">

                                            <button type="button" 
                                                    @click="openStatusDropdown = !openStatusDropdown; openRoleDropdown = false"
                                                    class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                    :class="openStatusDropdown ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2.5 h-2.5 rounded-full"
                                                          :class="{
                                                              'bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.5)]': status === 'Online',
                                                              'bg-slate-400 shadow-[0_0_6px_rgba(148,163,184,0.5)]': status === 'Offline',
                                                              'bg-amber-500 shadow-[0_0_6px_rgba(245,158,11,0.5)]': status === 'Occupied'
                                                          }"></span>
                                                    <span class="truncate" x-text="getStatusLabel(status)"></span>
                                                </div>
                                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0 ml-2"
                                                     :class="openStatusDropdown ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <!-- Custom Floating Menu with rounded-2xl -->
                                            <div x-show="openStatusDropdown" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 style="display: none;"
                                                 class="absolute left-0 right-0 z-50 mt-1.5 overflow-hidden rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/15 p-1.5 backdrop-blur-md">
                                                <template x-for="item in availableStatuses" :key="item.value">
                                                    <div @click="status = item.value; openStatusDropdown = false"
                                                         class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between cursor-pointer transition-colors"
                                                         :class="status === item.value 
                                                            ? 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold' 
                                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="w-2.5 h-2.5 rounded-full" :class="item.dot"></span>
                                                            <span x-text="item.label"></span>
                                                        </div>
                                                        <svg x-show="status === item.value" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-1">
                                        <label
                                            class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Schedule
                                            (Optional)</label>
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <template x-for="(isActive, day) in days" :key="day">
                                                <button type="button" @click="days[day] = !days[day]"
                                                    :class="isActive ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                    class="px-3.5 py-1.5 border rounded-xl text-xs font-bold transition-all cursor-pointer focus:outline-none"
                                                    x-text="day">
                                                </button>
                                            </template>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <!-- Time In Picker -->
                                            <div class="relative" x-data="{
                                                open: false,
                                                selectedHour: 8,
                                                selectedMinute: '00',
                                                period: 'AM',
                                                init() {
                                                    this.parseTime(timeIn);
                                                    this.$watch('timeIn', val => this.parseTime(val));
                                                },
                                                parseTime(val) {
                                                    if (!val) return;
                                                    let [h, m] = val.split(':').map(Number);
                                                    this.period = h >= 12 ? 'PM' : 'AM';
                                                    this.selectedHour = h % 12 || 12;
                                                    this.selectedMinute = String(m || 0).padStart(2, '0');
                                                },
                                                setTime(h, m, p) {
                                                    if (h !== undefined) this.selectedHour = h;
                                                    if (m !== undefined) this.selectedMinute = m;
                                                    if (p !== undefined) this.period = p;
                                                    
                                                    let hour24 = parseInt(this.selectedHour, 10);
                                                    if (this.period === 'PM' && hour24 !== 12) hour24 += 12;
                                                    if (this.period === 'AM' && hour24 === 12) hour24 = 0;
                                                    timeIn = `${String(hour24).padStart(2, '0')}:${this.selectedMinute}`;
                                                },
                                                get displayFormatted() {
                                                    return `${String(this.selectedHour).padStart(2, '0')}:${this.selectedMinute} ${this.period}`;
                                                }
                                            }" @click.outside="open = false">
                                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Duty Time In</label>
                                                
                                                <button type="button" @click="open = !open; openRoleDropdown = false; openStatusDropdown = false"
                                                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                        :class="open ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span x-text="displayFormatted"></span>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                                         :class="open ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>

                                                <!-- Custom Popover Panel -->
                                                <div x-show="open" 
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                     style="display: none;"
                                                     class="absolute left-0 z-50 mt-1.5 w-72 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-4 backdrop-blur-md space-y-3.5">
                                                    
                                                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Select Time</span>
                                                        <div class="flex p-0.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                            <button type="button" @click="setTime(undefined, undefined, 'AM')"
                                                                    :class="period === 'AM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                    class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">AM</button>
                                                            <button type="button" @click="setTime(undefined, undefined, 'PM')"
                                                                    :class="period === 'PM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                    class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">PM</button>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hour</div>
                                                        <div class="grid grid-cols-6 gap-1.5">
                                                            <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="h">
                                                                <button type="button" @click="setTime(h, undefined, undefined)"
                                                                        :class="selectedHour === h ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                        class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                        x-text="h"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Minute</div>
                                                        <div class="grid grid-cols-4 gap-1.5">
                                                            <template x-for="m in ['00', '15', '30', '45']" :key="m">
                                                                <button type="button" @click="setTime(undefined, m, undefined)"
                                                                        :class="selectedMinute === m ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                        class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                        x-text="':' + m"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Common Presets</div>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            <button type="button" @click="setTime(8, '00', 'AM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">8:00 AM</button>
                                                            <button type="button" @click="setTime(9, '00', 'AM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">9:00 AM</button>
                                                            <button type="button" @click="setTime(1, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">1:00 PM</button>
                                                            <button type="button" @click="setTime(5, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">5:00 PM</button>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="button" @click="open = false"
                                                            class="w-full py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                                                        Done
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Time Out Picker -->
                                            <div class="relative" x-data="{
                                                open: false,
                                                selectedHour: 5,
                                                selectedMinute: '00',
                                                period: 'PM',
                                                init() {
                                                    this.parseTime(timeOut);
                                                    this.$watch('timeOut', val => this.parseTime(val));
                                                },
                                                parseTime(val) {
                                                    if (!val) return;
                                                    let [h, m] = val.split(':').map(Number);
                                                    this.period = h >= 12 ? 'PM' : 'AM';
                                                    this.selectedHour = h % 12 || 12;
                                                    this.selectedMinute = String(m || 0).padStart(2, '0');
                                                },
                                                setTime(h, m, p) {
                                                    if (h !== undefined) this.selectedHour = h;
                                                    if (m !== undefined) this.selectedMinute = m;
                                                    if (p !== undefined) this.period = p;
                                                    
                                                    let hour24 = parseInt(this.selectedHour, 10);
                                                    if (this.period === 'PM' && hour24 !== 12) hour24 += 12;
                                                    if (this.period === 'AM' && hour24 === 12) hour24 = 0;
                                                    timeOut = `${String(hour24).padStart(2, '0')}:${this.selectedMinute}`;
                                                },
                                                get displayFormatted() {
                                                    return `${String(this.selectedHour).padStart(2, '0')}:${this.selectedMinute} ${this.period}`;
                                                }
                                            }" @click.outside="open = false">
                                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1.5 font-medium">Duty Time Out</label>
                                                
                                                <button type="button" @click="open = !open; openRoleDropdown = false; openStatusDropdown = false"
                                                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                        :class="open ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span x-text="displayFormatted"></span>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                                         :class="open ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>

                                                <!-- Custom Popover Panel -->
                                                <div x-show="open" 
                                                     x-transition:enter="transition ease-out duration-150"
                                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave="transition ease-in duration-100"
                                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                     style="display: none;"
                                                     class="absolute left-0 sm:left-auto right-0 z-50 mt-1.5 w-72 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-4 backdrop-blur-md space-y-3.5">
                                                    
                                                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Select Time</span>
                                                        <div class="flex p-0.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                            <button type="button" @click="setTime(undefined, undefined, 'AM')"
                                                                    :class="period === 'AM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                    class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">AM</button>
                                                            <button type="button" @click="setTime(undefined, undefined, 'PM')"
                                                                    :class="period === 'PM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                    class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">PM</button>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hour</div>
                                                        <div class="grid grid-cols-6 gap-1.5">
                                                            <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="h">
                                                                <button type="button" @click="setTime(h, undefined, undefined)"
                                                                        :class="selectedHour === h ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                        class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                        x-text="h"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Minute</div>
                                                        <div class="grid grid-cols-4 gap-1.5">
                                                            <template x-for="m in ['00', '15', '30', '45']" :key="m">
                                                                <button type="button" @click="setTime(undefined, m, undefined)"
                                                                        :class="selectedMinute === m ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                        class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                        x-text="':' + m"></button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Common Presets</div>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            <button type="button" @click="setTime(12, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">12:00 PM</button>
                                                            <button type="button" @click="setTime(1, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">1:00 PM</button>
                                                            <button type="button" @click="setTime(5, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">5:00 PM</button>
                                                            <button type="button" @click="setTime(6, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">6:00 PM</button>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="button" @click="open = false"
                                                            class="w-full py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                                                        Done
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-3 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                            <svg class="shrink-0 w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span
                                                x-text="formattedSchedule ? 'Preview: ' + formattedSchedule : 'No schedule selected'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Security -->
                                <div x-show="step === 3" style="display: none;" class="space-y-6">
                                    <div
                                        class="bg-amber-50 dark:bg-amber-950/30 p-4 rounded-2xl border border-amber-200/80 dark:border-amber-800/60">
                                        <p class="text-xs sm:text-sm text-amber-900 dark:text-amber-200">
                                            <strong>Security Requirements:</strong> Create a temporary password for the
                                            staff member. They will be required to change it upon their first login.
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Temporary
                                                Password <span class="text-red-500">*</span></label>
                                            <input type="password" name="password" x-model="password" :required="step === 3"
                                                class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                            <!-- Strength Meter -->
                                            <div class="mt-2.5 flex items-center gap-2" x-show="password.length > 0">
                                                <div class="flex-1 flex gap-1 h-1.5">
                                                    <div class="flex-1 rounded-full transition-colors duration-300"
                                                        :class="strength >= 1 ? strengthColor : 'bg-slate-200 dark:bg-slate-700'">
                                                    </div>
                                                    <div class="flex-1 rounded-full transition-colors duration-300"
                                                        :class="strength >= 2 ? strengthColor : 'bg-slate-200 dark:bg-slate-700'">
                                                    </div>
                                                    <div class="flex-1 rounded-full transition-colors duration-300"
                                                        :class="strength >= 3 ? strengthColor : 'bg-slate-200 dark:bg-slate-700'">
                                                    </div>
                                                    <div class="flex-1 rounded-full transition-colors duration-300"
                                                        :class="strength >= 4 ? strengthColor : 'bg-slate-200 dark:bg-slate-700'">
                                                    </div>
                                                </div>
                                                <span class="text-xs font-bold"
                                                    :class="{'text-red-500': strength <= 1, 'text-yellow-500': strength === 2, 'text-teal-500': strength === 3, 'text-green-500': strength === 4}"
                                                    x-text="strengthText"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Confirm
                                                Password <span class="text-red-500">*</span></label>
                                            <input type="password" name="password_confirmation"
                                                x-model="passwordConfirmation" :required="step === 3"
                                                class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                            <p x-show="passwordConfirmation.length > 0 && password !== passwordConfirmation"
                                                class="text-xs text-red-500 font-semibold mt-1.5">Passwords do not match.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="px-8 py-5 bg-slate-50/80 dark:bg-slate-900/80 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center shrink-0">
                                <div>
                                    <button type="button" x-show="step > 1" @click="prevStep()"
                                        class="h-11 px-4 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        <span>Back</span>
                                    </button>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click="showAddDoctor = false"
                                        class="h-11 px-5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl shadow-2xs transition-all cursor-pointer">
                                        Cancel
                                    </button>

                                    <button type="button" x-show="step < 3" @click="nextStep()"
                                        class="h-11 px-6 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-sm font-bold text-white rounded-xl shadow-md shadow-teal-600/20 hover:shadow-teal-600/30 transition-all flex items-center gap-1.5 cursor-pointer active:scale-95">
                                        <span>Next</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>

                                    <button type="submit" x-show="step === 3" :disabled="password !== passwordConfirmation"
                                        :class="password !== passwordConfirmation ? 'opacity-50 cursor-not-allowed' : 'hover:from-teal-700 hover:to-emerald-700 active:scale-95 cursor-pointer shadow-md shadow-teal-600/20'"
                                        class="h-11 px-6 bg-gradient-to-r from-teal-600 to-emerald-600 text-sm font-bold text-white rounded-xl transition-all flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Create Staff</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Staff Modal -->
        <div x-show="showEditStaff" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-data="{
                editMember: {},
                firstName: '',
                middleName: '',
                lastName: '',
                suffix: '',
                role: 'regular_doctor',
                status: 'Online',
                days: {Mon: false, Tue: false, Wed: false, Thu: false, Fri: false, Sat: false, Sun: false},
                timeIn: '08:00',
                timeOut: '17:00',
                editAvatarFileName: '',
                editAvatarPreview: null,
                dragCounter: 0,
                isDragging: false,
                isProcessingAvatar: false,
                avatarUploadSuccess: false,
                openRoleDropdown: false,
                openStatusDropdown: false,
                
                availableRoles: [
                    @can('promote-admin')
                    { value: 'super_admin', label: 'Super Admin' },
                    { value: 'admin', label: 'Admin' },
                    @endcan
                    { value: 'regular_doctor', label: 'Regular Doctor' },
                    { value: 'pedia_doctor', label: 'Pedia Doctor' },
                    { value: 'laboratory', label: 'Laboratory' },
                    { value: 'radiology', label: 'Radiology' },
                    { value: 'clinical_nurse', label: 'Clinical Nurse' },
                    { value: 'vitals_nurse', label: 'Vitals Nurse (Triage)' },
                    { value: 'pharmacy', label: 'Pharmacist' },
                    { value: 'information_desk', label: 'Front Desk / Information Desk' }
                ],

                availableStatuses: [
                    { value: 'Online', label: 'Online', dot: 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' },
                    { value: 'Offline', label: 'Offline', dot: 'bg-slate-400 shadow-[0_0_8px_rgba(148,163,184,0.5)]' },
                    { value: 'Occupied', label: 'Occupied', dot: 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' }
                ],

                getRoleLabel(val) {
                    const found = this.availableRoles.find(r => r.value === val);
                    return found ? found.label : (val ? val.replace(/_/g, ' ') : 'Select Role');
                },

                getStatusLabel(val) {
                    const found = this.availableStatuses.find(s => s.value === val);
                    return found ? found.label : (val || 'Select Status');
                },

                get fullName() {
                    return `${this.firstName} ${this.middleName} ${this.lastName} ${this.suffix}`.replace(/\s+/g, ' ').trim();
                },
                get formattedSchedule() {
                    const selected = Object.keys(this.days).filter(d => this.days[d]);
                    if (selected.length === 0) return '';
                    const formatTime = (t) => {
                        if(!t) return '';
                        let [h, m] = t.split(':');
                        const ap = h >= 12 ? 'PM' : 'AM';
                        h = h % 12 || 12;
                        return `${h}:${m} ${ap}`;
                    };
                    return `${selected.join(', ')} (${formatTime(this.timeIn)} - ${formatTime(this.timeOut)})`;
                },
                get schedulePayload() {
                    const selected = Object.keys(this.days).filter(d => this.days[d]);
                    if (selected.length === 0) return '';
                    return JSON.stringify(selected.map(day => ({
                        day: day,
                        time_in: this.timeIn,
                        time_out: this.timeOut
                    })));
                },

                init() {
                    if (this.editMember && this.editMember.id) {
                        this.loadMember(this.editMember);
                    }
                },

                loadMember(member) {
                    if (!member || !member.id) return;
                    this.editMember = member;
                    this.editAvatarPreview = null;
                    this.editAvatarFileName = '';
                    this.dragCounter = 0;
                    this.isDragging = false;
                    this.isProcessingAvatar = false;
                    this.avatarUploadSuccess = false;
                    this.openRoleDropdown = false;
                    this.openStatusDropdown = false;
                    this.role = member.role || 'regular_doctor';
                    const s = (member.status || 'Offline').toLowerCase();
                    if (['online', 'present', 'active', 'in office'].includes(s)) {
                        this.status = 'Online';
                    } else if (['occupied', 'seminar', 'on seminar', 'in meeting'].includes(s)) {
                        this.status = 'Occupied';
                    } else {
                        this.status = 'Offline';
                    }
                    
                    // Clean Name parsing
                    let cleanName = (member.name || '').replace(/^(Dr\.|Nurse|MedTech|RadTech)\s+/i, '').trim();
                    let parts = cleanName.split(/\s+/).filter(Boolean);
                    let suffixes = ['Jr.', 'Sr.', 'III', 'IV', 'II', 'Jr', 'Sr'];

                    if (parts.length > 0 && suffixes.includes(parts[parts.length - 1])) {
                        this.suffix = parts.pop();
                    } else {
                        this.suffix = '';
                    }

                    if (parts.length === 0) {
                        this.firstName = '';
                        this.middleName = '';
                        this.lastName = '';
                    } else if (parts.length === 1) {
                        this.firstName = parts[0];
                        this.middleName = '';
                        this.lastName = '';
                    } else if (parts.length === 2) {
                        this.firstName = parts[0];
                        this.middleName = '';
                        this.lastName = parts[1];
                    } else {
                        this.firstName = parts.shift();
                        this.lastName = parts.pop();
                        this.middleName = parts.join(' ');
                    }

                    // Schedule parsing
                    Object.keys(this.days).forEach(d => this.days[d] = false);
                    this.timeIn = '08:00';
                    this.timeOut = '17:00';
                    if (member.schedule) {
                        const s = member.schedule;
                        ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].forEach(d => {
                            if (s.includes(d)) this.days[d] = true;
                        });
                        const tMatch = s.match(/\((.*?) - (.*?)\)/);
                        if (tMatch) {
                            const to24 = (t) => {
                                const p = t.trim().split(' ');
                                const ap = p[1];
                                let [h, m] = p[0].split(':').map(Number);
                                if (ap === 'PM' && h !== 12) h += 12;
                                if (ap === 'AM' && h === 12) h = 0;
                                return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                            };
                            try {
                                this.timeIn = to24(tMatch[1]);
                                this.timeOut = to24(tMatch[2]);
                            } catch(e) {}
                        }
                    }
                },

                handleDragEnter(e) {
                    if (e.dataTransfer && e.dataTransfer.types && Array.from(e.dataTransfer.types).includes('Files')) {
                        this.dragCounter++;
                        this.isDragging = true;
                    }
                },

                handleDragLeave(e) {
                    if (e.dataTransfer && e.dataTransfer.types && Array.from(e.dataTransfer.types).includes('Files')) {
                        this.dragCounter--;
                        if (this.dragCounter <= 0) {
                            this.isDragging = false;
                            this.dragCounter = 0;
                        }
                    }
                },

                handleDrop(e) {
                    this.isDragging = false;
                    this.dragCounter = 0;
                    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                        const file = e.dataTransfer.files[0];
                        if (file && file.type.startsWith('image/')) {
                            this.handleStaffEditAvatar(file);
                        }
                    }
                },

                handleStaffEditAvatar(file) {
                    if (!file || !file.type.startsWith('image/')) return;
                    this.isProcessingAvatar = true;
                    this.avatarUploadSuccess = false;
                    const input = document.getElementById('staff_edit_avatar_input');
                    $store.imageCropper.open(file, {
                        aspectRatio: 1,
                        circular: true,
                        subtitle: 'Square crop (1:1) — Staff Avatar',
                        onApply: (blob, previewUrl) => {
                            this.isProcessingAvatar = true;
                            setTimeout(() => {
                                this.editAvatarPreview = previewUrl;
                                this.editAvatarFileName = file.name;
                                setCroppedFile(input, blob, file.name || 'avatar.jpg');
                                this.isProcessingAvatar = false;
                                this.avatarUploadSuccess = true;
                                setTimeout(() => { this.avatarUploadSuccess = false; }, 4000);
                            }, 350);
                        },
                        onCancel: () => {
                            this.isProcessingAvatar = false;
                        }
                    });
                }
             }"
             @open-edit-staff.window="loadMember($event.detail)">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"
                    @click="showEditStaff = false">
                </div>
                
                <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl transform transition-all sm:max-w-2xl lg:max-w-3xl w-full z-10 max-h-[92vh] flex flex-col border border-slate-200/80 dark:border-slate-800 overflow-hidden"
                     @dragenter.prevent="handleDragEnter($event)"
                     @dragleave.prevent="handleDragLeave($event)"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event)">
                    
                    <!-- Full Modal Drag & Drop Overlay (Whole Box Dropzone) -->
                    <div x-show="isDragging"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-98"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-98"
                         style="display: none;"
                         class="absolute inset-0 z-50 rounded-3xl bg-slate-900/90 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center pointer-events-none border-4 border-dashed border-teal-400">
                        <div class="w-20 h-20 rounded-full bg-teal-500/20 ring-8 ring-teal-500/30 flex items-center justify-center text-teal-400 animate-bounce mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-xl font-black text-white tracking-tight mb-1">Drop Image to Update Photo</h4>
                        <p class="text-sm text-teal-200/90 max-w-sm font-medium">
                            Release your photo anywhere inside this box to open the 1:1 image cropper
                        </p>
                        <div class="mt-4 px-4 py-1.5 rounded-full bg-teal-500/20 text-xs font-bold text-teal-300 border border-teal-400/30 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Supports JPG, PNG, WEBP, GIF</span>
                        </div>
                    </div>

                    <form :action="'/admin/staff/' + editMember.id" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
                        @csrf
                        @method('PUT')
                        
                        <!-- Modal Header -->
                        <div class="px-8 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/80 flex justify-between items-center shrink-0 rounded-t-3xl">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800/60 text-teal-600 dark:text-teal-400 flex items-center justify-center shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Edit Staff Member</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Updating credentials & schedule for <span class="font-semibold text-teal-600 dark:text-teal-400" x-text="editMember.name || 'Staff Member'"></span>
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="showEditStaff = false"
                                class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Scrollable Body -->
                        <div class="px-6 sm:px-8 py-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                            
                            <!-- Profile Picture & Identity Banner -->
                            <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/70 dark:from-slate-800/70 dark:to-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-center gap-5">
                                <div class="relative group shrink-0">
                                    <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-2xl ring-4 shadow-md overflow-hidden bg-gradient-to-br from-teal-500 to-emerald-700 flex items-center justify-center text-white text-2xl font-black relative"
                                         :class="avatarUploadSuccess ? 'ring-emerald-400 dark:ring-emerald-500 ring-offset-2' : 'ring-white dark:ring-slate-700'">
                                        
                                        <!-- Dynamic Avatar Preview or Existing URL or Initials -->
                                        <template x-if="editAvatarPreview">
                                            <img :src="editAvatarPreview" class="w-full h-full object-cover" alt="Avatar Preview">
                                        </template>
                                        <template x-if="!editAvatarPreview && editMember.avatar_url">
                                            <img :src="editMember.avatar_url" class="w-full h-full object-cover" alt="Staff Avatar">
                                        </template>
                                        <template x-if="!editAvatarPreview && !editMember.avatar_url">
                                            <span x-text="editMember.initials || 'ST'"></span>
                                        </template>

                                        <!-- Processing / Loading Overlay -->
                                        <div x-show="isProcessingAvatar"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             style="display: none;"
                                             class="absolute inset-0 bg-slate-900/80 rounded-2xl flex flex-col items-center justify-center text-white z-20">
                                            <svg class="w-6 h-6 animate-spin text-teal-400" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-[10px] font-bold text-teal-200 mt-1">Processing...</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Hover Change Overlay -->
                                    <button type="button" 
                                            @click="document.getElementById('staff_edit_avatar_input').click()"
                                            class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[11px] font-bold gap-1 cursor-pointer z-10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>Change</span>
                                    </button>
                                </div>

                                <div class="flex-1 w-full space-y-2 text-center sm:text-left">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <div>
                                            <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                                                <h4 class="text-base font-bold text-slate-900 dark:text-white" x-text="editMember.name || 'Staff Member'"></h4>
                                                
                                                <!-- Success Finished Animation Badge -->
                                                <div x-show="avatarUploadSuccess"
                                                     x-transition:enter="transition ease-out duration-300"
                                                     x-transition:enter-start="opacity-0 scale-90 translate-y-1"
                                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                     x-transition:leave="transition ease-in duration-300"
                                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                     x-transition:leave-end="opacity-0 scale-90 translate-y-1"
                                                     style="display: none;"
                                                     class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-700/60 text-emerald-800 dark:text-emerald-300 text-[11px] font-bold shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <span>Photo ready!</span>
                                                </div>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="editMember.email"></p>
                                        </div>
                                        <div>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider capitalize"
                                                  :class="{
                                                      'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300': role && role.includes('doctor'),
                                                      'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300': role && role.includes('nurse'),
                                                      'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300': role && (role === 'laboratory' || role === 'radiology'),
                                                      'bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300': role && role.includes('admin'),
                                                      'bg-teal-100 dark:bg-teal-950/60 text-teal-800 dark:text-teal-300': role === 'pharmacy',
                                                      'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300': !role || role === 'information_desk'
                                                  }"
                                                  x-text="getRoleLabel(role)"></span>
                                        </div>
                                    </div>

                                    <!-- Upload Input & Quick Browse / Drop Action -->
                                    <input type="file" id="staff_edit_avatar_input" name="avatar" accept="image/*" class="hidden"
                                           @change="if ($event.target.files.length) handleStaffEditAvatar($event.target.files[0])">
                                    
                                    <div class="pt-1 flex items-center gap-2">
                                        <div @click="document.getElementById('staff_edit_avatar_input').click()"
                                             class="flex-1 border border-dashed rounded-xl px-3.5 py-2 cursor-pointer transition-all flex items-center justify-center gap-2 group border-slate-300/80 dark:border-slate-600/80 bg-white dark:bg-slate-900/60 hover:border-teal-500 hover:bg-teal-50/30 dark:hover:bg-slate-800">
                                            <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 truncate" 
                                                  x-text="editAvatarFileName ? 'Selected: ' + editAvatarFileName : 'Click to browse or drop photo anywhere in modal (1:1 crop)'"></span>
                                        </div>

                                        <template x-if="editAvatarPreview">
                                            <button type="button" 
                                                    @click="editAvatarPreview = null; editAvatarFileName = ''; document.getElementById('staff_edit_avatar_input').value = ''"
                                                    class="p-2 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all border border-slate-200 dark:border-slate-700 cursor-pointer shrink-0 flex items-center gap-1 text-xs font-bold"
                                                    title="Revert photo changes">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                <span class="hidden sm:inline">Revert</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Section 1: Personal Information -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span>Personal Information</span>
                                </div>

                                <input type="hidden" name="name" :value="fullName">
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                            First Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" x-model="firstName" required
                                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                            Middle Name
                                        </label>
                                        <input type="text" x-model="middleName"
                                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                            Last Name <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" x-model="lastName" required
                                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                            Suffix
                                        </label>
                                        <input type="text" x-model="suffix" placeholder="Jr., Sr., III"
                                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                            Email Address <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" name="email" :value="editMember.email" required
                                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                            <span>New Password</span>
                                            <span class="text-slate-400 font-normal lowercase">(leave blank to keep current)</span>
                                        </label>
                                        <input type="password" name="password" placeholder="••••••••"
                                            class="block w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3.5 py-2.5 text-sm font-medium shadow-2xs focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all">
                                    </div>
                                </div>
                            </div>

                            <!-- Form Section 2: Role & Availability (Custom Floating Rounded Dropdowns) -->
                            <div class="space-y-4 pt-2">
                                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span>Role & Availability Status</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Custom Floating Role Dropdown -->
                                    <div class="relative" @click.outside="openRoleDropdown = false">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                            Role & Access Level <span class="text-red-500">*</span>
                                        </label>
                                        <input type="hidden" name="role" :value="role">
                                        
                                        <button type="button" 
                                                @click="openRoleDropdown = !openRoleDropdown; openStatusDropdown = false"
                                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                :class="openRoleDropdown ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                            <span class="truncate" x-text="getRoleLabel(role)"></span>
                                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0 ml-2"
                                                 :class="openRoleDropdown ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <!-- Custom Floating Menu with rounded-2xl -->
                                        <div x-show="openRoleDropdown" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                             style="display: none;"
                                             class="absolute left-0 right-0 z-50 mt-1.5 max-h-60 overflow-y-auto rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/15 p-1.5 custom-scrollbar backdrop-blur-md">
                                            <template x-for="item in availableRoles" :key="item.value">
                                                <div @click="role = item.value; openRoleDropdown = false"
                                                     class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between cursor-pointer transition-colors"
                                                     :class="role === item.value 
                                                        ? 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold' 
                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                    <span x-text="item.label"></span>
                                                    <svg x-show="role === item.value" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Custom Floating Status Dropdown -->
                                    <div class="relative" @click.outside="openStatusDropdown = false">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                            Current Status <span class="text-red-500">*</span>
                                        </label>
                                        <input type="hidden" name="status" :value="status">

                                        <button type="button" 
                                                @click="openStatusDropdown = !openStatusDropdown; openRoleDropdown = false"
                                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                :class="openStatusDropdown ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full"
                                                      :class="{
                                                          'bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.5)]': status === 'Online',
                                                          'bg-slate-400 shadow-[0_0_6px_rgba(148,163,184,0.5)]': status === 'Offline',
                                                          'bg-amber-500 shadow-[0_0_6px_rgba(245,158,11,0.5)]': status === 'Occupied'
                                                      }"></span>
                                                <span class="truncate" x-text="getStatusLabel(status)"></span>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0 ml-2"
                                                 :class="openStatusDropdown ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <!-- Custom Floating Menu with rounded-2xl -->
                                        <div x-show="openStatusDropdown" 
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                             style="display: none;"
                                             class="absolute left-0 right-0 z-50 mt-1.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl shadow-slate-900/15 p-1.5 backdrop-blur-md">
                                            <template x-for="item in availableStatuses" :key="item.value">
                                                <div @click="status = item.value; openStatusDropdown = false"
                                                     class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium flex items-center justify-between cursor-pointer transition-colors"
                                                     :class="status === item.value 
                                                        ? 'bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold' 
                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2.5 h-2.5 rounded-full" :class="item.dot"></span>
                                                        <span x-text="item.label"></span>
                                                    </div>
                                                    <svg x-show="status === item.value" class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Section 3: Work Schedule -->
                            <div class="space-y-4 pt-2">
                                <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>Work Schedule (Optional)</span>
                                </div>

                                <input type="hidden" name="schedule" :value="schedulePayload">
                                
                                <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-700/80 space-y-4">
                                    <div>
                                        <span class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-2">Duty Days</span>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="(isActive, day) in days" :key="day">
                                                <button type="button" @click="days[day] = !days[day]"
                                                    :class="isActive 
                                                        ? 'bg-teal-600 text-white shadow-sm ring-2 ring-teal-500/30 font-bold border-teal-600' 
                                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 font-medium'"
                                                    class="px-3.5 py-1.5 border rounded-xl text-xs transition-all focus:outline-none cursor-pointer"
                                                    x-text="day">
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Time In Picker -->
                                        <div class="relative" x-data="{
                                            open: false,
                                            selectedHour: 8,
                                            selectedMinute: '00',
                                            period: 'AM',
                                            init() {
                                                this.parseTime(timeIn);
                                                this.$watch('timeIn', val => this.parseTime(val));
                                            },
                                            parseTime(val) {
                                                if (!val) return;
                                                let [h, m] = val.split(':').map(Number);
                                                this.period = h >= 12 ? 'PM' : 'AM';
                                                this.selectedHour = h % 12 || 12;
                                                this.selectedMinute = String(m || 0).padStart(2, '0');
                                            },
                                            setTime(h, m, p) {
                                                if (h !== undefined) this.selectedHour = h;
                                                if (m !== undefined) this.selectedMinute = m;
                                                if (p !== undefined) this.period = p;
                                                
                                                let hour24 = parseInt(this.selectedHour, 10);
                                                if (this.period === 'PM' && hour24 !== 12) hour24 += 12;
                                                if (this.period === 'AM' && hour24 === 12) hour24 = 0;
                                                timeIn = `${String(hour24).padStart(2, '0')}:${this.selectedMinute}`;
                                            },
                                            get displayFormatted() {
                                                return `${String(this.selectedHour).padStart(2, '0')}:${this.selectedMinute} ${this.period}`;
                                            }
                                        }" @click.outside="open = false">
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Duty Time In</label>
                                            
                                            <button type="button" @click="open = !open; openRoleDropdown = false; openStatusDropdown = false"
                                                    class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                    :class="open ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span x-text="displayFormatted"></span>
                                                </div>
                                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                                     :class="open ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <!-- Custom Popover Panel -->
                                            <div x-show="open" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 style="display: none;"
                                                 class="absolute left-0 z-50 mt-1.5 w-72 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-4 backdrop-blur-md space-y-3.5">
                                                
                                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Select Time</span>
                                                    <div class="flex p-0.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                        <button type="button" @click="setTime(undefined, undefined, 'AM')"
                                                                :class="period === 'AM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">AM</button>
                                                        <button type="button" @click="setTime(undefined, undefined, 'PM')"
                                                                :class="period === 'PM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">PM</button>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hour</div>
                                                    <div class="grid grid-cols-6 gap-1.5">
                                                        <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="h">
                                                            <button type="button" @click="setTime(h, undefined, undefined)"
                                                                    :class="selectedHour === h ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                    class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                    x-text="h"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Minute</div>
                                                    <div class="grid grid-cols-4 gap-1.5">
                                                        <template x-for="m in ['00', '15', '30', '45']" :key="m">
                                                            <button type="button" @click="setTime(undefined, m, undefined)"
                                                                    :class="selectedMinute === m ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                    class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                    x-text="':' + m"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Common Presets</div>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        <button type="button" @click="setTime(8, '00', 'AM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">8:00 AM</button>
                                                        <button type="button" @click="setTime(9, '00', 'AM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">9:00 AM</button>
                                                        <button type="button" @click="setTime(1, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">1:00 PM</button>
                                                        <button type="button" @click="setTime(5, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">5:00 PM</button>
                                                    </div>
                                                </div>
                                                
                                                <button type="button" @click="open = false"
                                                        class="w-full py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                                                    Done
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Time Out Picker -->
                                        <div class="relative" x-data="{
                                            open: false,
                                            selectedHour: 5,
                                            selectedMinute: '00',
                                            period: 'PM',
                                            init() {
                                                this.parseTime(timeOut);
                                                this.$watch('timeOut', val => this.parseTime(val));
                                            },
                                            parseTime(val) {
                                                if (!val) return;
                                                let [h, m] = val.split(':').map(Number);
                                                this.period = h >= 12 ? 'PM' : 'AM';
                                                this.selectedHour = h % 12 || 12;
                                                this.selectedMinute = String(m || 0).padStart(2, '0');
                                            },
                                            setTime(h, m, p) {
                                                if (h !== undefined) this.selectedHour = h;
                                                if (m !== undefined) this.selectedMinute = m;
                                                if (p !== undefined) this.period = p;
                                                
                                                let hour24 = parseInt(this.selectedHour, 10);
                                                if (this.period === 'PM' && hour24 !== 12) hour24 += 12;
                                                if (this.period === 'AM' && hour24 === 12) hour24 = 0;
                                                timeOut = `${String(hour24).padStart(2, '0')}:${this.selectedMinute}`;
                                            },
                                            get displayFormatted() {
                                                return `${String(this.selectedHour).padStart(2, '0')}:${this.selectedMinute} ${this.period}`;
                                            }
                                        }" @click.outside="open = false">
                                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1.5">Duty Time Out</label>
                                            
                                            <button type="button" @click="open = !open; openRoleDropdown = false; openStatusDropdown = false"
                                                    class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-all cursor-pointer"
                                                    :class="open ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span x-text="displayFormatted"></span>
                                                </div>
                                                <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0"
                                                     :class="open ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <!-- Custom Popover Panel -->
                                            <div x-show="open" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 style="display: none;"
                                                 class="absolute left-0 sm:left-auto right-0 z-50 mt-1.5 w-72 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-4 backdrop-blur-md space-y-3.5">
                                                
                                                <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-700">
                                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">Select Time</span>
                                                    <div class="flex p-0.5 rounded-xl bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                        <button type="button" @click="setTime(undefined, undefined, 'AM')"
                                                                :class="period === 'AM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">AM</button>
                                                        <button type="button" @click="setTime(undefined, undefined, 'PM')"
                                                                :class="period === 'PM' ? 'bg-teal-600 text-white shadow-xs font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white font-medium'"
                                                                class="px-2.5 py-1 rounded-lg text-xs transition-all cursor-pointer">PM</button>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Hour</div>
                                                    <div class="grid grid-cols-6 gap-1.5">
                                                        <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="h">
                                                            <button type="button" @click="setTime(h, undefined, undefined)"
                                                                    :class="selectedHour === h ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                    class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                    x-text="h"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Minute</div>
                                                    <div class="grid grid-cols-4 gap-1.5">
                                                        <template x-for="m in ['00', '15', '30', '45']" :key="m">
                                                            <button type="button" @click="setTime(undefined, m, undefined)"
                                                                    :class="selectedMinute === m ? 'bg-teal-600 text-white shadow-xs font-bold' : 'bg-slate-50 dark:bg-slate-750 text-slate-700 dark:text-slate-300 hover:bg-teal-50 dark:hover:bg-slate-700 hover:text-teal-600'"
                                                                    class="py-1.5 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                    x-text="':' + m"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Common Presets</div>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        <button type="button" @click="setTime(12, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">12:00 PM</button>
                                                        <button type="button" @click="setTime(1, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">1:00 PM</button>
                                                        <button type="button" @click="setTime(5, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">5:00 PM</button>
                                                        <button type="button" @click="setTime(6, '00', 'PM'); open = false" class="px-2 py-1 rounded-md bg-slate-100 dark:bg-slate-700 hover:bg-teal-50 dark:hover:bg-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-200 transition-colors cursor-pointer">6:00 PM</button>
                                                    </div>
                                                </div>
                                                
                                                <button type="button" @click="open = false"
                                                        class="w-full py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs cursor-pointer">
                                                    Done
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 pt-1">
                                        <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-medium" x-text="formattedSchedule ? 'Current Schedule: ' + formattedSchedule : 'No schedule days selected'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="px-8 py-4 bg-slate-50/70 dark:bg-slate-900/80 border-t border-slate-100 dark:border-slate-800 flex justify-end items-center gap-3 shrink-0 rounded-b-3xl">
                            <button type="button" @click="showEditStaff = false"
                                class="px-5 py-2.5 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xs transition-all cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-sm font-bold text-white rounded-xl shadow-md shadow-teal-600/20 hover:shadow-teal-600/30 transition-all cursor-pointer flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Save Changes</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
                <div class="overflow-x-auto overflow-y-auto max-h-[600px] custom-scrollbar">
                    <table class="min-w-full divide-y dark:divide-slate-600 relative">
                        <thead class="dark:bg-slate-800 bg-slate-100 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-6 py-3 text-left w-12">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll"
                                        class="rounded border-slate-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Staff
                                    Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Role &
                                    Details</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Current Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Action
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Manage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 dark:divide-slate-700 divide-y divide-slate-100">
                            @forelse($staff as $member)
                                <tr class="hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $member->id }}" x-model="selectedIds"
                                            @change="if(!selectedIds.includes('{{ $member->id }}')) selectAll = false"
                                            class="rowCheckbox rounded border-slate-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300 font-bold overflow-hidden border border-slate-300 dark:border-slate-600 shadow-2xs">
                                                    @if($member->avatar_url)
                                                        <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}"
                                                            class="h-full w-full object-cover">
                                                    @else
                                                        {{ $member->initials }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-300">
                                                    @php
                                                        $formattedName = $member->formatted_name;
                                                        $role = $member->role;

                                                        $roleColors = [
                                                            'super_admin' => 'text-slate-500 dark:text-slate-400',
                                                            'admin' => 'text-slate-500 dark:text-slate-400',
                                                            'regular_doctor' => 'text-green-600 dark:text-green-500',
                                                            'pedia_doctor' => 'text-red-600 dark:text-red-500',
                                                            'laboratory' => 'text-blue-600 dark:text-blue-400',
                                                            'radiology' => 'text-purple-600 dark:text-purple-400',
                                                            'clinical_nurse' => 'text-yellow-500 dark:text-yellow-400',
                                                            'vitals_nurse' => 'text-yellow-500 dark:text-yellow-400',
                                                            'pharmacy' => 'text-teal-600 dark:text-teal-400',
                                                            'information_desk' => 'text-slate-500 dark:text-slate-400',
                                                        ];

                                                        $prefixColor = $roleColors[$role] ?? 'text-green-600 dark:text-green-500';

                                                        $prefixes = ['Dr.', 'Nurse', 'MedTech', 'RadTech'];
                                                        $foundPrefix = null;
                                                        foreach ($prefixes as $p) {
                                                            if (str_starts_with($formattedName, $p)) {
                                                                $foundPrefix = $p;
                                                                break;
                                                            }
                                                        }
                                                    @endphp
                                                    @if($foundPrefix)
                                                        <span class="{{ $prefixColor }} font-bold">{{ $foundPrefix }}</span>
                                                        {{ str_replace($foundPrefix . ' ', '', $formattedName) }}
                                                    @else
                                                        {{ $formattedName }}
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 dark:text-slate-300 font-medium capitalize">
                                            {{ str_replace('_', ' ', $member->role) }}
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            {{ $member->formatted_schedule ?? 'No Schedule Set' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $rawStat = strtolower($member->status ?? '');
                                            $normalizedMemberStatus = match(true) {
                                                in_array($rawStat, ['online', 'present', 'active', 'in office']) => 'Online',
                                                in_array($rawStat, ['occupied', 'seminar', 'on seminar', 'in meeting']) => 'Occupied',
                                                default => 'Offline'
                                            };
                                        @endphp
                                        @if($normalizedMemberStatus === 'Online')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.7)]"></span>
                                                <span>Online</span>
                                            </span>
                                        @elseif($normalizedMemberStatus === 'Occupied')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_6px_rgba(245,158,11,0.7)]"></span>
                                                <span>Occupied</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700/80 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                                <span>Offline</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/70 gap-1 shadow-2xs" x-data>
                                            <button
                                                @click="updateStatus({{ $member->id }}, 'Online', '{{ addslashes($member->formatted_name) }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $normalizedMemberStatus === 'Online' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-white dark:hover:bg-slate-800' }}">
                                                Online
                                            </button>
                                            <button
                                                @click="updateStatus({{ $member->id }}, 'Offline', '{{ addslashes($member->formatted_name) }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $normalizedMemberStatus === 'Offline' ? 'bg-slate-600 dark:bg-slate-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-white dark:hover:bg-slate-800' }}">
                                                Offline
                                            </button>
                                            <button
                                                @click="updateStatus({{ $member->id }}, 'Occupied', '{{ addslashes($member->formatted_name) }}')"
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $normalizedMemberStatus === 'Occupied' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-white dark:hover:bg-slate-800' }}">
                                                Occupied
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-1.5 items-center">
                                            <!-- Edit Button -->
                                            <button
                                                @click="$dispatch('open-edit-staff', {{ json_encode([
                                                    'id' => $member->id, 
                                                    'name' => $member->name, 
                                                    'email' => $member->email, 
                                                    'role' => $member->role, 
                                                    'status' => $member->status, 
                                                    'schedule' => $member->schedule,
                                                    'avatar_url' => $member->avatar_url,
                                                    'initials' => $member->initials,
                                                ]) }})"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-emerald-300 dark:hover:border-emerald-700 transition-all cursor-pointer shadow-2xs"
                                                title="Edit Staff Member">
                                                <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <!-- Archive Button -->
                                            <button @click="$dispatch('open-confirmation', {
                                                        title: 'Archive Staff Member',
                                                        message: 'Are you sure you want to archive {{ addslashes($member->formatted_name) }}?',
                                                        confirmText: 'Yes, Archive',
                                                        type: 'danger',
                                                        action: '{{ route('admin.staff.destroy', $member->id) }}',
                                                        method: 'DELETE'
                                                    })"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-rose-300 dark:hover:border-rose-700 transition-all cursor-pointer shadow-2xs"
                                                title="Archive Staff Member">
                                                <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>

                                            <!-- Promote Button -->
                                            @can('promote-admin')
                                                @if(!in_array($member->role, ['admin', 'super_admin']))
                                                    <button @click="$dispatch('open-confirmation', {
                                                                title: 'Promote to Admin',
                                                                message: 'Are you sure you want to promote {{ addslashes($member->formatted_name) }} to Administrator?',
                                                                confirmText: 'Yes, Promote',
                                                                type: 'info',
                                                                action: '{{ route('admin.staff.promote', $member->id) }}',
                                                                method: 'POST'
                                                            })"
                                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-blue-300 dark:hover:border-blue-700 transition-all cursor-pointer shadow-2xs"
                                                        title="Promote to Administrator">
                                                        <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-sm font-medium text-slate-600">No staff members found.</p>
                                        <p class="text-xs text-slate-400 mt-1">Add a staff member using the button above.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Bulk Action Modal -->
                <!-- Bulk Archive Modal -->
                <template x-teleport="body">
                    <div x-show="showBulkModal" x-cloak style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkModal = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                                <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-6">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 dark:bg-rose-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-6 w-6 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">Bulk Archive Confirmation</h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-slate-500 dark:text-slate-400">Are you sure you want to archive <span class="font-black text-rose-600 dark:text-rose-400" x-text="selectedIds.length"></span> selected staff members? This will remove them from the active roster.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                                    <form method="POST" action="{{ route('admin.staff.bulk-delete') }}" @submit="
                                        $el.querySelectorAll('input[name=\'ids[]\']').forEach(e => e.remove());
                                        selectedIds.forEach(id => {
                                            const inp = document.createElement('input');
                                            inp.type = 'hidden';
                                            inp.name = 'ids[]';
                                            inp.value = id;
                                            $el.appendChild(inp);
                                        });
                                    ">
                                        @csrf
                                        @method('DELETE')
                                        <template x-for="id in selectedIds" :key="id">
                                            <input type="hidden" name="ids[]" :value="id">
                                        </template>
                                        <button type="submit"
                                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-rose-600 text-sm font-bold text-white hover:bg-rose-700 transition-all cursor-pointer">
                                            Archive Selected
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer"
                                        @click="showBulkModal = false">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <script>
                function updateStatus(doctorId, status, doctorName) {
                    window.dispatchEvent(new CustomEvent('open-confirmation', {
                        detail: {
                            title: 'Update Staff Status',
                            message: `Are you sure you want to set ${doctorName} as "${status}"?`,
                            confirmText: 'Yes, Update',
                            action: `/admin/staff/${doctorId}/status?status=${status}`,
                            method: 'POST'
                        }
                    }));
                }
            </script>

@include('partials.image-cropper')
@endsection