@extends('layouts.lab')

@section('header', $type . ' Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12" x-data="{ activeTab: 'pending' }">
    
    <!-- Header / Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-800 p-6 flex flex-col justify-center">
            <h3 class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pending {{ $type }} Requests</h3>
            <p class="text-4xl font-extrabold text-blue-600 dark:text-blue-500">{{ $pendingRequests->count() }} <span class="text-lg font-medium text-slate-400">Waiting</span></p>
        </div>
        <div class="md:col-span-2 relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-700 dark:from-blue-800 dark:to-indigo-900 rounded-2xl shadow-sm border border-blue-700 dark:border-blue-900 p-8 flex items-center justify-between text-white">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-400 opacity-10 rounded-full blur-3xl"></div>
            
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
                    <h2 class="text-3xl font-extrabold mb-2 drop-shadow-sm tracking-tight">Welcome back, {{ auth()->user()->formatted_name }}</h2>
                    <p class="text-blue-100 text-sm font-medium max-w-lg">Process pending {{ strtolower($type) }} requests and encode diagnostic findings for attending physicians.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ TAB NAVIGATION ═══════════════════ -->
    <div class="bg-slate-100/60 dark:bg-slate-800/50 p-1.5 rounded-xl flex gap-1 border border-slate-200 dark:border-slate-700">
        <button @click="activeTab = 'pending'"
            :class="activeTab === 'pending' ? 'bg-white dark:bg-slate-900 text-blue-700 dark:text-blue-400 shadow-sm border-blue-200 dark:border-blue-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 border-transparent'"
            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 border flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Pending
            <span class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 text-xs font-extrabold px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
        </button>
        <button @click="activeTab = 'finished'"
            :class="activeTab === 'finished' ? 'bg-white dark:bg-slate-900 text-emerald-700 dark:text-emerald-400 shadow-sm border-emerald-200 dark:border-emerald-800' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 border-transparent'"
            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 border flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Finished
            <span class="bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 text-xs font-extrabold px-2 py-0.5 rounded-full">{{ $completedRequests->count() }}</span>
        </button>
        <button @click="activeTab = 'archive'"
            :class="activeTab === 'archive' ? 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 shadow-sm border-slate-300 dark:border-slate-600' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 border-transparent'"
            class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 border flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            Archive
            <span class="bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-extrabold px-2 py-0.5 rounded-full">{{ $archivedRequests->count() }}</span>
        </button>
    </div>

    <!-- ═══════════════════ TAB 1: PENDING TESTS ═══════════════════ -->
    <div x-show="activeTab === 'pending'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-2xl flex items-center gap-2 mb-6">
            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 p-1.5 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </span>
            Pending Tests
        </h3>

        @if($pendingRequests->count() > 0)
            <div id="pending-queue-section" data-dynamic-block="true" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pendingRequests as $index => $req)
                    <div class="group flex flex-col bg-white dark:bg-slate-900 rounded-2xl shadow-sm hover:shadow-xl border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300 relative">
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-400 font-extrabold px-3 py-1.5 rounded-lg text-sm border border-blue-200 dark:border-blue-800/30">
                                    {{ $req->test_name }}
                                </span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                    {{ $req->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h4 class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight mb-1 truncate">{{ $req->consultation->patient->full_name }}</h4>
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 mb-4">
                                <span class="text-emerald-600 dark:text-emerald-400">{{ $req->consultation->patient->classification }}</span>
                                <span>&bull;</span>
                                <span>{{ $req->consultation->patient->dob ? \Carbon\Carbon::parse($req->consultation->patient->dob)->age . ' yrs' : '? yrs' }}</span>
                                <span>&bull;</span>
                                <span>{{ $req->consultation->patient->sex ?? 'N/A' }}</span>
                            </div>

                            <div class="flex-1 mb-5">
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Requested By</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-[10px] border border-slate-200 dark:border-slate-700">
                                        {{ strtoupper(substr(str_replace('Dr. ', '', $req->consultation->doctor->name ?? 'U'), 0, 1)) }}
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $req->consultation->doctor->name ?? 'Unassigned' }}</p>
                                </div>
                                @if($req->remarks)
                                <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-xl border border-yellow-100 dark:border-yellow-900/50">
                                    <p class="text-[10px] font-bold text-yellow-800 dark:text-yellow-500 uppercase tracking-wider mb-0.5">Clinical Remarks</p>
                                    <p class="text-xs text-yellow-900 dark:text-yellow-400 font-medium italic">"{{ $req->remarks }}"</p>
                                </div>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <div x-data="{ open: false }" class="flex-1">
                                    <button type="button" @click="open = true" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all duration-300 text-sm group-hover:ring-4 group-hover:ring-blue-500/20">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Enter Results
                                    </button>

                                    <!-- ═══════ RESULT ENTRY MODAL ═══════ -->
                                    <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                            <div x-show="open" x-transition class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-slate-200 dark:border-slate-700">
                                                <form action="{{ route('lab.ancillary.complete', $req->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-6 max-h-[80vh] overflow-y-auto">
                                                        <div class="flex items-center gap-3 mb-6">
                                                            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $req->test_name }}</h3>
                                                                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Patient: {{ $req->consultation->patient->full_name }}</p>
                                                            </div>
                                                        </div>
                                                        
                                                        @if($req->type === 'Laboratory')
                                                            @include('lab.partials.lab-form', ['req' => $req])
                                                        @else
                                                            @include('lab.partials.rad-form', ['req' => $req])
                                                        @endif
                                                        
                                                        <!-- Optional File Upload (All Tests) -->
                                                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                                                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Attach File / Scan (Optional)</label>
                                                            <input type="file" name="result_file" class="w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/50 dark:file:text-blue-400 transition cursor-pointer">
                                                            <p class="mt-1 text-xs text-slate-400">Accepts JPG, PNG, PDF, DICOM up to 10MB.</p>
                                                        </div>
                                                    </div>
                                                    <div class="bg-slate-50 dark:bg-slate-800/80 px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3 rounded-b-2xl">
                                                        <button type="button" @click="open = false" class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Cancel</button>
                                                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            Submit Results
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Archive Button -->
                                <form action="{{ route('lab.ancillary.archive', $req->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="Mark as No-Show / Archive" class="p-3 bg-slate-100 dark:bg-slate-800 hover:bg-rose-100 dark:hover:bg-rose-900/30 text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 dark:border-slate-700 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 py-20 px-6 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-5 text-blue-500 dark:text-blue-400">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h4 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-2">Queue is Clear!</h4>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">There are currently no pending {{ strtolower($type) }} requests. New requests will appear here automatically.</p>
            </div>
        @endif
    </div>

    <!-- ═══════════════════ TAB 2: FINISHED TESTS ═══════════════════ -->
    <div x-show="activeTab === 'finished'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-xl flex items-center gap-2 mb-6">
            <span class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            Completed (Last 7 Days)
        </h3>

        @if($completedRequests->count() > 0)
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="p-4 w-[25%]">Test Name</th>
                            <th class="p-4 flex-1">Patient</th>
                            <th class="p-4 w-[20%]">Processed By</th>
                            <th class="p-4 w-[20%]">Time Completed</th>
                            <th class="p-4 w-[15%] text-right">Results</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                        @foreach($completedRequests as $req)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors" x-data="{ showResult: false }">
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $req->test_name }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $req->consultation->patient->full_name }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $req->consultation->patient->patient_id }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($req->technician->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ $req->technician->name ?? 'Unknown' }}</p>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">{{ $req->completed_at->format('M d, h:i A') }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <button @click="showResult = !showResult" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        <span x-text="showResult ? 'Hide' : 'View'"></span>
                                    </button>

                                    <div x-show="showResult" x-transition style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showResult = false"></div>
                                        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 max-w-lg w-full max-h-[80vh] overflow-y-auto p-6">
                                            <div class="flex justify-between items-center mb-4">
                                                <h4 class="text-lg font-extrabold text-slate-900 dark:text-white">{{ $req->test_name }} Results</h4>
                                                <button @click="showResult = false" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                            </div>
                                            @if($req->result_data)
                                                <div class="space-y-2">
                                                    @foreach($req->result_data as $key => $value)
                                                        <div class="flex justify-between items-center bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                                                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">{{ str_replace('_', ' ', $key) }}</span>
                                                            <span class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-slate-500 italic text-sm">No structured data available.</p>
                                            @endif
                                            @if($req->result_file_path)
                                                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                                                    <a href="{{ Storage::url($req->result_file_path) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 bg-blue-50 dark:bg-blue-900/30 px-3 py-2 rounded-lg border border-blue-200 dark:border-blue-800/50 transition w-full justify-center">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                        View Attached File
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 py-16 px-6 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-full flex items-center justify-center mb-4 text-emerald-500">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">No completed tests yet</h4>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Completed tests from the last 7 days will appear here.</p>
            </div>
        @endif
    </div>

    <!-- ═══════════════════ TAB 3: ARCHIVE / NO-SHOWS ═══════════════════ -->
    <div x-show="activeTab === 'archive'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-xl flex items-center gap-2 mb-6">
            <span class="bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </span>
            Archive / No-Shows (Last 7 Days)
        </h3>

        @if($archivedRequests->count() > 0)
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700/70 text-[10px] sm:text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                            <th class="p-4">Test Name</th>
                            <th class="p-4">Patient</th>
                            <th class="p-4">Requested By</th>
                            <th class="p-4">Reason</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/50 text-sm">
                        @foreach($archivedRequests as $req)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition-colors">
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $req->test_name }}</p>
                                    <p class="text-[10px] font-semibold text-slate-400 uppercase">{{ $req->type }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-slate-900 dark:text-white">{{ $req->consultation->patient->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $req->consultation->patient->patient_id ?? '' }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $req->consultation->doctor->name ?? 'Unassigned' }}</p>
                                </td>
                                <td class="p-4">
                                    @if($req->archived_at)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-extrabold {{ $req->archived_reason === 'manual' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                                            {{ $req->archived_reason === 'manual' ? 'Manually Archived' : 'Auto-Archived' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400">
                                            No-Show (Past Date)
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium text-xs">{{ $req->created_at->format('M d, Y h:i A') }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('lab.ancillary.restore', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline bg-blue-50 dark:bg-blue-900/30 px-3 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800/50 transition hover:bg-blue-100">
                                            Restore to Pending
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 py-16 px-6 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4 text-slate-400">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
                <h4 class="text-xl font-extrabold text-slate-900 dark:text-white mb-2">Archive is empty</h4>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">No-show or archived requests from the last 7 days will appear here.</p>
            </div>
        @endif
    </div>
</div>
@endsection
