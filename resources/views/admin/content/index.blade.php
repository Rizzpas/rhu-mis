@extends('layouts.admin')

@section('header', 'Content Management')

@section('content')
<div x-data="{ activeTab: 'hero' }" x-on:switch-tab.window="activeTab = $event.detail" class="max-w-6xl mx-auto space-y-6">

    {{-- Top Action & Header Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </span>
                <span>Landing Page Content</span>
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage public homepage text, hero banner, facilities, and municipal healthcare advisories.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('welcome') }}" target="_blank" 
               class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold shadow-2xs flex items-center gap-1.5 transition-all cursor-pointer">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Live Landing Page</span>
            </a>
        </div>
    </div>

    {{-- Modern Segmented Navigation Tabs (8 Dedicated Content Areas) --}}
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-1.5">
            <template x-for="tab in [
                { id: 'hero', name: 'Hero Banner', icon: 'hero' },
                { id: 'topbar', name: 'Top Bar', icon: 'topbar' },
                { id: 'facilities', name: 'Facilities', icon: 'facilities' },
                { id: 'about', name: 'Mission & Charter', icon: 'about' },
                { id: 'steps', name: 'Process Steps', icon: 'steps' },
                { id: 'faq', name: 'FAQs', icon: 'faq' },
                { id: 'privacy', name: 'Privacy Policy', icon: 'privacy' },
                { id: 'footer', name: 'Footer Info', icon: 'footer' }
            ]" :key="tab.id">
                <button @click.prevent="activeTab = tab.id"
                    :class="{
                        'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/20 font-bold': activeTab === tab.id,
                        'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold hover:bg-slate-100/80 dark:hover:bg-slate-800/80': activeTab !== tab.id
                    }"
                    class="px-2.5 py-2.5 rounded-xl text-xs transition-all duration-200 flex flex-col sm:flex-row items-center justify-center gap-1.5 cursor-pointer text-center select-none active:scale-[0.98]">
                    
                    {{-- Dynamic Tab Icons --}}
                    <span :class="activeTab === tab.id ? 'text-white' : 'text-slate-400 dark:text-slate-500'">
                        <template x-if="tab.icon === 'hero'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'topbar'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'facilities'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </template>
                        <template x-if="tab.icon === 'about'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        </template>
                        <template x-if="tab.icon === 'steps'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </template>
                        <template x-if="tab.icon === 'faq'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'privacy'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </template>
                        <template x-if="tab.icon === 'footer'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </template>
                    </span>

                    <span class="whitespace-nowrap font-medium" x-text="tab.name"></span>
                </button>
            </template>
        </div>
    </div>

    <script>
        function landingContentManager() {
            return {
                showConfirmModal: false,
                showResetModal: false,
                validateAndConfirm() {
                    const form = document.getElementById('landingContentForm');
                    const titleInputs = form ? form.querySelectorAll('input[name*="guiding_principles"][name$="[title]"]') : [];
                    const descInputs = form ? form.querySelectorAll('textarea[name*="guiding_principles"][name$="[description]"]') : [];
                    let hasEmpty = false;
                    let firstInvalid = null;

                    titleInputs.forEach(input => {
                        if (!input.value || input.value.trim() === '') {
                            hasEmpty = true;
                            if (!firstInvalid) firstInvalid = input;
                            input.classList.add('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        } else {
                            input.classList.remove('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        }
                    });

                    descInputs.forEach(input => {
                        if (!input.value || input.value.trim() === '') {
                            hasEmpty = true;
                            if (!firstInvalid) firstInvalid = input;
                            input.classList.add('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        } else {
                            input.classList.remove('!border-rose-400', 'dark:!border-rose-500', '!ring-2', '!ring-rose-500/20');
                        }
                    });

                    if (hasEmpty) {
                        window.dispatchEvent(new CustomEvent('add-toast', {
                            detail: {
                                type: 'error',
                                message: 'Please fill in all required Principle Title and Charter Description fields before saving.',
                                duration: 5000
                            }
                        }));
                        window.dispatchEvent(new CustomEvent('switch-tab', { detail: 'about' }));
                        window.dispatchEvent(new CustomEvent('validate-principles'));
                        if (firstInvalid) {
                            setTimeout(() => {
                                firstInvalid.focus();
                                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }, 100);
                        }
                        return;
                    }

                    this.showConfirmModal = true;
                }
            };
        }
    </script>

    {{-- Main Content Settings Form --}}
    <form id="landingContentForm" data-no-loader="true" x-data="landingContentManager()" @submit.prevent="validateAndConfirm()" action="{{ route('admin.content.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Save Changes Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="showConfirmModal"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="save-modal-title"
                 role="dialog"
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showConfirmModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                         @click="showConfirmModal = false"
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showConfirmModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                        <div class="p-6 sm:p-8">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 sm:h-10 sm:w-10 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="save-modal-title">
                                        Save Landing Content?
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Updated text and images will immediately go live on the public website. Are you sure you want to apply these changes?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="button"
                                    @click="showConfirmModal = false; if (window.showSkeleton) { window.showSkeleton(); } document.getElementById('landingContentForm').submit();"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-sm font-bold text-white transition-all cursor-pointer">
                                Yes, Apply Changes
                            </button>
                            <button type="button"
                                    @click="showConfirmModal = false"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Reset Fields Confirmation Modal --}}
        <template x-teleport="body">
            <div x-show="showResetModal"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="reset-modal-title"
                 role="dialog"
                 aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showResetModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                         @click="showResetModal = false"
                         aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showResetModal"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                        <div class="p-6 sm:p-8">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 sm:h-10 sm:w-10 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="reset-modal-title">
                                        Reset All Content Fields?
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-slate-500 dark:text-slate-400">
                                            Are you sure you want to reset all fields back to normal? Any unsaved changes made across all tabs will be discarded.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="button"
                                    @click="showResetModal = false; window.location.reload();"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-amber-600 hover:bg-amber-700 text-sm font-bold text-white transition-all cursor-pointer">
                                Yes, Reset to Normal
                            </button>
                            <button type="button"
                                    @click="showResetModal = false"
                                    class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 1: HERO SECTION
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'hero'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 initBadge: '{{ old('settings.hero_badge_text', $settings['hero']['hero_badge_text']->value ?? 'Municipality of Silang') }}',
                 initLine1: '{{ old('settings.hero_title_line1', $settings['hero']['hero_title_line1']->value ?? 'Accessible') }}',
                 initHighlight: '{{ old('settings.hero_title_highlight', $settings['hero']['hero_title_highlight']->value ?? 'Public Healthcare') }}',
                 initLine2: '{{ old('settings.hero_title_line2', $settings['hero']['hero_title_line2']->value ?? 'for Every Silang Constituent.') }}',
                 initDesc: '{{ addslashes(old('settings.hero_description', $settings['hero']['hero_description']->value ?? 'The Rural Health Unit is the official municipal healthcare gateway of Silang, Cavite. We provide online appointments, digital triage, doctor consultations, and primary diagnostic referrals.')) }}',
                 initPreview: '{{ isset($settings['hero']['hero_image']->value) ? asset($settings['hero']['hero_image']->value) : '' }}',
                 imagePreview: '{{ isset($settings['hero']['hero_image']->value) ? asset($settings['hero']['hero_image']->value) : '' }}',
                 isDragging: false,
                 badge: '',
                 line1: '',
                 highlight: '',
                 line2: '',
                 desc: '',
                 init() {
                     this.resetHero();
                 },
                 resetHero() {
                     this.badge = this.initBadge;
                     this.line1 = this.initLine1;
                     this.highlight = this.initHighlight;
                     this.line2 = this.initLine2;
                     this.desc = this.initDesc;
                     this.imagePreview = this.initPreview;
                     const input = document.getElementById('hero_image_file_input');
                     if (input) input.value = '';
                 },
                 handleHeroFile(file) {
                     if (!file || !file.type.startsWith('image/')) return;
                     const input = document.getElementById('hero_image_file_input');
                     $store.imageCropper.open(file, {
                         aspectRatio: 1,
                         subtitle: 'Square crop (1:1) — Hero image banner',
                         onApply: (blob, previewUrl) => {
                             this.imagePreview = previewUrl;
                             setCroppedFile(input, blob, file.name || 'hero.jpg');
                         }
                     });
                 }
             }"
             @content-reset.window="resetHero()">
            
            {{-- Live Visual Preview Wireframe Card with Browser Chrome Mockup --}}
            <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl relative overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 inline-block shadow-xs"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80 inline-block shadow-xs"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 inline-block shadow-xs"></span>
                        </div>
                        <div class="px-3 py-1 rounded-lg bg-white/10 text-[11px] text-slate-300 font-mono flex items-center gap-1.5 border border-white/5">
                            <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>silang.gov.ph / Rural Health Unit</span>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Interactive Hero Preview
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-8 space-y-4">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 text-emerald-300 text-[11px] font-bold uppercase tracking-widest rounded-lg border border-emerald-500/40 shadow-xs"
                                  x-text="badge || 'Municipality of Silang'">
                            </span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight text-white">
                            <span x-text="line1 || 'Accessible'"></span>
                            <span class="text-emerald-400" x-text="highlight || 'Public Healthcare'"></span><br>
                            <span x-text="line2 || 'for Every Silang Constituent.'"></span>
                        </h1>

                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal max-w-xl" x-text="desc"></p>

                        <div class="pt-2 flex flex-wrap gap-2.5">
                            <span class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-bold shadow-md shadow-emerald-500/20">Book an Appointment</span>
                            <span class="px-4 py-2 rounded-xl bg-white/10 text-white text-xs font-semibold border border-white/10">Manage Booking</span>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex justify-center">
                        <div class="relative w-44 h-44 sm:w-52 sm:h-52 rounded-full overflow-hidden border-4 border-emerald-400/40 shadow-2xl shadow-emerald-500/20 bg-slate-800 flex items-center justify-center ring-8 ring-emerald-500/10">
                            <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                            <div x-show="!imagePreview" class="text-center p-4">
                                <svg class="w-10 h-10 text-slate-500 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-[11px] font-semibold text-slate-400">Hero Facility Photo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hero Form Inputs Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Headline & Institutional Typography</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure the primary public headline, accent keywords, and introduction text.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- Badge Text --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Institutional Badge Kicker
                        </label>
                        <input type="text" name="settings[hero_badge_text]" x-model="badge"
                               class="w-full sm:w-1/2 h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Shown inside the top institutional badge (e.g. "Municipality of Silang").</p>
                    </div>

                    {{-- Title 3-part grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Title Line 1</label>
                            <input type="text" name="settings[hero_title_line1]" x-model="line1"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Title Highlight (Green Accent)</label>
                            <input type="text" name="settings[hero_title_highlight]" x-model="highlight"
                                   class="w-full h-11 px-4 rounded-xl border border-emerald-400 dark:border-emerald-600 bg-emerald-50/40 dark:bg-emerald-950/30 text-emerald-800 dark:text-emerald-300 text-sm font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Title Line 2</label>
                            <input type="text" name="settings[hero_title_line2]" x-model="line2"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                    </div>

                    {{-- Hero Description --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Hero Description Paragraph</label>
                        <textarea name="settings[hero_description]" x-model="desc" rows="3"
                                  class="w-full p-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                    </div>
                </div>
            </div>

            {{-- Carousel Banner Text Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/20 to-emerald-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Announcement Carousel Overlay</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Default headline and subtitle shown on the first carousel slide when no cover post is selected.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Slide Headline</label>
                        <input type="text" name="settings[carousel_hero_title]" value="{{ old('settings.carousel_hero_title', $settings['hero']['carousel_hero_title']->value ?? 'RHU Silang, Cavite') }}"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Slide Subtitle</label>
                        <input type="text" name="settings[carousel_hero_subtitle]" value="{{ old('settings.carousel_hero_subtitle', $settings['hero']['carousel_hero_subtitle']->value ?? 'Providing Responsive & Quality Healthcare for All Constituents') }}"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                </div>
            </div>

            {{-- Hero Image Card with Interactive Cropper --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Hero Facility Banner</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Primary photograph of the RHU facility featured in the circular hero frame on the public website.</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-start gap-6">
                    <div class="flex-1 space-y-3 w-full">
                        <input type="file" id="hero_image_file_input" name="hero_image_file" accept="image/*" class="hidden"
                               @change="if ($event.target.files.length) handleHeroFile($event.target.files[0])">
                        
                        <div @dragover.prevent="isDragging = true"
                             @dragleave.prevent="isDragging = false"
                             @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) handleHeroFile($event.dataTransfer.files[0])"
                             @click="document.getElementById('hero_image_file_input').click()"
                             :class="isDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800/70'"
                             class="w-full border-2 border-dashed rounded-2xl p-7 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2 group">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Drag & drop image here, or <span class="text-emerald-600 dark:text-emerald-400 underline underline-offset-2">browse files</span>
                            </p>
                            <p class="text-xs text-slate-400">Supports JPG, PNG, WEBP · 1:1 square crop</p>
                        </div>
                    </div>

                    <div class="w-full sm:w-48 h-48 bg-slate-100 dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm shrink-0 flex items-center justify-center relative group">
                        <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover">
                        <span x-show="!imagePreview" class="text-xs font-semibold text-slate-400">No Image</span>
                        <template x-if="imagePreview">
                            <button type="button" @click="document.getElementById('hero_image_file_input').click()" 
                                    class="absolute inset-0 bg-slate-900/60 text-white flex flex-col items-center justify-center text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity gap-1.5 backdrop-blur-xs cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Change Image</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 2: TOP BAR
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'topbar'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 republic: '{{ old('settings.topbar_republic', $settings['topbar']['topbar_republic']->value ?? 'Republic of the Philippines') }}',
                 province: '{{ old('settings.topbar_province', $settings['topbar']['topbar_province']->value ?? 'Province of Cavite') }}',
                 municipality: '{{ old('settings.topbar_municipality', $settings['topbar']['topbar_municipality']->value ?? 'Municipality of Silang') }}',
                 hours: '{{ old('settings.clinic_hours', $settings['topbar']['clinic_hours']->value ?? 'Mon - Fri | 8:00 AM - 5:00 PM') }}',
                 hotlines: '{{ old('settings.emergency_hotlines', $settings['topbar']['emergency_hotlines']->value ?? '911 | (046) 432-1234') }}'
             }">

            {{-- Live Top Bar Preview --}}
            <div class="bg-gradient-to-r from-emerald-950 via-slate-950 to-teal-950 text-white rounded-3xl p-6 sm:p-7 border border-emerald-900/60 shadow-xl relative overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-3 border-b border-white/10">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Live Top Bar Preview
                    </span>
                    <span class="text-[11px] text-slate-400">Exact live preview rendered at the very top of all public pages</span>
                </div>

                <div class="bg-emerald-900/90 rounded-2xl p-4 sm:p-4.5 border border-emerald-800/80 shadow-inner flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 text-white">
                        <span class="px-2 py-0.5 rounded bg-emerald-800/80 text-[10px] font-black uppercase tracking-wider text-emerald-300 border border-emerald-700/60">Official</span>
                        <span class="font-bold tracking-wide" x-text="republic || 'Republic of the Philippines'"></span>
                        <span class="text-emerald-400 opacity-60">•</span>
                        <span class="text-emerald-200" x-text="province || 'Province of Cavite'"></span>
                        <span class="text-emerald-400 opacity-60">•</span>
                        <span class="text-emerald-200 font-semibold" x-text="municipality || 'Municipality of Silang'"></span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 text-emerald-100/90 text-xs">
                        <div class="flex items-center gap-1.5 bg-emerald-950/60 px-2.5 py-1 rounded-lg border border-emerald-800/60">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="hours || 'Mon - Fri | 8:00 AM - 5:00 PM'"></span>
                        </div>
                        <div class="flex items-center gap-1.5 bg-rose-500/20 text-rose-200 px-2.5 py-1 rounded-lg border border-rose-500/30 font-semibold">
                            <svg class="w-3.5 h-3.5 text-rose-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span x-text="hotlines || '911 | (046) 432-1234'"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Banner Text Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Institutional Government Banner</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">The official municipal hierarchy labels displayed in the public header.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Republic Label</label>
                        <input type="text" name="settings[topbar_republic]" x-model="republic"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">e.g. "Republic of the Philippines"</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Province Label</label>
                        <input type="text" name="settings[topbar_province]" x-model="province"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">e.g. "Province of Cavite"</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Municipality Label</label>
                        <input type="text" name="settings[topbar_municipality]" x-model="municipality"
                               class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">e.g. "Municipality of Silang"</p>
                    </div>
                </div>
            </div>

            {{-- Clinic Hours & Hotlines Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/20 to-emerald-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Operating Hours & Emergency Contact</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Shown on the right side of the top bar — clinic schedule and emergency hotline numbers.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Clinic Hours</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <input type="text" name="settings[clinic_hours]" x-model="hours"
                                   class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Shown on the top right of the site header.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Emergency Hotlines</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input type="text" name="settings[emergency_hotlines]" x-model="hotlines"
                                   class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Shown on the top right with emergency highlight.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 3: FACILITIES & UNITS
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'facilities'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 showAddModal: false,
                 showEditModal: false,
                 showDeleteModal: false,
                 editUnit: { id: null, name: '', category: '', description: '', operating_hours: '', contact_number: '', location: '', services_offered: '', sort_order: 0, is_active: true },
                 openEdit(unit) {
                     this.editUnit = {
                         id: unit.id,
                         name: unit.name,
                         category: unit.category || '',
                         description: unit.description || '',
                         operating_hours: unit.operating_hours || '',
                         contact_number: unit.contact_number || '',
                         location: unit.location || '',
                         services_offered: Array.isArray(unit.services_offered) ? unit.services_offered.join('\n') : '',
                         sort_order: unit.sort_order || 0,
                         is_active: Boolean(unit.is_active)
                     };
                     this.showEditModal = true;
                 }
             }">

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Municipal Health Facilities & Specialized Clinics</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage public clinical departments, operating schedules, contact lines, and published services.</p>
                        </div>
                    </div>
                    <button type="button" @click="showAddModal = true"
                            class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer shrink-0 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Facility Unit</span>
                    </button>
                </div>

                {{-- Modernized Facilities Table --}}
                <div class="border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/90 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-extrabold">
                                <th class="p-4 w-14 text-center">Order</th>
                                <th class="p-4">Facility & Discipline</th>
                                <th class="p-4">Operating Hours & Contact</th>
                                <th class="p-4">Services Count</th>
                                <th class="p-4">Public Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                            @forelse($facilityUnits as $fUnit)
                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="p-4 text-center font-mono font-bold text-slate-400">{{ $fUnit->sort_order }}</td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-900 dark:text-white text-sm tracking-tight">{{ $fUnit->name }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">
                                                {{ $fUnit->category ?: 'General Health' }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">/units/{{ $fUnit->slug }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-slate-800 dark:text-slate-200 font-semibold flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>{{ $fUnit->operating_hours ?: 'Mon - Fri | 8:00 AM - 5:00 PM' }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $fUnit->contact_number ?: $fUnit->location }}</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-[11px] border border-slate-200/60 dark:border-slate-700/60">
                                            {{ is_array($fUnit->services_offered) ? count($fUnit->services_offered) : 0 }} services
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @if($fUnit->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Active</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700">
                                                <span>Hidden</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" @click="openEdit({{ json_encode($fUnit) }})" 
                                                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs" 
                                                    title="Edit Facility">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <a href="{{ route('units.show', $fUnit->slug) }}" target="_blank" 
                                               class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 border border-slate-200/70 dark:border-slate-700/70 hover:border-blue-300 transition-all cursor-pointer shadow-2xs" 
                                               title="View Public Unit Page">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 italic">No facility units configured. Click "Add Facility Unit" above.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ADD FACILITY MODAL --}}
            <template x-teleport="body">
                <div x-show="showAddModal" style="display: none;" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 sm:p-6 overflow-y-auto">
                <div @click.away="showAddModal = false" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                     x-transition:leave="transition ease-in duration-150" 
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                     class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-2xl w-full my-8 border border-slate-200/90 dark:border-slate-800 flex flex-col max-h-[90vh]">
                    
                    {{-- Modal Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/90 flex items-center justify-between shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white leading-tight">Add New Health Facility / Clinic</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Register a municipal clinic, diagnostic unit, or specialized healthcare service.</p>
                            </div>
                        </div>
                        <button type="button" @click="showAddModal = false" 
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                title="Close modal">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body Form --}}
                    <form action="{{ route('admin.facilities.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf
                        <div class="p-6 sm:p-7 space-y-5 overflow-y-auto flex-1">
                            
                            {{-- Row 1: Name and Category --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Facility Name <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <input type="text" name="name" required placeholder="e.g. OB-GYN Unit" 
                                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Official unit or clinical department name.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Category / Discipline
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                        <input type="text" name="category" placeholder="e.g. Maternal Health, Dental" 
                                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Classification shown as tag badge.</p>
                                </div>
                            </div>

                            {{-- Row 2: Clinical Overview --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Clinical Overview & Description
                                </label>
                                <textarea name="description" rows="3" placeholder="Provide a summary of medical services, capabilities, and target patient group..." 
                                          class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Displayed on the public unit details page.</p>
                            </div>

                            {{-- Row 3: Hours, Contact, Location (Equally Aligned 3-Col Grid) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Operating Hours
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <input type="text" name="operating_hours" placeholder="Mon - Fri | 8AM - 5PM" 
                                               class="w-full h-11 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Contact Number
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input type="text" name="contact_number" placeholder="(046) 414-XXXX" 
                                               class="w-full h-11 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Unit Location
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <input type="text" name="location" placeholder="e.g. Ground Floor, Wing B" 
                                               class="w-full h-11 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                </div>
                            </div>

                            {{-- Row 4: Services Offered --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        Services Offered
                                    </label>
                                    <span class="text-[11px] text-slate-400 font-medium">One service per line</span>
                                </div>
                                <textarea name="services_offered" rows="3" placeholder="Prenatal Care&#10;Ultrasound Exam&#10;Family Planning Consultation" 
                                          class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-mono leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Listed as bullet items under the clinic's public profile.</p>
                            </div>

                            {{-- Row 5: Visibility & Sorting --}}
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <label class="flex items-center gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="is_active" value="1" checked 
                                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition">
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Active Status</span>
                                        <span class="text-[11px] text-slate-400 block">Make this facility immediately visible on the public landing page</span>
                                    </div>
                                </label>
                                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Display Order:</label>
                                    <input type="number" name="sort_order" value="10" 
                                           class="w-20 h-10 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-center text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                </div>
                            </div>

                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/90 flex items-center justify-end gap-3 shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-b-2xl">
                            <button type="button" @click="showAddModal = false" 
                                    class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition-all shadow-xs hover:shadow flex items-center gap-2 cursor-pointer active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Facility</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            </template>

            {{-- EDIT FACILITY MODAL --}}
            <template x-teleport="body">
                <div x-show="showEditModal" style="display: none;" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-[999] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 sm:p-6 overflow-y-auto">
                <div @click.away="showEditModal = false" 
                     x-transition:enter="transition ease-out duration-200" 
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2" 
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
                     x-transition:leave="transition ease-in duration-150" 
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                     class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-2xl w-full my-8 border border-slate-200/90 dark:border-slate-800 flex flex-col max-h-[90vh]">
                    
                    {{-- Modal Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800/90 flex items-center justify-between shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-t-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100/80 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-2xs">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white leading-tight">Edit Health Facility</h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Modify clinic details, operating schedule, or public status.</p>
                            </div>
                        </div>
                        <button type="button" @click="showEditModal = false" 
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer"
                                title="Close modal">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Modal Body Form --}}
                    <form :action="'{{ url('admin/facilities') }}/' + editUnit.id" method="POST" class="flex flex-col flex-1 overflow-hidden">
                        @csrf
                        @method('PUT')
                        <div class="p-6 sm:p-7 space-y-5 overflow-y-auto flex-1">
                            
                            {{-- Row 1: Name and Category --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Facility Name <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                        <input type="text" name="name" x-model="editUnit.name" required 
                                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Official unit or clinical department name.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Category / Discipline
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        </div>
                                        <input type="text" name="category" x-model="editUnit.category" 
                                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Classification shown as tag badge.</p>
                                </div>
                            </div>

                            {{-- Row 2: Overview --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                    Clinical Overview & Description
                                </label>
                                <textarea name="description" x-model="editUnit.description" rows="3" 
                                          class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Displayed on the public unit details page.</p>
                            </div>

                            {{-- Row 3: Hours, Contact, Location (Equally Aligned 3-Col Grid) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Operating Hours
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <input type="text" name="operating_hours" x-model="editUnit.operating_hours" 
                                               class="w-full h-11 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Contact Number
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input type="text" name="contact_number" x-model="editUnit.contact_number" 
                                               class="w-full h-11 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                        Unit Location
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <input type="text" name="location" x-model="editUnit.location" 
                                               class="w-full h-11 pl-10 pr-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    </div>
                                </div>
                            </div>

                            {{-- Row 4: Services Offered --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        Services Offered
                                    </label>
                                    <span class="text-[11px] text-slate-400 font-medium">One service per line</span>
                                </div>
                                <textarea name="services_offered" x-model="editUnit.services_offered" rows="3" 
                                          class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-xs font-mono leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Listed as bullet items under the clinic's public profile.</p>
                            </div>

                            {{-- Row 5: Visibility & Sorting --}}
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <label class="flex items-center gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="is_active" value="1" x-model="editUnit.is_active" 
                                           class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition">
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Active Status</span>
                                        <span class="text-[11px] text-slate-400 block">Make this facility visible on the public landing page</span>
                                    </div>
                                </label>
                                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400">Display Order:</label>
                                    <input type="number" name="sort_order" x-model="editUnit.sort_order" 
                                           class="w-20 h-10 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 text-center text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs">
                                </div>
                            </div>

                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800/90 flex items-center justify-between gap-3 shrink-0 bg-slate-50/50 dark:bg-slate-800/30 rounded-b-2xl">
                            <button type="button" @click="showDeleteModal = true" 
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-transparent hover:border-rose-200 dark:hover:border-rose-800/50 transition-all flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Delete Facility</span>
                            </button>
                            <div class="flex items-center gap-2.5">
                                <button type="button" @click="showEditModal = false" 
                                        class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white transition-all shadow-xs hover:shadow flex items-center gap-2 cursor-pointer active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Update Facility</span>
                                </button>
                            </div>
                        </div>
                    </form>
                    <form x-ref="deleteForm" :action="'{{ url('admin/facilities') }}/' + editUnit.id" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
            </template>

            {{-- DELETE FACILITY CONFIRMATION MODAL --}}
            <template x-teleport="body">
                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-[1000] flex items-center justify-center bg-slate-950/70 backdrop-blur-sm px-4">
                    <div @click.away="showDeleteModal = false" 
                         x-transition:enter="transition ease-out duration-200" 
                         x-transition:enter-start="opacity-0 scale-95" 
                         x-transition:enter-end="opacity-100 scale-100" 
                         x-transition:leave="transition ease-in duration-150" 
                         x-transition:leave-start="opacity-100 scale-100" 
                         x-transition:leave-end="opacity-0 scale-95" 
                         class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 border border-slate-200/90 dark:border-slate-800">
                        <div class="flex items-center gap-3.5 mb-3">
                            <div class="w-11 h-11 rounded-2xl bg-rose-100/80 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Delete Facility?</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Remove clinical unit from public directory.</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mt-2.5">
                            Are you sure you want to delete <span class="font-bold text-slate-900 dark:text-white" x-text="editUnit.name"></span>? This will permanently remove the facility from the public healthcare portal.
                        </p>
                        <div class="mt-6 flex justify-end gap-2.5">
                            <button type="button" @click="showDeleteModal = false" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition border border-slate-300 dark:border-slate-700 cursor-pointer">Cancel</button>
                            <button type="button" @click="showDeleteModal = false; $refs.deleteForm.submit();" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white transition shadow-sm cursor-pointer active:scale-95 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Yes, Delete Facility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 4: MISSION, VISION & PUBLIC SERVICE CHARTER
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6"
             x-data="{
                 mission: '{{ addslashes(old('settings.mission_statement', $settings['about']['mission_statement']->value ?? 'To provide responsive, equitable, and quality primary healthcare services to all citizens of Silang. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs.')) }}',
                 vision: '{{ addslashes(old('settings.vision_statement', $settings['about']['vision_statement']->value ?? 'A healthy, resilient, and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable right for every citizen.')) }}'
             }">

            {{-- Mission & Vision Side-by-Side 2-Column Cards --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Mission Card --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Healthcare Mission</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Day-to-day healthcare service mandate.</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">
                                Mandate
                            </span>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Official Mission Statement
                            </label>
                            <textarea name="settings[mission_statement]" rows="5" x-model="mission"
                                      placeholder="Enter official mission statement..."
                                      class="w-full p-4 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Rendered prominently in the public About page narrative section.</span>
                    </p>
                </div>

                {{-- Vision Card --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500/20 to-cyan-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 border border-teal-500/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Community Vision</h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Long-term wellness & technology horizon.</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200/80 dark:border-teal-800/60">
                                Aspiration
                            </span>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Official Vision Statement
                            </label>
                            <textarea name="settings[vision_statement]" rows="5" x-model="vision"
                                      placeholder="Enter official vision statement..."
                                      class="w-full p-4 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Highlighted across municipal healthcare reports and public banners.</span>
                    </p>
                </div>
            </div>

            {{-- Public Service Charter: Guiding Principles Section --}}
            @php
                $rawPrinciples = null;
                if(isset($settings['about']['guiding_principles'])) {
                    $rawPrinciples = json_decode($settings['about']['guiding_principles']->value, true);
                }
                if(empty($rawPrinciples) || !is_array($rawPrinciples)) {
                    $rawPrinciples = [
                        ['number' => '01', 'title' => 'Compassionate Care', 'description' => 'Treating every patient with dignity, empathy, and dedicated professional attention.'],
                        ['number' => '02', 'title' => 'Digital Innovation', 'description' => 'Streamlining triage, clinical schedules, and patient records with modern MIS solutions.'],
                        ['number' => '03', 'title' => 'Transparency & Ethics', 'description' => 'Upholding absolute accountability in pharmacy inventories and healthcare governance.'],
                        ['number' => '04', 'title' => 'Universal Inclusivity', 'description' => 'Guaranteeing barrier-free medical access for all constituents regardless of status.']
                    ];
                }
            @endphp

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6"
                 x-data="{
                     initPrinciples: {{ json_encode($rawPrinciples) }},
                     principles: {{ json_encode($rawPrinciples) }},
                     principleErrors: [],
                     resetPrinciples() {
                         this.principles = JSON.parse(JSON.stringify(this.initPrinciples));
                         this.principleErrors = [];
                     },
                     addPrinciple() {
                         const nextNum = String(this.principles.length + 1).padStart(2, '0');
                         this.principles.push({ number: nextNum, title: '', description: '' });
                     },
                     removePrinciple(index) {
                         this.principles.splice(index, 1);
                         this.principleErrors.splice(index, 1);
                         if (this.principles.length === 0) {
                             this.addPrinciple();
                         }
                     },
                     validatePrinciples() {
                         this.principleErrors = [];
                         let valid = true;
                         this.principles.forEach((p, i) => {
                             let errs = { title: false, description: false };
                             if (!p.title || p.title.trim() === '') { errs.title = true; valid = false; }
                             if (!p.description || p.description.trim() === '') { errs.description = true; valid = false; }
                             this.principleErrors.push(errs);
                         });
                         return valid;
                     }
                 }"
                 @content-reset.window="resetPrinciples()"
                 @validate-principles.window="validatePrinciples()">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Public Service Charter — Guiding Principles</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure ethical healthcare standards and numbered pillars featured on the public About Us page.</p>
                        </div>
                    </div>

                    <button type="button" @click="addPrinciple()"
                            class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-xs font-bold shadow-sm hover:shadow transition-all flex items-center gap-2 cursor-pointer self-start sm:self-auto shrink-0 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Principle</span>
                    </button>
                </div>

                {{-- Interactive Principles Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <template x-for="(principle, index) in principles" :key="index">
                        <div class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-4 relative shadow-2xs group hover:border-emerald-300 dark:hover:border-emerald-700/70 transition-all">
                            
                            {{-- Principle Card Header --}}
                            <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 flex items-center justify-center font-black text-xs font-mono shadow-2xs" x-text="principle.number || (index + 1)"></span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Charter Standard</span>
                                </div>
                                
                                <button type="button" @click="removePrinciple(index)" 
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" 
                                        title="Remove Principle">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            {{-- Number Tag & Title Inputs --}}
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
                                <div class="sm:col-span-1">
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Number Tag</label>
                                    <input type="text" :name="`guiding_principles[${index}][number]`" x-model="principle.number" placeholder="01"
                                           class="w-full h-11 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-bold text-center font-mono focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Principle Title <span class="text-rose-500">*</span></label>
                                    <input type="text" :name="`guiding_principles[${index}][title]`" x-model="principle.title" placeholder="e.g. Compassionate Care" required
                                           :class="principleErrors[index] && principleErrors[index].title ? 'border-rose-400 dark:border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-300 dark:border-slate-700'"
                                           @input="if (principleErrors[index]) principleErrors[index].title = false"
                                           class="w-full h-11 px-3.5 rounded-xl border bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                    <p x-show="principleErrors[index] && principleErrors[index].title" class="text-[11px] text-rose-500 font-medium mt-1">Principle title is required.</p>
                                </div>
                            </div>

                            {{-- Description Input --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Charter Description <span class="text-rose-500">*</span></label>
                                <textarea :name="`guiding_principles[${index}][description]`" x-model="principle.description" rows="2"
                                          placeholder="Explain the patient care standard..." required
                                          :class="principleErrors[index] && principleErrors[index].description ? 'border-rose-400 dark:border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-300 dark:border-slate-700'"
                                          @input="if (principleErrors[index]) principleErrors[index].description = false"
                                          class="w-full p-3.5 rounded-xl border bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                <p x-show="principleErrors[index] && principleErrors[index].description" class="text-[11px] text-rose-500 font-medium mt-1">Charter description is required.</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 5: PROCESS STEPS
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'steps'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Facility Process & Patient Flow Steps</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Step-by-step patient pathway displayed along the alternating timeline on facility unit pages.</p>
                    </div>
                </div>
            </div>

            @php
                $unitsList = $facilityUnits->pluck('name', 'slug')->toArray();
                if (empty($unitsList)) {
                    $unitsList = [
                        'main-health-center' => 'Main Health Center',
                        'lying-in-clinic' => 'Lying-in Clinic',
                        'ob-gyn-unit' => 'OB-GYN Unit',
                        'dental-clinic' => 'Dental Clinic',
                        'tb-dots-facility' => 'TB DOTS Facility',
                        'animal-bite-center' => 'Animal Bite Center'
                    ];
                }

                $unitSteps = [];
                foreach($unitsList as $slug => $name) {
                    $key = 'steps_data_' . $slug;
                    if(isset($settings['steps'][$key])) {
                        $unitSteps[$slug] = json_decode($settings['steps'][$key]->value, true) ?: [];
                    } else {
                        $unitSteps[$slug] = [];
                    }
                    if(empty($unitSteps[$slug])) {
                        $unitSteps[$slug] = [
                            ['title' => 'Admission & Triage Check-in', 'description' => 'Present your records or valid ID at the admission desk.', 'image' => ''],
                            ['title' => 'Clinical Monitoring & Vitals', 'description' => 'Staff will record vital signs and perform preliminary screening.', 'image' => '']
                        ];
                    }
                    foreach($unitSteps[$slug] as &$s) {
                        $s['image'] = $s['image'] ?? '';
                    }
                    unset($s);
                }
                $firstSlug = array_key_first($unitsList);
            @endphp

            <div x-data="{ 
                activeUnit: '{{ $firstSlug }}',
                initUnitSteps: {{ json_encode($unitSteps) }},
                unitSteps: {{ json_encode($unitSteps) }},
                resetSteps() {
                    this.unitSteps = JSON.parse(JSON.stringify(this.initUnitSteps));
                },
                addStep(slug) {
                    this.unitSteps[slug].push({ title: '', description: '', image: '' });
                },
                removeStep(slug, index) {
                    this.unitSteps[slug].splice(index, 1);
                    if(this.unitSteps[slug].length === 0) this.addStep(slug);
                }
            }"
            @content-reset.window="resetSteps()">
                {{-- Modern Segmented Unit Switcher Pills --}}
                <div class="flex flex-wrap gap-2 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    @foreach($unitsList as $slug => $name)
                        <button type="button" @click.prevent="activeUnit = '{{ $slug }}'"
                            :class="activeUnit === '{{ $slug }}' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold shadow-sm' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200/60 dark:border-slate-700/60'"
                            class="px-4 py-2.5 text-xs rounded-xl transition-all cursor-pointer select-none">
                            {{ $name }}
                        </button>
                    @endforeach
                </div>

                {{-- Unit Step Builders --}}
                @foreach($unitsList as $slug => $name)
                    <div x-show="activeUnit === '{{ $slug }}'" x-transition:enter="transition ease-out duration-150" class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Workflow Timeline for: <span class="text-emerald-600 dark:text-emerald-400">{{ $name }}</span></h4>
                            </div>
                            <button type="button" @click="addStep('{{ $slug }}')" 
                                    class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Step</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(step, index) in unitSteps['{{ $slug }}']" :key="index">
                                <div class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 relative space-y-4 shadow-2xs group hover:border-emerald-300 dark:hover:border-emerald-700/70 transition-all">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 font-black text-xs font-mono flex items-center justify-center shadow-2xs" x-text="index + 1"></span>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" x-text="'Patient Flow Step #' + (index + 1)"></span>
                                        </div>
                                        <button type="button" @click="removeStep('{{ $slug }}', index)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" title="Remove Step">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div class="space-y-3.5">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Step Title</label>
                                                <input type="text" :name="`steps[{{ $slug }}][${index}][title]`" x-model="step.title" placeholder="e.g. Check-in & Triage"
                                                       class="w-full h-11 px-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Instructions & Clinical Details</label>
                                                <textarea :name="`steps[{{ $slug }}][${index}][description]`" x-model="step.description" rows="3" placeholder="Explain what the patient should do in this phase..."
                                                          class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                                            </div>
                                        </div>

                                        {{-- Step Image Upload --}}
                                        <div class="space-y-2"
                                             x-data="{
                                                 isStepDragging: false,
                                                 handleStepFile(file, slug, idx) {
                                                     if (!file || !file.type.startsWith('image/')) return;
                                                     const input = document.getElementById(`step_image_input_${slug}_${idx}`);
                                                     $store.imageCropper.open(file, {
                                                         aspectRatio: 16/9,
                                                         subtitle: 'Landscape crop (16:9) — Process Step Image',
                                                         onApply: (blob, previewUrl) => {
                                                             step._preview = previewUrl;
                                                             setCroppedFile(input, blob, file.name || 'step.jpg');
                                                         }
                                                     });
                                                 }
                                             }">
                                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Step Visual (Optional 16:9 Banner)</label>
                                            <input type="hidden" :name="`steps[{{ $slug }}][${index}][image]`" x-model="step.image">
                                            <input type="file" :id="`step_image_input_{{ $slug }}_${index}`" :name="`step_images[{{ $slug }}][${index}]`" accept="image/*" class="hidden"
                                                   @change="if ($event.target.files.length) handleStepFile($event.target.files[0], '{{ $slug }}', index)">

                                            <div class="flex items-center gap-3">
                                                <div @dragover.prevent="isStepDragging = true"
                                                     @dragleave.prevent="isStepDragging = false"
                                                     @drop.prevent="isStepDragging = false; if ($event.dataTransfer.files.length) handleStepFile($event.dataTransfer.files[0], '{{ $slug }}', index)"
                                                     @click="document.getElementById(`step_image_input_{{ $slug }}_${index}`).click()"
                                                     :class="isStepDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800/60'"
                                                     class="flex-1 border-2 border-dashed rounded-2xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-1.5">
                                                    <div class="w-9 h-9 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-2xs">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Choose or drop step image</span>
                                                    <span class="text-[10px] text-slate-400">JPG, PNG, WEBP · 16:9 ratio</span>
                                                </div>

                                                <template x-if="step.image || step._preview">
                                                    <div class="relative w-28 h-20 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-2xs shrink-0 group/img">
                                                        <img :src="step._preview || '/uploads/' + step.image" class="w-full h-full object-cover">
                                                        <button type="button" @click="step.image = ''; step._preview = null; const inp = document.getElementById(`step_image_input_{{ $slug }}_${index}`); if(inp) inp.value = '';" 
                                                                class="absolute top-1.5 right-1.5 bg-rose-600 text-white rounded-lg p-1 opacity-0 group-hover/img:opacity-100 transition shadow-xs cursor-pointer" title="Remove image">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 6: FAQs
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'faq'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Frequently Asked Questions</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Common public inquiries and authoritative responses displayed in the public FAQ modal.</p>
                    </div>
                </div>
            </div>

            @php
                $faqs = [];
                if(isset($settings['faq']['faq_items'])) {
                    $faqs = json_decode($settings['faq']['faq_items']->value, true) ?: [];
                }
                if(empty($faqs)) $faqs = [['question' => '', 'answer' => '']];
            @endphp

            <div x-data="{ 
                initFaqs: {{ json_encode($faqs) }},
                faqs: {{ json_encode($faqs) }},
                resetFaqs() {
                    this.faqs = JSON.parse(JSON.stringify(this.initFaqs));
                },
                addFaq() {
                    this.faqs.push({ question: '', answer: '' });
                },
                removeFaq(index) {
                    this.faqs.splice(index, 1);
                    if(this.faqs.length === 0) this.addFaq();
                }
            }"
            @content-reset.window="resetFaqs()" class="space-y-4">
                <template x-for="(faq, index) in faqs" :key="index">
                    <div class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-4 relative shadow-2xs group hover:border-emerald-300 dark:hover:border-emerald-700/70 transition-all">
                        <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 flex items-center justify-center font-black text-xs font-mono shadow-2xs" x-text="'Q' + (index + 1)"></span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider" x-text="'Question Item #' + (index + 1)"></span>
                            </div>
                            <button type="button" @click="removeFaq(index)" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" title="Remove FAQ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Question Title</label>
                            <input type="text" :name="`faq[${index}][question]`" x-model="faq.question" placeholder="e.g. What are the clinic operating hours and emergency policies?"
                                   class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Official Response</label>
                            <textarea :name="`faq[${index}][answer]`" x-model="faq.answer" rows="3" placeholder="Provide a helpful, precise answer to constituents..."
                                      class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all"></textarea>
                        </div>
                    </div>
                </template>

                <div class="pt-2">
                    <button type="button" @click="addFaq()" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-2 shadow-sm hover:shadow cursor-pointer active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Question</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 7: PRIVACY POLICY
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'privacy'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Data Privacy Policy (RA 10173 Compliance)</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure the official health data privacy governance notice displayed to citizens and patients.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Introduction Text</label>
                    <textarea name="settings[privacy_intro]" rows="3"
                              placeholder="Enter data privacy statement introduction..."
                              class="w-full p-4 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">{{ old('settings.privacy_intro', $settings['privacy']['privacy_intro']->value ?? '') }}</textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Introductory declaration citing compliance with Republic Act No. 10173.</p>
                </div>

                @php
                    $privacyItems = [];
                    if(isset($settings['privacy']['privacy_items'])) {
                        $privacyItems = json_decode($settings['privacy']['privacy_items']->value, true) ?: [];
                    }
                    if(empty($privacyItems)) $privacyItems = [''];
                @endphp

                <div x-data="{ 
                    initItems: {{ json_encode($privacyItems) }},
                    items: {{ json_encode($privacyItems) }},
                    resetItems() {
                        this.items = JSON.parse(JSON.stringify(this.initItems));
                    },
                    addItem() { this.items.push(''); },
                    removeItem(index) {
                        this.items.splice(index, 1);
                        if(this.items.length === 0) this.addItem();
                    }
                }"
                @content-reset.window="resetItems()" class="p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 space-y-4 shadow-2xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 dark:border-slate-700/60">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Policy Commitments & Core Protections</label>
                        <span class="text-[11px] text-slate-400">Bulleted statutory commitments</span>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex gap-3 items-center">
                                <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-800/60 flex items-center justify-center font-black text-xs font-mono shrink-0 shadow-2xs" x-text="index + 1"></span>
                                <input type="text" :name="`privacy_list[]`" x-model="items[index]" placeholder="e.g. Personal information is processed exclusively for medical triage and clinical scheduling."
                                       class="flex-1 h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                                <button type="button" @click="removeItem(index)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition cursor-pointer" title="Remove point">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="addItem()" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Commitment Point</span>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Footer Contact Statement</label>
                    <input type="text" name="settings[privacy_footer]" value="{{ old('settings.privacy_footer', $settings['privacy']['privacy_footer']->value ?? '') }}"
                           placeholder="e.g. Inquiries regarding health records may be directed to our Data Protection Officer at privacy@silang.gov.ph"
                           class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    <p class="text-[11px] text-slate-400 mt-1">Conclusive statement rendered at the foot of the privacy charter modal.</p>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────────────────────
             TAB 8: FOOTER & CONTACT
        ───────────────────────────────────────────────────────────── --}}
        <div x-show="activeTab === 'footer'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Footer Address & Municipal Directory</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Official physical location, municipal email, and telephone lines displayed on the portal footer.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Street Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <input type="text" name="settings[footer_address_line1]" value="{{ old('settings.footer_address_line1', $settings['footer']['footer_address_line1']->value ?? 'M.H del Pilar St.') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Street address and building designation.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Municipality & Province</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <input type="text" name="settings[footer_address_line2]" value="{{ old('settings.footer_address_line2', $settings['footer']['footer_address_line2']->value ?? 'Silang, Cavite') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">City or Municipality, Province, and Postal code.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Public Telephone Line</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <input type="text" name="settings[footer_phone]" value="{{ old('settings.footer_phone', $settings['footer']['footer_phone']->value ?? '(046) 414-0209') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Landline telephone or direct health office line.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Official Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input type="email" name="settings[footer_email]" value="{{ old('settings.footer_email', $settings['footer']['footer_email']->value ?? 'contact@silang.gov.ph') }}"
                               class="w-full h-11 pl-10 pr-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 shadow-2xs transition-all">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Municipal portal contact or RHU health desk email.</p>
                </div>
            </div>
        </div>


        {{-- Unified Sticky Save Changes Bar --}}
        <div class="sticky bottom-6 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-3xl border border-slate-200/90 dark:border-slate-800 px-6 sm:px-8 py-4 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5 text-xs text-slate-500 dark:text-slate-400">
                <div class="w-7 h-7 rounded-lg bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="hidden sm:inline font-medium">Review your adjustments across tabs before publishing updates live.</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                {{-- Reset Back to Normal Button --}}
                <button type="button" 
                        @click="showResetModal = true"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs sm:text-sm shadow-2xs hover:shadow-xs transition-all active:scale-95 flex items-center gap-2 cursor-pointer"
                        title="Reset all fields back to saved normal values">
                    <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset to Normal</span>
                </button>

                <button type="button" 
                        @click="validateAndConfirm()"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-sm hover:shadow transition-all active:scale-95 flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>

    </form>
</div>

@include('partials.image-cropper')
@endsection

