@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-transparent py-10 sm:py-12" x-data="manageAppointment()">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <x-breadcrumb :items="[
            'Appointments' => route('appointment.create'),
            'Manage Appointment' => route('appointment.manage'),
            'Record #' . $appointment->reference_number => ''
        ]">
            <x-slot:right>
                <a href="{{ route('appointment.logout') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:text-rose-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Exit Record</span>
                </a>
            </x-slot:right>
        </x-breadcrumb>

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
                        message: 'Cancelled appointments will be permanently and automatically deleted from our system after 12 hours. Are you sure you want to cancel this appointment? This action cannot be undone. ',
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
             aria-labelledby="reschedule-modal-title" 
             role="dialog" 
             aria-modal="true"
             @keydown.escape.window="showReschedule = false">
            <div class="flex items-center justify-center min-h-screen p-3 sm:p-4 md:p-6 text-center">
                <!-- Backdrop -->
                <div x-show="showReschedule"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity"
                     @click="showReschedule = false"
                     aria-hidden="true"></div>

                <!-- Modal Window -->
                <div x-show="showReschedule"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.stop
                     class="relative w-full max-w-4xl bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl text-left shadow-2xl border border-slate-200/90 dark:border-slate-800 overflow-hidden transform transition-all my-2 sm:my-6 z-10 flex flex-col max-h-[94vh] sm:max-h-[90vh]">
                    
                    <!-- Modal Header -->
                    <div class="px-4 py-3.5 sm:px-6 sm:py-5 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl sm:rounded-2xl bg-blue-50 dark:bg-blue-950/50 border border-blue-200/60 dark:border-blue-800/50 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    <h3 class="text-base sm:text-xl font-display font-extrabold text-slate-900 dark:text-white truncate" id="reschedule-modal-title">
                                        {{ __('Reschedule Appointment') }}
                                    </h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        Ref: {{ $appointment->reference_number }}
                                    </span>
                                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60 capitalize">
                                        {{ $appointment->type }} Consultation
                                    </span>
                                </div>
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate sm:whitespace-normal">
                                    Select an available date on the calendar, then pick your preferred arrival window.
                                </p>
                            </div>
                        </div>

                        <!-- Close Button -->
                        <button type="button" 
                                @click="showReschedule = false" 
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer shrink-0"
                                aria-label="Close modal">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Follow-up Doctor Notice (when applicable) -->
                    <div x-show="isFollowUp && doctorName" x-cloak class="px-4 py-2.5 sm:px-6 sm:py-3 bg-teal-50/90 dark:bg-teal-950/40 border-b border-teal-100 dark:border-teal-900/50 flex items-center gap-2.5 sm:gap-3 shrink-0">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-teal-100 dark:bg-teal-900/70 text-teal-700 dark:text-teal-300 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0 text-xs">
                            <p class="font-bold text-teal-950 dark:text-teal-200 truncate">
                                Assigned Physician: <span x-text="doctorName"></span>
                            </p>
                            <p class="text-teal-700 dark:text-teal-400 text-[10px] sm:text-[11px] truncate">
                                Available Clinic Schedule: <strong x-text="doctorSchedule"></strong> (other weekdays disabled)
                            </p>
                        </div>
                    </div>

                    <!-- Modal Body / Content (2-Column Desktop Grid, 1-Column Stack on Mobile) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 overflow-y-auto divide-y lg:divide-y-0 lg:divide-x divide-slate-100 dark:divide-slate-800 flex-1" x-init="initCalendar(); fetchAvailability();">
                        
                        <!-- Left Column: Calendar Date Picker (7 cols on lg, full width on mobile) -->
                        <div class="lg:col-span-7 p-4 sm:p-6 flex flex-col justify-between">
                            <div>
                                <!-- Month Header & Navigation -->
                                <div class="flex items-center justify-between mb-3 sm:mb-4">
                                    <div>
                                        <div class="text-[9px] sm:text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">Step 1 • Pick Date</div>
                                        <h4 class="font-display font-extrabold text-base sm:text-lg text-slate-900 dark:text-white" x-text="monthName + ' ' + currentYear"></h4>
                                    </div>
                                    <div class="flex items-center gap-1 sm:gap-1.5 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                                        <button type="button" 
                                                @click="prevMonth()" 
                                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 hover:shadow-xs disabled:opacity-30 disabled:cursor-not-allowed transition-all cursor-pointer" 
                                                :disabled="isPrevMonthDisabled()"
                                                title="Previous Month">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                        </button>
                                        <button type="button" 
                                                @click="nextMonth()" 
                                                class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 hover:shadow-xs disabled:opacity-30 disabled:cursor-not-allowed transition-all cursor-pointer" 
                                                :disabled="isNextMonthDisabled()"
                                                title="Next Month">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Day-of-week Headers (Su/Mo on mobile, Sun/Mon on sm+) -->
                                <div class="grid grid-cols-7 gap-1 sm:gap-1.5 text-center mb-1.5 sm:mb-2">
                                    <template x-for="dayName in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="dayName">
                                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider py-1">
                                            <span class="sm:hidden" x-text="dayName.slice(0, 2)"></span>
                                            <span class="hidden sm:inline" x-text="dayName"></span>
                                        </span>
                                    </template>
                                </div>

                                <!-- Calendar Day Cells Grid (44px min height for mobile touch targets) -->
                                <div class="grid grid-cols-7 gap-1 sm:gap-1.5">
                                    <template x-for="blank in startDay" :key="'blank-'+blank">
                                        <div class="h-10 sm:h-12"></div>
                                    </template>
                                    
                                    <template x-for="day in daysInMonth" :key="day">
                                        <button type="button"
                                            @click="!isPastDate(day) && !isDayFull(day) && selectDate(getDateString(day))"
                                            :disabled="isPastDate(day) || isDayFull(day)"
                                            class="min-h-[42px] h-10 sm:h-12 rounded-xl flex flex-col items-center justify-center transition-all duration-150 relative border text-xs select-none group touch-manipulation"
                                            :class="{
                                                'bg-slate-100/60 dark:bg-slate-800/40 text-slate-300 dark:text-slate-600 border-transparent cursor-not-allowed': isPastDate(day),
                                                'bg-slate-50/50 dark:bg-slate-800/20 text-slate-400 dark:text-slate-500 border-dashed border-slate-200/70 dark:border-slate-800 cursor-not-allowed opacity-60': !isPastDate(day) && isDayFull(day),
                                                'bg-amber-50/70 hover:bg-amber-100/90 dark:bg-amber-950/20 dark:hover:bg-amber-900/40 text-amber-900 dark:text-amber-200 border-amber-300/60 dark:border-amber-700/50 cursor-pointer hover:scale-[1.03] active:scale-[0.98]': !isPastDate(day) && isDayLimited(day) && !isDayFull(day) && selectedDate !== getDateString(day),
                                                'bg-white dark:bg-slate-800/80 hover:bg-blue-50/50 dark:hover:bg-blue-950/30 text-slate-800 dark:text-slate-200 border-slate-200/80 dark:border-slate-700/80 hover:border-blue-300 dark:hover:border-blue-700 cursor-pointer hover:scale-[1.03] active:scale-[0.98] shadow-2xs': !isPastDate(day) && !isDayLimited(day) && !isDayFull(day) && selectedDate !== getDateString(day),
                                                'bg-blue-600! dark:bg-blue-600! text-white! border-blue-600! font-bold shadow-md shadow-blue-600/30 ring-2 ring-blue-500 ring-offset-2 dark:ring-offset-slate-900 scale-[1.04] z-10': selectedDate === getDateString(day)
                                            }">
                                            
                                            <div class="flex items-center gap-1">
                                                <span class="font-bold text-xs sm:text-sm" x-text="day"></span>
                                                <!-- Marker for original appointment date -->
                                                <span x-show="getDateString(day) === currentAppointmentDate && selectedDate !== getDateString(day)" 
                                                      title="Current Appointment Date"
                                                      class="w-1.5 h-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                            </div>

                                            <!-- Micro slot indicator for pedia -->
                                            <span x-show="type === 'pedia' && !isPastDate(day) && !isFollowUp && selectedDate !== getDateString(day)" 
                                                  class="text-[8px] sm:text-[9px] leading-tight font-medium truncate max-w-full px-0.5 mt-0.5" 
                                                  :class="isDayLimited(day) ? 'text-amber-700 dark:text-amber-300' : 'text-slate-400 dark:text-slate-500'"
                                                  x-text="getSlotsRemainingText(getDateString(day))"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Legend (Mobile Responsive Wrap) -->
                            <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-2 text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <span class="inline-flex items-center gap-1 sm:gap-1.5">
                                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 shadow-2xs"></span>
                                        <span>Available</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 sm:gap-1.5">
                                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-amber-400"></span>
                                        <span>Few Slots</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 sm:gap-1.5">
                                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                        <span>Unavailable</span>
                                    </span>
                                </div>
                                <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] text-blue-600 dark:text-blue-400 font-medium">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    <span>Original Date</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column: Time Slot & Reschedule Summary (5 cols on lg, full width on mobile) -->
                        <div class="lg:col-span-5 p-4 sm:p-6 bg-slate-50/60 dark:bg-slate-900/50 flex flex-col justify-between">
                            <div>
                                <!-- Schedule Comparison Overview Bento -->
                                <div class="p-3 sm:p-3.5 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs mb-3 sm:mb-4">
                                    <div class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-2 flex items-center justify-between">
                                        <span>Schedule Overview</span>
                                        <span class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1" x-show="selectedDate && selectedTime">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                            Ready to Confirm
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
                                        <!-- Current Booking -->
                                        <div class="p-2 sm:p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800/80">
                                            <div class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Current Schedule</div>
                                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5 flex items-center gap-1.5 flex-wrap">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>{{ $appointment->preferred_date->format('D, M j, Y') }}</span>
                                                @if($appointment->preferred_time)
                                                    <span class="text-[10px] font-semibold text-slate-500 dark:text-slate-400">• {{ $appointment->preferred_time }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- New Schedule -->
                                        <div class="p-2 sm:p-2.5 rounded-xl border transition-all"
                                             :class="(selectedDate && selectedTime) 
                                                 ? 'bg-blue-50/90 dark:bg-blue-950/40 border-blue-300 dark:border-blue-800 shadow-2xs' 
                                                 : 'bg-slate-50/50 dark:bg-slate-900/40 border-dashed border-slate-300 dark:border-slate-700/80'">
                                            <div class="text-[9px] font-bold uppercase tracking-wider"
                                                 :class="(selectedDate && selectedTime) ? 'text-blue-700 dark:text-blue-300' : 'text-slate-400 dark:text-slate-500'">
                                                New Schedule
                                            </div>
                                            <template x-if="selectedDate">
                                                <div class="mt-0.5">
                                                    <div class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <span x-text="formatDateShort(selectedDate)"></span>
                                                    </div>
                                                    <div class="text-[11px] mt-0.5 flex items-center gap-1.5"
                                                         :class="selectedTime ? 'font-semibold text-blue-700 dark:text-blue-300' : 'text-amber-600 dark:text-amber-400 italic'">
                                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <span x-text="selectedTime ? selectedTime : 'Choose arrival window below...'"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!selectedDate">
                                                <div class="text-xs text-slate-400 dark:text-slate-500 italic mt-0.5 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span>Select a date on calendar</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Time Slot Picker -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Step 2 • Arrival Window</span>
                                        </h4>
                                    </div>

                                    <!-- Empty state: No date chosen yet -->
                                    <div x-show="!selectedDate" class="text-center py-6 sm:py-7 px-4 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800 bg-white/60 dark:bg-slate-800/30">
                                        <div class="w-8 h-8 sm:w-9 sm:h-9 mx-auto rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Choose a Consultation Date</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 max-w-xs mx-auto">
                                            Click an available date on the calendar above to view arrival windows.
                                        </p>
                                    </div>

                                    <!-- Loading State -->
                                    <div x-show="selectedDate && loadingSlots" class="text-center py-6 sm:py-7 px-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-800/60">
                                        <svg class="animate-spin h-6 w-6 text-blue-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Fetching available arrival windows...</p>
                                    </div>

                                    <!-- No slots available on selected date -->
                                    <div x-show="selectedDate && !loadingSlots && timeSlots.length === 0" class="text-center py-5 sm:py-6 px-4 rounded-2xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50">
                                        <div class="w-8 h-8 mx-auto rounded-full bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-rose-800 dark:text-rose-300">No Slots Available</p>
                                        <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5">Please choose a different date on the calendar.</p>
                                    </div>

                                    <!-- Slots Grid (Touch friendly) -->
                                    <div x-show="selectedDate && !loadingSlots && timeSlots.length > 0" class="space-y-1.5">
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 mb-1 flex items-center justify-between">
                                            <span>Select your arrival window:</span>
                                            <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500" x-text="timeSlots.length + ' available'"></span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 max-h-44 overflow-y-auto pr-1">
                                            <template x-for="slot in timeSlots" :key="slot.value">
                                                <button type="button" 
                                                        @click="selectedTime = slot.value"
                                                        class="min-h-[40px] p-2 sm:p-2.5 rounded-xl border text-[11px] sm:text-xs font-semibold text-center transition-all cursor-pointer flex items-center justify-center gap-1.5 touch-manipulation active:scale-[0.98]"
                                                        :class="selectedTime === slot.value 
                                                            ? 'border-blue-600 bg-blue-600 text-white font-bold shadow-sm shadow-blue-600/30 ring-2 ring-blue-500 ring-offset-1 dark:ring-offset-slate-900' 
                                                            : 'border-slate-200/90 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50/50 dark:hover:bg-blue-950/20'">
                                                    <svg class="w-3.5 h-3.5 shrink-0" :class="selectedTime === slot.value ? 'text-white' : 'text-slate-400 dark:text-slate-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <span class="truncate" x-text="slot.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Triage Advice Card -->
                            <div class="mt-3 sm:mt-4 p-3 rounded-xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200/70 dark:border-amber-800/50 flex items-start gap-2.5 text-[10px] sm:text-[11px] text-amber-900 dark:text-amber-200">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <p class="leading-relaxed">
                                    <strong>Triage Processing:</strong> Please arrive <strong>30 minutes before</strong> your arrival window for vital signs check and queue number assignment.
                                </p>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer (Balanced Mobile Layout with 50/50 Touch Buttons) -->
                    <div class="px-4 py-3 sm:px-6 sm:py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800/80 flex flex-col-reverse sm:flex-row items-center justify-between gap-2.5 sm:gap-3 shrink-0">
                        <div class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 text-center sm:text-left w-full sm:w-auto">
                            <template x-if="!selectedDate || !selectedTime">
                                <span class="flex items-center justify-center sm:justify-start gap-1.5 text-slate-400 dark:text-slate-500">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Select date & arrival window to confirm</span>
                                </span>
                            </template>
                            <template x-if="selectedDate && selectedTime">
                                <span class="flex items-center justify-center sm:justify-start gap-1.5 font-bold text-blue-600 dark:text-blue-400">
                                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    <span>New appointment schedule ready</span>
                                </span>
                            </template>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-2.5 w-full sm:w-auto">
                            <button type="button" 
                                    @click="showReschedule = false" 
                                    class="w-1/2 sm:w-auto inline-flex items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 sm:px-5 py-2.5 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 active:scale-[0.98] transition-all cursor-pointer">
                                {{ __('Cancel') }}
                            </button>

                            <form action="{{ route('appointment.reschedule') }}" method="POST" class="w-1/2 sm:w-auto">
                                @csrf
                                <input type="hidden" name="new_date" x-model="selectedDate">
                                <input type="hidden" name="new_time" x-model="selectedTime">
                                <button type="submit" 
                                        :disabled="!selectedDate || !selectedTime"
                                        :class="(!selectedDate || !selectedTime) 
                                            ? 'opacity-40 cursor-not-allowed bg-slate-300 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-transparent' 
                                            : 'bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-600/30 hover:scale-[1.02] active:scale-[0.98] cursor-pointer border-transparent'"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 sm:gap-2 rounded-xl border px-4 sm:px-6 py-2.5 text-xs font-bold transition-all truncate">
                                    <span>{{ __('Confirm') }}</span>
                                    <span class="hidden sm:inline">{{ __('Reschedule') }}</span>
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        </div>
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
            currentAppointmentDate: '{{ $appointment->preferred_date->format('Y-m-d') }}',
            
            async openRescheduleModal() {
                this.showReschedule = true;
                this.selectedDate = '';
                this.selectedTime = '';
                this.timeSlots = [];
                
                let today = new Date();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                this.updateCalendar();
                
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
                let m = String(this.currentMonth + 1).padStart(2, '0');
                let d = String(day).padStart(2, '0');
                return `${this.currentYear}-${m}-${d}`;
            },
            isPastDate(day) {
                let today = new Date();
                today.setHours(0,0,0,0);
                let checkDate = new Date(this.currentYear, this.currentMonth, day);
                checkDate.setHours(0,0,0,0);
                return checkDate < today;
            },
            isDayFull(day) {
                let ds = this.getDateString(day);
                
                if (this.isFollowUp) {
                    if (!this.validDays) return true;
                    let d = new Date(this.currentYear, this.currentMonth, day);
                    let dayShort = d.toLocaleString('en-US', { weekday: 'short' });
                    return !this.validDays.includes(dayShort);
                } else {
                    let slotData = this.availability[ds];
                    if (!slotData) return false;
                    if (typeof slotData === 'object') {
                        if (slotData.capacity <= 0) return true;
                        return slotData.available <= 0;
                    }
                    return false;
                }
            },
            isDayLimited(day) {
                if (this.isFollowUp) return false;

                let ds = this.getDateString(day);
                let slotData = this.availability[ds];
                if (!slotData || typeof slotData !== 'object' || slotData.capacity <= 0) return false;
                
                let limitThreshold = slotData.capacity * 0.25;
                return slotData.available > 0 && slotData.available <= limitThreshold;
            },
            getSlotsRemainingText(date) {
                const slotData = this.availability[date];
                if (!slotData || typeof slotData !== 'object') return '';
                if (slotData.available !== undefined) {
                    if (slotData.available <= 0) return 'Full';
                    return slotData.available + ' left';
                }
                return '';
            },

            async fetchAvailability() {
                let d2 = new Date(this.currentYear, this.currentMonth + 1, 0);
                let m = String(this.currentMonth + 1).padStart(2, '0');
                let lastDay = String(d2.getDate()).padStart(2, '0');
                
                const start = `${this.currentYear}-${m}-01`;
                const end = `${this.currentYear}-${m}-${lastDay}`;
                
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
                const parts = dateString.split('-');
                if (parts.length === 3) {
                    const date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    return date.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                }
                return dateString;
            },

            formatDateShort(dateString) {
                if(!dateString) return '';
                const parts = dateString.split('-');
                if (parts.length === 3) {
                    const date = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
                }
                return dateString;
            }
        }
    }
</script>
@endsection
