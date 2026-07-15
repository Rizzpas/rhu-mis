@extends('layouts.lab')

@section('header', 'Laboratory / Radiology Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Stats / Header -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-slate-100 dark:border-slate-800 p-6 flex flex-col justify-center transition-all duration-300">
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Active Requests</h3>
            <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-500">{{ $ancillaryRequests->count() }} <span class="text-lg font-medium text-slate-400 dark:text-slate-500">Pending</span></p>
        </div>
        <div class="md:col-span-2 relative overflow-hidden bg-gradient-to-r from-emerald-600 to-green-700 dark:from-emerald-800 dark:to-green-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.1)] border border-emerald-700 dark:border-emerald-900 p-8 flex items-center justify-between text-white transition-colors duration-300">
            <!-- Glassmorphism decorative elements -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-green-400 opacity-10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex items-center gap-6">
                <div class="shrink-0">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center overflow-hidden shadow-lg">
                        @if(auth()->user() && auth()->user()->avatar_url)
                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-black text-white">
                                {{ auth()->user() ? auth()->user()->initials : 'LB' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold mb-2 drop-shadow-sm tracking-tight">Welcome back, {{ auth()->user() ? auth()->user()->formatted_name : 'Laboratory Staff' }}</h2>
                    <p class="text-emerald-50 dark:text-emerald-100/80 text-sm font-medium max-w-lg">Manage pending laboratory and radiology requests, encode findings, and update patient records.</p>
                </div>
            </div>
            <div class="hidden sm:block relative z-10 bg-white/20 dark:bg-black/20 p-4 rounded-2xl backdrop-blur-md shadow-[0_8px_30px_rgb(0,0,0,0.1)] border border-white/10 transition-transform duration-500 hover:rotate-3">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Simulation Mode Banner -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 flex items-center gap-4 shadow-sm animate-pulse">
        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 border border-indigo-200">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <h4 class="text-sm font-black text-indigo-900 uppercase tracking-widest">Simulation Mode Active</h4>
            <p class="text-xs text-indigo-700 font-medium">The Ancillary and Pharmacy modules are currently placeholders for structural demonstration. Results encoded here will be visible to doctors for flow testing only.</p>
        </div>
    </div>

    <!-- Active Consultations List -->
    <div class="pt-4">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h3 class="font-extrabold text-slate-900 dark:text-white text-2xl flex items-center gap-2">
                    <span class="bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 p-1.5 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </span>
                    Today's Requests
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">Patients sent for laboratory/radiology work today.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-lg border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="p-4 sm:p-5 w-[15%]">Test Details</th>
                            <th class="p-4 sm:p-5 flex-1">Patient Details</th>
                            <th class="p-4 sm:p-5 w-[25%]">Requested By</th>
                            <th class="p-4 sm:p-5 w-[15%]">Status</th>
                            <th class="p-4 sm:p-5 w-[20%] text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                        @forelse($ancillaryRequests as $request)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors group">
                                <td class="p-4 sm:p-5">
                                    <span class="bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-400 font-extrabold px-3 py-1.5 rounded-lg text-sm sm:text-base border border-emerald-200 dark:border-emerald-800/30 inline-block shadow-sm">
                                        {{ $request->type }}
                                    </span>
                                    <p class="text-xs font-bold text-slate-500 mt-2">{{ $request->test_name }}</p>
                                </td>
                                <td class="p-4 sm:p-5 align-top">
                                    <p class="font-bold text-slate-900 dark:text-white text-base group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $request->consultation->patient->full_name }}</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $request->consultation->patient->classification }}</span> &bull; 
                                        {{ \Carbon\Carbon::parse($request->consultation->patient->dob)->age }} yrs &bull; 
                                        {{ $request->consultation->patient->sex ?? 'N/A' }}
                                    </p>
                                </td>
                                <td class="p-4 sm:p-5 align-top">
                                    @if($request->consultation->doctor)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs shrink-0 shadow-sm border border-slate-200 dark:border-slate-600">
                                                {{ strtoupper(substr(str_replace('Dr. ', '', $request->consultation->doctor->name), 0, 1)) }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="font-bold text-slate-800 dark:text-white text-sm truncate">{{ $request->consultation->doctor->name }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 italic font-medium text-xs sm:text-sm">Unassigned</span>
                                    @endif
                                </td>
                                <td class="p-4 sm:p-5 align-top">
                                    @if($request->status === 'Pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-extrabold tracking-wider bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-400 border border-blue-200 dark:border-blue-800/30 uppercase">
                                            Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] sm:text-xs font-extrabold tracking-wider bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-400 border border-green-200 dark:border-green-800/30 uppercase">
                                            Done
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 sm:p-5 text-right align-top" x-data="{ open: false }">
                                    @if($request->status === 'Pending')
                                        <button type="button" @click="open = true" class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 border border-emerald-200 dark:border-emerald-800/50 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all duration-300 w-full sm:w-auto inline-flex items-center justify-center gap-1.5 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 hover:shadow-md hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Mark Complete</span>
                                        </button>
    
                                        <!-- Upload Results Modal -->
                                        <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="open = false" aria-hidden="true"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div x-show="open" x-transition class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-700">
                                                    <form action="{{ route('lab.ancillary.complete', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-6">
                                                            <h3 class="text-xl leading-6 font-extrabold text-slate-900 dark:text-white" id="modal-title">Upload Results: {{ $request->test_name }}</h3>
                                                            <div class="mt-4">
                                                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Test Results / Notes</label>
                                                                <textarea name="result_text" rows="4" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm p-3 placeholder-slate-400" placeholder="Enter findings, physical notes, or diagnostic results here..."></textarea>
                                                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Digital document uploading is pending future implementation. Please encode essential results here.</p>
                                                            </div>
                                                        </div>
                                                        <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3 rounded-b-2xl">
                                                            <button type="button" @click="open = false" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Cancel</button>
                                                            <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white transition focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 flex items-center justify-center">
                                                                Submit Results
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 italic flex items-center justify-end gap-1">
                                            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Results Uploaded
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 sm:p-20 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 rounded-full mb-5 shadow-sm border border-slate-100 dark:border-slate-800">
                                            <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        </div>
                                        <p class="text-xl font-extrabold text-slate-800 dark:text-white mb-2">No active patients found</p>
                                        <p class="text-sm max-w-sm mx-auto">There are no patients currently in consultation or completed today.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
