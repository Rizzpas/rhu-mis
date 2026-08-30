@extends('layouts.admin')

@section('header', 'Edit Announcement')

@section('content')
<script>
    function announcementEditForm() {
        return {
            new_sections: [],
            selectedSections: [],
            showBulkModal: false,
            mainImageName: '',
            display_type: '{{ $announcement->display_type }}',
            mainPreviews: [],
            fullscreenImage: null,
            isMainDragging: false,

            handleMainFiles(files) {
                if (!files || files.length === 0) return;
                const file = files[0];
                const input = document.getElementById('main_image');
                
                if (file.type.startsWith('image/')) {
                    window.openImageCropper(file, {
                        aspectRatio: NaN,
                        subtitle: 'Announcement Cover Media — Free crop or choose ratio',
                        onApply: (blob, previewUrl) => {
                            this.mainImageName = file.name;
                            this.mainPreviews = [previewUrl];
                            setCroppedFile(input, blob, file.name || 'cover.jpg');
                        }
                    });
                } else {
                    this.mainImageName = file.name;
                    this.mainPreviews = [];
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
            },

            handleMainImageChange(event) {
                this.handleMainFiles(event.target.files);
            },

            handleExistingSectionFiles(files, sectionId) {
                if (!files || files.length === 0) return;
                const file = files[0];
                const input = document.getElementById('existing_image_' + sectionId);
                const label = document.getElementById('filename_' + sectionId);
                
                if (file.type.startsWith('image/')) {
                    window.openImageCropper(file, {
                        aspectRatio: NaN,
                        subtitle: 'Free crop — Section Media',
                        onApply: (blob, previewUrl) => {
                            if (label) label.innerText = file.name;
                            setCroppedFile(input, blob, file.name || 'section.jpg');
                        }
                    });
                } else {
                    if (label) label.innerText = file.name;
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
            },

            handleNewSectionFiles(files, index) {
                if (!files || files.length === 0) return;
                const file = files[0];
                const input = document.getElementById('new_section_image_' + index);
                
                if (file.type.startsWith('image/')) {
                    window.openImageCropper(file, {
                        aspectRatio: NaN,
                        subtitle: 'Free crop — Section Media',
                        onApply: (blob, previewUrl) => {
                            this.new_sections[index].fileName = file.name;
                            this.new_sections[index].preview = previewUrl;
                            setCroppedFile(input, blob, file.name || 'section.jpg');
                        }
                    });
                } else {
                    this.new_sections[index].fileName = file.name;
                    this.new_sections[index].preview = '';
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
            },
            
            addSection() {
                if(this.new_sections.length < 5) {
                    this.new_sections.push({ fileName: '', layout: 'left', preview: null, isDragging: false });
                } else {
                    alert('Maximum of 5 additional sections allowed.');
                }
            },
            removeSection(index) {
                this.new_sections.splice(index, 1);
            }
        }
    }
    window.announcementEditForm = announcementEditForm;
    if (window.Alpine) {
        Alpine.data('announcementEditForm', announcementEditForm);
    }
    document.addEventListener('alpine:init', () => {
        Alpine.data('announcementEditForm', announcementEditForm);
    });
</script>

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 max-w-4xl mx-auto overflow-hidden" x-data="announcementEditForm()">
    <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-white dark:bg-slate-900 relative overflow-hidden">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-5 dark:opacity-10 pointer-events-none">
            <svg class="h-full w-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 L100 0" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
                <path d="M0 0 L100 100" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
            </svg>
        </div>

        <div class="relative z-10">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Edit Announcement</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Refine and update: {{ Str::limit($announcement->title, 40) }}</p>
        </div>
        
        <a href="{{ route('admin.announcements.index') }}" class="relative z-10 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold transition-all border border-slate-200 dark:border-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
    </div>

    <!-- Bulk Delete Form (Moved outside main form) -->
    <form id="bulk-delete-form" action="{{ route('admin.announcements.bulk-delete-images') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8 bg-white dark:bg-slate-900">
        @csrf
        @method('PUT')

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6" role="alert">
                <p class="font-bold text-red-700">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Main Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Heading / Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ $announcement->title }}" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-3 text-lg font-bold transition-all">
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Subheading</label>
                <input type="text" name="subheading" value="{{ $announcement->subheading }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-3 transition-all">
            </div>

            <!-- Event Date & Time Section -->
            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Event Schedule (Optional)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Custom Calendar UI -->
                    <div>
                        <div class="border border-slate-200 dark:border-slate-700 rounded-2xl p-5 bg-slate-50 dark:bg-slate-800/50 shadow-inner" x-data="{ 
                            currentDate: new Date('{{ $announcement->event_date ? $announcement->event_date->format('Y-m-d') : date('Y-m-d') }}'),
                            selectedDate: '{{ $announcement->event_date ? $announcement->event_date->format('Y-m-d') : '' }}',
                            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                            get daysInMonth() {
                                return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate();
                            },
                            get startDay() {
                                return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
                            },
                            get monthYear() {
                                return this.monthNames[this.currentDate.getMonth()] + ' ' + this.currentDate.getFullYear();
                            },
                            prevMonth() {
                                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                            },
                            nextMonth() {
                                this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                            },
                            selectDate(day) {
                                let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                // Format as YYYY-MM-DD for the hidden input
                                let offset = date.getTimezoneOffset();
                                date = new Date(date.getTime() - (offset*60*1000));
                                this.selectedDate = date.toISOString().split('T')[0];
                            },
                            isSelected(day) {
                                if(!this.selectedDate) return false;
                                let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                let offset = date.getTimezoneOffset();
                                date = new Date(date.getTime() - (offset*60*1000));
                                return this.selectedDate === date.toISOString().split('T')[0];
                            },
                            isPast(day) {
                                let date = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                                let today = new Date();
                                today.setHours(0,0,0,0);
                                return date < today;
                            }
                        }">
                            
                            <!-- Calendar Header -->
                            <div class="flex justify-between items-center mb-4">
                                <button type="button" @click="prevMonth" class="p-1.5 hover:bg-white dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-xl transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <h4 class="font-bold text-slate-800 dark:text-white tracking-tight" x-text="monthYear"></h4>
                                <button type="button" @click="nextMonth" class="p-1.5 hover:bg-white dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-xl transition-all border border-transparent hover:border-slate-200 dark:hover:border-slate-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">
                                <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                            </div>
                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="blank in startDay">
                                    <div></div>
                                </template>
                                <template x-for="day in daysInMonth">
                                    <div 
                                        @click="!isPast(day) && selectDate(day)"
                                        class="h-9 md:h-10 rounded-xl flex items-center justify-center text-sm transition-all border border-transparent relative group"
                                        :class="{
                                            'bg-emerald-600 text-white font-black shadow-lg shadow-emerald-900/20 scale-105 z-10': isSelected(day),
                                            'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer hover:border-emerald-200 dark:hover:border-emerald-800/50': !isSelected(day) && !isPast(day),
                                            'text-slate-300 dark:text-slate-600 cursor-not-allowed': isPast(day)
                                        }"
                                    >
                                        <span x-text="day"></span>
                                        <template x-if="!isPast(day) && !isSelected(day)">
                                            <div class="absolute bottom-1 w-1 h-1 bg-emerald-500 rounded-full opacity-0 group-hover:opacity-100"></div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Hidden Input -->
                            <input type="hidden" name="event_date" x-model="selectedDate">
                            
                            <div class="mt-4 text-center" x-show="selectedDate">
                                <span class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-900/50 px-3 py-1.5 rounded-full border border-emerald-200 dark:border-emerald-800 uppercase tracking-widest">
                                    Selected: <span x-text="selectedDate"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Time Picker -->
                    <div x-data="{ timeValue: '{{ $announcement->start_time ? \Carbon\Carbon::parse($announcement->start_time)->format('H:i') : '' }}' }">
                        <div class="relative group">
                            <!-- Custom Clock Icon (Left Side) -->
                            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none group-focus-within:text-emerald-500 text-slate-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>

                            <input type="time" name="start_time" x-model="timeValue" 
                                class="w-full rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 pl-12 pr-12 py-4 text-xl font-bold tracking-tight transition-all">
                            
                            <!-- Clear Button (Optional Field) - Right Side -->
                            <button type="button" x-show="timeValue" @click="timeValue = ''" 
                                class="absolute inset-y-0 right-12 flex items-center text-slate-400 hover:text-red-500 transition-colors"
                                title="Clear Time">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 font-medium flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Optional. Set a specific start time for the event.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Main Content <span class="text-red-500">*</span></label>
                <textarea name="content" rows="6" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 p-5 leading-relaxed transition-all">{{ $announcement->content }}</textarea>
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Cover Image/Video</label>
                @if($announcement->image_path)
                    <div class="mb-3">
                        <img src="{{ asset('uploads/' . $announcement->image_path) }}" class="h-32 w-auto rounded-lg border border-gray-200 dark:border-gray-700 object-cover shadow-sm">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">Current Cover</p>
                    </div>
                @endif
                
                <!-- File Input Wrapper / Drag & Drop -->
                <div class="relative group"
                     @dragenter.prevent="isMainDragging = true"
                     @dragover.prevent="isMainDragging = true"
                     @dragleave.prevent="if ($event.currentTarget.contains($event.relatedTarget)) return; isMainDragging = false"
                     @drop.prevent="isMainDragging = false; if ($event.dataTransfer && $event.dataTransfer.files.length) handleMainFiles($event.dataTransfer.files)">
                    <input type="file" name="images[]" id="main_image" class="hidden" multiple accept="image/*,video/mp4" @change="handleMainImageChange($event)">
                    <div @click="document.getElementById('main_image').click()" 
                         :class="isMainDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800'"
                         class="flex flex-col items-center justify-center w-full px-4 py-6 border-2 border-dashed rounded-xl cursor-pointer transition-all text-center gap-1.5">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-1 pointer-events-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <span class="text-slate-700 dark:text-slate-200 text-sm font-semibold pointer-events-none" x-text="mainImageName || 'Change Cover Image (Drag & Drop or browse)'"></span>
                        <span class="text-xs text-slate-400 pointer-events-none">Interactive crop — Free, 16:9, Poster, or Full Image</span>
                    </div>
                </div>

                <!-- Main Image Previews -->
                <div class="mt-4 grid grid-cols-3 gap-4" x-show="mainPreviews.length > 0">
                    <template x-for="(src, index) in mainPreviews" :key="index">
                        <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm" @click="fullscreenImage = src">
                            <img :src="src" class="w-full h-24 object-cover transform group-hover:scale-110 transition-transform duration-500">
                        </div>
                    </template>
                </div>
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Display Layout</label>
                <div class="relative">
                    <select name="display_type" x-model="display_type" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-3 appearance-none font-bold">
                        <option value="list">Standard List (Alternating)</option>
                        <option value="carousel">Carousel / Slider Mode</option>
                    </select>
                     <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Status</label>
                <div class="relative">
                    <select name="status" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-3 appearance-none font-bold">
                        <option value="published" {{ $announcement->status == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="pending" {{ $announcement->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="draft" {{ $announcement->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-slate-100 dark:border-slate-800 my-8">

        <!-- Existing Sections -->
        <div>
            <div class="flex justify-between items-end mb-6">
                <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Existing Sections
                </h4>
                
                <!-- Bulk Actions Toolbar -->
                <div x-show="selectedSections.length > 0" x-transition class="flex items-center gap-3 bg-red-50 dark:bg-red-900/20 px-4 py-2 rounded-xl border border-red-100 dark:border-red-900/50">
                    <span class="text-xs text-red-700 dark:text-red-400 font-bold" x-text="selectedSections.length + ' selected'"></span>
                    <button type="button" @click="showBulkModal = true" class="text-[10px] bg-red-600 text-white px-3 py-1.5 rounded-lg font-black uppercase tracking-wider shadow-sm hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
            
            @php
                $sections = $announcement->images->where('type', 'section');
            @endphp

            @if($sections->count() > 0)
                <div class="space-y-6">
                    @foreach($sections as $section)
                        <div x-data="{ layout: '{{ $section->layout ?? 'left' }}' }" 
                             class="bg-slate-50 dark:bg-slate-950/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 relative group flex flex-col md:flex-row items-center gap-6"
                             :class="{
                                'md:flex-row-reverse': layout === 'right',
                                'flex-col': layout === 'middle',
                                'ring-2 ring-emerald-500': selectedSections.includes('{{ $section->id }}')
                             }">
                            
                            <!-- Checkbox -->
                            <div class="absolute top-4 left-4 z-10">
                                <input type="checkbox" value="{{ $section->id }}" x-model="selectedSections" class="w-5 h-5 text-emerald-600 rounded-lg border-slate-300 dark:border-slate-700 shadow-sm cursor-pointer bg-white dark:bg-slate-900">
                            </div>

                            <!-- Media Column -->
                            <div class="w-full" :class="{ 'md:w-full': layout === 'middle', 'md:w-1/2': layout !== 'middle' }">
                                <div class="bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                                    @if($section->image_path)
                                        <div class="aspect-video mb-3 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800">
                                            @if($section->media_type == 'video_upload' || Str::endsWith($section->image_path, '.mp4'))
                                                 <video src="{{ asset('uploads/' . $section->image_path) }}" controls class="object-cover w-full h-full"></video>
                                            @else
                                                 <img src="{{ asset('uploads/' . $section->image_path) }}" class="object-cover w-full h-full">
                                            @endif
                                        </div>
                                    @endif
                                    
                                 <div class="relative group"
                                     x-data="{ isDragging: false }"
                                     @dragover.prevent="isDragging = true"
                                     @dragleave.prevent="isDragging = false"
                                     @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) handleExistingSectionFiles($event.dataTransfer.files, {{ $section->id }})">
                                    <input type="file" name="existing_sections[{{ $section->id }}][image]" id="existing_image_{{ $section->id }}" class="hidden" accept="image/*,video/mp4" @change="handleExistingSectionFiles($event.target.files, {{ $section->id }})">
                                    <div @click="document.getElementById('existing_image_{{ $section->id }}').click()" 
                                         :class="isDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800'"
                                         class="flex items-center justify-between w-full px-3 py-2 border border-dashed rounded-lg cursor-pointer transition-colors">
                                        <span id="filename_{{ $section->id }}" class="text-[10px] text-slate-500 truncate">Change Media (Free crop)...</span>
                                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex justify-center gap-3">
                                 <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="existing_sections[{{ $section->id }}][layout]" value="left" x-model="layout" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700">
                                    <span class="ml-1 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Left</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="existing_sections[{{ $section->id }}][layout]" value="middle" x-model="layout" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700">
                                    <span class="ml-1 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Middle</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="existing_sections[{{ $section->id }}][layout]" value="right" x-model="layout" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700">
                                    <span class="ml-1 text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Right</span>
                                </label>
                            </div>
                        </div>

                        <!-- Content Column -->
                        <div class="w-full" :class="{ 'md:w-full': layout === 'middle', 'md:w-1/2': layout !== 'middle' }">
                            <textarea name="existing_sections[{{ $section->id }}][content]" rows="6" class="w-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm p-4 leading-relaxed transition-all">{{ $section->content }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 dark:bg-slate-950/30 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">No additional sections yet.</p>
            </div>
        @endif
    </div>

    <!-- New Sections -->
    <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800">
        <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Sections
        </h4>
        
        <div class="space-y-6">
            <template x-for="(section, index) in new_sections" :key="index">
                <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-6 rounded-2xl border border-emerald-100 dark:border-emerald-800/30 relative group transition hover:shadow-md">
                    <button type="button" @click="removeSection(index)" class="absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors bg-white dark:bg-slate-900 rounded-full p-1 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3" x-text="'New Section ' + (index + 1) + ' Media'"></label>
                            
                            <div class="relative mb-4"
                                 @dragover.prevent="section.isDragging = true"
                                 @dragleave.prevent="section.isDragging = false"
                                 @drop.prevent="section.isDragging = false; if ($event.dataTransfer.files.length) handleNewSectionFiles($event.dataTransfer.files, index)">
                                <input type="file" :name="'new_sections[' + index + '][image]'" :id="'new_section_image_' + index" class="hidden" accept="image/*,video/mp4" @change="handleNewSectionFiles($event.target.files, index)">
                                <div @click="document.getElementById('new_section_image_' + index).click()" 
                                     :class="section.isDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-emerald-200 dark:border-emerald-800/50 bg-white dark:bg-slate-900 hover:bg-emerald-50'"
                                     class="flex items-center justify-between w-full px-4 py-3 border border-dashed rounded-xl cursor-pointer transition-all">
                                    <span class="text-xs text-slate-500 truncate" x-text="section.fileName || 'Drag & drop or browse media (Free crop)...'"></span>
                                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </div>
                            </div>

                            <div x-show="section.preview" class="mb-4 aspect-video rounded-xl overflow-hidden border border-emerald-100 shadow-sm">
                                <img :src="section.preview" class="w-full h-full object-cover">
                            </div>

                            <div class="flex gap-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" :name="'new_sections[' + index + '][layout]'" value="left" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700" checked>
                                    <span class="ml-1 text-[10px] font-black text-slate-500 uppercase tracking-tighter">Left</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" :name="'new_sections[' + index + '][layout]'" value="middle" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700">
                                    <span class="ml-1 text-[10px] font-black text-slate-500 uppercase tracking-tighter">Middle</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" :name="'new_sections[' + index + '][layout]'" value="right" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700">
                                    <span class="ml-1 text-[10px] font-black text-slate-500 uppercase tracking-tighter">Right</span>
                                </label>
                            </div>
                        </div>

                                <div x-show="section.preview" class="mb-4 aspect-video rounded-xl overflow-hidden border border-emerald-100 shadow-sm">
                                    <img :src="section.preview" class="w-full h-full object-cover">
                                </div>

                                <div class="flex gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" :name="'new_sections[' + index + '][layout]'" value="left" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700" checked>
                                        <span class="ml-1 text-[10px] font-black text-slate-500 uppercase tracking-tighter">Left</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" :name="'new_sections[' + index + '][layout]'" value="middle" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700">
                                        <span class="ml-1 text-[10px] font-black text-slate-500 uppercase tracking-tighter">Middle</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" :name="'new_sections[' + index + '][layout]'" value="right" class="h-3 w-3 text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700">
                                        <span class="ml-1 text-[10px] font-black text-slate-500 uppercase tracking-tighter">Right</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3" x-text="'New Section ' + (index + 1) + ' Content'"></label>
                                <textarea :name="'new_sections[' + index + '][content]'" rows="6" class="w-full rounded-xl border border-emerald-100 dark:border-emerald-800 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm p-4 leading-relaxed transition-all" placeholder="Enter section content..."></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <button type="button" @click="addSection()" class="mt-6 inline-flex items-center px-6 py-4 border-2 border-dashed border-slate-200 dark:border-slate-800 shadow-sm text-xs font-black uppercase tracking-widest rounded-2xl text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:border-emerald-400 hover:text-emerald-600 focus:outline-none transition-all w-full justify-center gap-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Add Another Section
            </button>
        </div>

        <div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-end gap-3.5">
            <a href="{{ route('admin.announcements.index') }}" class="h-11 px-6 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-center flex items-center justify-center shadow-2xs cursor-pointer text-sm">
                Discard Changes
            </a>
            <button type="submit" class="h-11 px-8 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-md shadow-emerald-600/25 hover:shadow-emerald-600/35 transition-all flex items-center justify-center gap-2 active:scale-95 cursor-pointer text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>Save Announcement</span>
            </button>
        </div>
    </form>
    
    <!-- Dedicated Bulk Delete Modal -->
    <template x-if="showBulkModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="showBulkModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <div class="bg-white dark:bg-slate-900 px-6 pt-6 pb-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">Delete Selected Sections?</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">Are you sure you want to delete <span x-text="selectedSections.length" class="font-black text-red-600"></span> sections? This cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <form method="POST" action="{{ route('admin.announcements.bulk-delete-images') }}">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selectedSections">
                                <input type="hidden" name="image_ids[]" :value="id">
                            </template>
                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-red-600 text-sm font-bold text-white hover:bg-red-700 transition-all">
                                Yes, Delete All
                            </button>
                        </form>
                        <button type="button" @click="showBulkModal = false" class="inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

@include('partials.image-cropper')
@endsection
