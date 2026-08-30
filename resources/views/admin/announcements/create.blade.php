@extends('layouts.admin')

@section('header', 'Post Announcement')

@section('content')
<script>
    function announcementForm() {
        return {
            sections: [],
            mainImageName: '',
            mainPreviews: [],
            isMainDragging: false,
            
            addSection() {
                this.sections.push({
                    id: Date.now(),
                    fileName: '',
                    preview: '',
                    isDragging: false
                });
            },
            
            removeSection(index) {
                this.sections.splice(index, 1);
            },
            
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
                    // Non-image (e.g. video), bypass crop
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
            
            handleSectionFiles(files, index) {
                if (!files || files.length === 0) return;
                const file = files[0];
                const input = document.getElementById('file_' + this.sections[index].id);
                
                if (file.type.startsWith('image/')) {
                    window.openImageCropper(file, {
                        aspectRatio: NaN, // Free crop for gallery / section images
                        subtitle: 'Free crop — Section Media',
                        onApply: (blob, previewUrl) => {
                            this.sections[index].fileName = file.name;
                            this.sections[index].preview = previewUrl;
                            setCroppedFile(input, blob, file.name || 'section.jpg');
                        }
                    });
                } else {
                    this.sections[index].fileName = file.name;
                    this.sections[index].preview = '';
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
            },

            handleSectionFileChange(event, index) {
                this.handleSectionFiles(event.target.files, index);
            }
        }
    }
    window.announcementForm = announcementForm;
    if (window.Alpine) {
        Alpine.data('announcementForm', announcementForm);
    }
    document.addEventListener('alpine:init', () => {
        Alpine.data('announcementForm', announcementForm);
    });
</script>

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 max-w-4xl mx-auto overflow-hidden" x-data="announcementForm()">
    <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-white dark:bg-slate-900 relative overflow-hidden">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-5 dark:opacity-10 pointer-events-none">
            <svg class="h-full w-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 L100 0" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
                <path d="M0 0 L100 100" stroke="currentColor" class="text-slate-900 dark:text-white" stroke-width="0.1" />
            </svg>
        </div>

        <div class="relative z-10">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Post New Announcement</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Broadcast important news, events, or health advisories.</p>
        </div>
        
        <a href="{{ route('admin.announcements.index') }}" class="relative z-10 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-bold transition-all border border-slate-200 dark:border-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
    </div>

    <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8 bg-white dark:bg-slate-900">
        @csrf

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6" role="alert">
                <p class="font-bold text-red-700 dark:text-red-400">Please fix the following errors:</p>
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 mt-1">
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
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Community Health Mission 2026" required class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-3 text-lg font-bold transition-all">
            </div>

            <div class="col-span-2 md:col-span-1">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Subheading</label>
                <input type="text" name="subheading" value="{{ old('subheading') }}" placeholder="Short summary or catchphrase" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 px-4 py-3 transition-all">
            </div>

            <!-- Event Date & Time Section -->
            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Event Schedule (Optional)</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Custom Calendar UI -->
                    <div>
                        <div class="border border-slate-200 dark:border-slate-700 rounded-2xl p-5 bg-slate-50 dark:bg-slate-800/50 shadow-inner" x-data="{ 
                            currentDate: new Date(),
                            selectedDate: '{{ old('event_date') }}',
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
                    <div x-data="{ timeValue: '' }">
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
                <textarea name="content" rows="6" required placeholder="Describe the announcement in detail..." class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 p-5 leading-relaxed transition-all">{{ old('content') }}</textarea>
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">Cover Image/Video</label>
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
                        <span class="text-slate-700 dark:text-slate-200 text-sm font-semibold pointer-events-none" x-text="mainImageName || 'Drag & drop image(s) or click to browse'"></span>
                        <span class="text-xs text-slate-400 pointer-events-none">Interactive crop — Free, 16:9, Poster, or Full Image</span>
                    </div>
                </div>
                <!-- Main Image Previews -->
                <div class="mt-4 grid grid-cols-3 gap-4" x-show="mainPreviews.length > 0">
                    <template x-for="(src, index) in mainPreviews" :key="index">
                        <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <img :src="src" class="w-full h-24 object-cover transform group-hover:scale-110 transition-transform duration-500">
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <hr class="border-slate-100 dark:border-slate-800 my-8">

        <!-- Additional Sections Section -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Additional Content Sections
                    </h4>
                    <p class="text-[10px] text-slate-500 mt-1">Add rich content blocks with images and flexible layouts.</p>
                </div>
                <button type="button" @click="addSection" class="bg-slate-900 dark:bg-white dark:text-slate-900 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg transition-all hover:scale-105 active:scale-95">
                    Add Section
                </button>
            </div>

            <div class="space-y-6">
                <template x-for="(section, index) in sections" :key="section.id">
                    <div class="p-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/30 relative group transition-all hover:border-slate-300 dark:hover:border-slate-600">
                        <button type="button" @click="removeSection(index)" class="absolute top-4 right-4 text-slate-400 hover:text-red-500 p-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Section Content</label>
                                    <textarea :name="`sections[${index}][content]`" rows="4" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-4 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Layout Orientation</label>
                                    <select :name="`sections[${index}][layout]`" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-3 text-sm font-bold">
                                        <option value="left">Image Left</option>
                                        <option value="right">Image Right</option>
                                        <option value="middle">Full Width (Center)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Section Media (Image/Video)</label>
                                    <div class="relative group"
                                         @dragenter.prevent="section.isDragging = true"
                                         @dragover.prevent="section.isDragging = true"
                                         @dragleave.prevent="if ($event.currentTarget.contains($event.relatedTarget)) return; section.isDragging = false"
                                         @drop.prevent="section.isDragging = false; if ($event.dataTransfer && $event.dataTransfer.files.length) handleSectionFiles($event.dataTransfer.files, index)">
                                        <input type="file" :name="`sections[${index}][image]`" :id="'file_' + section.id" class="hidden" accept="image/*,video/mp4" @change="handleSectionFileChange($event, index)">
                                        <div @click="document.getElementById('file_' + section.id).click()" 
                                             :class="section.isDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-slate-300 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 bg-white/50 dark:bg-slate-900/50'"
                                             class="flex items-center justify-between w-full px-4 py-3 border-2 border-dashed rounded-xl cursor-pointer transition-all">
                                            <span class="text-slate-500 dark:text-slate-400 truncate text-xs pointer-events-none" x-text="section.fileName || 'Drag & drop or select media (Free crop)...'"></span>
                                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="section.preview" class="aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
                                    <img :src="section.preview" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <div x-show="sections.length === 0" class="text-center py-12 bg-slate-50 dark:bg-slate-950/30 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <p class="text-slate-400 text-sm italic">No additional sections added yet. Click 'Add Section' to enhance your announcement.</p>
            </div>
        </div>

        <!-- Submit Section -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 pt-8 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="status" value="published" checked class="peer sr-only">
                        <div class="w-12 h-6 bg-slate-200 dark:bg-slate-800 rounded-full peer peer-checked:bg-emerald-500 transition-colors"></div>
                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-6 shadow-sm"></div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200 group-hover:text-emerald-600 transition-colors">Publish Immediately</span>
                        <p class="text-[10px] text-slate-400">If unchecked, announcement will be saved as pending.</p>
                    </div>
                </label>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <button type="button" @click="window.location.href='{{ route('admin.announcements.index') }}'" class="flex-1 md:flex-none h-11 px-6 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-sm shadow-2xs cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="flex-1 md:flex-none h-11 px-8 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-md shadow-emerald-600/25 hover:shadow-emerald-600/35 transition-all active:scale-95 cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Save Announcement</span>
                </button>
            </div>
        </div>
    </form>
</div>

@include('partials.image-cropper')
@endsection
