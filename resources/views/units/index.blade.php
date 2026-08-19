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
        
        {{-- Header Section with 21st.dev Glass & Aura Styling --}}
        <div class="text-center mb-12" data-reveal>
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-xs font-bold rounded-full mb-5 uppercase tracking-wider border border-emerald-500/20 backdrop-blur-md shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                RHU Healthcare Facilities
            </span>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
                Our Specialized <span class="hero-highlight">Health Units</span>
            </h1>
            <p class="mt-4 text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto font-normal leading-relaxed">
                Explore our dedicated public health facilities, clinical departments, and comprehensive community medical services in Silang, Cavite.
            </p>
            <div class="section-accent mx-auto mt-6"></div>
        </div>

        {{-- Interactive Filter & Search Bar (21st.dev Pill Controls) --}}
        <div class="max-w-4xl mx-auto mb-10" data-reveal>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 p-2.5 rounded-2xl bg-white/70 dark:bg-gray-900/70 border border-white/80 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
                
                {{-- Category Pill Tabs --}}
                <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto p-1 scrollbar-none">
                    <button @click="activeCategory = 'all'" 
                            :class="activeCategory === 'all' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shrink-0 cursor-pointer">
                        All Facilities
                    </button>
                    <button @click="activeCategory = 'primary'" 
                            :class="activeCategory === 'primary' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shrink-0 cursor-pointer">
                        Primary Care
                    </button>
                    <button @click="activeCategory = 'maternal'" 
                            :class="activeCategory === 'maternal' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shrink-0 cursor-pointer">
                        Maternal & Child
                    </button>
                    <button @click="activeCategory = 'specialized'" 
                            :class="activeCategory === 'specialized' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800'"
                            class="px-4 py-2 rounded-xl text-xs font-semibold transition-all duration-200 shrink-0 cursor-pointer">
                        Specialized Programs
                    </button>
                </div>

                {{-- Search Input --}}
                <div class="relative w-full md:w-72">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input x-model="searchQuery" type="text" placeholder="Search facilities..." 
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition">
                </div>
            </div>
        </div>

        @php
            $unitMeta = [
                'main-health-center' => [
                    'category' => 'primary',
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                    'color' => 'emerald',
                    'accent_hex' => '#10b981',
                    'badge' => 'General Consultations',
                    'status' => 'Open Mon - Fri',
                    'tags' => ['Doctor Consult', 'Laboratory', 'E-Prescription', 'Medical Certificate'],
                    'highlight' => 'Walk-ins & Online Booking',
                ],
                'lying-in-clinic' => [
                    'category' => 'maternal',
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>',
                    'color' => 'pink',
                    'accent_hex' => '#ec4899',
                    'badge' => '24/7 Maternity Ward',
                    'status' => '24/7 Emergency Service',
                    'tags' => ['Normal Delivery', 'Newborn Screening', 'Prenatal Care', 'Post-Partum'],
                    'highlight' => '24/7 Midwife On-Duty',
                ],
                'dental-clinic' => [
                    'category' => 'primary',
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>',
                    'color' => 'sky',
                    'accent_hex' => '#0ea5e9',
                    'badge' => 'Oral Healthcare',
                    'status' => 'Open Mon - Fri',
                    'tags' => ['Tooth Extraction', 'Cleaning & Check-up', 'Oral Health Education', 'Pediatric Dental'],
                    'highlight' => 'Free Routine Dental',
                ],
                'tb-dots-facility' => [
                    'category' => 'specialized',
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>',
                    'color' => 'amber',
                    'accent_hex' => '#f59e0b',
                    'badge' => 'National TB Program',
                    'status' => 'Open Mon - Fri',
                    'tags' => ['GeneXpert Testing', 'Free Medication', 'Treatment Monitoring', 'Sputum Exam'],
                    'highlight' => '100% Free TB Meds',
                ],
                'animal-bite-center' => [
                    'category' => 'specialized',
                    'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285zm0 13.036h.008v.008H12v-.008z"/></svg>',
                    'color' => 'red',
                    'accent_hex' => '#ef4444',
                    'badge' => 'Rabies Prevention',
                    'status' => 'Immediate Treatment',
                    'tags' => ['Post-Exposure Rabies', 'Anti-Rabies Vaccine', 'Tetanus Shot', 'Wound Management'],
                    'highlight' => 'Free Initial Shots',
                ],
            ];
        @endphp

        {{-- Bento Grid of Facility Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($units as $index => $unit)
                @php
                    $m = $unitMeta[$unit['slug']] ?? [
                        'category' => 'primary',
                        'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
                        'color' => 'emerald',
                        'accent_hex' => '#10b981',
                        'badge' => 'Health Facility',
                        'status' => 'Open Mon - Fri',
                        'tags' => ['Healthcare', 'Checkup'],
                        'highlight' => 'Public Service',
                    ];
                @endphp

                <div x-show="matchesFilter('{{ $m['category'] }}', '{{ addslashes($unit['name']) }}', '{{ addslashes($unit['desc']) }}', {{ json_encode($m['tags']) }})"
                     x-transition:enter="transition ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     data-reveal style="transition-delay: {{ $index * 60 }}ms"
                     class="group relative flex flex-col justify-between rounded-3xl p-7 sm:p-8 bg-white/75 dark:bg-gray-900/75 border border-white/90 dark:border-gray-800/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.25)] card-hover overflow-hidden">
                    
                    {{-- Ambient Glow in Card Corner --}}
                    <div class="absolute -top-12 -right-12 w-36 h-36 rounded-full blur-3xl pointer-events-none opacity-40 group-hover:opacity-75 transition-opacity duration-500"
                         style="background: {{ $m['accent_hex'] }};"></div>

                    <div>
                        {{-- Top Header: Icon & Live Status Badge --}}
                        <div class="flex items-start justify-between gap-4 mb-6">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-inner text-white transition-transform duration-300 group-hover:scale-105"
                                 style="background: linear-gradient(135deg, {{ $m['accent_hex'] }}, #0F3D3E);">
                                {!! $m['icon'] !!}
                            </div>
                            <div class="flex flex-col items-end gap-1.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-gray-200 border border-gray-200/80 dark:border-gray-700/80 shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $m['accent_hex'] }};"></span>
                                    {{ $m['status'] }}
                                </span>
                                <span class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                                    {{ $m['highlight'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Facility Title & Description --}}
                        <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                            {{ $unit['name'] }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
                            {{ $unit['desc'] }}
                        </p>

                        {{-- Key Service Pills --}}
                        <div class="flex flex-wrap gap-1.5 mb-8">
                            @foreach($m['tags'] as $tag)
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-gray-100/80 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 border border-gray-200/40 dark:border-gray-700/40">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer Action Link --}}
                    <div class="pt-5 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">
                            Silang RHU Unit
                        </span>
                        <a href="{{ route('units.show', $unit['slug']) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white shadow-md transition-all duration-200 group-hover:gap-3 cursor-pointer"
                           style="background: linear-gradient(135deg, {{ $m['accent_hex'] }}, #15803d);">
                            View Guide & Details
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
