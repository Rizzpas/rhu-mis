@extends('layouts.admin')

@section('header', 'Edit Announcement')

@section('content')
<script>
    function customDatePicker(initialDate = '') {
        return {
            showDatePicker: false,
            dateValue: initialDate,
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            days: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            init() {
                if (this.dateValue) {
                    let d = new Date(this.dateValue + 'T00:00:00');
                    if (!isNaN(d.getTime())) {
                        this.currentMonth = d.getMonth();
                        this.currentYear = d.getFullYear();
                    }
                }
            },
            get formattedDate() {
                if (!this.dateValue) return '';
                let d = new Date(this.dateValue + 'T00:00:00');
                if (isNaN(d.getTime())) return this.dateValue;
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            },
            get daysInMonth() {
                return new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
            },
            get startDayOfWeek() {
                return new Date(this.currentYear, this.currentMonth, 1).getDay();
            },
            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
            },
            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
            },
            selectDay(day) {
                let m = String(this.currentMonth + 1).padStart(2, '0');
                let d = String(day).padStart(2, '0');
                this.dateValue = `${this.currentYear}-${m}-${d}`;
                this.showDatePicker = false;
            },
            clearDate() {
                this.dateValue = '';
                this.showDatePicker = false;
            },
            selectToday() {
                let today = new Date();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                let m = String(today.getMonth() + 1).padStart(2, '0');
                let d = String(today.getDate()).padStart(2, '0');
                this.dateValue = `${today.getFullYear()}-${m}-${d}`;
                this.showDatePicker = false;
            },
            isSelected(day) {
                if (!this.dateValue) return false;
                let m = String(this.currentMonth + 1).padStart(2, '0');
                let d = String(day).padStart(2, '0');
                return this.dateValue === `${this.currentYear}-${m}-${d}`;
            },
            isToday(day) {
                let today = new Date();
                return today.getFullYear() === this.currentYear && today.getMonth() === this.currentMonth && today.getDate() === day;
            }
        };
    }
    window.customDatePicker = customDatePicker;

    function customTimePicker(initialTime = '') {
        return {
            showTimePicker: false,
            timeValue: initialTime,
            hour: '08',
            minute: '00',
            period: 'AM',
            hoursList: ['01','02','03','04','05','06','07','08','09','10','11','12'],
            commonMinutes: ['00','05','10','15','20','25','30','35','40','45','50','55'],
            init() {
                if (this.timeValue) {
                    let parts = this.timeValue.split(':');
                    if (parts.length >= 2) {
                        let h = parseInt(parts[0], 10);
                        let m = parts[1].substring(0, 2);
                        this.period = h >= 12 ? 'PM' : 'AM';
                        let h12 = h % 12;
                        if (h12 === 0) h12 = 12;
                        this.hour = String(h12).padStart(2, '0');
                        this.minute = m;
                    }
                }
            },
            get formattedDisplay() {
                if (!this.timeValue) return '';
                let h = parseInt(this.hour, 10);
                return `${h}:${this.minute} ${this.period}`;
            },
            updateTime() {
                let h = parseInt(this.hour, 10);
                if (this.period === 'AM') {
                    if (h === 12) h = 0;
                } else {
                    if (h !== 12) h += 12;
                }
                let hStr = String(h).padStart(2, '0');
                this.timeValue = `${hStr}:${this.minute}`;
            },
            setHour(h) {
                this.hour = h;
                this.updateTime();
            },
            setMinute(m) {
                this.minute = m;
                this.updateTime();
            },
            setPeriod(p) {
                this.period = p;
                this.updateTime();
            },
            setPreset(h, m, p) {
                this.hour = String(h).padStart(2, '0');
                this.minute = String(m).padStart(2, '0');
                this.period = p;
                this.updateTime();
                this.showTimePicker = false;
            },
            clearTime() {
                this.timeValue = '';
                this.showTimePicker = false;
            }
        };
    }
    window.customTimePicker = customTimePicker;

    function announcementEditForm() {
        return {
            contentAlign: '{{ old('content_align', $announcement->content_align ?? 'left') }}',
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

            removeMainImage() {
                this.mainImageName = '';
                this.mainPreviews = [];
                const input = document.getElementById('main_image');
                if (input) input.value = '';
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
                    this.new_sections.push({ fileName: '', layout: 'middle', textAlign: 'left', preview: null, isDragging: false });
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

<div class="max-w-6xl mx-auto" x-data="announcementEditForm()">

    {{-- Top Header Bar --}}
    <div class="flex items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.announcements.index') }}" 
               class="w-8 h-8 rounded-lg flex items-center justify-center bg-white dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white border border-slate-200 dark:border-slate-700 shadow-2xs hover:bg-slate-50 dark:hover:bg-slate-700/60 transition-all"
               title="Back to Announcements">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white tracking-tight">Edit Announcement</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Refine content, schedule, and media for: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ Str::limit($announcement->title, 40) }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('announcements.show', $announcement) }}" target="_blank"
               class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition shadow-2xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Live View</span>
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 rounded-xl p-3.5 mb-5 shadow-2xs" role="alert">
            <div class="flex items-center gap-2 text-xs font-bold text-red-700 dark:text-red-400 mb-1">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please fix the following validation errors:</span>
            </div>
            <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400/90 space-y-0.5 ml-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Bulk Delete Form (Standalone) --}}
    <form id="bulk-delete-form" action="{{ route('admin.announcements.bulk-delete-images') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    {{-- Main Edit Form (2-Column Architecture) --}}
    <form id="announcement-edit-form" action="{{ route('admin.announcements.update', $announcement) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        @csrf
        @method('PUT')

        {{-- ────────────────────────────────────────────────────────────
             LEFT COLUMN (lg:col-span-8): Content & Sections
        ──────────────────────────────────────────────────────────── --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Cover Media Card (top of content, above Title) --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-2xs space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100/70 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <span>Cover Media</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Featured banner image or video displayed on announcement cards.</p>
                </div>

                {{-- Current Cover Preview --}}
                @if($announcement->image_path)
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Current Cover</label>
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-2xs bg-slate-100 dark:bg-slate-800 flex items-center justify-center" style="max-height: 320px;">
                            <img src="{{ asset('uploads/' . $announcement->image_path) }}" alt="Current Cover" class="w-full object-contain" style="max-height: 320px;">
                        </div>
                    </div>
                @endif

                {{-- Replace / Upload Dropzone --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
                        {{ $announcement->image_path ? 'Replace Cover' : 'Upload Cover' }}
                    </label>
                    <div class="relative group"
                         @dragenter.prevent="isMainDragging = true"
                         @dragover.prevent="isMainDragging = true"
                         @dragleave.prevent="if ($event.currentTarget.contains($event.relatedTarget)) return; isMainDragging = false"
                         @drop.prevent="isMainDragging = false; if ($event.dataTransfer && $event.dataTransfer.files.length) handleMainFiles($event.dataTransfer.files)">
                        <input type="file" name="images[]" id="main_image" class="hidden" multiple accept="image/*,video/mp4" @change="handleMainImageChange($event)">

                        <div @click="document.getElementById('main_image').click()"
                             :class="isMainDragging ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800/70'"
                             class="flex flex-col items-center justify-center w-full px-4 py-8 border-2 border-dashed rounded-xl cursor-pointer transition-all text-center gap-1.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100/80 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-1 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-slate-700 dark:text-slate-200 text-sm font-semibold pointer-events-none" x-text="mainImageName || '{{ $announcement->image_path ? 'Click or drag to replace cover image' : 'Click or drag to upload cover image' }}'"></span>
                            <span class="text-xs text-slate-400 pointer-events-none">Supports JPG, PNG, GIF, MP4 · Free crop</span>
                        </div>
                    </div>

                    {{-- New Cover Preview --}}
                    <div x-show="mainPreviews.length > 0" class="mt-3 relative group rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-2xs aspect-video">
                        <template x-for="(src, index) in mainPreviews" :key="index">
                            <img :src="src" class="w-full h-full object-cover">
                        </template>
                        <button type="button" @click.stop="removeMainImage()"
                                class="absolute top-2.5 right-2.5 p-1.5 rounded-lg bg-slate-900/80 text-white hover:bg-rose-600 transition shadow-xs cursor-pointer"
                                title="Remove Selected Image">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Primary Details Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-2xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Announcement Content</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Primary headline, brief subheading, and full descriptive text.</p>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
                        Heading / Title <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required 
                           class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-semibold transition-all">
                </div>

                {{-- Subheading --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
                        Subheading / Brief Summary
                    </label>
                    <input type="text" name="subheading" value="{{ old('subheading', $announcement->subheading) }}" 
                           placeholder="Short summary or catchphrase" 
                           class="w-full h-11 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-sm font-medium transition-all">
                </div>

                {{-- Main Content --}}
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Main Content <span class="text-rose-500">*</span>
                        </label>

                        {{-- Text Alignment Selector --}}
                        <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <input type="hidden" name="content_align" :value="contentAlign">
                            
                            <button type="button" @click="contentAlign = 'left'" 
                                    :class="contentAlign === 'left' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                    class="px-2 py-1 rounded-md text-xs flex items-center gap-1 transition-all cursor-pointer" title="Align Left">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
                                <span class="text-[11px] hidden sm:inline">Left</span>
                            </button>

                            <button type="button" @click="contentAlign = 'center'" 
                                    :class="contentAlign === 'center' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                    class="px-2 py-1 rounded-md text-xs flex items-center gap-1 transition-all cursor-pointer" title="Align Center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
                                <span class="text-[11px] hidden sm:inline">Center</span>
                            </button>

                            <button type="button" @click="contentAlign = 'right'" 
                                    :class="contentAlign === 'right' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                    class="px-2 py-1 rounded-md text-xs flex items-center gap-1 transition-all cursor-pointer" title="Align Right">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"/></svg>
                                <span class="text-[11px] hidden sm:inline">Right</span>
                            </button>

                            <button type="button" @click="contentAlign = 'justify'" 
                                    :class="contentAlign === 'justify' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                    class="px-2 py-1 rounded-md text-xs flex items-center gap-1 transition-all cursor-pointer" title="Justify">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                <span class="text-[11px] hidden sm:inline">Justify</span>
                            </button>
                        </div>
                    </div>

                    <textarea name="content" rows="7" required 
                              :class="{
                                  'text-left': contentAlign === 'left',
                                  'text-center': contentAlign === 'center',
                                  'text-right': contentAlign === 'right',
                                  'text-justify': contentAlign === 'justify'
                              }"
                              class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 p-4 text-sm leading-relaxed transition-all">{{ old('content', $announcement->content) }}</textarea>
                </div>
            </div>

            {{-- Existing Sections Card --}}
            @php
                $sections = $announcement->images->where('type', 'section');
            @endphp
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-2xs space-y-6">
                <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100/70 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </span>
                            <span>Existing Sections ({{ $sections->count() }})</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Modify content, re-align photos, or replace media for current blocks.</p>
                    </div>

                    {{-- Bulk Actions Toolbar --}}
                    <div x-show="selectedSections.length > 0" x-transition class="flex items-center gap-2 bg-rose-50 dark:bg-rose-950/50 px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-900/60">
                        <span class="text-xs text-rose-700 dark:text-rose-300 font-bold" x-text="selectedSections.length + ' selected'"></span>
                        <button type="button" @click="showBulkModal = true" class="text-[11px] bg-rose-600 text-white px-2.5 py-0.5 rounded-lg font-bold uppercase tracking-wider hover:bg-rose-700 transition cursor-pointer">
                            Delete
                        </button>
                    </div>
                </div>

                @if($sections->count() > 0)
                    <div class="space-y-5">
                        @foreach($sections as $section)
                            <div x-data="{ layout: '{{ $section->layout ?? 'left' }}', textAlign: '{{ $section->text_align ?? 'left' }}' }" 
                                 class="p-5 rounded-2xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/60 dark:bg-slate-800/40 relative transition-all space-y-4"
                                 :class="selectedSections.includes('{{ $section->id }}') ? 'ring-2 ring-emerald-500' : ''">
                                
                                {{-- Section Top Bar: Checkbox and Layout Badges --}}
                                <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-200/60 dark:border-slate-700/60">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" value="{{ $section->id }}" x-model="selectedSections" 
                                               class="w-4 h-4 text-emerald-600 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-emerald-500 cursor-pointer">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Select Section</span>
                                    </label>

                                    {{-- Layout Radios --}}
                                    <div class="flex items-center gap-1 bg-slate-200/60 dark:bg-slate-900/60 p-1 rounded-xl border border-slate-300/40 dark:border-slate-700/60">
                                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider px-1.5">Layout</span>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="existing_sections[{{ $section->id }}][layout]" value="left" x-model="layout" class="sr-only">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg transition-all flex items-center gap-1 select-none"
                                                  :class="layout === 'left' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-xs ring-1 ring-emerald-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                                Left
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="existing_sections[{{ $section->id }}][layout]" value="middle" x-model="layout" class="sr-only">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg transition-all flex items-center gap-1 select-none"
                                                  :class="layout === 'middle' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-xs ring-1 ring-emerald-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                                Center
                                            </span>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="existing_sections[{{ $section->id }}][layout]" value="right" x-model="layout" class="sr-only">
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg transition-all flex items-center gap-1 select-none"
                                                  :class="layout === 'right' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-xs ring-1 ring-emerald-500/20' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                                Right
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    {{-- Content Column --}}
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Section Text</label>

                                            {{-- Text Alignment Selector (Icon only) --}}
                                            <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-200/70 dark:bg-slate-800 border border-slate-300/60 dark:border-slate-700">
                                                <input type="hidden" name="existing_sections[{{ $section->id }}][text_align]" :value="textAlign">
                                                
                                                <button type="button" @click="textAlign = 'left'" 
                                                        :class="textAlign === 'left' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Align Left">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
                                                </button>

                                                <button type="button" @click="textAlign = 'center'" 
                                                        :class="textAlign === 'center' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Align Center">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
                                                </button>

                                                <button type="button" @click="textAlign = 'right'" 
                                                        :class="textAlign === 'right' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Align Right">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"/></svg>
                                                </button>

                                                <button type="button" @click="textAlign = 'justify'" 
                                                        :class="textAlign === 'justify' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Justify">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        <textarea name="existing_sections[{{ $section->id }}][content]" rows="4" 
                                                  :class="{
                                                      'text-left': textAlign === 'left',
                                                      'text-center': textAlign === 'center',
                                                      'text-right': textAlign === 'right',
                                                      'text-justify': textAlign === 'justify'
                                                  }"
                                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-2xs">{{ $section->content }}</textarea>
                                    </div>

                                    {{-- Media Column --}}
                                    <div class="space-y-3">
                                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Section Media</label>
                                        @if($section->image_path)
                                            <div class="aspect-video max-h-36 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 mb-2 shadow-2xs">
                                                @if($section->media_type == 'video_upload' || Str::endsWith($section->image_path, '.mp4'))
                                                    <video src="{{ asset('uploads/' . $section->image_path) }}" controls class="object-cover w-full h-full"></video>
                                                @else
                                                    <img src="{{ asset('uploads/' . $section->image_path) }}" class="object-cover w-full h-full">
                                                @endif
                                            </div>
                                        @endif

                                        <input type="file" name="existing_sections[{{ $section->id }}][image]" id="existing_image_{{ $section->id }}" class="hidden" accept="image/*,video/mp4" @change="handleExistingSectionFiles($event.target.files, {{ $section->id }})">
                                        <div @click="document.getElementById('existing_image_{{ $section->id }}').click()" 
                                             class="flex items-center justify-between w-full px-4 py-3 border-2 border-dashed rounded-xl border-slate-300 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 bg-white/70 dark:bg-slate-900/50 cursor-pointer transition-all">
                                            <span id="filename_{{ $section->id }}" class="text-xs text-slate-500 dark:text-slate-400 truncate">Replace Media (Free crop)...</span>
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700/80">
                        <p class="text-slate-400 dark:text-slate-500 text-xs font-medium">No additional sections currently attached to this announcement.</p>
                    </div>
                @endif
            </div>

            {{-- Add New Sections Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-2xs space-y-6">
                <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100/70 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </span>
                            <span>Add New Content Sections</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Append new narrative paragraphs and featured media to this post.</p>
                    </div>

                    <button type="button" @click="addSection()" 
                            class="px-3 py-1.5 rounded-xl bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 text-xs font-bold transition-all hover:bg-slate-800 dark:hover:bg-white shadow-2xs flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Section</span>
                    </button>
                </div>

                <div class="space-y-5">
                    <template x-for="(section, index) in new_sections" :key="index">
                        <div class="p-5 rounded-2xl border border-emerald-100 dark:border-emerald-800/40 bg-emerald-50/30 dark:bg-emerald-950/20 relative transition-all space-y-4">
                            <div class="flex items-center justify-between gap-2 pb-3 border-b border-emerald-100/80 dark:border-emerald-800/50">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-100/70 dark:bg-emerald-950/60 px-2.5 py-1 rounded-lg" 
                                      x-text="'New Section #' + (index + 1)"></span>
                                <button type="button" @click="removeSection(index)" 
                                        class="text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-1.5 rounded-lg transition-colors hover:bg-rose-50 dark:hover:bg-rose-950/40" title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Content</label>
                                            
                                            {{-- Text Alignment Selector (Icon only) --}}
                                            <div class="inline-flex items-center p-0.5 rounded-lg bg-slate-200/70 dark:bg-slate-800 border border-slate-300/60 dark:border-slate-700">
                                                <input type="hidden" :name="'new_sections[' + index + '][text_align]'" :value="section.textAlign || 'left'">
                                                
                                                <button type="button" @click="section.textAlign = 'left'" 
                                                        :class="(section.textAlign || 'left') === 'left' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Align Left">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h14"/></svg>
                                                </button>

                                                <button type="button" @click="section.textAlign = 'center'" 
                                                        :class="(section.textAlign || 'left') === 'center' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Align Center">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M5 18h14"/></svg>
                                                </button>

                                                <button type="button" @click="section.textAlign = 'right'" 
                                                        :class="(section.textAlign || 'left') === 'right' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Align Right">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"/></svg>
                                                </button>

                                                <button type="button" @click="section.textAlign = 'justify'" 
                                                        :class="(section.textAlign || 'left') === 'justify' ? 'bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                                                        class="w-7 h-7 rounded-md flex items-center justify-center transition-all cursor-pointer" title="Justify">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                                </button>
                                            </div>
                                        </div>

                                        <textarea :name="'new_sections[' + index + '][content]'" rows="4" 
                                                  placeholder="Enter section paragraph..." 
                                                  :class="{
                                                      'text-left': (section.textAlign || 'left') === 'left',
                                                      'text-center': section.textAlign === 'center',
                                                      'text-right': section.textAlign === 'right',
                                                      'text-justify': section.textAlign === 'justify'
                                                  }"
                                                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-3 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition shadow-2xs"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Media Orientation</label>
                                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                            <input type="hidden" :name="'new_sections[' + index + '][layout]'" :value="section.layout || 'middle'">
                                            
                                            {{-- Trigger Button --}}
                                            <button type="button"
                                                    @click="open = !open"
                                                    class="w-full h-11 px-3.5 flex items-center justify-between text-left rounded-xl border bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-2xs transition-all duration-150 cursor-pointer focus:outline-none"
                                                    :class="open ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                                                <div class="flex items-center gap-2.5 truncate">
                                                    <template x-if="(section.layout || 'middle') === 'left'">
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h7v14H4zM15 8h5M15 12h5M15 16h3"/></svg>
                                                            </span>
                                                            <span class="text-sm font-semibold text-slate-900 dark:text-white">Image Left (Text Right)</span>
                                                        </div>
                                                    </template>
                                                    <template x-if="(section.layout || 'middle') === 'right'">
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5h7v14h-7zM4 8h5M4 12h5M4 16h3"/></svg>
                                                            </span>
                                                            <span class="text-sm font-semibold text-slate-900 dark:text-white">Image Right (Text Left)</span>
                                                        </div>
                                                    </template>
                                                    <template x-if="(section.layout || 'middle') === 'middle'">
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v8H4zM6 17h12M8 20h8"/></svg>
                                                            </span>
                                                            <span class="text-sm font-semibold text-slate-900 dark:text-white">Full Width Center</span>
                                                        </div>
                                                    </template>
                                                </div>
                                                <span class="text-slate-400 transition-transform duration-200 shrink-0 ml-2" :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </span>
                                            </button>

                                            {{-- Options Dropdown Menu --}}
                                            <div x-show="open"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 style="display: none;"
                                                 class="absolute z-50 mt-1.5 w-full rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl p-1.5 focus:outline-none">
                                                
                                                <div class="space-y-1">
                                                    {{-- Option 1: Image Left --}}
                                                    <div @click="section.layout = 'left'; open = false"
                                                         class="flex items-center justify-between px-3 py-2.5 text-xs sm:text-sm rounded-xl transition-colors cursor-pointer select-none"
                                                         :class="(section.layout || 'middle') === 'left' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg" :class="(section.layout || 'middle') === 'left' ? 'bg-emerald-200/70 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500'">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h7v14H4zM15 8h5M15 12h5M15 16h3"/></svg>
                                                            </span>
                                                            <span>Image Left (Text Right)</span>
                                                        </div>
                                                        <svg x-show="(section.layout || 'middle') === 'left'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>

                                                    {{-- Option 2: Image Right --}}
                                                    <div @click="section.layout = 'right'; open = false"
                                                         class="flex items-center justify-between px-3 py-2.5 text-xs sm:text-sm rounded-xl transition-colors cursor-pointer select-none"
                                                         :class="(section.layout || 'middle') === 'right' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg" :class="(section.layout || 'middle') === 'right' ? 'bg-emerald-200/70 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500'">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5h7v14h-7zM4 8h5M4 12h5M4 16h3"/></svg>
                                                            </span>
                                                            <span>Image Right (Text Left)</span>
                                                        </div>
                                                        <svg x-show="(section.layout || 'middle') === 'right'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>

                                                    {{-- Option 3: Full Width Center --}}
                                                    <div @click="section.layout = 'middle'; open = false"
                                                         class="flex items-center justify-between px-3 py-2.5 text-xs sm:text-sm rounded-xl transition-colors cursor-pointer select-none"
                                                         :class="(section.layout || 'middle') === 'middle' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg" :class="(section.layout || 'middle') === 'middle' ? 'bg-emerald-200/70 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500'">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h16v8H4zM6 17h12M8 20h8"/></svg>
                                                            </span>
                                                            <span>Full Width Center</span>
                                                        </div>
                                                        <svg x-show="(section.layout || 'middle') === 'middle'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Media</label>
                                    <input type="file" :name="'new_sections[' + index + '][image]'" :id="'new_section_image_' + index" class="hidden" accept="image/*,video/mp4" @change="handleNewSectionFiles($event.target.files, index)">
                                    <div @click="document.getElementById('new_section_image_' + index).click()" 
                                         class="flex items-center justify-between w-full px-4 py-3 border-2 border-dashed rounded-xl border-emerald-300 dark:border-emerald-700/60 bg-white/80 dark:bg-slate-900/60 cursor-pointer hover:bg-emerald-50/50 transition">
                                        <span class="text-slate-500 dark:text-slate-400 truncate text-xs" x-text="section.fileName || 'Select media (Free crop)...'"></span>
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div x-show="section.preview" class="aspect-video max-h-40 rounded-xl overflow-hidden border border-emerald-200 dark:border-emerald-800 shadow-xs">
                                        <img :src="section.preview" class="w-full h-full object-cover">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="new_sections.length === 0" class="text-center py-8 bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700/80">
                    <p class="text-slate-400 dark:text-slate-500 text-xs">Click <span class="font-bold text-slate-600 dark:text-slate-300">"Add Section"</span> to append additional highlight sections.</p>
                </div>
            </div>

        </div>

        {{-- ────────────────────────────────────────────────────────────
             RIGHT COLUMN (lg:col-span-4): Settings, Schedule & Media
        ──────────────────────────────────────────────────────────── --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Publishing & Status Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-2xs space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Publishing Settings</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">Control lifecycle state and display mode.</p>
                </div>

                {{-- Status Selection --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Publication Status</label>
                    <x-select 
                        name="status" 
                        :options="[
                            'published' => 'Published (Active)',
                            'pending' => 'Pending Review',
                            'draft' => 'Draft (Hidden)'
                        ]" 
                        :value="old('status', $announcement->status)"
                        class="!h-11 !py-0 flex items-center font-semibold text-sm bg-slate-50/70 dark:bg-slate-800/60"
                    />
                </div>

                {{-- Display Mode with Option-Level Preview Tooltips --}}
                <div class="relative" 
                     x-data="{
                         open: false,
                         selected: '{{ old('display_mode', $announcement->display_mode ?? 'standard') }}',
                         activePreview: null,
                         options: [
                             { value: 'standard', label: 'Standard Article Post' },
                             { value: 'infographic', label: 'Infographic / Visual Notice' }
                         ],
                         get selectedLabel() {
                             const found = this.options.find(o => o.value === this.selected);
                             return found ? found.label : 'Standard Article Post';
                         },
                         selectOption(val) {
                             this.selected = val;
                             this.open = false;
                             this.activePreview = null;
                         }
                     }"
                     @click.outside="open = false; activePreview = null">
                    
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Display Mode</label>
                    
                    {{-- Hidden input for form submission --}}
                    <input type="hidden" name="display_mode" :value="selected">

                    {{-- Trigger Button --}}
                    <button type="button"
                            @click="open = !open"
                            :aria-expanded="open"
                            class="relative w-full h-11 px-4 flex items-center justify-between text-left rounded-xl border bg-slate-50/70 dark:bg-slate-800/60 text-slate-900 dark:text-white shadow-2xs transition-all duration-150 cursor-pointer focus:outline-none"
                            :class="open ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                        <span class="truncate block font-semibold text-sm" x-text="selectedLabel"></span>
                        <span class="text-slate-400 transition-transform duration-200 shrink-0 ml-2" :class="open ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>

                    {{-- Options Dropdown Menu --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         style="display: none;"
                         class="absolute z-50 mt-1.5 w-full rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl p-1.5 focus:outline-none">
                        
                        <div class="space-y-1">
                            {{-- Option 1: Standard Article Post --}}
                            <div @click="selectOption('standard')"
                                 class="flex items-center justify-between px-3 py-2.5 text-xs sm:text-sm rounded-xl transition-colors cursor-pointer select-none"
                                 :class="selected === 'standard' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                <div class="flex items-center gap-2">
                                    <span class="truncate">Standard Article Post</span>
                                    <svg x-show="selected === 'standard'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                                {{-- Question Mark Icon for Option 1 --}}
                                <div class="ml-2" @click.stop>
                                    <button type="button"
                                            @mouseenter="activePreview = 'standard'"
                                            @mouseleave="activePreview = null"
                                            @click.stop="activePreview = (activePreview === 'standard' ? null : 'standard')"
                                            class="w-5 h-5 rounded-full bg-slate-200/80 hover:bg-emerald-600 hover:text-white dark:bg-slate-700 dark:hover:bg-emerald-500 text-slate-600 dark:text-slate-300 flex items-center justify-center text-[10px] font-bold transition-all shadow-2xs cursor-pointer"
                                            title="View preview of Standard Article Post">
                                        ?
                                    </button>
                                </div>
                            </div>

                            {{-- Option 2: Infographic / Visual Notice --}}
                            <div @click="selectOption('infographic')"
                                 class="flex items-center justify-between px-3 py-2.5 text-xs sm:text-sm rounded-xl transition-colors cursor-pointer select-none"
                                 :class="selected === 'infographic' ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-800 dark:text-sky-300 font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60'">
                                <div class="flex items-center gap-2">
                                    <span class="truncate">Infographic / Visual Notice</span>
                                    <svg x-show="selected === 'infographic'" class="w-4 h-4 text-sky-600 dark:text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                                {{-- Question Mark Icon for Option 2 --}}
                                <div class="ml-2" @click.stop>
                                    <button type="button"
                                            @mouseenter="activePreview = 'infographic'"
                                            @mouseleave="activePreview = null"
                                            @click.stop="activePreview = (activePreview === 'infographic' ? null : 'infographic')"
                                            class="w-5 h-5 rounded-full bg-slate-200/80 hover:bg-sky-600 hover:text-white dark:bg-slate-700 dark:hover:bg-sky-500 text-slate-600 dark:text-slate-300 flex items-center justify-center text-[10px] font-bold transition-all shadow-2xs cursor-pointer"
                                            title="View preview of Infographic / Visual Notice">
                                        ?
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Preview Popover (Generous wide card with isolated z-[80]) --}}
                        <div x-show="activePreview !== null"
                             @mouseenter="/* keep preview open if hovered */"
                             @mouseleave="activePreview = null"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display: none;"
                             class="absolute z-[80] right-full top-0 mr-4 w-[380px] sm:w-[440px] max-w-[90vw] p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl space-y-3.5 pointer-events-auto">

                            {{-- Standard Article Preview --}}
                            <div x-show="activePreview === 'standard'" class="space-y-3">
                                <div class="flex items-center justify-between gap-3 pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 whitespace-nowrap">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span>Standard Article Post</span>
                                    </span>
                                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-200/60 dark:border-emerald-800/60 shrink-0">Editorial</span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs space-y-2 select-none">
                                    <div class="h-2.5 w-3/4 bg-slate-300 dark:bg-slate-600 rounded-full"></div>
                                    <div class="h-14 w-full bg-slate-200 dark:bg-slate-700 rounded-lg border border-dashed border-slate-300 dark:border-slate-600 flex items-center justify-center text-xs text-slate-500 dark:text-slate-400 gap-2 font-medium">
                                        <svg class="w-5 h-5 shrink-0" style="width: 20px; height: 20px; max-width: 20px; max-height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Header Cover Image Banner</span>
                                    </div>
                                    <div class="space-y-1.5 pt-1">
                                        <div class="h-2 w-full bg-slate-300/80 dark:bg-slate-600 rounded-full"></div>
                                        <div class="h-2 w-5/6 bg-slate-300/80 dark:bg-slate-600 rounded-full"></div>
                                        <div class="h-2 w-2/3 bg-slate-300/80 dark:bg-slate-600 rounded-full"></div>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Traditional editorial layout with story paragraphs, header photo, and alternating media sections. Best for advisories, press releases, and articles.
                                </p>
                            </div>

                            {{-- Infographic Notice Preview --}}
                            <div x-show="activePreview === 'infographic'" class="space-y-3">
                                <div class="flex items-center justify-between gap-3 pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 whitespace-nowrap">
                                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 shrink-0"></span>
                                        <span>Infographic / Visual Notice</span>
                                    </span>
                                    <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/60 px-2 py-0.5 rounded-md border border-sky-200/60 dark:border-sky-800/60 shrink-0">Poster-First</span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 shadow-2xs space-y-2 select-none">
                                    <div class="h-24 w-full bg-gradient-to-br from-sky-100 to-indigo-100 dark:from-sky-950/50 dark:to-indigo-950/40 rounded-lg border border-sky-200/60 dark:border-sky-800/60 flex flex-col items-center justify-center text-sky-600 dark:text-sky-400 p-2 text-center">
                                        <svg class="w-6 h-6 mb-1 shrink-0" style="width: 24px; height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="text-xs font-bold uppercase tracking-wider">Prominent Poster / Flyer</span>
                                    </div>
                                    <div class="flex items-center gap-2 pt-1">
                                        <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                                        <div class="h-2 w-full bg-slate-300/80 dark:bg-slate-600 rounded-full"></div>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                    Poster & flyer showcase layout. The uploaded graphic takes central prominence, keeping essential information concise and readable at a glance.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Display Layout Mode --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Display Layout Mode</label>
                    <x-select 
                        name="display_type" 
                        :options="[
                            'list' => 'Standard Article (Alternating)',
                            'carousel' => 'Carousel / Slider Mode'
                        ]" 
                        :value="old('display_type', $announcement->display_type)"
                        class="!h-11 !py-0 flex items-center font-semibold text-sm bg-slate-50/70 dark:bg-slate-800/60"
                    />
                </div>

                {{-- Action Buttons --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 space-y-2.5">
                    <button type="submit" 
                            class="w-full h-11 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-sm hover:shadow transition-all active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Update Announcement</span>
                    </button>
                    <a href="{{ route('admin.announcements.index') }}" 
                       class="w-full h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold text-sm transition flex items-center justify-center shadow-2xs">
                        Discard Changes
                    </a>
                </div>
            </div>

            {{-- Event Schedule Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-6 sm:p-7 shadow-2xs space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-800/80 pb-4">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100/70 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <span>Event Schedule</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">Optional. Attach start and end dates/times for health missions, vaccination drives, or public advisories.</p>
                </div>

                <div class="space-y-5">
                    {{-- ── START SCHEDULE GROUP ── --}}
                    <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-700/60 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Start Schedule</span>
                        </div>

                        {{-- Start Date Picker --}}
                        <div class="relative" x-data="customDatePicker('{{ old('event_date', $announcement->event_date ? $announcement->event_date->format('Y-m-d') : '') }}')">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Start Date</label>
                            <input type="hidden" name="event_date" :value="dateValue">

                            <div @click="showDatePicker = !showDatePicker" 
                                 class="h-11 px-4 flex items-center justify-between w-full rounded-xl border bg-white dark:bg-slate-800 shadow-2xs text-sm font-medium transition-all cursor-pointer select-none"
                                 :class="showDatePicker ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                                <span x-text="dateValue ? formattedDate : 'Select Start Date'" 
                                      class="truncate font-medium text-sm"
                                      :class="dateValue ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-400 dark:text-slate-500'"></span>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" x-show="dateValue" @click.stop="clearDate()" class="p-1 text-slate-400 hover:text-rose-500 transition rounded cursor-pointer" title="Clear Date">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>

                            {{-- Calendar Dropdown --}}
                            <div x-show="showDatePicker" 
                                 @click.away="showDatePicker = false" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 style="display: none;"
                                 class="absolute z-50 mt-2 left-0 right-0 p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl">
                                
                                <div class="flex items-center justify-between gap-1 mb-3">
                                    <button type="button" @click="prevMonth()" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                    <button type="button" @click="nextMonth()" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[10px] font-bold text-slate-400 dark:text-slate-500">
                                    <template x-for="day in days" :key="day"><span x-text="day"></span></template>
                                </div>

                                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                    <template x-for="blank in startDayOfWeek" :key="'blank-es-' + blank"><span class="p-1"></span></template>
                                    <template x-for="day in daysInMonth" :key="'day-es-' + day">
                                        <button type="button" 
                                                @click="selectDay(day)" 
                                                :class="{
                                                    'bg-emerald-600 text-white font-bold shadow-xs': isSelected(day),
                                                    'ring-1 ring-emerald-500 text-emerald-600 font-bold': isToday(day) && !isSelected(day),
                                                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700': !isSelected(day) && !isToday(day)
                                                }"
                                                class="p-1.5 rounded-lg text-xs font-medium transition cursor-pointer" 
                                                x-text="day"></button>
                                    </template>
                                </div>

                                <div class="flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                                    <button type="button" @click="clearDate()" class="text-slate-500 hover:text-rose-600 font-semibold transition cursor-pointer">Clear</button>
                                    <button type="button" @click="selectToday()" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline transition cursor-pointer">Today</button>
                                </div>
                            </div>
                        </div>

                        {{-- Start Time Picker --}}
                        <div class="relative" x-data="customTimePicker('{{ old('start_time', $announcement->start_time ? \Carbon\Carbon::parse($announcement->start_time)->format('H:i') : '') }}')">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Start Time</label>
                            <input type="hidden" name="start_time" :value="timeValue">

                            <div @click="showTimePicker = !showTimePicker" 
                                 class="h-11 px-4 flex items-center justify-between w-full rounded-xl border bg-white dark:bg-slate-800 shadow-2xs text-sm font-medium transition-all cursor-pointer select-none"
                                 :class="showTimePicker ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                                <span x-text="timeValue ? formattedDisplay : 'Select Start Time'" 
                                      class="truncate font-medium text-sm"
                                      :class="timeValue ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-400 dark:text-slate-500'"></span>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" x-show="timeValue" @click.stop="clearTime()" class="p-1 text-slate-400 hover:text-rose-500 transition rounded cursor-pointer" title="Clear Time">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-5 h-5 text-slate-400 shrink-0" :class="showTimePicker ? 'text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Time Picker Popover --}}
                            <div x-show="showTimePicker" 
                                 @click.away="showTimePicker = false" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 style="display: none;"
                                 class="absolute z-50 mt-2 left-0 right-0 p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl space-y-3.5">
                                
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/80 dark:border-slate-700/80">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xl font-black text-slate-900 dark:text-white px-2.5 py-1 bg-white dark:bg-slate-800 rounded-lg shadow-2xs border border-slate-200 dark:border-slate-700" x-text="hour"></span>
                                        <span class="text-xl font-bold text-slate-400">:</span>
                                        <span class="text-xl font-black text-slate-900 dark:text-white px-2.5 py-1 bg-white dark:bg-slate-800 rounded-lg shadow-2xs border border-slate-200 dark:border-slate-700" x-text="minute"></span>
                                    </div>
                                    <div class="flex items-center p-1 bg-slate-200/80 dark:bg-slate-800 rounded-lg text-xs font-bold">
                                        <button type="button" @click="setPeriod('AM')" 
                                                :class="period === 'AM' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                                                class="px-2.5 py-1 rounded-md transition cursor-pointer">AM</button>
                                        <button type="button" @click="setPeriod('PM')" 
                                                :class="period === 'PM' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                                                class="px-2.5 py-1 rounded-md transition cursor-pointer">PM</button>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Quick Presets</span>
                                    <div class="grid grid-cols-3 gap-1.5 text-xs font-medium">
                                        <button type="button" @click="setPreset(8, '00', 'AM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">8:00 AM</button>
                                        <button type="button" @click="setPreset(9, '00', 'AM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">9:00 AM</button>
                                        <button type="button" @click="setPreset(10, '00', 'AM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">10:00 AM</button>
                                        <button type="button" @click="setPreset(1, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">1:00 PM</button>
                                        <button type="button" @click="setPreset(2, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">2:00 PM</button>
                                        <button type="button" @click="setPreset(3, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">3:00 PM</button>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Hours</span>
                                    <div class="grid grid-cols-6 gap-1 text-center">
                                        <template x-for="h in hoursList" :key="h">
                                            <button type="button" @click="setHour(h)" 
                                                    :class="hour === h ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                                    class="py-1 rounded-lg text-xs font-semibold transition cursor-pointer" x-text="h"></button>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Minutes</span>
                                    <div class="grid grid-cols-6 gap-1 text-center">
                                        <template x-for="m in commonMinutes" :key="m">
                                            <button type="button" @click="setMinute(m)" 
                                                    :class="minute === m ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                                    class="py-1 rounded-lg text-xs font-semibold transition cursor-pointer" x-text="m"></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                                    <button type="button" @click="clearTime()" class="text-slate-500 hover:text-rose-600 font-semibold transition cursor-pointer">Clear</button>
                                    <button type="button" @click="updateTime(); showTimePicker = false" class="px-3.5 py-1 bg-slate-900 text-white dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-700 rounded-lg font-bold transition shadow-2xs cursor-pointer">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Connecting Arrow / Divider --}}
                    <div class="relative flex items-center justify-center my-1">
                        <div class="w-full border-t border-dashed border-slate-200 dark:border-slate-700"></div>
                        <div class="absolute px-2.5 py-0.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                            <span>TO</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </div>
                    </div>

                    {{-- ── END SCHEDULE GROUP ── --}}
                    <div class="p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200/70 dark:border-slate-700/60 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">End Schedule (Optional)</span>
                        </div>

                        {{-- End Date Picker --}}
                        <div class="relative" x-data="customDatePicker('{{ old('end_date', $announcement->end_date ? $announcement->end_date->format('Y-m-d') : '') }}')">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">End Date</label>
                            <input type="hidden" name="end_date" :value="dateValue">

                            <div @click="showDatePicker = !showDatePicker" 
                                 class="h-11 px-4 flex items-center justify-between w-full rounded-xl border bg-white dark:bg-slate-800 shadow-2xs text-sm font-medium transition-all cursor-pointer select-none"
                                 :class="showDatePicker ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                                <span x-text="dateValue ? formattedDate : 'Select End Date'" 
                                      class="truncate font-medium text-sm"
                                      :class="dateValue ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-400 dark:text-slate-500'"></span>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" x-show="dateValue" @click.stop="clearDate()" class="p-1 text-slate-400 hover:text-rose-500 transition rounded cursor-pointer" title="Clear Date">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>

                            {{-- Calendar Dropdown --}}
                            <div x-show="showDatePicker" 
                                 @click.away="showDatePicker = false" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 style="display: none;"
                                 class="absolute z-50 mt-2 left-0 right-0 p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl">
                                
                                <div class="flex items-center justify-between gap-1 mb-3">
                                    <button type="button" @click="prevMonth()" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="monthNames[currentMonth] + ' ' + currentYear"></span>
                                    <button type="button" @click="nextMonth()" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-7 gap-1 text-center mb-1 text-[10px] font-bold text-slate-400 dark:text-slate-500">
                                    <template x-for="day in days" :key="day"><span x-text="day"></span></template>
                                </div>

                                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                                    <template x-for="blank in startDayOfWeek" :key="'blank-ee-' + blank"><span class="p-1"></span></template>
                                    <template x-for="day in daysInMonth" :key="'day-ee-' + day">
                                        <button type="button" 
                                                @click="selectDay(day)" 
                                                :class="{
                                                    'bg-teal-600 text-white font-bold shadow-xs': isSelected(day),
                                                    'ring-1 ring-teal-500 text-teal-600 font-bold': isToday(day) && !isSelected(day),
                                                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700': !isSelected(day) && !isToday(day)
                                                }"
                                                class="p-1.5 rounded-lg text-xs font-medium transition cursor-pointer" 
                                                x-text="day"></button>
                                    </template>
                                </div>

                                <div class="flex items-center justify-between pt-2.5 mt-2.5 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                                    <button type="button" @click="clearDate()" class="text-slate-500 hover:text-rose-600 font-semibold transition cursor-pointer">Clear</button>
                                    <button type="button" @click="selectToday()" class="text-teal-600 dark:text-teal-400 font-bold hover:underline transition cursor-pointer">Today</button>
                                </div>
                            </div>
                        </div>

                        {{-- End Time Picker --}}
                        <div class="relative" x-data="customTimePicker('{{ old('end_time', $announcement->end_time ? \Carbon\Carbon::parse($announcement->end_time)->format('H:i') : '') }}')">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">End Time</label>
                            <input type="hidden" name="end_time" :value="timeValue">

                            <div @click="showTimePicker = !showTimePicker" 
                                 class="h-11 px-4 flex items-center justify-between w-full rounded-xl border bg-white dark:bg-slate-800 shadow-2xs text-sm font-medium transition-all cursor-pointer select-none"
                                 :class="showTimePicker ? 'border-teal-500 ring-2 ring-teal-500/20' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600'">
                                <span x-text="timeValue ? formattedDisplay : 'Select End Time'" 
                                      class="truncate font-medium text-sm"
                                      :class="timeValue ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-400 dark:text-slate-500'"></span>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" x-show="timeValue" @click.stop="clearTime()" class="p-1 text-slate-400 hover:text-rose-500 transition rounded cursor-pointer" title="Clear Time">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    <svg class="w-5 h-5 text-slate-400 shrink-0" :class="showTimePicker ? 'text-teal-600 dark:text-teal-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Time Picker Popover --}}
                            <div x-show="showTimePicker" 
                                 @click.away="showTimePicker = false" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 style="display: none;"
                                 class="absolute z-50 mt-2 left-0 right-0 p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl space-y-3.5">
                                
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-900/60 rounded-xl border border-slate-200/80 dark:border-slate-700/80">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xl font-black text-slate-900 dark:text-white px-2.5 py-1 bg-white dark:bg-slate-800 rounded-lg shadow-2xs border border-slate-200 dark:border-slate-700" x-text="hour"></span>
                                        <span class="text-xl font-bold text-slate-400">:</span>
                                        <span class="text-xl font-black text-slate-900 dark:text-white px-2.5 py-1 bg-white dark:bg-slate-800 rounded-lg shadow-2xs border border-slate-200 dark:border-slate-700" x-text="minute"></span>
                                    </div>
                                    <div class="flex items-center p-1 bg-slate-200/80 dark:bg-slate-800 rounded-lg text-xs font-bold">
                                        <button type="button" @click="setPeriod('AM')" 
                                                :class="period === 'AM' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                                                class="px-2.5 py-1 rounded-md transition cursor-pointer">AM</button>
                                        <button type="button" @click="setPeriod('PM')" 
                                                :class="period === 'PM' ? 'bg-teal-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                                                class="px-2.5 py-1 rounded-md transition cursor-pointer">PM</button>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Quick Presets</span>
                                    <div class="grid grid-cols-3 gap-1.5 text-xs font-medium">
                                        <button type="button" @click="setPreset(12, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">12:00 PM</button>
                                        <button type="button" @click="setPreset(3, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">3:00 PM</button>
                                        <button type="button" @click="setPreset(4, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">4:00 PM</button>
                                        <button type="button" @click="setPreset(5, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">5:00 PM</button>
                                        <button type="button" @click="setPreset(6, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">6:00 PM</button>
                                        <button type="button" @click="setPreset(8, '00', 'PM')" class="py-1 px-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-teal-500 hover:bg-teal-50 dark:hover:bg-teal-950/40 text-slate-700 dark:text-slate-300 transition text-[11px] cursor-pointer">8:00 PM</button>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Hours</span>
                                    <div class="grid grid-cols-6 gap-1 text-center">
                                        <template x-for="h in hoursList" :key="h">
                                            <button type="button" @click="setHour(h)" 
                                                    :class="hour === h ? 'bg-teal-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                                    class="py-1 rounded-lg text-xs font-semibold transition cursor-pointer" x-text="h"></button>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1.5">Minutes</span>
                                    <div class="grid grid-cols-6 gap-1 text-center">
                                        <template x-for="m in commonMinutes" :key="m">
                                            <button type="button" @click="setMinute(m)" 
                                                    :class="minute === m ? 'bg-teal-600 text-white font-bold shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
                                                    class="py-1 rounded-lg text-xs font-semibold transition cursor-pointer" x-text="m"></button>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 dark:border-slate-700/60 text-xs">
                                    <button type="button" @click="clearTime()" class="text-slate-500 hover:text-rose-600 font-semibold transition cursor-pointer">Clear</button>
                                    <button type="button" @click="updateTime(); showTimePicker = false" class="px-3.5 py-1 bg-slate-900 text-white dark:bg-teal-600 hover:bg-slate-800 dark:hover:bg-teal-700 rounded-lg font-bold transition shadow-2xs cursor-pointer">Done</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </form>


    {{-- Dedicated Bulk Delete Modal for Sections --}}
    <template x-teleport="body">
        <div x-show="showBulkModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showBulkModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
                     @click="showBulkModal = false"
                     aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showBulkModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-200 dark:border-slate-800">
                    <div class="p-6 sm:p-8">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl sm:mx-0 sm:h-10 sm:w-10 bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white" id="modal-title">
                                    Delete Selected Sections?
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        Are you sure you want to delete <span x-text="selectedSections.length" class="font-bold text-rose-600"></span> section(s)? This action cannot be reversed.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-950/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="button"
                                @click="
                                    const form = document.getElementById('bulk-delete-form');
                                    form.querySelectorAll('input[name=\'image_ids[]\']').forEach(e => e.remove());
                                    selectedSections.forEach(id => {
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'image_ids[]';
                                        input.value = id;
                                        form.appendChild(input);
                                    });
                                    form.submit();
                                "
                                class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-lg px-6 py-2 bg-rose-600 hover:bg-rose-700 text-sm font-bold text-white transition-all cursor-pointer">
                            Delete Sections
                        </button>
                        <button type="button"
                                @click="showBulkModal = false"
                                class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 dark:border-slate-700 shadow-sm px-6 py-2 bg-white dark:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all cursor-pointer">
                            Cancel
                        </button>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>

@include('partials.image-cropper')
@endsection
