@extends('layouts.frontdesk')

@section('title', 'Queue Overview')
@section('header', 'Queue Overview')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Live Queue Dashboard</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">Monitor all patients currently waiting for their assigned doctors and nurses.</p>
    </div>
    <div>
        <button onclick="window.location.reload()" class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition flex items-center gap-2 font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh List
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($queuesByStaff as $staffId => $data)
        @php
            $staff = $data['staff'];
            $role = $data['role'];
            $patients = $data['patients'];
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full transition hover:shadow-md">
            <div class="p-4 {{ $role === 'Doctor' ? 'bg-indigo-50 border-b border-indigo-100' : 'bg-blue-50 border-b border-blue-100' }}">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $staff->formatted_name }}</h3>
                        <p class="text-xs font-semibold {{ $role === 'Doctor' ? 'text-indigo-600' : 'text-blue-600' }} mt-0.5">{{ $staff->specialization ?? $role }}</p>
                    </div>
                    <div class="bg-white px-3 py-1.5 rounded-lg shadow-sm border {{ $role === 'Doctor' ? 'border-indigo-200 text-indigo-700' : 'border-blue-200 text-blue-700' }} text-sm font-bold flex flex-col items-center justify-center leading-none">
                        <span class="text-lg">{{ count($patients) }}</span>
                        <span class="text-[10px] uppercase tracking-wider">Waiting</span>
                    </div>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto max-h-[28rem] min-h-[12rem]">
                @if(count($patients) > 0)
                    <ul class="divide-y divide-gray-100">
                        @foreach($patients as $index => $consultation)
                        <li class="p-4 hover:bg-gray-50 transition relative overflow-hidden group">
                            <!-- Left Accent Line -->
                            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $consultation->status === 'queued' ? 'bg-orange-400' : ($consultation->status === 'called' ? 'bg-indigo-500' : 'bg-teal-500') }}"></div>
                            
                            <div class="flex justify-between items-start pl-2">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-teal-700 transition">
                                        <span class="text-gray-400 mr-1 font-mono text-xs">#{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        {{ $consultation->patient->full_name }}
                                    </p>
                                    <div class="mt-1.5 flex items-center gap-2 flex-wrap">
                                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded border
                                            {{ $consultation->patient->classification === 'Senior Citizen' ? 'bg-blue-50 border-blue-200 text-blue-700' : 
                                               ($consultation->patient->classification === 'Pediatric' ? 'bg-purple-50 border-purple-200 text-purple-700' : 
                                               ($consultation->patient->classification === 'PWD' ? 'bg-orange-50 border-orange-200 text-orange-700' : 'bg-gray-50 border-gray-200 text-gray-600')) }}">
                                            {{ $consultation->patient->classification }}
                                        </span>
                                        <span class="text-xs font-semibold text-gray-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ $consultation->created_at->format('h:i A') }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 flex flex-wrap gap-x-3 gap-y-1">
                                        @if($consultation->preTriage)
                                            @php $pt = $consultation->preTriage; @endphp
                                            @if($pt->temperature) <span><b>Temp:</b> {{ $pt->temperature }}°C</span> @endif
                                            @if($pt->blood_pressure) <span><b>BP:</b> {{ $pt->blood_pressure }}</span> @endif
                                            @if($pt->weight) <span><b>Wt:</b> {{ $pt->weight }}kg</span> @endif
                                            @if($pt->height) <span><b>Ht:</b> {{ $pt->height }}cm</span> @endif
                                            @if($pt->heart_rate) <span><b>HR:</b> {{ $pt->heart_rate }} bpm</span> @endif
                                            @if($pt->pulse_rate) <span><b>PR:</b> {{ $pt->pulse_rate }} bpm</span> @endif
                                            @php $oxy = $pt->oxygen_saturation ?: $pt->spo2; @endphp
                                            @if($oxy) <span><b>O2/SpO2:</b> {{ $oxy }}%</span> @endif
                                        @elseif($consultation->temperature) {{-- Fallback to consultation vitals if pre-triage is skipped --}}
                                            <span><b>Temp:</b> {{ $consultation->temperature }}°C</span>
                                            @if($consultation->blood_pressure) <span><b>BP:</b> {{ $consultation->blood_pressure }}</span> @endif
                                            @if($consultation->weight) <span><b>Wt:</b> {{ $consultation->weight }}kg</span> @endif
                                            @php $oxyFallback = $consultation->spo2; @endphp
                                            @if($oxyFallback) <span><b>SpO2:</b> {{ $oxyFallback }}%</span> @endif
                                        @else
                                            <span class="italic text-gray-400">No vitals recorded</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="whitespace-nowrap text-[10px] font-bold uppercase tracking-widest bg-white border px-2 py-1 rounded 
                                        {{ $consultation->status === 'queued' ? 'text-orange-600 border-orange-200 shadow-sm' : 
                                           ($consultation->status === 'called' ? 'text-indigo-600 border-indigo-200 shadow-sm' : 
                                           ($consultation->status === 'active' ? 'text-amber-600 border-amber-200 shadow-sm' : 'text-teal-600 border-teal-200 shadow-sm')) }}">
                                        {{ $consultation->status === 'active' ? 'In Consult' : $consultation->status }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="h-full flex flex-col items-center justify-center p-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-sm font-bold">Queue is empty</p>
                        <p class="text-xs mt-1">No patients waiting</p>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white p-12 rounded-xl border border-gray-200 text-center shadow-sm">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <h3 class="text-lg font-bold text-gray-800">No active queues today</h3>
            <p class="text-gray-500 mt-2">There are currently no assigned patients for any doctor or nurse today.</p>
        </div>
    @endforelse
</div>

@if(count($unassigned) > 0)
<div class="mt-8 mb-6">
    <div class="flex items-center gap-3 mb-4">
        <h3 class="text-lg font-bold text-red-600">Unassigned Patients</h3>
        <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full border border-red-200">{{ count($unassigned) }} Action Required</span>
    </div>
    
    <div class="bg-red-50 rounded-xl border border-red-200 shadow-sm overflow-hidden">
        <ul class="divide-y divide-red-100">
            @foreach($unassigned as $consultation)
            <li class="p-5 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-red-100/50 transition gap-4">
                <div>
                    <p class="font-bold text-gray-900 text-lg">{{ $consultation->patient->full_name }}</p>
                    <div class="mt-1 text-sm text-red-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Pending Triage Assignment &bull; Added {{ $consultation->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('frontdesk.registration.index', ['selected_id' => $consultation->patient_id]) }}" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-white bg-red-600 border border-red-700 px-5 py-2.5 rounded-lg hover:bg-red-700 transition shadow-sm w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Resolve Assignment
                    </a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@endsection
