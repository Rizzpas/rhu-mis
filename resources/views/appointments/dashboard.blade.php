@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-transparent py-10 sm:py-12" x-data="manageAppointment()">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back to Home -->
        <div class="mb-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 shadow-2xs text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Home
            </a>
        </div>

        <!-- Status Showcase Card -->
        <div class="rounded-3xl p-6 sm:p-8 mb-6 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <div class="mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                            @if($appointment->status === 'pending') bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300/60
                            @elseif($appointment->status === 'rescheduled') bg-blue-100 text-blue-900 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300/60
                            @elseif($appointment->status === 'cancelled') bg-red-100 text-red-900 dark:bg-red-950/60 dark:text-red-300 border border-red-300/60
                            @else bg-emerald-100 text-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/60 @endif">
                            <span class="w-1.5 h-1.5 rounded-full
                                @if($appointment->status === 'pending') bg-amber-600
                                @elseif($appointment->status === 'rescheduled') bg-blue-600
                                @elseif($appointment->status === 'cancelled') bg-red-600
                                @else bg-emerald-600 @endif"></span>
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>
                    <h2 class="font-display text-2xl font-extrabold text-slate-900 dark:text-white">Appointment Record</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Reference No: <span class="font-mono font-bold text-slate-900 dark:text-white px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600">{{ $appointment->reference_number }}</span></p>
                </div>
            </div>
        </div>

        <!-- Appointment Details Bento Card -->
        <div class="rounded-3xl p-6 sm:p-8 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs mb-8">
            <div class="pb-4 mb-6 border-b border-slate-100 dark:border-slate-700/70 flex items-center justify-between">
                <h3 class="font-display text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-700 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Booking Information
                </h3>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300/50 dark:border-emerald-700/50 capitalize">
                    {{ $appointment->type }} Consultation
                </span>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Patient Name') }}</dt>
                    <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $appointment->first_name }} {{ $appointment->last_name }}</dd>
                </div>
                <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Service Type') }}</dt>
                    <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white capitalize">{{ $appointment->type }}</dd>
                </div>
                <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Contact Number') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $appointment->contact_number }}</dd>
                </div>
                <div class="p-3.5 rounded-2xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Email Address') }}</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $appointment->email }}</dd>
                </div>
                <div class="col-span-1 sm:col-span-2 p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/60">
                    <dt class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">{{ __('Scheduled Date & Arrival') }}</dt>
                    <dd class="mt-1 text-base font-extrabold text-emerald-900 dark:text-emerald-200 flex flex-wrap items-center gap-2">
                        <span>{{ $appointment->preferred_date->format('l, F j, Y') }}</span>
                        @if($appointment->preferred_time)
                            <span class="px-2.5 py-0.5 rounded-md bg-white dark:bg-gray-800 text-emerald-700 dark:text-emerald-300 text-xs font-bold shadow-xs">
                                Arrival: {{ $appointment->preferred_time }}
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="col-span-1 sm:col-span-2 p-3.5 rounded-2xl bg-gray-50/80 dark:bg-gray-800/60 border border-gray-200/50 dark:border-gray-700/50">
                    <dt class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Chief Complaint') }}</dt>
                    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 italic">
                        "{{ $appointment->complaint ?: 'N/A' }}"
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Actions (21st.dev Button Style) -->
        @if(in_array($appointment->status, ['pending', 'approved', 'rescheduled']))
            <div class="flex flex-col sm:flex-row gap-3.5 justify-end mb-8" data-reveal style="transition-delay: 120ms">
                <!-- Reschedule Button -->
                <button type="button" 
                    @click="openRescheduleModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-white shadow-md bg-blue-600 hover:bg-blue-700 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ __('Reschedule Appointment') }}
                </button>

                <!-- Cancel Button -->
                <button type="button" 
                    @click="$dispatch('open-confirmation', {
                        title: 'Cancel Appointment',
                        message: 'Are you sure you want to cancel this appointment? This action cannot be undone. IMPORTANT: Cancelled appointments will be permanently and automatically deleted from our system after 12 hours.',
                        confirmText: '{{ __('Yes, Cancel Appointment') }}',
                        type: 'danger',
                        action: '{{ route('appointment.cancel') }}',
                        method: 'POST'
                    })"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-xs font-bold text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-900/60 border border-red-200 dark:border-red-800 transition-all hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ __('Cancel Appointment') }}
                </button>
            </div>
        @endif
        
        <!-- Reschedule Modal with Calendar -->
        <div x-show="showReschedule" 
             x-cloak
             style="display: none;"
             class="fixed inset-0 z-[80] overflow-y-auto" 
             aria-labelledby="reschedule-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showReschedule"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                     @click="showReschedule = false"
                     aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showReschedule"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.stop
                     class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800 relative z-50">
                    <div class="p-6 sm:p-8">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="reschedule-modal-title">Reschedule Appointment</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Please select a new date for your appointment.</p>
                                    
                                <!-- Calendar UI -->
                                    <div class="border rounded-lg p-4" x-init="initCalendar(); fetchAvailability();">
                                        <div class="flex justify-between items-center mb-4">
                                            <button type="button" @click="prevMonth()" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed" :disabled="isPrevMonthDisabled()"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></button>
                                            <h4 class="font-bold text-lg" x-text="monthName + ' ' + currentYear"></h4>
                                            <button type="button" @click="nextMonth()" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed" :disabled="isNextMonthDisabled()"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></button>
                                        </div>
                                        <div class="flex gap-2 text-xs mb-4 justify-center">
                                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Available</span>
                                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-400"></span> Limited</span>
                                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500"></span> Full</span>
                                        </div>
                                        <div class="grid grid-cols-7 gap-2 text-center text-sm mb-2">
                                            <span class="text-gray-400">Su</span><span class="text-gray-400">Mo</span><span class="text-gray-400">Tu</span>
                                            <span class="text-gray-400">We</span><span class="text-gray-400">Th</span><span class="text-gray-400">Fr</span><span class="text-gray-400">Sa</span>
                                        </div>
                                        <div class="grid grid-cols-7 gap-2">
                                            <template x-for="blank in startDay" :key="'blank-'+blank">
                                                <div></div>
                                            </template>
                                            
                                            <template x-for="day in daysInMonth" :key="day">
                                                <div @click="!isPastDate(day) && !isDayFull(day) && selectDate(getDateString(day))"
                                                    class="h-12 md:h-14 rounded-lg flex flex-col items-center justify-center font-medium transition text-xs md:text-sm relative group border"
                                                    :class="{
                                                            'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 cursor-not-allowed border-transparent': isPastDate(day),
                                                            'bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 cursor-not-allowed border-red-200 dark:border-red-800': !isPastDate(day) && isDayFull(day),
                                                            'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-800/50 cursor-pointer border-yellow-200 dark:border-yellow-800': !isPastDate(day) && isDayLimited(day) && !isDayFull(day),
                                                            'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800/50 cursor-pointer border-green-200 dark:border-green-800': !isPastDate(day) && !isDayLimited(day) && !isDayFull(day),
                                                            'ring-2 ring-teal-500 ring-offset-2': selectedDate === getDateString(day)
                                                         }">
                                                    <span class="font-bold" x-text="day"></span>
                                                    <span x-show="type === 'pedia' && !isPastDate(day) && !isFollowUp" class="text-[0.6rem]" x-text="getSlotsUsage(getDateString(day))"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <div x-show="selectedDate" class="text-center mt-4">
                                        <p class="text-sm text-blue-600 font-medium">Selected: <span x-text="formatDate(selectedDate)"></span></p>
                                    </div>

                                    <!-- Time Slot Picker -->
                                    <div x-show="selectedDate" x-cloak class="mt-6 text-left">
                                        <h4 class="text-md font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Select Preferred Arrival Time
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                            Choose a 30-minute window based on the doctor's schedule.
                                        </p>

                                        <div x-show="loadingSlots" class="flex items-center justify-center py-4">
                                            <svg class="animate-spin h-6 w-6 text-teal-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>

                                        <div x-show="!loadingSlots && timeSlots.length === 0" class="text-center py-4 bg-red-50 rounded-lg border border-red-200">
                                            <p class="text-sm text-red-600 font-medium">No time slots available. Please select a different date.</p>
                                        </div>

                                        <div x-show="!loadingSlots && timeSlots.length > 0" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                            <template x-for="slot in timeSlots" :key="slot.value">
                                                <button type="button" @click="selectedTime = slot.value"
                                                    class="p-2 rounded-lg border-2 text-sm font-semibold text-center transition-all duration-200"
                                                    :class="selectedTime === slot.value 
                                                        ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 ring-2 ring-teal-500 ring-offset-1 shadow-md' 
                                                        : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:border-teal-300 hover:bg-teal-50/50'">
                                                    <span x-text="slot.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form action="{{ route('appointment.reschedule') }}" method="POST">
                            @csrf
                            <input type="hidden" name="new_date" x-model="selectedDate">
                            <input type="hidden" name="new_time" x-model="selectedTime">
                            <button type="submit" 
                                    :disabled="!selectedDate || !selectedTime"
                                    :class="(!selectedDate || !selectedTime) ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-blue-600 hover:bg-blue-700 text-sm font-bold text-white transition-all cursor-pointer">
                                {{ __('Confirm New Date & Time') }}
                            </button>
                        </form>
                        <button type="button" @click="showReschedule = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function manageAppointment() {
        return {
            showReschedule: false,
            selectedDate: '',
            selectedTime: '',
            availability: {},
            timeSlots: [],
            loadingSlots: false,
            type: '{{ $appointment->type }}',
            isFollowUp: {{ ($appointment->type === 'adult' || $appointment->is_follow_up) ? 'true' : 'false' }},
            validDays: null,
            followUpDoctorId: null,
            doctorName: '',
            doctorSchedule: '',
            
            async openRescheduleModal() {
                this.showReschedule = true;
                this.selectedDate = '';
                this.selectedTime = '';
                
                if (this.isFollowUp) {
                    await this.verifyFollowUp();
                }
                this.fetchAvailability();
            },

            async verifyFollowUp() {
                try {
                    const response = await fetch('{{ route("appointment.verify-follow-up") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            first_name: '{{ $appointment->first_name }}',
                            last_name: '{{ $appointment->last_name }}',
                            dob: '{{ $appointment->dob }}'
                        })
                    });
                    const data = await response.json();
                    if (data.valid) {
                        this.validDays = data.valid_days;
                        this.followUpDoctorId = data.doctor_id;
                        this.doctorName = data.doctor_name;
                        this.doctorSchedule = data.doctor_schedule;
                    }
                } catch (e) {
                    console.error("Failed to verify follow-up", e);
                }
            },

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
                    if (!this.validDays) return true; // Default to true (full/unavailable) while loading validDays
                    
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
            getSlotsUsage(date) {
                const avail = this.availability[date];
                if (!avail) return '0/20';
                if (typeof avail === 'object') {
                    const cap = avail.capacity || 20;
                    const booked = avail.booked !== undefined ? avail.booked : 0;
                    return booked + '/' + cap;
                }
                return avail + '/20';
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

            selectDate(date) {
                this.selectedDate = date;
                this.selectedTime = '';
                this.fetchTimeSlots(date);
            },
            
            async fetchTimeSlots(date) {
                this.loadingSlots = true;
                this.timeSlots = [];
                try {
                    const params = new URLSearchParams({ date, type: this.type });
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

            formatDate(dateString) {
                if(!dateString) return '';
                const date = new Date(dateString);
                // Use UTC
                return date.toLocaleDateString('en-US', { timeZone: 'UTC', weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }
        }
    }
</script>
@endsection
