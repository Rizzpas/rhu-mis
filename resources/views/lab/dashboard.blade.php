@extends('layouts.lab')

@section('header', $type . ' Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-12">
    
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

    <!-- Pending Queue Grid -->
    <div class="pt-4">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-2xl flex items-center gap-2 mb-6">
            <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 p-1.5 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </span>
            Pending Tests
            <span class="ml-2 text-sm font-bold bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 py-0.5 px-2.5 rounded-full">{{ $pendingRequests->count() }}</span>
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

                            <div x-data="{ open: false }">
                                <button type="button" @click="open = true" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all duration-300 text-sm group-hover:ring-4 group-hover:ring-blue-500/20">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    Enter Results
                                </button>

                                <!-- Result Entry Modal -->
                                <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="open = false"></div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                        <div x-show="open" x-transition class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-200 dark:border-slate-700">
                                            <form action="{{ route('lab.ancillary.complete', $req->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-6">
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
                                                        @if($req->test_name === 'Complete Blood Count (CBC)' || str_contains($req->test_name, 'CBC'))
                                                            <div x-data="{
                                                                wbc: '', rbc: '', hgb: '', hct: '', platelets: '',
                                                                fillNormal() {
                                                                    this.wbc = '7.5';
                                                                    this.rbc = '4.8';
                                                                    this.hgb = '14.0';
                                                                    this.hct = '42.0';
                                                                    this.platelets = '250';
                                                                }
                                                            }">
                                                                <div class="mb-3 flex justify-between items-center bg-blue-50 dark:bg-blue-900/20 p-2.5 rounded-xl border border-blue-100 dark:border-blue-800">
                                                                    <span class="text-xs font-bold text-blue-900 dark:text-blue-300">Fast Data Entry:</span>
                                                                    <button type="button" @click="fillNormal()" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold rounded-lg transition shadow-sm">
                                                                        ⚡ Pre-fill Normal Standard Ranges
                                                                    </button>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">White Blood Cells (WBC)</label>
                                                                        <input type="text" name="results[wbc]" x-model="wbc" required placeholder="e.g. 7.5 x10^9/L" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Red Blood Cells (RBC)</label>
                                                                        <input type="text" name="results[rbc]" x-model="rbc" required placeholder="e.g. 4.8 x12^9/L" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Hemoglobin (Hgb)</label>
                                                                        <input type="text" name="results[hemoglobin]" x-model="hgb" required placeholder="e.g. 14.0 g/dL" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Hematocrit (Hct)</label>
                                                                        <input type="text" name="results[hematocrit]" x-model="hct" required placeholder="e.g. 42.0 %" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div class="col-span-2">
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Platelet Count</label>
                                                                        <input type="text" name="results[platelets]" x-model="platelets" required placeholder="e.g. 250 x10^9/L" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @elseif($req->test_name === 'Urinalysis')
                                                            <div x-data="{
                                                                color: '', transparency: '', ph: '', gravity: '', protein: '', glucose: '', micro: '',
                                                                fillNormal() {
                                                                    this.color = 'Straw / Yellow';
                                                                    this.transparency = 'Clear';
                                                                    this.ph = '6.0';
                                                                    this.gravity = '1.015';
                                                                    this.protein = 'Negative';
                                                                    this.glucose = 'Negative';
                                                                    this.micro = 'WBC: 0-2 /hpf, RBC: 0-1 /hpf, Epithelial cells: Few, Bacteria: None';
                                                                }
                                                            }">
                                                                <div class="mb-3 flex justify-between items-center bg-emerald-50 dark:bg-emerald-900/20 p-2.5 rounded-xl border border-emerald-100 dark:border-emerald-800">
                                                                    <span class="text-xs font-bold text-emerald-900 dark:text-emerald-300">Fast Data Entry:</span>
                                                                    <button type="button" @click="fillNormal()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-lg transition shadow-sm">
                                                                        ⚡ Fill All Normal / Negative
                                                                    </button>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Color</label>
                                                                        <input type="text" name="results[color]" x-model="color" required placeholder="Yellow" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Transparency</label>
                                                                        <input type="text" name="results[transparency]" x-model="transparency" required placeholder="Clear" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">pH</label>
                                                                        <input type="text" name="results[ph]" x-model="ph" required placeholder="6.0" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Specific Gravity</label>
                                                                        <input type="text" name="results[specific_gravity]" x-model="gravity" required placeholder="1.015" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Protein</label>
                                                                        <input type="text" name="results[protein]" x-model="protein" required placeholder="Negative" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Glucose</label>
                                                                        <input type="text" name="results[glucose]" x-model="glucose" required placeholder="Negative" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3">
                                                                    </div>
                                                                    <div class="col-span-2">
                                                                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Microscopic Findings</label>
                                                                        <textarea name="results[microscopic]" x-model="micro" rows="2" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            {{-- General Laboratory Template --}}
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">General Findings / Results</label>
                                                                <textarea name="results[findings]" rows="5" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3 placeholder-slate-400" placeholder="Enter findings or diagnostic results here..."></textarea>
                                                            </div>
                                                        @endif
                                                    @else
                                                        {{-- Radiology (Chest X-Ray) Template --}}
                                                        <div x-data="{
                                                            impression: '', findings: '',
                                                            setImpression(imp, find) {
                                                                this.impression = imp;
                                                                this.findings = find;
                                                            }
                                                        }" class="space-y-4">
                                                            <div>
                                                                <span class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Quick Presets:</span>
                                                                <div class="flex flex-wrap gap-1.5 mb-2">
                                                                    <button type="button" @click="setImpression('Normal Chest X-Ray / Unremarkable Findings', 'Both lung fields are clear. Heart and mediastinal contours are within normal limits. Osseous structures intact.')" 
                                                                            class="text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-1 rounded-lg hover:bg-indigo-100 transition">
                                                                        ⚡ Clear / Normal
                                                                    </button>
                                                                    <button type="button" @click="setImpression('Pneumonic Infiltrates', 'Focal density noted in the right lower lung field indicative of pneumonic process. Heart size normal.')" 
                                                                            class="text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg hover:bg-amber-100 transition">
                                                                        ⚡ Pneumonia Infiltrates
                                                                    </button>
                                                                    <button type="button" @click="setImpression('Koch\'s / Pulmonary Tuberculosis Suspected', 'Fibronodular opacities noted in upper lobes suggestive of active PTB. Advise clinical correlation.')" 
                                                                            class="text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-1 rounded-lg hover:bg-purple-100 transition">
                                                                        ⚡ TB Suspected
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Impression</label>
                                                                <textarea name="results[impression]" x-model="impression" rows="2" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3 font-semibold" placeholder="e.g. No significant findings / Cardiomegaly"></textarea>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Detailed Findings</label>
                                                                <textarea name="results[findings]" x-model="findings" rows="3" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-3" placeholder="Heart is normal in size and configuration..."></textarea>
                                                            </div>
                                                        </div>
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

    <!-- Completed Today -->
    @if($completedRequests->count() > 0)
    <div class="pt-8">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-xl flex items-center gap-2 mb-6">
            <span class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
            Completed Today
            <span class="ml-2 text-sm font-bold bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 py-0.5 px-2.5 rounded-full">{{ $completedRequests->count() }}</span>
        </h3>
        
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
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">{{ $req->completed_at->format('h:i A') }}</span>
                                </td>
                                <td class="p-4 text-right">
                                    <button @click="showResult = !showResult" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        <span x-text="showResult ? 'Hide' : 'View'"></span>
                                    </button>

                                    {{-- Inline result viewer --}}
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
    </div>
    @endif
</div>
@endsection
