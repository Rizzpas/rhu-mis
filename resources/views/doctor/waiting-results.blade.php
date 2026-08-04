@extends('layouts.doctor')

@section('header', 'Patients Waiting for Lab / Radiology Results')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Page Header & Quick Stats -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 p-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 p-2 rounded-xl">
                    <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </span>
                Pending Ancillary Tests
            </h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Track patients undergoing laboratory or radiology diagnostic procedures.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('doctor.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-bold rounded-xl transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Active Queue
            </a>
        </div>
    </div>

    <!-- Awaiting Patients List -->
    <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                        <th class="p-4 sm:p-5 w-[25%]">Patient Details</th>
                        <th class="p-4 sm:p-5 w-[30%]">Requested Ancillaries</th>
                        <th class="p-4 sm:p-5 w-[20%]">Status</th>
                        <th class="p-4 sm:p-5 w-[25%] text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                    @forelse($awaitingPatients as $consultation)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                            <td class="p-4 sm:p-5">
                                <p class="font-extrabold text-slate-900 dark:text-white">{{ $consultation->patient->full_name }}</p>
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ $consultation->patient->patient_id }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $consultation->patient->dob ? \Carbon\Carbon::parse($consultation->patient->dob)->age : '?' }} yrs • {{ $consultation->patient->sex }}</p>
                            </td>
                            <td class="p-4 sm:p-5">
                                <div class="space-y-1.5">
                                    @foreach($consultation->ancillaryRequests as $req)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $req->type === 'Laboratory' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' }}">
                                                {{ $req->type }}
                                            </span>
                                            <span class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $req->test_name }}</span>
                                            @if($req->status === 'Done')
                                                <span class="text-[9px] font-extrabold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Done</span>
                                            @else
                                                <span class="text-[9px] font-extrabold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Pending</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4 sm:p-5">
                                @if($consultation->status === 'results_ready')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300 border border-emerald-300 animate-pulse">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Results Ready! (Next in Line)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 border border-amber-200">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Awaiting Lab Staff
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 sm:p-5 text-right">
                                <a href="{{ route('doctor.consultation.start', $consultation->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                                    Open Consultation
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-900 rounded-full mb-3 border border-slate-100 dark:border-slate-800">
                                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-base font-bold text-slate-700 dark:text-slate-200">No patients waiting for results</p>
                                    <p class="text-xs text-slate-400 mt-1">All requested laboratory and radiology tests have been completed.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($awaitingPatients->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                {{ $awaitingPatients->links('vendor.pagination.shadcn') }}
            </div>
        @endif
    </div>
</div>
@endsection
