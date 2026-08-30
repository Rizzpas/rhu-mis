@extends('layouts.app')

@section('content')
<div class="bg-transparent min-h-screen pb-24" x-data="{
    activeCategory: 'all',
    searchQuery: '',
    matchesFilter(category, name, desc, tags) {
        const matchesCat = this.activeCategory === 'all' || category === this.activeCategory;
        if (!matchesCat) return false;
        if (!this.searchQuery.trim()) return true;
        const q = this.searchQuery.toLowerCase();
        return name.toLowerCase().includes(q) || desc.toLowerCase().includes(q) || tags.some(t => t.toLowerCase().includes(q));
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16">
        
        {{-- Institutional Header — Municipal Healthcare Facilities --}}
        <div class="mb-10" data-reveal>
            <div class="mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-md border border-emerald-300/50 dark:border-emerald-700/50">
                    <svg class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    Republic of the Philippines • Municipality of Silang
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-end">
                <div class="lg:col-span-7">
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-[3.25rem] font-extrabold text-slate-900 dark:text-white tracking-tight leading-[1.12]">
                        Our Specialized <span class="text-emerald-700 dark:text-emerald-400">Health Units</span>
                    </h1>
                </div>
                <div class="lg:col-span-5 lg:pb-1">
                    <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                        Comprehensive municipal clinical departments, diagnostic laboratories, and 24/7 maternal care centers serving the citizens of Silang.
                    </p>
                </div>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="mb-10" data-reveal>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-3 rounded-2xl bg-white dark:bg-slate-800/90 border border-slate-200/90 dark:border-slate-700/80 shadow-xs">
                
                {{-- Category Tabs --}}
                <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto p-1 scrollbar-none">
                    <button @click="activeCategory = 'all'" 
                            :class="activeCategory === 'all' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                        All Facilities
                    </button>
                    <button @click="activeCategory = 'primary'" 
                            :class="activeCategory === 'primary' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                        Primary Care
                    </button>
                    <button @click="activeCategory = 'maternal'" 
                            :class="activeCategory === 'maternal' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                        Maternal Care
                    </button>
                    <button @click="activeCategory = 'specialized'" 
                            :class="activeCategory === 'specialized' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                        Specialized Centers
                    </button>
                </div>

                {{-- Search Input --}}
                <div class="relative w-full md:w-72">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input x-model="searchQuery" type="text" placeholder="Search facilities, services..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition">
                </div>
            </div>
        </div>

        @php
            $unitMeta = [
                'main-health-center' => [
                    'category' => 'primary',
                    'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                    'badge' => 'General Consultations',
                    'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
                    'tags' => ['Doctor Consult', 'Laboratory', 'E-Prescription', 'Medical Certificate'],
                    'highlight' => 'Walk-ins & Online Booking',
                ],
                'lying-in-clinic' => [
                    'category' => 'maternal',
                    'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
                    'badge' => '24/7 Maternity Ward',
                    'status' => '24/7 Emergency Care',
                    'tags' => ['Normal Delivery', 'Newborn Screening', 'Prenatal Care', 'Post-Partum'],
                    'highlight' => '24/7 Midwife On-Duty',
                ],
                'dental-clinic' => [
                    'category' => 'primary',
                    'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
                    'badge' => 'Oral Healthcare',
                    'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
                    'tags' => ['Tooth Extraction', 'Cleaning & Check-up', 'Oral Health Education', 'Pediatric Dental'],
                    'highlight' => 'Free Routine Dental',
                ],
                'tb-dots-facility' => [
                    'category' => 'specialized',
                    'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>',
                    'badge' => 'National TB Program',
                    'status' => 'Mon - Fri | 8:00 AM - 5:00 PM',
                    'tags' => ['GeneXpert Testing', 'Free Medication', 'Treatment Monitoring', 'Sputum Exam'],
                    'highlight' => '100% Free TB Meds',
                ],
                'animal-bite-center' => [
                    'category' => 'specialized',
                    'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H12v-.008z"/></svg>',
                    'badge' => 'Rabies Prevention',
                    'status' => 'Urgent Care & Shots',
                    'tags' => ['Post-Exposure Rabies', 'Anti-Rabies Vaccine', 'Tetanus Shot', 'Wound Management'],
                    'highlight' => 'Free Initial Shots',
                ],
            ];
        @endphp

        {{-- Facility Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($units as $index => $unit)
                @php
                    $uSlug = is_array($unit) ? $unit['slug'] : $unit->slug;
                    $uName = is_array($unit) ? $unit['name'] : $unit->name;
                    $uDesc = is_array($unit) ? ($unit['desc'] ?? $unit['description'] ?? '') : ($unit->description ?? '');
                    $uCategory = is_array($unit) ? ($unit['category'] ?? 'primary') : ($unit->category ?? 'primary');
                    $uHours = is_array($unit) ? ($unit['operating_hours'] ?? null) : $unit->operating_hours;
                    $uServices = is_array($unit) ? ($unit['services_offered'] ?? null) : $unit->services_offered;

                    $m = $unitMeta[$uSlug] ?? [
                        'category' => strtolower($uCategory) == 'maternity' || strtolower($uCategory) == 'women\'s health' ? 'maternal' : (strtolower($uCategory) == 'general medicine' || strtolower($uCategory) == 'dental care' ? 'primary' : 'specialized'),
                        'icon' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                        'badge' => $uCategory ?: 'Health Facility',
                        'status' => $uHours ?: 'Mon - Fri | 8:00 AM - 5:00 PM',
                        'tags' => is_array($uServices) && count($uServices) > 0 ? array_slice($uServices, 0, 4) : ['Healthcare', 'Checkup'],
                        'highlight' => 'Public Service',
                    ];
                    if ($uHours) {
                        $m['status'] = $uHours;
                    }
                    if (is_array($uServices) && count($uServices) > 0) {
                        $m['tags'] = array_slice($uServices, 0, 4);
                    }
                @endphp

                <div x-show="matchesFilter('{{ $m['category'] }}', '{{ addslashes($uName) }}', '{{ addslashes($uDesc) }}', {{ json_encode($m['tags']) }})"
                     class="group flex flex-col justify-between rounded-2xl p-7 bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:shadow-md transition-all duration-200">
                    
                    <div>
                        {{-- Top Header: Icon & Operating Badge --}}
                        <div class="flex items-start justify-between gap-4 mb-5">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/50 flex items-center justify-center shrink-0">
                                {!! $m['icon'] !!}
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 dark:bg-slate-700/70 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-600/60">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    {{ $m['status'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Facility Title & Description --}}
                        <h3 class="font-display text-xl font-bold text-slate-900 dark:text-white mb-2 group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition-colors">
                            {{ $uName }}
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6 font-normal">
                            {{ $uDesc }}
                        </p>

                        {{-- Key Service Tags --}}
                        <div class="flex flex-wrap gap-1.5 mb-6">
                            @foreach($m['tags'] as $tag)
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100/90 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-600/50">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer Action Link --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700/70 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                            Silang RHU
                        </span>
                        <a href="{{ route('units.show', $uSlug) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition-colors cursor-pointer">
                            View Clinical Guide
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
