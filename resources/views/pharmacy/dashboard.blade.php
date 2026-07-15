@extends('layouts.pharmacy')

@section('header', 'Pharmacy Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
    <!-- Stats / Header -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-slate-100 dark:border-slate-800 p-6 flex flex-col justify-center transition-all duration-300">
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pending Prescriptions</h3>
            <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-500">{{ $prescriptions->count() }} <span class="text-lg font-medium text-slate-400 dark:text-slate-500">Pending</span></p>
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
                                {{ auth()->user() ? auth()->user()->initials : 'PH' }}
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold mb-2 drop-shadow-sm tracking-tight">Welcome back, {{ auth()->user() ? auth()->user()->formatted_name : 'Pharmacist' }}</h2>
                    <p class="text-emerald-50 dark:text-emerald-100/80 text-sm font-medium max-w-lg">Manage pending prescriptions, dispense medicines, and track pharmacy inventory.</p>
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
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">Patients waiting for prescription dispensing today.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm hover:shadow-lg border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="p-4 sm:p-5 w-[15%]">Prescription Details</th>
                            <th class="p-4 sm:p-5 flex-1">Patient Details</th>
                            <th class="p-4 sm:p-5 w-[25%]">Requested By</th>
                            <th class="p-4 sm:p-5 w-[15%]">Status</th>
                            <th class="p-4 sm:p-5 w-[20%] text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                        @forelse($prescriptions as $request)
                            <!-- Placeholder loop content -->
                        @empty
                            <tr>
                                <td colspan="5" class="p-12 sm:p-20 text-center text-slate-500 dark:text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 rounded-full mb-5 shadow-sm border border-slate-100 dark:border-slate-800">
                                            <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        </div>
                                        <p class="text-xl font-extrabold text-slate-800 dark:text-white mb-2">No pending prescriptions found</p>
                                        <p class="text-sm max-w-sm mx-auto">There are no patients waiting for prescription dispensing today.</p>
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
