@extends('layouts.app')

@section('content')
@php
    $categories = $units->pluck('category')->filter()->unique()->values();
@endphp

<div class="bg-transparent min-h-screen pb-24" x-data="{
    activeCategory: 'all',
    searchQuery: '',
    matchesFilter(category, name, desc, services) {
        const matchesCat = this.activeCategory === 'all' || category === this.activeCategory;
        if (!matchesCat) return false;
        if (!this.searchQuery.trim()) return true;
        const q = this.searchQuery.toLowerCase();
        const inServices = Array.isArray(services) && services.some(s => String(s).toLowerCase().includes(q));
        return name.toLowerCase().includes(q) || desc.toLowerCase().includes(q) || inServices || category.toLowerCase().includes(q);
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
                    Municipality of Silang
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
                
                {{-- Dynamic Category Tabs --}}
                <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto p-1 scrollbar-none">
                    <button @click="activeCategory = 'all'" 
                            :class="activeCategory === 'all' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                        All Facilities ({{ $units->count() }})
                    </button>
                    @foreach($categories as $cat)
                        <button @click="activeCategory = '{{ addslashes($cat) }}'" 
                                :class="activeCategory === '{{ addslashes($cat) }}' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700/60'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 cursor-pointer">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>

                {{-- Search Input --}}
                <div class="relative w-full md:w-72">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input x-model="searchQuery" type="text" placeholder="Search facilities, services..." 
                           class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition">
                </div>
            </div>
        </div>

        {{-- Facility Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @forelse($units as $unit)
                @php
                    $uSlug = $unit->slug;
                    $uName = $unit->name;
                    $uDesc = $unit->description ?? '';
                    $uCategory = $unit->category ?? 'General Medicine';
                    $uHours = $unit->operating_hours ?? 'Mon - Fri | 8:00 AM - 5:00 PM';
                    $uServices = is_array($unit->services_offered) ? $unit->services_offered : [];
                    $uImg = $unit->image_url;
                    $isMain = $unit->is_main_center;
                @endphp

                <div x-show="matchesFilter('{{ addslashes($uCategory) }}', '{{ addslashes($uName) }}', '{{ addslashes($uDesc) }}', {{ json_encode($uServices) }})"
                     class="group flex flex-col justify-between rounded-3xl overflow-hidden bg-white dark:bg-slate-800/95 border border-slate-200/90 dark:border-slate-700/80 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    
                    <div>
                        {{-- Facility Image Header --}}
                        <div class="relative h-48 sm:h-52 w-full overflow-hidden bg-slate-900">
                            <img src="{{ $uImg }}" alt="{{ $uName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/30 to-transparent"></div>
                            
                            {{-- Top Status & Category Bar --}}
                            <div class="absolute top-3.5 inset-x-3.5 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wide uppercase bg-slate-900/85 backdrop-blur-md text-white border border-white/15 shadow-sm truncate max-w-[180px]">
                                    {{ $uCategory }}
                                </span>
                                @if($isMain)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-500/90 text-slate-950 shadow-sm">
                                        Appointments Center
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-emerald-950/85 backdrop-blur-md text-emerald-300 border border-emerald-400/30 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Walk-in & Referrals
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-6 pt-5">
                            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-2 font-medium">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ $uHours }}</span>
                                </span>
                            </div>

                            <h3 class="font-display text-xl font-bold text-slate-900 dark:text-white mb-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $uName }}
                            </h3>
                            <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed mb-4 line-clamp-3 font-normal">
                                {{ $uDesc }}
                            </p>

                            {{-- Key Service Tags --}}
                            @if(count($uServices) > 0)
                                <div class="flex flex-wrap gap-1.5 mb-2">
                                    @foreach(array_slice($uServices, 0, 4) as $tag)
                                        <span class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100/90 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-slate-600/50">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                    @if(count($uServices) > 4)
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold text-slate-400 bg-slate-50 dark:bg-slate-800">
                                            +{{ count($uServices) - 4 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer Action Link --}}
                    <div class="px-6 pb-6 pt-3 border-t border-slate-100 dark:border-slate-700/70 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                            {{ $unit->contact_number ?: 'Silang RHU' }}
                        </span>
                        <a href="{{ route('units.show', $uSlug) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition-colors cursor-pointer group-hover:shadow-md">
                            View Clinical Guide
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">No Facilities Currently Available</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Please check back later or visit the Rural Health Unit main desk.</p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
