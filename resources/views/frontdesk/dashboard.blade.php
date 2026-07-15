@extends('layouts.frontdesk')

@section('header', 'Front Desk Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12 cursor-default" x-data="dashboardCalendar()">
    
    <!-- Welcome Banner & Quick Stats (Optional Future Use) -->
    <div class="bg-teal-700 bg-linear-to-r from-teal-700 to-teal-900 dark:from-teal-800 dark:to-teal-950 rounded-2xl shadow-lg border border-teal-800 dark:border-teal-900 overflow-hidden relative transition-colors duration-300">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="p-8 relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-6">
                <div class="shrink-0">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center overflow-hidden shadow-lg">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-black text-white">
                                {{ auth()->user()->initials }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight drop-shadow-sm">Welcome back, {{ auth()->user()->formatted_name }}!</h1>
                    <p class="text-teal-50 dark:text-teal-100/80 ml-0.5 mt-2 text-base font-medium">Manage today's scheduled consultations and appointments here.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('frontdesk.registration.index') }}" class="bg-white/10 dark:bg-black/20 hover:bg-white/20 dark:hover:bg-black/40 text-white border border-white/20 dark:border-white/10 px-5 py-2.5 rounded-xl font-bold shadow-sm backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Walk-in Registration
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Left Sidebar: Staff Widget -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-lg border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700/70 bg-slate-50/50 dark:bg-slate-900/50">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Staff Present
                    </h2>
                </div>
                <div class="p-5 space-y-6 max-h-[700px] overflow-y-auto">
                    @php
                        $hasAnyStaff = false;
                    @endphp
                    @foreach($staffGroups as $groupName => $staffMembers)
                        @if($staffMembers->count() > 0)
                            @php $hasAnyStaff = true; @endphp
                            <div>
                                <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">{{ $groupName }}</h3>
                                <div class="space-y-2">
                                    @foreach($staffMembers as $staff)
                                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                                            <div class="w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold shrink-0 shadow-sm overflow-hidden border border-teal-200 dark:border-teal-700">
                                                @if($staff->avatar_url)
                                                    <img src="{{ $staff->avatar_url }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ $staff->initials }}
                                                @endif
                                            </div>
                                            <div class="overflow-hidden">
                                                <h4 class="text-sm font-bold text-slate-800 dark:text-white group-hover:text-teal-600 dark:group-hover:text-teal-400 truncate transition-colors">{{ $staff->formatted_name }}</h4>
                                                <p class="text-[10px] uppercase font-bold tracking-wider text-slate-500 dark:text-slate-400 truncate">{{ ucwords(str_replace('_', ' ', $staff->role)) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!$hasAnyStaff)
                        <div class="text-center py-4 text-sm text-slate-500 dark:text-slate-400 italic">No staff currently present.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Calendar Card -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden h-full flex flex-col transition-all duration-300">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700/70 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row md:items-center justify-between shrink-0 gap-4">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Appointments Calendar
                    </h2>
                    <!-- Legend -->
                    <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-gray-600 dark:text-gray-400">
                        <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#0d9488]"></span> Approved</div>
                        <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#ca8a04]"></span> Registered</div>
                        <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-[#16a34a]"></span> Done</div>
                    </div>
                </div>
                
                <div class="p-6 grow">
                    <!-- FullCalendar Container -->
                    <div id="calendar" class="min-h-[700px]"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Day View Modal (Alpine.js) -->
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        
        <!-- Background overlay -->
        <div x-show="isModalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
             @click="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal panel -->
            <div x-show="isModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-gray-100 dark:border-gray-700 flex flex-col max-h-[85vh]">
                
                <!-- Modal Header -->
                <div class="bg-linear-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900 dark:text-white" id="modal-title">
                            Appointments for <span x-text="selectedDateText" class="text-teal-700"></span>
                        </h3>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5" x-text="selectedEvents.length + ' scheduled visit(s)'"></p>
                    </div>
                    <button type="button" @click="closeModal()" class="rounded-full bg-white dark:bg-gray-800 p-2 text-gray-400 hover:text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-900 focus:outline-none transition-colors border border-gray-200 dark:border-gray-700 shadow-sm">
                        <span class="sr-only">Close panel</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable List of Appointments) -->
                <div class="px-6 py-4 overflow-y-auto grow bg-gray-50 dark:bg-gray-900/50">
                    
                    <template x-if="selectedEvents.length === 0">
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No appointments</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">There are no approved appointments for this date.</p>
                        </div>
                    </template>

                    <div class="space-y-4" x-show="selectedEvents.length > 0">
                        <template x-for="event in selectedEvents" :key="event.id">
                            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden hover:border-teal-300 hover:shadow-md transition-all group">
                                <div class="p-5 flex flex-col md:flex-row md:items-start justify-between gap-4">
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="event.extendedProps.time"></span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider text-white" 
                                                  :style="`background-color: ${event.backgroundColor}`"
                                                  x-text="event.extendedProps.status"></span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700" 
                                                  x-text="event.extendedProps.type"></span>
                                        </div>
                                         <h4 class="text-lg font-bold text-teal-900 dark:text-teal-400 group-hover:text-teal-700 dark:group-hover:text-teal-300 transition-colors" x-text="event.extendedProps.patient_name"></h4>
                                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-0.5" x-text="event.extendedProps.classification || 'Unclassified'"></p>
                                        
                                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-4 text-sm">
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span x-text="event.extendedProps.contact || 'No contact'"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 truncate">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                <span x-text="event.extendedProps.email || 'No email'" class="truncate"></span>
                                            </div>
                                            <div class="sm:col-span-2 flex items-start gap-1.5 text-slate-600 dark:text-slate-400">
                                                <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                <span class="italic text-sm" x-text="event.extendedProps.reason"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions for the specific appointment -->
                                    <div class="flex md:flex-col gap-2 shrink-0 md:items-end md:justify-center border-t border-slate-100 dark:border-slate-700 md:border-t-0 pt-3 md:pt-0">
                                        <template x-if="event.extendedProps.status === 'Approved'">
                                            <form :action="`/frontdesk/appointments/${event.id}/check-in`" method="POST" class="w-full md:w-auto">
                                                @csrf
                                                <button type="submit" class="w-full md:w-auto bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition flex items-center justify-center gap-1.5">
                                                    Check In Patient
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                                </button>
                                            </form>
                                        </template>
                                        <template x-if="event.extendedProps.status === 'Registered'">
                                            <span class="w-full md:w-auto bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 dark:border-gray-700 flex items-center justify-center gap-1.5 cursor-not-allowed">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Already Queued
                                            </span>
                                        </template>
                                    </div>
                                    
                                </div>
                            </div>
                        </template>
                    </div>

                </div>
                
                <!-- Modal Footer -->
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-100 dark:border-gray-700 sm:flex sm:flex-row-reverse shrink-0">
                    <button type="button" @click="closeModal()" class="w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 shadow-sm px-5 py-2.5 bg-white dark:bg-gray-800 text-base font-bold text-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 hover:text-gray-900 dark:hover:text-white dark:text-white focus:outline-none sm:w-auto sm:text-sm transition-colors">
                        Close Overview
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardCalendar', () => ({
            isModalOpen: false,
            selectedDateText: '',
            selectedEvents: [],
            calendar: null,

            init() {
                var calendarEl = document.getElementById('calendar');
                
                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    timeZone: 'local',
                    initialView: 'dayGridMonth',
                    themeSystem: 'standard',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    events: '/frontdesk/api/appointments',
                    eventColor: '#0d9488', 
                    eventDisplay: 'block',
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },
                    dayMaxEvents: 3, // Allow "more" link when too many events
                    
                    // The core interaction requested by user
                    dateClick: (info) => {
                        this.openDayModal(info.dateStr);
                    },
                    
                    // Make events clickable too to open the same day modal
                    eventClick: (info) => {
                        info.jsEvent.preventDefault(); // don't let the browser navigate
                        // Get the date string of the event start to open that day in local time
                        const date = info.event.start;
                        const pad = n => String(n).padStart(2, '0');
                        const dateStr = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                        this.openDayModal(dateStr);
                    }
                });
                
                this.calendar.render();
            },

            openDayModal(dateStr) {
                // Parse date string locally by appending T00:00:00 to avoid UTC shifting
                const dateObj = new Date(dateStr + 'T00:00:00');
                this.selectedDateText = dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                
                // Get all events for this specific date
                const allEvents = this.calendar.getEvents();
                
                this.selectedEvents = allEvents.filter(event => {
                    const date = event.start;
                    const pad = n => String(n).padStart(2, '0');
                    const eventDateStr = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
                    return eventDateStr === dateStr;
                }).map(event => {
                    return {
                        id: event.id,
                        title: event.title,
                        backgroundColor: event.backgroundColor,
                        extendedProps: event.extendedProps
                    };
                });
                
                // Sort by time
                this.selectedEvents.sort((a, b) => {
                    return new Date('1970/01/01 ' + a.extendedProps.time) - new Date('1970/01/01 ' + b.extendedProps.time);
                });

                this.isModalOpen = true;
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            },

            closeModal() {
                this.isModalOpen = false;
                setTimeout(() => {
                    document.body.style.overflow = '';
                }, 300);
            }
        }));
    });
</script>

<style>
    /* FullCalendar Custom Tailoring */
    .fc {
        font-family: 'Inter', sans-serif;
    }
    
    /* Default (Light) Theme Overrides */
    .fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid {
        border-color: #f1f5f9;
        transition: border-color 0.2s;
    }
    .fc-col-header-cell-cushion {
        padding: 12px 0 !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: #64748b;
    }
    .fc-daygrid-day-number {
        font-weight: 700;
        color: #334155;
        padding: 8px !important;
    }
    .fc-day-today {
        background-color: #f0fdfa !important; /* teal-50 */
    }
    .fc-daygrid-day:hover {
        background-color: #f8fafc;
        cursor: pointer;
    }
    
    /* Dark Mode Theme Overrides using Tailwind's .dark class */
    .dark .fc-theme-standard td, .dark .fc-theme-standard th, .dark .fc-theme-standard .fc-scrollgrid {
        border-color: #334155 !important;
    }
    .dark .fc-col-header-cell-cushion {
        color: #94a3b8;
    }
    .dark .fc-daygrid-day-number {
        color: #e2e8f0;
    }
    .dark .fc-day-today {
        background-color: #115e59 !important; /* teal-800 */
    }
    .dark .fc-day-today .fc-daygrid-day-number {
        color: #ccfbf1 !important;
    }
    .dark .fc-daygrid-day:hover {
        background-color: #1e293b !important;
    }
    .dark .fc-toolbar-title {
        color: #f8fafc !important;
    }

    /* Event Styling */
    .fc-event {
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.75rem;
        font-weight: 600;
        border: none !important;
        margin-bottom: 2px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .fc-event-main {
        color: white !important;
    }
    .fc-button-primary {
        background-color: #0d9488 !important; /* teal-600 */
        border-color: #0d9488 !important;
        font-weight: 600 !important;
        text-transform: capitalize !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    .fc-button-primary:hover {
        background-color: #0f766e !important; /* teal-700 */
        border-color: #0f766e !important;
    }
    .fc-button-primary:not(:disabled).fc-button-active, .fc-button-primary:not(:disabled):active {
        background-color: #115e59 !important; /* teal-800 */
        border-color: #115e59 !important;
    }
    .fc-toolbar-title {
        font-weight: 800 !important;
        color: #111827 !important;
        font-size: 1.5rem !important;
    }
    /* Hide empty time Grid rows */
    .fc-timegrid-slot-minor {
        border-top-style: dashed;
    }
</style>
@endpush
@endsection
