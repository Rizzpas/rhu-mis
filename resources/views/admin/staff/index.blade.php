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
                        class="bg-red-600 text-white px-4 py-2 rounded font-medium shadow-sm hover:bg-red-700 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Archive Selected (<span x-text="selectedIds.length"></span>)
                    </button>
                </div>
                <button @click="showAddDoctor = true"
                    class="bg-teal-600 text-white px-4 py-2 rounded font-medium shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Staff
                </button>
            </div>
        </div>

        <!-- Advanced Search and Filter Bar -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
            <form method="GET" action="{{ route('admin.staff.index') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                <!-- Search Input -->
                <div class="flex-1 w-full">
                    <label
                        class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Search
                        Staff Members</label>
                    <div class="relative group">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-teal-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or email..."
                            class="pl-12 pr-4 block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 py-3.5 text-sm transition-all font-medium">
                    </div>
                </div>

                <!-- Role Filter -->
                <div class="w-full lg:w-48">
                    <label
                        class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Role</label>
                    <div class="relative">
                        <select name="role"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 py-3.5 px-5 text-sm appearance-none font-bold">
                            <option value="all">All Roles</option>
                            @can('promote-admin')
                                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin
                                </option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            @endcan
                            <option value="regular_doctor" {{ request('role') == 'regular_doctor' ? 'selected' : '' }}>Regular
                                Doctor</option>
                            <option value="pedia_doctor" {{ request('role') == 'pedia_doctor' ? 'selected' : '' }}>Pedia
                                Doctor</option>
                            <option value="laboratory" {{ request('role') == 'laboratory' ? 'selected' : '' }}>Laboratory
                            </option>
                            <option value="radiology" {{ request('role') == 'radiology' ? 'selected' : '' }}>Radiology
                            </option>
                            <option value="clinical_nurse" {{ request('role') == 'clinical_nurse' ? 'selected' : '' }}>
                                Clinical Nurse</option>
                            <option value="vitals_nurse" {{ request('role') == 'vitals_nurse' ? 'selected' : '' }}>Vitals
                                Nurse</option>
                            <option value="information_desk" {{ request('role') == 'information_desk' ? 'selected' : '' }}>
                                Front Desk</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="w-full lg:w-48">
                    <label
                        class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1.5">Availability</label>
                    <div class="relative">
                        <select name="status"
                            class="block w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 py-3.5 px-5 text-sm appearance-none font-bold">
                            <option value="all">All Status</option>
                            <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>Present (Active)
                            </option>
                            <option value="Seminar" {{ request('status') == 'Seminar' ? 'selected' : '' }}>On Seminar</option>
                            <option value="Out of Office" {{ request('status') == 'Out of Office' ? 'selected' : '' }}>Out of
                                Office</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    @if(request()->anyFilled(['q', 'role', 'status']) && (request('q') || request('role') != 'all' || request('status') != 'all'))
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex-1 lg:flex-none px-6 py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl font-bold text-sm text-center hover:bg-slate-50 dark:hover:bg-slate-700 transition-all border border-slate-200 dark:border-slate-700">
                            Reset Filters
                        </a>
                    @endif
                    <button type="submit"
                        class="flex-1 lg:flex-none px-6 py-3 bg-slate-900 dark:bg-teal-600 text-white rounded-xl font-bold text-sm shadow-lg hover:bg-slate-800 dark:hover:bg-teal-700 transition-all active:scale-95">
                        Apply Filters
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
                            get fullName() {
                                return `${this.firstName} ${this.middleName} ${this.lastName} ${this.suffix}`.replace(/\s+/g, ' ').trim();
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
                                if (this.step === 2 && !this.$refs.roleSelect.value) {
                                    alert('Please select a role.');
                                    return;
                                }
                                if (this.step < 3) this.step++;
                            },
                            prevStep() {
                                if (this.step > 1) this.step--;
                            }
                        }" class="relative z-10 w-full max-w-2xl mx-auto flex flex-col pointer-events-auto">
                    <div
                        class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transform transition-all w-full max-h-[90vh] flex flex-col border border-slate-200 dark:border-slate-800">
                        <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data"
                            class="flex flex-col h-full"
                            @submit="if(step < 3) { $event.preventDefault(); nextStep(); } else if (password !== passwordConfirmation) { $event.preventDefault(); alert('Passwords do not match'); }">
                            @csrf
                            <input type="hidden" name="_form" value="add_staff">
                            <input type="hidden" name="name" :value="fullName">
                            <input type="hidden" name="schedule" :value="schedulePayload">

                            <div
                                class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center relative overflow-hidden">
                                <!-- Progress Bar -->
                                <div class="absolute bottom-0 left-0 h-1 bg-teal-500 transition-all duration-300"
                                    :style="`width: ${(step / 3) * 100}%`"></div>
                                <div class="relative z-10">
                                    <h3 class="text-lg leading-6 font-semibold text-slate-900 dark:text-white"
                                        x-text="step === 1 ? 'Step 1: Personal Info' : (step === 2 ? 'Step 2: Role & Schedule' : 'Step 3: Security & Finish')">
                                        Add New Staff Member</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Adding Staff Member as an
                                        Administrator.</p>
                                </div>

                                <button type="button" @click="showAddDoctor = false"
                                    class="text-slate-400 hover:text-slate-600 relative z-10">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="px-6 py-5 overflow-y-auto">
                                <!-- Step 1: Personal Info -->
                                <div x-show="step === 1" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300">First
                                                Name <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="firstName" name="first_name" :required="step === 1"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300">Middle
                                                Name</label>
                                            <input type="text" x-model="middleName" name="middle_name"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Last
                                                Name <span class="text-red-500">*</span></label>
                                            <input type="text" x-model="lastName" name="last_name" :required="step === 1"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300">Suffix
                                                (e.g. Jr., Sr.)</label>
                                            <input type="text" x-model="suffix" name="suffix"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email
                                            Address <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" x-model="email" :required="step === 1"
                                            class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Profile
                                            Photo (Max 2MB)</label>
                                        <input type="file" name="avatar" accept="image/*"
                                            class="mt-1 block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                    </div>
                                </div>

                                <!-- Step 2: Role & Schedule -->
                                <div x-show="step === 2" style="display: none;" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role
                                            <span class="text-red-500">*</span></label>
                                        <select name="role" x-ref="roleSelect" :required="step === 2"
                                            class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                            <option value="" disabled {{ !old('role') ? 'selected' : '' }}>Select Role
                                            </option>
                                            @can('promote-admin')
                                                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>
                                                    Super Admin</option>
                                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin
                                                </option>
                                            @endcan
                                            <option value="regular_doctor" {{ old('role') == 'regular_doctor' ? 'selected' : '' }}>Regular Doctor</option>
                                            <option value="pedia_doctor" {{ old('role') == 'pedia_doctor' ? 'selected' : '' }}>Pedia Doctor</option>
                                            <option value="laboratory" {{ old('role') == 'laboratory' ? 'selected' : '' }}>
                                                Laboratory</option>
                                            <option value="radiology" {{ old('role') == 'radiology' ? 'selected' : '' }}>
                                                Radiology</option>
                                            <option value="clinical_nurse" {{ old('role') == 'clinical_nurse' ? 'selected' : '' }}>Clinical Nurse</option>
                                            <option value="vitals_nurse" {{ old('role') == 'vitals_nurse' ? 'selected' : '' }}>Vitals Nurse (Triage)</option>
                                            <option value="information_desk" {{ old('role') == 'information_desk' ? 'selected' : '' }}>Front Desk / Information Desk</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Initial
                                            Status</label>
                                        <select name="status"
                                            class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                            <option value="Present" {{ old('status') == 'Present' ? 'selected' : '' }}>Present
                                                (Active)</option>
                                            <option value="Seminar" {{ old('status') == 'Seminar' ? 'selected' : '' }}>On
                                                Seminar</option>
                                            <option value="Out of Office" {{ old('status') == 'Out of Office' ? 'selected' : '' }}>Out of Office</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Schedule
                                            (Optional)</label>
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            <template x-for="(isActive, day) in days" :key="day">
                                                <button type="button" @click="days[day] = !days[day]"
                                                    :class="isActive ? 'bg-teal-600 text-white border-teal-600' : 'bg-white dark:bg-gray-700 text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-gray-600'"
                                                    class="px-3 py-1.5 border rounded-md text-xs font-medium transition-colors focus:outline-none"
                                                    x-text="day">
                                                </button>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1">
                                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Time
                                                    In</label>
                                                <input type="time" x-model="timeIn"
                                                    class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                            </div>
                                            <div class="text-slate-400 mt-5">-</div>
                                            <div class="flex-1">
                                                <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Time
                                                    Out</label>
                                                <input type="time" x-model="timeOut"
                                                    class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                            </div>
                                        </div>
                                        <div
                                            class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
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
                                <div x-show="step === 3" style="display: none;" class="space-y-4">
                                    <div
                                        class="bg-amber-50 dark:bg-amber-900/30 p-4 rounded-md border border-amber-200 dark:border-amber-700 mb-4">
                                        <p class="text-sm text-amber-800 dark:text-amber-200">
                                            <strong>Security Requirements:</strong> Create a temporary password for the
                                            staff member. They will be required to change it upon their first login.
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300">Temporary
                                                Password <span class="text-red-500">*</span></label>
                                            <input type="password" name="password" x-model="password" :required="step === 3"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                            <!-- Strength Meter -->
                                            <div class="mt-2 flex items-center gap-2" x-show="password.length > 0">
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
                                                <span class="text-xs font-semibold"
                                                    :class="{'text-red-500': strength <= 1, 'text-yellow-500': strength === 2, 'text-teal-500': strength === 3, 'text-green-500': strength === 4}"
                                                    x-text="strengthText"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm
                                                Password <span class="text-red-500">*</span></label>
                                            <input type="password" name="password_confirmation"
                                                x-model="passwordConfirmation" :required="step === 3"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 p-2 text-sm">
                                            <p x-show="passwordConfirmation.length > 0 && password !== passwordConfirmation"
                                                class="text-xs text-red-500 mt-1">Passwords do not match.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="px-6 py-4 bg-slate-50 dark:bg-gray-800/80 border-t border-slate-200 dark:border-gray-700 flex justify-between items-center">
                                <div>
                                    <button type="button" x-show="step > 1" @click="prevStep()"
                                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white focus:outline-none flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Back
                                    </button>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click="showAddDoctor = false"
                                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-slate-300 dark:border-gray-600 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-gray-600 rounded shadow-sm focus:outline-none">Cancel</button>

                                    <button type="button" x-show="step < 3" @click="nextStep()"
                                        class="px-5 py-2 bg-teal-600 text-sm font-medium text-white hover:bg-teal-700 rounded shadow-sm focus:outline-none flex items-center gap-1">
                                        Next
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>

                                    <button type="submit" x-show="step === 3" :disabled="password !== passwordConfirmation"
                                        :class="password !== passwordConfirmation ? 'opacity-50 cursor-not-allowed' : 'hover:bg-teal-700'"
                                        class="px-5 py-2 bg-teal-600 text-sm font-medium text-white rounded shadow-sm focus:outline-none flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Create Staff
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Staff Modal -->
                <div x-show="showEditStaff" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 py-8">
                        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"
                            @click="showEditStaff = false">
                        </div>
                        <div
                            class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full z-10 max-h-[90vh] overflow-y-auto border border-slate-200 dark:border-slate-800">
                            <form :action="'/admin/staff/' + editMember.id" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div
                                    class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex justify-between items-center">
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Edit Staff Member</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1"
                                            x-text="'Updating access for ' + (editMember.name || '')"></p>
                                    </div>
                                    <button type="button" @click="showEditStaff = false"
                                        class="text-slate-400 hover:text-slate-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="px-6 py-5">
                                    <div class="space-y-4">
                                        <div x-data="{
                                        firstName: '',
                                        middleName: '',
                                        lastName: '',
                                        suffix: '',
                                        initializedFor: null,
                                        get fullName() {
                                            return `${this.firstName} ${this.middleName} ${this.lastName} ${this.suffix}`.replace(/\s+/g, ' ').trim();
                                        }
                                    }">
                                            <div x-effect='
                                            if (showEditStaff && editMember.name && initializedFor !== editMember.id) {
                                                // Strip any potential prefixes from the raw name if they somehow got in there
                                                let cleanName = editMember.name.replace(/^(Dr\.|Nurse|MedTech|RadTech)\s+/i, "").trim();
                                                let parts = cleanName.split(" ");
                                                let suffixes = ["Jr.", "Sr.", "III", "IV", "II"];

                                                if (parts.length > 0 && suffixes.includes(parts[parts.length - 1])) {
                                                    suffix = parts.pop();
                                                } else {
                                                    suffix = "";
                                                }

                                                if (parts.length === 1) {
                                                    firstName = parts[0];
                                                    middleName = "";
                                                    lastName = "";
                                                } else if (parts.length === 2) {
                                                    firstName = parts[0];
                                                    middleName = "";
                                                    lastName = parts[1];
                                                } else if (parts.length >= 3) {
                                                    firstName = parts.shift();
                                                    lastName = parts.pop();
                                                    middleName = parts.join(" ");
                                                }
                                                initializedFor = editMember.id;
                                            }
                                        '></div>
                                            <input type="hidden" name="name" :value="fullName">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">First
                                                        Name <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="firstName" required
                                                        class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 border p-2 text-sm">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Middle
                                                        Name</label>
                                                    <input type="text" x-model="middleName"
                                                        class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 border p-2 text-sm">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Last
                                                        Name <span class="text-red-500">*</span></label>
                                                    <input type="text" x-model="lastName" required
                                                        class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 border p-2 text-sm">
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-sm font-medium text-slate-700 dark:text-slate-300">Suffix
                                                        (e.g. Jr., Sr.)</label>
                                                    <input type="text" x-model="suffix"
                                                        class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 border p-2 text-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email
                                                Address <span class="text-red-500">*</span></label>
                                            <input type="email" name="email" :value="editMember.email" required
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 border p-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">New
                                                Password <span class="text-slate-400 font-normal text-xs">(leave blank to
                                                    keep
                                                    current)</span></label>
                                            <input type="password" name="password" placeholder="••••••••"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role
                                                <span class="text-red-500">*</span></label>
                                            <select name="role" x-effect="$el.value = editMember.role" required
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 border p-2 text-sm">
                                                @can('promote-admin')
                                                    <option value="super_admin">Super Admin</option>
                                                    <option value="admin">Admin</option>
                                                @endcan
                                                <option value="regular_doctor">Regular Doctor</option>
                                                <option value="pedia_doctor">Pedia Doctor</option>
                                                <option value="laboratory">Laboratory</option>
                                                <option value="radiology">Radiology</option>
                                                <option value="clinical_nurse">Clinical Nurse</option>
                                                <option value="vitals_nurse">Vitals Nurse (Triage)</option>
                                                <option value="information_desk">Front Desk / Information Desk</option>
                                            </select>
                                        </div>
                                        <div x-data="{
                                            days: {Mon: false, Tue: false, Wed: false, Thu: false, Fri: false, Sat: false, Sun: false},
                                            timeIn: '08:00',
                                            timeOut: '17:00',
                                            init() {
                                                this.$watch('$root.editMember', (member) => {
                                                    Object.keys(this.days).forEach(d => this.days[d] = false);
                                                    this.timeIn = '08:00';
                                                    this.timeOut = '17:00';
                                                    if (!member || !member.schedule) return;
                                                    const s = member.schedule;
                                                    ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'].forEach(d => {
                                                        if (s.includes(d)) this.days[d] = true;
                                                    });
                                                    const tMatch = s.match(/\((.*?) - (.*?)\)/);
                                                    if (tMatch) {
                                                        const to24 = (t) => {
                                                            const parts = t.trim().split(' ');
                                                            const ap = parts[1];
                                                            let [h, m] = parts[0].split(':').map(Number);
                                                            if (ap === 'PM' && h !== 12) h += 12;
                                                            if (ap === 'AM' && h === 12) h = 0;
                                                            return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
                                                        };
                                                        this.timeIn = to24(tMatch[1]);
                                                        this.timeOut = to24(tMatch[2]);
                                                    }
                                                });
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
                                            }
                                        }">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Schedule
                                                (Optional)</label>
                                            <input type="hidden" name="schedule" :value="schedulePayload">
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                <template x-for="(isActive, day) in days" :key="day">
                                                    <button type="button" @click="days[day] = !days[day]"
                                                        :class="isActive ? 'bg-teal-600 text-white border-teal-600' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                        class="px-3 py-1.5 border rounded-md text-xs font-medium transition-colors focus:outline-none"
                                                        x-text="day">
                                                    </button>
                                                </template>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1">
                                                    <label
                                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Time
                                                        In</label>
                                                    <input type="time" x-model="timeIn"
                                                        class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm">
                                                </div>
                                                <div class="text-slate-400 mt-5">-</div>
                                                <div class="flex-1">
                                                    <label
                                                        class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Time
                                                        Out</label>
                                                    <input type="time" x-model="timeOut"
                                                        class="block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 border p-2 text-sm">
                                                </div>
                                            </div>
                                            <div
                                                class="mt-2 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                <svg class="w-4 h-4 text-teal-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                <span
                                                    x-text="formattedSchedule ? 'Preview: ' + formattedSchedule : 'No schedule selected'"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                                            <select name="status" x-effect="$el.value = editMember.status"
                                                class="mt-1 block w-full rounded-md border-slate-300 dark:border-slate-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-amber-500 focus:ring-amber-500 border p-2 text-sm">
                                                <option value="Present">Present (Active)</option>
                                                <option value="Seminar">On Seminar</option>
                                                <option value="Out of Office">Out of Office</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700">Update Profile
                                                Photo</label>
                                            <input type="file" name="avatar" accept="image/*"
                                                class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="px-8 py-6 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                                    <button type="button" @click="showEditStaff = false"
                                        class="px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-300 dark:border-slate-700 rounded-xl shadow-sm transition-all">Cancel</button>
                                    <button type="submit"
                                        class="px-6 py-2 bg-teal-600 text-sm font-bold text-white hover:bg-teal-700 border border-transparent rounded-xl shadow-lg transition-all">Save
                                        Changes</button>
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
                                                    class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold overflow-hidden border border-slate-300">
                                                    @if($member->avatar_path)
                                                        <img src="{{ asset('storage/' . $member->avatar_path) }}" alt=""
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
                                        <span
                                            class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $member->status === 'Present' ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200' : '' }}
                                                    {{ $member->status === 'Seminar' ? 'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200' : '' }}
                                                    {{ str_contains(strtolower($member->status), 'out') || str_contains(strtolower($member->status), 'absent') ? 'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200' : '' }}">
                                            {{ $member->status ?: 'Unknown' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2" x-data>
                                            <button
                                                @click="updateStatus({{ $member->id }}, 'Present', '{{ addslashes($member->formatted_name) }}')"
                                                class="text-green-600 dark:text-green-400 border hover:bg-green-200 dark:hover:bg-green-800 border-green-400 px-2 py-1 rounded bg-white dark:bg-gray-800 dark:bg-green-900/30 text-xs shadow-sm transition-colors">Present</button>
                                            <button
                                                @click="updateStatus({{ $member->id }}, 'Seminar', '{{ addslashes($member->formatted_name) }}')"
                                                class="text-yellow-500 border hover:bg-yellow-200 dark:hover:bg-yellow-800 border-yellow-400 px-2 py-1 rounded bg-white dark:bg-gray-800 text-xs shadow-sm transition-colors">Seminar</button>
                                            <button
                                                @click="updateStatus({{ $member->id }}, 'Out of Office', '{{ addslashes($member->formatted_name) }}')"
                                                class="text-red-500 border hover:bg-red-200 dark:hover:bg-red-800 border-red-400 px-2 py-1 rounded bg-white dark:bg-gray-800 text-xs shadow-sm transition-colors">Unavailable</button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-3 items-center">
                                            <!-- Edit Button -->
                                            <button
                                                @click="$dispatch('open-edit-staff', {{ json_encode(['id' => $member->id, 'name' => $member->name, 'email' => $member->email, 'role' => $member->role, 'status' => $member->status, 'schedule' => $member->schedule]) }})"
                                                class="text-slate-400 hover:text-amber-600 transition-colors p-1 rounded hover:bg-amber-50"
                                                title="Edit Staff">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <button @click="$dispatch('open-confirmation', {
                                                        title: 'Archive Staff Member',
                                                        message: 'Are you sure you want to archive {{ addslashes($member->formatted_name) }}?',
                                                        confirmText: 'Yes, Archive',
                                                        type: 'danger',
                                                        action: '{{ route('admin.staff.destroy', $member->id) }}',
                                                        method: 'DELETE'
                                                    })"
                                                class="text-slate-400 hover:text-red-600 transition-colors p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30"
                                                title="Archive">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                                                        class="text-slate-400 hover:text-blue-600 transition-colors p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/30"
                                                        title="Promote to Admin">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                <template x-if="showBulkModal">
                    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                        aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                @click="showBulkModal = false"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                aria-hidden="true">&#8203;</span>
                            <div
                                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div
                                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                                                id="modal-title">Bulk Archive Confirmation</h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to
                                                    archive <span class="font-bold text-red-600"
                                                        x-text="selectedIds.length"></span> selected staff members? This
                                                    will remove them from the active roster.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <form method="POST" action="{{ route('admin.staff.bulk-delete') }}">
                                        @csrf
                                        @method('DELETE')
                                        <template x-for="id in selectedIds">
                                            <input type="hidden" name="ids[]" :value="id">
                                        </template>
                                        <button type="submit"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Archive Selected
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
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
@endsection