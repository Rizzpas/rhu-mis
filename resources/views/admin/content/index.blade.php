@extends('layouts.admin')

@section('header', 'Content Management')

@section('content')
<div x-data="{ activeTab: 'topbar' }" class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col gap-2 mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-50">Landing Page Content</h2>
        <p class="text-slate-500 dark:text-slate-400">Manage the text, images, and content displayed on the public landing page.</p>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="bg-slate-100/50 dark:bg-slate-800/50 p-1 rounded-lg flex flex-wrap gap-1 border border-slate-200 dark:border-slate-800">
        <template x-for="tab in [
            { id: 'topbar', name: 'Top Bar' },
            { id: 'hero', name: 'Hero Section' },
            { id: 'about', name: 'Mission & Vision' },
            { id: 'steps', name: 'Process Steps' },
            { id: 'faq', name: 'FAQs' },
            { id: 'privacy', name: 'Privacy Policy' },
            { id: 'footer', name: 'Footer' },
            { id: 'demo', name: 'System Demo' }
        ]">
            <button @click.prevent="activeTab = tab.id"
                :class="{
                    'bg-white dark:bg-slate-950 text-slate-950 dark:text-slate-50 shadow-sm': activeTab === tab.id,
                    'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-50': activeTab !== tab.id
                }"
                class="whitespace-nowrap px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex-1 text-center"
                x-text="tab.name">
            </button>
        </template>
    </div>

    <form x-data="{ showConfirmModal: false }" @submit.prevent="showConfirmModal = true" action="{{ route('admin.content.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-slate-950 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative">
        @csrf
        @method('PUT')

        <!-- Confirmation Modal -->
        <div x-show="showConfirmModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
            <div @click.away="showConfirmModal = false" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 scale-95" 
                 x-transition:enter-end="opacity-100 scale-100" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 scale-100" 
                 x-transition:leave-end="opacity-0 scale-95" 
                 class="bg-white dark:bg-slate-900 rounded-lg shadow-xl max-w-md w-full p-6 border border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-100 dark:bg-teal-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Save Changes?</h3>
                    </div>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400">Are you sure you want to update the landing page content? These changes will be reflected publicly right away.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="showConfirmModal = false" class="px-4 py-2 rounded-md text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border border-slate-200 dark:border-slate-700 shadow-sm">Cancel</button>
                    <button type="button" @click="$el.closest('form').submit()" class="px-4 py-2 rounded-md text-sm font-medium bg-teal-600 text-white hover:bg-teal-700 transition-colors shadow-sm focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">Yes, Save Changes</button>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <!-- Top Bar Tab -->
            <div x-show="activeTab === 'topbar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Top Bar Information</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Configure the top-most navigation bar details.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Clinic Hours</label>
                        <input type="text" name="settings[clinic_hours]" value="{{ old('settings.clinic_hours', $settings['topbar']['clinic_hours']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                        <p class="text-xs text-slate-500">Displayed in the top-left of the navigation bar.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Emergency Hotlines</label>
                        <input type="text" name="settings[emergency_hotlines]" value="{{ old('settings.emergency_hotlines', $settings['topbar']['emergency_hotlines']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                        <p class="text-xs text-slate-500">Displayed in the top-right of the navigation bar.</p>
                    </div>
                </div>
            </div>

            <!-- Hero Section Tab -->
            <div x-show="activeTab === 'hero'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-8">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Hero Section</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage the main banner content and imagery.</p>
                </div>
                
                <div class="grid gap-6 p-6 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Badge Text</label>
                        <input type="text" name="settings[hero_badge_text]" value="{{ old('settings.hero_badge_text', $settings['hero']['hero_badge_text']->value ?? '') }}" class="flex h-10 w-full md:w-1/2 rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Title Line 1</label>
                            <input type="text" name="settings[hero_title_line1]" value="{{ old('settings.hero_title_line1', $settings['hero']['hero_title_line1']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Title Highlight</label>
                            <input type="text" name="settings[hero_title_highlight]" value="{{ old('settings.hero_title_highlight', $settings['hero']['hero_title_highlight']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Title Line 2</label>
                            <input type="text" name="settings[hero_title_line2]" value="{{ old('settings.hero_title_line2', $settings['hero']['hero_title_line2']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Hero Description</label>
                        <textarea name="settings[hero_description]" rows="3" class="flex min-h-[80px] w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">{{ old('settings.hero_description', $settings['hero']['hero_description']->value ?? '') }}</textarea>
                    </div>
                </div>

                <div class="grid gap-6 p-6 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    <h4 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-50">Carousel Hero Title</h4>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Headline Text</label>
                            <input type="text" name="settings[carousel_hero_title]" value="{{ old('settings.carousel_hero_title', $settings['hero']['carousel_hero_title']->value ?? '') }}" class="flex h-10 w-full md:w-1/2 rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Subtitle Text</label>
                            <input type="text" name="settings[carousel_hero_subtitle]" value="{{ old('settings.carousel_hero_subtitle', $settings['hero']['carousel_hero_subtitle']->value ?? '') }}" class="flex h-10 w-full md:w-1/2 rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                        </div>
                    </div>
                </div>

                <div class="p-6 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50" 
                     x-data="{ 
                         imagePreview: '{{ isset($settings['hero']['hero_image']->value) ? asset($settings['hero']['hero_image']->value) : '' }}',
                         isDragging: false,
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
                     }">
                    <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300 mb-4 block">Hero Image</label>
                    <div class="flex flex-col md:flex-row items-start gap-6">
                        <div class="flex-1 space-y-4 w-full">
                            <input type="file" id="hero_image_file_input" name="hero_image_file" accept="image/*" class="hidden"
                                @change="if ($event.target.files.length) handleHeroFile($event.target.files[0])">
                            
                            <div @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) handleHeroFile($event.dataTransfer.files[0])"
                                 @click="document.getElementById('hero_image_file_input').click()"
                                 :class="isDragging ? 'border-teal-500 bg-teal-50/50 dark:bg-teal-950/20 ring-2 ring-teal-500/20' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900'"
                                 class="w-full border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2 group">
                                <div class="w-12 h-12 rounded-full bg-teal-50 dark:bg-teal-950/50 text-teal-600 dark:text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                    Drag & drop your hero image here, or <span class="text-teal-600 dark:text-teal-400 underline">browse</span>
                                </p>
                                <p class="text-xs text-slate-400">Supports JPG, PNG, WEBP. Recommended 800×800px (1:1 crop)</p>
                            </div>
                        </div>
                        <div class="w-full md:w-48 h-48 bg-white dark:bg-slate-950 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm flex-shrink-0 flex items-center justify-center relative group">
                            <img x-show="imagePreview" :src="imagePreview" class="w-full h-full object-cover" style="display: none;">
                            <span x-show="!imagePreview" class="text-sm text-slate-400">No Image</span>
                            <template x-if="imagePreview">
                                <button type="button" @click="document.getElementById('hero_image_file_input').click()" class="absolute inset-0 bg-slate-900/60 text-white flex flex-col items-center justify-center text-xs font-semibold opacity-0 group-hover:opacity-100 transition-opacity gap-1 backdrop-blur-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Change / Crop
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision Tab -->
            <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">About Us</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Define the organizational mission and vision statements.</p>
                </div>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Mission Statement</label>
                        <textarea name="settings[mission_statement]" rows="5" class="flex min-h-[80px] w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">{{ old('settings.mission_statement', $settings['about']['mission_statement']->value ?? '') }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Vision Statement</label>
                        <textarea name="settings[vision_statement]" rows="5" class="flex min-h-[80px] w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">{{ old('settings.vision_statement', $settings['about']['vision_statement']->value ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Process Steps Tab -->
            <div x-show="activeTab === 'steps'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Process Steps</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage the step-by-step instructions displayed on each specific unit's page.</p>
                </div>
                
                @php
                    $units = [
                        'main-health-center' => 'Main Health Center',
                        'lying-in-clinic' => 'Lying-in Clinic',
                        'dental-clinic' => 'Dental Clinic',
                        'tb-dots-facility' => 'TB DOTS Facility',
                        'animal-bite-center' => 'Animal Bite Center'
                    ];
                    
                    $unitSteps = [];
                    foreach($units as $slug => $name) {
                        $key = 'steps_data_' . $slug;
                        if(isset($settings['steps'][$key])) {
                            $unitSteps[$slug] = json_decode($settings['steps'][$key]->value, true) ?: [];
                        } else {
                            $unitSteps[$slug] = [];
                        }
                        if(empty($unitSteps[$slug])) {
                            $unitSteps[$slug] = [['title' => '', 'description' => '', 'image' => '']];
                        }
                        // Ensure each step has an image key
                        foreach($unitSteps[$slug] as &$s) {
                            $s['image'] = $s['image'] ?? '';
                        }
                        unset($s);
                    }
                @endphp

                <div x-data="{ 
                    activeUnit: 'main-health-center',
                    unitSteps: {{ json_encode($unitSteps) }},
                    addStep(slug) {
                        this.unitSteps[slug].push({ title: '', description: '', image: '' });
                    },
                    removeStep(slug, index) {
                        this.unitSteps[slug].splice(index, 1);
                        if(this.unitSteps[slug].length === 0) this.addStep(slug);
                    }
                }">
                    <!-- Unit Sub-Navigation -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($units as $slug => $name)
                            <button @click.prevent="activeUnit = '{{ $slug }}'"
                                :class="activeUnit === '{{ $slug }}' ? 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-100 ring-1 ring-teal-500/20' : 'bg-white dark:bg-slate-950 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-900 border border-slate-200 dark:border-slate-800'"
                                class="px-4 py-2 text-sm font-medium rounded-full transition-all shadow-sm">
                                {{ $name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Step Forms -->
                    @foreach($units as $slug => $name)
                        <div x-show="activeUnit === '{{ $slug }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;" class="space-y-4">
                            <h4 class="text-sm font-semibold tracking-tight text-slate-900 dark:text-slate-50 mb-4 pb-2 border-b border-slate-200 dark:border-slate-800">Steps for {{ $name }}</h4>
                            
                            <template x-for="(step, index) in unitSteps['{{ $slug }}']" :key="index">
                                <div class="flex flex-col sm:flex-row gap-4 items-start p-5 bg-slate-50/50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm relative group">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center font-bold text-sm shadow-sm" x-text="index + 1"></div>
                                    <div class="flex-1 space-y-4 w-full">
                                        <div class="space-y-1">
                                            <input type="text" :name="`steps[{{ $slug }}][${index}][title]`" x-model="step.title" placeholder="Step Title" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm font-medium placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors shadow-sm">
                                        </div>
                                        <div class="space-y-1">
                                            <textarea :name="`steps[{{ $slug }}][${index}][description]`" x-model="step.description" rows="2" placeholder="Detailed instructions for this step..." class="flex min-h-[80px] w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors shadow-sm"></textarea>
                                        </div>
                                        <!-- Step Image Upload -->
                                        <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-700"
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
                                            <label class="text-xs font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Step Image (shown on opposite side of timeline)
                                            </label>
                                            <!-- Hidden field to preserve existing image path -->
                                            <input type="hidden" :name="`steps[{{ $slug }}][${index}][image]`" x-model="step.image">
                                            
                                            <input type="file" :id="`step_image_input_{{ $slug }}_${index}`" :name="`step_images[{{ $slug }}][${index}]`" accept="image/*" class="hidden"
                                                @change="if ($event.target.files.length) handleStepFile($event.target.files[0], '{{ $slug }}', index)">

                                            <div class="flex items-center gap-4">
                                                <div @dragover.prevent="isStepDragging = true"
                                                     @dragleave.prevent="isStepDragging = false"
                                                     @drop.prevent="isStepDragging = false; if ($event.dataTransfer.files.length) handleStepFile($event.dataTransfer.files[0], '{{ $slug }}', index)"
                                                     @click="document.getElementById(`step_image_input_{{ $slug }}_${index}`).click()"
                                                     :class="isStepDragging ? 'border-teal-500 bg-teal-50/50 dark:bg-teal-950/20' : 'border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 hover:bg-slate-50 dark:hover:bg-slate-900'"
                                                     class="flex-1 border border-dashed rounded-lg px-4 py-2 text-center cursor-pointer transition-all flex items-center justify-center gap-2">
                                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span class="text-xs text-slate-600 dark:text-slate-300">Drag & drop or <span class="text-teal-600 dark:text-teal-400 underline">browse</span> to crop (16:9)</span>
                                                </div>

                                                <!-- Current Image Preview -->
                                                <template x-if="step.image || step._preview">
                                                    <div class="relative w-20 h-14 rounded-md overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm flex-shrink-0 group/img">
                                                        <img :src="step._preview || '/uploads/' + step.image" class="w-full h-full object-cover">
                                                        <button type="button" @click="step.image = ''; step._preview = null; const inp = document.getElementById(`step_image_input_{{ $slug }}_${index}`); if(inp) inp.value = '';" class="absolute top-0.5 right-0.5 bg-red-500 text-white rounded-full p-0.5 opacity-0 group-hover/img:opacity-100 transition-opacity hover:bg-red-600" title="Remove image">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <p class="text-[10px] text-slate-400">Optional. Displayed on the opposite side of the text card in the alternating timeline.</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeStep('{{ $slug }}', index)" class="sm:absolute top-5 right-5 p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors" title="Remove Step">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            
                            <button type="button" @click="addStep('{{ $slug }}')" class="mt-4 px-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm text-slate-700 dark:text-slate-300 rounded-md text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Step
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- FAQs Tab -->
            <div x-show="activeTab === 'faq'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Frequently Asked Questions</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage the global FAQ list.</p>
                </div>
                
                @php
                    $faqs = [];
                    if(isset($settings['faq']['faq_items'])) {
                        $faqs = json_decode($settings['faq']['faq_items']->value, true) ?: [];
                    }
                    if(empty($faqs)) $faqs = [['question' => '', 'answer' => '']];
                @endphp

                <div x-data="{ 
                    faqs: {{ json_encode($faqs) }},
                    addFaq() {
                        this.faqs.push({ question: '', answer: '' });
                    },
                    removeFaq(index) {
                        this.faqs.splice(index, 1);
                        if(this.faqs.length === 0) this.addFaq();
                    }
                }">
                    <div class="space-y-4">
                        <template x-for="(faq, index) in faqs" :key="index">
                            <div class="flex flex-col sm:flex-row gap-4 items-start p-5 bg-slate-50/50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm relative">
                                <div class="flex-1 space-y-4 w-full">
                                    <input type="text" :name="`faq[${index}][question]`" x-model="faq.question" placeholder="Question" class="flex h-10 w-full font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors shadow-sm">
                                    <textarea :name="`faq[${index}][answer]`" x-model="faq.answer" rows="3" placeholder="Answer" class="flex min-h-[80px] w-full rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors shadow-sm"></textarea>
                                </div>
                                <button type="button" @click="removeFaq(index)" class="sm:absolute top-5 right-5 p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors" title="Remove FAQ">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addFaq()" class="mt-4 px-4 py-2 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm text-slate-700 dark:text-slate-300 rounded-md text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add FAQ
                    </button>
                </div>
            </div>

            <!-- Privacy Policy Tab -->
            <div x-show="activeTab === 'privacy'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Data Privacy Policy</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Configure the global privacy policy statements.</p>
                </div>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Introduction Text</label>
                        <textarea name="settings[privacy_intro]" rows="3" class="flex min-h-[80px] w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">{{ old('settings.privacy_intro', $settings['privacy']['privacy_intro']->value ?? '') }}</textarea>
                    </div>

                    @php
                        $privacyItems = [];
                        if(isset($settings['privacy']['privacy_items'])) {
                            $privacyItems = json_decode($settings['privacy']['privacy_items']->value, true) ?: [];
                        }
                        if(empty($privacyItems)) $privacyItems = [''];
                    @endphp

                    <div x-data="{ 
                        items: {{ json_encode($privacyItems) }},
                        addItem() {
                            this.items.push('');
                        },
                        removeItem(index) {
                            this.items.splice(index, 1);
                            if(this.items.length === 0) this.addItem();
                        }
                    }" class="space-y-4">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Policy Points (Bulleted List)</label>
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex gap-3 items-start group">
                                    <div class="pt-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-teal-500 mt-1"></div>
                                    </div>
                                    <textarea :name="`privacy_list[]`" x-model="items[index]" rows="2" class="flex min-h-[60px] flex-1 rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors shadow-sm"></textarea>
                                    <button type="button" @click="removeItem(index)" class="p-2 text-slate-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addItem()" class="mt-3 text-sm text-teal-600 dark:text-teal-400 font-medium hover:text-teal-700 dark:hover:text-teal-300 transition-colors flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Policy Point
                        </button>
                    </div>

                    <div class="space-y-2 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Footer Text</label>
                        <textarea name="settings[privacy_footer]" rows="2" class="flex min-h-[60px] w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">{{ old('settings.privacy_footer', $settings['privacy']['privacy_footer']->value ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Footer Tab -->
            <div x-show="activeTab === 'footer'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Footer Information</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Manage the global footer contact details.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Address Line 1</label>
                        <input type="text" name="settings[footer_address_line1]" value="{{ old('settings.footer_address_line1', $settings['footer']['footer_address_line1']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Address Line 2 (City, Province)</label>
                        <input type="text" name="settings[footer_address_line2]" value="{{ old('settings.footer_address_line2', $settings['footer']['footer_address_line2']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Contact Phone</label>
                        <input type="text" name="settings[footer_phone]" value="{{ old('settings.footer_phone', $settings['footer']['footer_phone']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium leading-none text-slate-900 dark:text-slate-300">Contact Email</label>
                        <input type="email" name="settings[footer_email]" value="{{ old('settings.footer_email', $settings['footer']['footer_email']->value ?? '') }}" class="flex h-10 w-full rounded-md border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent dark:text-slate-50 transition-colors">
                    </div>
                </div>
            </div>

            <!-- System Demo Tab -->
            <div x-show="activeTab === 'demo'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;" class="space-y-6">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-slate-50">Defense Presentation Mode</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Enable this mode during presentations to bypass strict schedule time-windows.</p>
                </div>
                
                <div class="p-6 rounded-lg border-2 border-teal-500 bg-teal-50 dark:bg-teal-900/20 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="text-base font-bold text-teal-800 dark:text-teal-400 mb-1">Live Demo Mode is {{ (\App\Models\SiteSetting::get('demo_mode') == '1') ? 'ON' : 'OFF' }}</h4>
                        <p class="text-sm text-teal-600 dark:text-teal-300">When enabled, the system expands the heartbeat threshold to 60 minutes and completely ignores the doctor's specific shift hours for the "Present" status calculation. This ensures doctors appear online easily during the demo.</p>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="hidden" name="settings[demo_mode]" value="0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[demo_mode]" value="1" class="sr-only peer" {{ (\App\Models\SiteSetting::get('demo_mode') == '1') ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all dark:border-slate-600 peer-checked:bg-teal-600"></div>
                            <span class="ml-3 text-sm font-bold text-slate-900 dark:text-slate-300 whitespace-nowrap">Enable Demo Mode</span>
                        </label>
                    </div>
                </div>
            </div>

        </div>

        <div class="px-6 md:px-8 py-4 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 rounded-b-xl flex justify-end">
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950 disabled:pointer-events-none disabled:opacity-50 bg-teal-600 text-white hover:bg-teal-700 h-10 px-6 py-2 shadow-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Save Changes
            </button>
        </div>
    </form>
</div>

@include('partials.image-cropper')
@endsection
