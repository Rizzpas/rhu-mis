@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-transparent py-12" x-data="manageAppointment()">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Status Banner -->
        <div class="mb-8 bg-white rounded-lg shadow-sm border border-l-4 p-6
            @if($appointment->status === 'pending') border-l-yellow-400
            @elseif($appointment->status === 'rescheduled') border-l-blue-400
            @elseif($appointment->status === 'cancelled') border-l-red-400
            @else border-l-green-400 @endif">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Appointment Status</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ref: <span class="font-mono font-bold">{{ $appointment->reference_number }}</span></p>
                </div>
                <div class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                    @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($appointment->status === 'rescheduled') bg-blue-100 text-blue-800
                    @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-green-100 text-green-800 @endif">
                    {{ ucfirst($appointment->status) }}
                </div>
            </div>
        </div>
        
        <!-- Back to Home -->
        <div class="mb-4">
            <a href="{{ route('welcome') }}" class="inline-flex items-center text-sm font-medium text-teal-600 hover:text-teal-500">
                <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Home
            </a>
        </div>

        <!-- Appointment Details -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Appointment Details</h3>
            </div>
            <div class="px-6 py-5">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Patient Name') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $appointment->first_name }} {{ $appointment->last_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Service Type') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white capitalize">{{ $appointment->type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Contact Number') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $appointment->contact_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Email Address') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $appointment->email }}</dd>
                    </div>
                    <div class="col-span-1 sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Scheduled Date') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-bold text-teal-600">
                            {{ $appointment->preferred_date->format('l, F j, Y') }}
                            @if($appointment->preferred_time)
                                <span class="ml-2 text-sm font-normal text-gray-600">· Arrival: <strong class="text-teal-700">{{ $appointment->preferred_time }}</strong></span>
                            @endif
                        </dd>
                    </div>
                     <div class="col-span-1 sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Chief Complaint') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white italic">
                            "{{ $appointment->complaint ?: 'N/A' }}"
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Actions -->
        @if(in_array($appointment->status, ['pending', 'approved', 'rescheduled']))
            <div class="flex flex-col sm:flex-row gap-4 justify-end mb-6">
                <!-- Reschedule Button -->
                <button type="button" 
                    @click="openRescheduleModal()"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
                    class="bg-red-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    {{ __('Cancel Appointment') }}
                </button>
            </div>
        @endif
        
        <!-- Reschedule Modal with Calendar -->
        <div x-show="showReschedule" 
             style="display: none;"
             class="fixed inset-0 z-[80] overflow-y-auto" 
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showReschedule" class="fixed inset-0 transition-opacity" style="background-color: rgba(107, 114, 128, 0.5);" @click="showReschedule = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showReschedule" @click.stop class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full relative z-50">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Reschedule Appointment</h3>
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
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <form action="{{ route('appointment.reschedule') }}" method="POST">
                            @csrf
                            <input type="hidden" name="new_date" x-model="selectedDate">
                            <input type="hidden" name="new_time" x-model="selectedTime">
                            <button type="submit" 
                                    :disabled="!selectedDate || !selectedTime"
                                    :class="(!selectedDate || !selectedTime) ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                {{ __('Confirm New Date & Time') }}
                            </button>
                        </form>
                        <button type="button" @click="showReschedule = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">{{ __('Cancel') }}</button>
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
