@php
    $layout = match(auth()->user()->role) {
        'super_admin', 'admin' => 'layouts.admin',
        'regular_doctor', 'pedia_doctor' => 'layouts.doctor',
        'clinical_nurse', 'vitals_nurse' => 'layouts.nurse',
        'information_desk' => 'layouts.frontdesk',
        'laboratory', 'radiology' => 'layouts.lab',
        'pharmacy' => 'layouts.pharmacy',
        default => 'layouts.app'
    };

    $roleTitle = match(auth()->user()->role) {
        'super_admin' => 'Super Administrator',
        'admin' => 'Administrator',
        'regular_doctor' => 'General Physician',
        'pedia_doctor' => 'Pediatrician',
        'clinical_nurse' => 'Clinical Nurse',
        'vitals_nurse' => 'Triage & Vitals Nurse',
        'information_desk' => 'Front Desk Officer',
        'laboratory' => 'Medical Technologist',
        'radiology' => 'Radiologist',
        'pharmacy' => 'Pharmacist',
        default => 'Staff Member'
    };

    $isAdminOrSuperAdmin = in_array(auth()->user()->role, ['admin', 'super_admin']);

    // Computed presence (schedule + override), not the stored status column.
    $initStatus = auth()->user()->effective_status;

    $navSections = [
        [
            'id' => 'profile',
            'label' => 'Profile',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
        ],
        [
            'id' => 'security',
            'label' => 'Security',
            'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'
        ],
        [
            'id' => 'schedule',
            'label' => 'Duty & Schedule',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
        ]
    ];
@endphp

@extends($layout)

@section('header', 'Profile Settings')

@section('content')
<div class="w-full max-w-[1120px] mx-auto py-8 px-4 sm:px-6"
     x-data="{
         activeSection: 'profile',
         isDraggingFile: false,
         dragCounter: 0,
         init() {
             const urlParams = new URLSearchParams(window.location.search);
             const sectionParam = urlParams.get('section');
             const hash = window.location.hash.replace('#', '');
             const valid = ['profile', 'security', 'schedule'];
             if (valid.includes(sectionParam)) {
                 this.activeSection = sectionParam;
             } else if (valid.includes(hash)) {
                 this.activeSection = hash;
             }

             window.addEventListener('popstate', () => {
                 const params = new URLSearchParams(window.location.search);
                 const s = params.get('section') || window.location.hash.replace('#', '');
                 if (valid.includes(s)) {
                     this.activeSection = s;
                 } else {
                     this.activeSection = 'profile';
                 }
             });
         },
         switchSection(id) {
             if (this.activeSection === id) return;
             this.activeSection = id;
             const url = new URL(window.location.href);
             url.searchParams.set('section', id);
             window.history.pushState({ section: id }, '', url.toString());
         },
         handleWindowDragEnter(e) {
             if (e.dataTransfer && e.dataTransfer.types && Array.from(e.dataTransfer.types).includes('Files')) {
                 this.dragCounter++;
                 this.isDraggingFile = true;
             }
         },
         handleWindowDragLeave(e) {
             this.dragCounter--;
             if (this.dragCounter <= 0) {
                 this.isDraggingFile = false;
                 this.dragCounter = 0;
             }
         },
         handleWindowDrop(e) {
             this.isDraggingFile = false;
             this.dragCounter = 0;
             const files = e.dataTransfer ? e.dataTransfer.files : [];
             if (files && files.length > 0) {
                 if (this.activeSection !== 'profile') {
                     this.switchSection('profile');
                 }
                 window.dispatchEvent(new CustomEvent('profile-file-dropped', { detail: { file: files[0] } }));
             }
         }
     }"
     @dragenter.window="handleWindowDragEnter($event)"
     @dragleave.window="handleWindowDragLeave($event)"
     @dragover.window.prevent
     @drop.window.prevent="handleWindowDrop($event)"
     @keydown.escape.window="isDraggingFile = false; dragCounter = 0">

    <!-- FULL PAGE DRAG & DROP OVERLAY -->
    <template x-teleport="body">
        <div x-show="isDraggingFile" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-8 select-none"
             style="display: none;"
             @dragenter.prevent
             @dragover.prevent
             @drop.prevent="handleWindowDrop($event)">
            
            <!-- Dashed Inner Frame covering the whole page with Emerald Accent -->
            <div class="w-full h-full rounded-[24px] sm:rounded-[32px] border-3 border-dashed border-emerald-400 dark:border-emerald-500 bg-emerald-950/20 dark:bg-emerald-950/40 flex flex-col items-center justify-center p-6 text-center pointer-events-none shadow-2xl">
                
                <!-- Central Floating Dropzone Card -->
                <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-emerald-200/90 dark:border-emerald-700/80 p-8 sm:p-10 flex flex-col items-center text-center space-y-4">
                    
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl sm:rounded-3xl bg-emerald-100 dark:bg-emerald-900/50 text-[#08795a] dark:text-emerald-400 flex items-center justify-center shadow-inner">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>

                    <div class="space-y-1.5">
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            Drop image to update profile picture
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">
                            Release your image anywhere on the screen to launch the square cropper
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-[11px] sm:text-xs font-bold text-emerald-700 dark:text-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Square Crop (1:1) • JPEG, PNG, WEBP</span>
                    </div>
                </div>
                
            </div>
        </div>
    </template>

    <div class="grid grid-cols-1 min-[900px]:grid-cols-[300px_minmax(0,1fr)] gap-8 items-start">
        
        <!-- LEFT COLUMN: Settings Navigation -->
        <aside class="space-y-1">
            <div class="mb-5">
                <h2 class="text-[22px] font-semibold text-slate-900 dark:text-white tracking-tight">Account settings</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">Manage your profile, security and duty schedule.</p>
            </div>

            <nav class="space-y-1" aria-label="Account Settings Navigation">
                @foreach($navSections as $sec)
                    <button type="button"
                            @click="switchSection('{{ $sec['id'] }}')"
                            :aria-current="activeSection === '{{ $sec['id'] }}' ? 'page' : null"
                            class="w-full min-h-[48px] h-12 flex items-center gap-3 px-3.5 rounded-xl text-[15px] transition-all text-left cursor-pointer"
                            :class="activeSection === '{{ $sec['id'] }}' 
                                ? 'bg-[#d9f3e8] text-[#065f46] font-semibold dark:bg-emerald-950/70 dark:text-emerald-300 shadow-2xs' 
                                : 'text-slate-600 dark:text-slate-400 font-medium hover:bg-slate-200/60 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sec['icon'] }}" />
                        </svg>
                        <span>{{ $sec['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </aside>

        <!-- RIGHT COLUMN: Content Panel -->
        <main class="bg-white dark:bg-slate-900 rounded-[20px] border border-slate-200 dark:border-slate-800 p-5 sm:p-8 shadow-xs transition-all min-w-0">
            
            <!-- SECTION 1: Profile -->
            <div x-show="activeSection === 'profile'" x-cloak class="space-y-6"
                 x-data="{ 
                     photoName: null, 
                     photoPreview: null, 
                     uploadError: null,
                     showConfirmModal: false, 
                     submitting: false, 
                     formName: '{{ addslashes($user->name) }}',
                     formEmail: '{{ addslashes($user->email) }}',
                     formRole: '{{ old('role', $user->role) }}',
                     openRoleDropdown: false,
                     roles: [
                         { value: 'super_admin', label: 'Super Administrator', desc: 'Full administrative access and governance' },
                         { value: 'admin', label: 'Administrator', desc: 'Management access and portal operations' }
                     ],
                     get roleLabel() {
                         const found = this.roles.find(r => r.value === this.formRole);
                         return found ? found.label : (this.formRole === 'super_admin' ? 'Super Administrator' : 'Administrator');
                     },
                     formSchedule: '{{ addslashes(old('schedule', $user->schedule ?? ($user->formatted_schedule ?: 'No Schedule Set'))) }}',
                     formSchedulePayload: '',
                     status: '{{ $initStatus }}',
                     hasAvatar() {
                         const input = document.getElementById('profile_avatar_input');
                         return !!(this.photoPreview || (input && input.files && input.files.length > 0));
                     },
                     setStatus(val) {
                         if (this.status === val) return;
                         this.status = val;
                         fetch('{{ route('profile.status.update') }}', {
                             method: 'POST',
                             headers: {
                                 'Content-Type': 'application/json',
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                 'Accept': 'application/json'
                             },
                             body: JSON.stringify({ status: val })
                         }).catch(err => console.error(err));
                     },
                     async handleAvatarFile(file) {
                         const input = document.getElementById('profile_avatar_input');
                         if (!file) return;

                         if (window.SecureImageValidator) {
                             const res = await window.SecureImageValidator.validateFile(file);
                             if (!res.valid) {
                                 this.uploadError = res.message;
                                 if (input) input.value = '';
                                 if (window.toast && typeof window.toast.error === 'function') {
                                     window.toast.error(res.message, 'Invalid Image');
                                 }
                                 return;
                             }
                         }
                         this.uploadError = null;

                         $store.imageCropper.open(file, {
                             aspectRatio: 1,
                             circular: true,
                             subtitle: 'Square crop (1:1) — Profile Avatar',
                             onApply: (blob, previewUrl) => {
                                 this.photoPreview = previewUrl;
                                 this.photoName = file.name;
                                 this.uploadError = null;
                                 const defaultName = (blob && blob.type === 'image/webp') ? 'avatar.webp' : 'avatar.jpg';
                                 setCroppedFile(input, blob, file.name || defaultName);
                                 window.dispatchEvent(new CustomEvent('profile-avatar-changed', {
                                     detail: { previewUrl: previewUrl, fileName: file.name }
                                 }));
                             }
                         });
                     }
                 }"
                 @profile-file-dropped.window="handleAvatarFile($event.detail.file)"
                 @profile-avatar-changed.window="photoPreview = $event.detail.previewUrl; photoName = $event.detail.fileName"
                 @admin-schedule-updated.window="formSchedule = $event.detail.schedule; if ($event.detail.payload !== undefined) formSchedulePayload = $event.detail.payload">
                
                <!-- Profile Hero Banner (Mint #ecf8f2 background) -->
                <div class="rounded-2xl bg-[#ecf8f2] dark:bg-emerald-950/40 p-6 flex flex-col sm:flex-row items-center sm:items-center gap-6 text-center sm:text-left transition-all relative overflow-hidden">
                    
                    <!-- 88px Circular Avatar with Bottom-Right Camera Button -->
                    <div class="relative group shrink-0 cursor-pointer"
                         @click="document.getElementById('profile_avatar_input').click()"
                         title="Click to change photo or drag & drop image anywhere on screen">
                        
                        <div class="w-[88px] h-[88px] min-w-[88px] min-h-[88px] rounded-full overflow-hidden bg-[#d1f2e2] dark:bg-emerald-900/60 relative ring-2 ring-emerald-500/20 shadow-md transition-all duration-200 group-hover:scale-105">
                            
                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="h-full w-full object-cover" alt="New Avatar Preview">
                            </template>
                            <template x-if="!photoPreview">
                                @if(auth()->user()->avatar_url)
                                    <img class="h-full w-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-[#065f46] dark:text-emerald-300 font-bold text-2xl select-none">
                                        {{ auth()->user()->initials }}
                                    </div>
                                @endif
                            </template>

                            <!-- Hover Overlay on Avatar -->
                            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[10px] font-bold pointer-events-none">
                                <svg class="w-5 h-5 mb-0.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Change</span>
                            </div>
                        </div>

                        <!-- Small Round Camera Button on Bottom-Right Corner -->
                        <button type="button"
                                @click.stop="document.getElementById('profile_avatar_input').click()"
                                class="absolute bottom-0 right-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#065f46] text-white ring-2 ring-white dark:ring-slate-900 shadow-md flex items-center justify-center hover:bg-[#044e39] transition-transform active:scale-95 cursor-pointer"
                                title="Upload new photo">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Identity & Status Details -->
                    <div class="flex-1 min-w-0">
                        <!-- Full Name & Live Status Badge -->
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                            <h2 class="text-[26px] font-bold text-slate-900 dark:text-white leading-tight">
                                {{ auth()->user()->name }}
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white dark:bg-slate-800 border shadow-2xs transition-all shrink-0"
                                  :class="{
                                      'border-emerald-200/80 text-emerald-700 dark:border-emerald-800 dark:text-emerald-400': status === 'Online',
                                      'border-slate-200/80 text-slate-600 dark:border-slate-700 dark:text-slate-400': status === 'Offline',
                                      'border-amber-200/80 text-amber-700 dark:border-amber-800 dark:text-amber-400': status === 'Occupied'
                                  }">
                                <span class="w-1.5 h-1.5 rounded-full transition-colors"
                                      :class="{
                                          'bg-emerald-500 animate-pulse': status === 'Online',
                                          'bg-slate-400': status === 'Offline',
                                          'bg-amber-500': status === 'Occupied'
                                      }"></span>
                                <span x-text="status">{{ $initStatus }}</span>
                            </span>
                        </div>

                        <!-- Email & Dynamic Upload Status -->
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                            <span>{{ auth()->user()->email }}</span>
                            <template x-if="photoPreview && !uploadError">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-100/70 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    New photo selected
                                </span>
                            </template>
                            <template x-if="uploadError">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-md" role="alert">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span x-text="uploadError"></span>
                                </span>
                            </template>
                        </div>

                        <!-- Duty Status Segmented Control -->
                        <div class="mt-3 inline-flex items-center p-1 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-2xs">
                            <button type="button" 
                                    @click="setStatus('Online')"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer"
                                    :class="status === 'Online' 
                                        ? 'bg-[#d9f3e8] text-[#065f46] font-bold shadow-2xs dark:bg-emerald-950/60 dark:text-emerald-300' 
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                Online
                            </button>
                            <button type="button" 
                                    @click="setStatus('Offline')"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer"
                                    :class="status === 'Offline' 
                                        ? 'bg-slate-200/80 text-slate-800 font-bold shadow-2xs dark:bg-slate-700 dark:text-slate-200' 
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                Offline
                            </button>
                            <button type="button" 
                                    @click="setStatus('Occupied')"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer"
                                    :class="status === 'Occupied' 
                                        ? 'bg-amber-100 text-amber-800 font-bold shadow-2xs dark:bg-amber-950/60 dark:text-amber-300' 
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                Occupied
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Hidden Input for Avatar Upload -->
                <input type="file" id="profile_avatar_input" name="avatar" form="profile-info-form" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden"
                    @change="if ($event.target.files.length) handleAvatarFile($event.target.files[0])">

                <!-- Profile Form -->
                <form id="profile-info-form" x-ref="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6 pt-2">
                    @csrf
                    @method('patch')

                    <!-- Hidden bindings for schedule data -->
                    <input type="hidden" name="schedule" :value="formSchedule">
                    <input type="hidden" name="schedule_payload" :value="formSchedulePayload">

                    <!-- Section Header -->
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-[#d9f3e8] dark:bg-emerald-950/60 text-[#065f46] dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Profile & Account Information</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Update your display details and review locked administrative records.</p>
                        </div>
                    </div>

                    <!-- 2-Column Form Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Full Name (Full Width) -->
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Full Name @if($isAdminOrSuperAdmin) <span class="text-rose-500">*</span> @endif
                            </label>
                            @if($isAdminOrSuperAdmin)
                                <input type="text" name="name" id="name" x-model="formName" value="{{ old('name', $user->name) }}" required
                                       class="h-12 w-full px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-normal focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-2xs"
                                       style="text-transform: none !important;">
                            @else
                                <input type="text" value="{{ $user->name }}" readonly disabled
                                       class="h-12 w-full px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-sm font-normal cursor-not-allowed select-none">
                            @endif
                            @error('name') <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Assigned Role -->
                        <div class="space-y-1.5" @if(auth()->user()->can('promote-admin')) @click.outside="openRoleDropdown = false" @endif>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Assigned Role
                            </label>
                            @if(auth()->user()->can('promote-admin'))
                                <div class="relative">
                                    <input type="hidden" name="role" :value="formRole">
                                    <button type="button" 
                                            @click="openRoleDropdown = !openRoleDropdown"
                                            @keydown.escape="openRoleDropdown = false"
                                            class="h-12 w-full flex items-center justify-between px-4 rounded-xl border bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-semibold transition-all cursor-pointer shadow-2xs select-none"
                                            :class="openRoleDropdown 
                                                ? 'border-emerald-500 ring-2 ring-emerald-500/20' 
                                                : 'border-slate-200 dark:border-slate-700 hover:border-emerald-500/70'">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <div class="w-2 h-2 rounded-full shrink-0"
                                                 :class="formRole === 'super_admin' ? 'bg-emerald-500' : 'bg-teal-500'"></div>
                                            <span class="truncate" x-text="roleLabel"></span>
                                        </div>
                                        <svg class="w-4 h-4 text-slate-400 dark:text-slate-400 transition-transform duration-200 shrink-0 ml-2"
                                             :class="openRoleDropdown ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Custom Floating Role Menu -->
                                    <div x-show="openRoleDropdown" 
                                         x-cloak
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                                         style="display: none;"
                                         class="absolute left-0 right-0 z-50 mt-1.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-xl shadow-slate-900/10 dark:shadow-slate-950/50 p-1.5 space-y-1 backdrop-blur-md">
                                        <template x-for="r in roles" :key="r.value">
                                            <button type="button"
                                                    @click="formRole = r.value; openRoleDropdown = false"
                                                    class="w-full text-left px-3.5 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-between group"
                                                    :class="formRole === r.value 
                                                        ? 'bg-[#d9f3e8] dark:bg-emerald-950/70 text-[#065f46] dark:text-emerald-300 font-bold shadow-2xs' 
                                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 font-medium'">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                                                         :class="formRole === r.value 
                                                             ? 'bg-emerald-600 text-white shadow-2xs' 
                                                             : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 group-hover:bg-emerald-100 group-hover:text-emerald-700 dark:group-hover:bg-emerald-950/60 dark:group-hover:text-emerald-300'">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm leading-tight" x-text="r.label"></div>
                                                        <div class="text-[11px] font-normal text-slate-400 dark:text-slate-400 mt-0.5 leading-none" x-text="r.desc"></div>
                                                    </div>
                                                </div>
                                                <svg x-show="formRole === r.value" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            @else
                                <input type="text" value="{{ $roleTitle }}" readonly disabled
                                       class="h-12 w-full px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-sm font-normal cursor-not-allowed select-none">
                            @endif
                        </div>

                        <!-- Staff ID (Locked) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Staff ID
                            </label>
                            <input type="text" value="{{ $user->staff_id ?? '—' }}" readonly disabled
                                   class="h-12 w-full px-4 font-mono rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-sm font-normal cursor-not-allowed select-none">
                        </div>

                        <!-- Official Email Address -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Official Email Address @if($isAdminOrSuperAdmin) <span class="text-rose-500">*</span> @endif
                            </label>
                            @if($isAdminOrSuperAdmin)
                                <input type="email" name="email" id="email" x-model="formEmail" value="{{ old('email', $user->email) }}" required
                                       class="h-12 w-full px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-normal focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-2xs"
                                       style="text-transform: none !important;">
                            @else
                                <input type="email" value="{{ $user->email }}" readonly disabled
                                       class="h-12 w-full px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-sm font-normal cursor-not-allowed select-none">
                            @endif
                            @error('email') <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Member Since (Locked) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Member Since
                            </label>
                            <input type="text" value="{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Active' }}" readonly disabled
                                   class="h-12 w-full px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-sm font-normal cursor-not-allowed select-none">
                        </div>

                    </div>

                    @if(!$isAdminOrSuperAdmin)
                        <!-- Administrative Fields Locked Notice Banner -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/60 flex items-start gap-3">
                            <div class="w-7 h-7 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                                <strong class="font-semibold text-slate-700 dark:text-slate-300">Administrative Fields Locked:</strong> Your assigned role, official email, and schedule credentials can only be modified by a System Administrator in Staff Management. Contact the administrator's office to request updates.
                            </p>
                        </div>
                    @else
                        <!-- Locked Fields Notice Helper -->
                        <p class="text-xs text-slate-400 dark:text-slate-500 leading-relaxed">
                            Role, Staff ID and Member since are locked and managed by an administrator.
                        </p>
                    @endif

                    <!-- Footer Action Buttons -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center gap-3">
                        <button type="button" 
                                @click="showConfirmModal = true"
                                class="h-12 px-6 rounded-xl font-semibold text-sm text-white bg-[#08795a] hover:bg-[#065f46] active:scale-[0.99] transition-all cursor-pointer shadow-sm">
                            Save changes
                        </button>
                        <button type="button" 
                                @click="formName = '{{ addslashes($user->name) }}'; formEmail = '{{ addslashes($user->email) }}'; formRole = '{{ old('role', $user->role) }}'; photoPreview = null; if (document.getElementById('profile_avatar_input')) document.getElementById('profile_avatar_input').value = '';"
                                class="h-12 px-6 rounded-xl font-semibold text-sm text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750 active:scale-[0.99] transition-all cursor-pointer">
                            Cancel
                        </button>
                    </div>

                    <!-- POP-UP CONFIRMATION MODAL: Profile Changes -->
                    <template x-teleport="body">
                        <div x-show="showConfirmModal" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4" 
                             x-cloak
                             style="display: none;">
                            
                            <div @click.away="if (!submitting) showConfirmModal = false"
                                 class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden p-6 sm:p-7 space-y-5">
                                
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-inner">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Confirm Profile Changes</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Are you sure you want to save the changes made to your profile?</p>
                                    </div>
                                </div>

                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/60 space-y-2.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500 dark:text-slate-400 font-medium">Name:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200" x-text="formName || '{{ $user->name }}'"></span>
                                    </div>
                                    @if(auth()->user()->can('promote-admin'))
                                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                            <span class="text-slate-500 dark:text-slate-400 font-medium">Assigned Role:</span>
                                            <span class="font-bold text-slate-800 dark:text-slate-200" x-text="roleLabel"></span>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                        <span class="text-slate-500 dark:text-slate-400 font-medium">Official Email:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200" x-text="formEmail || '{{ $user->email }}'"></span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                        <span class="text-slate-500 dark:text-slate-400 font-medium">Avatar Photo:</span>
                                        <template x-if="hasAvatar()">
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">New Photo Selected</span>
                                        </template>
                                        <template x-if="!hasAvatar()">
                                            <span class="font-medium text-slate-400 dark:text-slate-500">Unchanged</span>
                                        </template>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button type="button" 
                                            @click="showConfirmModal = false"
                                            :disabled="submitting"
                                            class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer disabled:opacity-50">
                                        Cancel
                                    </button>
                                    <button type="button"
                                            @click="submitting = true; document.getElementById('profile-info-form').submit()"
                                            :disabled="submitting"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-xs font-bold bg-[#08795a] hover:bg-[#065f46] shadow-md active:scale-[0.98] transition cursor-pointer disabled:opacity-50">
                                        <template x-if="submitting">
                                            <svg class="animate-spin -ml-0.5 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="submitting ? 'Saving...' : 'Yes, Save Changes'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>

            <!-- SECTION 2: Security -->
            <div x-show="activeSection === 'security'" x-cloak class="space-y-6">
                <!-- Section Header -->
                <div class="flex items-start gap-3.5 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-xl bg-[#d9f3e8] dark:bg-emerald-950/60 text-[#065f46] dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Security & Password</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Ensure your account uses a secure password (minimum 8 characters with numbers).</p>
                    </div>
                </div>

                <!-- Password Form (Single column max-width 520px) -->
                <form id="password-update-form" x-ref="passwordForm" method="POST" action="{{ route('password.update') }}"
                      x-data="{
                          password: '',
                          confirmPassword: '',
                          showCurrent: false,
                          showNew: false,
                          showConfirm: false,
                          showPassModal: false,
                          submittingPass: false,
                          get hasMinLength() { return this.password.length >= 8; },
                          get hasNumber() { return /[0-9]/.test(this.password); },
                          get strength() {
                              let score = 0;
                              if (this.hasMinLength) score++;
                              if (this.hasNumber) score++;
                              return score;
                          },
                          get strengthLabel() {
                              if (this.password.length === 0) return '';
                              if (this.strength < 2) return 'Incomplete';
                              return 'Ready';
                          },
                          get strengthColor() {
                              if (this.password.length === 0) return 'bg-slate-200 dark:bg-slate-700';
                              if (this.strength === 1) return 'bg-amber-500';
                              return 'bg-emerald-500';
                          },
                          get strengthTextColor() {
                              if (this.strength < 2) return 'text-amber-500';
                              return 'text-emerald-500';
                          },
                          get match() {
                              if (this.confirmPassword.length === 0) return null;
                              return this.password === this.confirmPassword;
                          }
                      }"
                      class="max-w-[520px] space-y-4">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div class="space-y-1.5">
                        <label for="current_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Current Password
                        </label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" id="current_password" required
                                   class="h-12 w-full px-4 pr-12 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-2xs">
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer" tabindex="-1">
                                <svg x-show="!showCurrent" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showCurrent" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword') <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- New Password -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                New Password
                            </label>
                            <span x-text="strengthLabel" class="text-xs font-bold" :class="strengthTextColor"></span>
                        </div>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'" name="password" id="password" x-model="password" required
                                   class="h-12 w-full px-4 pr-12 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-2xs">
                            <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer" tabindex="-1">
                                <svg x-show="!showNew" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showNew" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>

                        <!-- Strength meter and Checklist -->
                        <div class="space-y-2 pt-1">
                            <div class="flex gap-1.5">
                                <template x-for="i in 2" :key="i">
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                         :class="i <= strength ? strengthColor : 'bg-slate-200 dark:bg-slate-700'"></div>
                                </template>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <div class="flex items-center gap-1.5 text-xs font-medium" :class="hasMinLength ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-400 dark:text-slate-500'">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="hasMinLength" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        <circle x-show="!hasMinLength" cx="12" cy="12" r="9" stroke-width="2"></circle>
                                    </svg>
                                    <span>8+ Characters</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs font-medium" :class="hasNumber ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-slate-400 dark:text-slate-500'">
                                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="hasNumber" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        <circle x-show="!hasNumber" cx="12" cy="12" r="9" stroke-width="2"></circle>
                                    </svg>
                                    <span>Number (0-9)</span>
                                </div>
                            </div>
                        </div>
                        @error('password', 'updatePassword') <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Confirm Password
                            </label>
                            <span x-show="match === false" class="text-xs font-bold text-rose-500 flex items-center gap-1">
                                Passwords do not match
                            </span>
                            <span x-show="match === true" class="text-xs font-bold text-emerald-500 flex items-center gap-1">
                                Passwords match!
                            </span>
                        </div>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" x-model="confirmPassword" required
                                   class="h-12 w-full px-4 pr-12 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-2xs"
                                   :class="{'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20': match === false, 'border-emerald-400 focus:border-emerald-500 focus:ring-emerald-500/20': match === true}">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors cursor-pointer" tabindex="-1">
                                <svg x-show="!showConfirm" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" 
                                @click="if (hasMinLength && hasNumber && match) showPassModal = true"
                                class="h-12 px-6 rounded-xl font-semibold text-sm text-white bg-[#08795a] hover:bg-[#065f46] transition-all cursor-pointer shadow-sm disabled:cursor-not-allowed disabled:opacity-50"
                                :disabled="!hasMinLength || !hasNumber || !match">
                            Update password
                        </button>
                    </div>

                    <!-- POP-UP CONFIRMATION MODAL: Password Update -->
                    <template x-teleport="body">
                        <div x-show="showPassModal" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-[9999] overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4" 
                             x-cloak
                             style="display: none;">
                            
                            <div @click.away="if (!submittingPass) showPassModal = false"
                                 class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden p-6 sm:p-7 space-y-5">
                                
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Confirm Password Update</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Are you sure you want to change your account password?</p>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 dark:text-slate-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 p-3.5 rounded-2xl leading-relaxed">
                                    <strong class="font-semibold text-amber-800 dark:text-amber-300">Important Note:</strong> Your current session will remain active, but you must enter this new password the next time you sign into the portal.
                                </p>

                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button type="button" 
                                            @click="showPassModal = false"
                                            :disabled="submittingPass"
                                            class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer disabled:opacity-50">
                                        Cancel
                                    </button>
                                    <button type="button"
                                            @click="submittingPass = true; document.getElementById('password-update-form').submit()"
                                            :disabled="submittingPass"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-xs font-bold bg-[#08795a] hover:bg-[#065f46] shadow-md active:scale-[0.98] transition cursor-pointer disabled:opacity-50">
                                        <template x-if="submittingPass">
                                            <svg class="animate-spin -ml-0.5 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="submittingPass ? 'Updating...' : 'Yes, Update Password'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </form>
            </div>

            <!-- SECTION 3: Duty & Schedule -->
            <div x-show="activeSection === 'schedule'" x-cloak class="space-y-6">
                <!-- Section Header -->
                <div class="flex items-start gap-3.5 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 rounded-xl bg-[#d9f3e8] dark:bg-emerald-950/60 text-[#065f46] dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Duty & Schedule</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Configure your weekly recurring clinic duty schedule and active shift times.</p>
                    </div>
                </div>

                @if($isAdminOrSuperAdmin)
                    <div class="space-y-5 max-w-2xl" x-data="adminSchedulePicker('{{ addslashes(old('schedule', $user->schedule ?? ($user->formatted_schedule ?: 'No Schedule Set'))) }}')">
                        
                        <!-- Assigned Schedule Field -->
                        <div class="space-y-1.5">
                            <label for="schedule_display_field" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                Assigned Schedule
                            </label>
                            <div class="relative">
                                <input type="text" id="schedule_display_field" x-model="scheduleText"
                                       @input="onManualInput($event.target.value)"
                                       placeholder="e.g. Mon - Fri • 8:00 AM - 5:00 PM"
                                       class="h-12 w-full px-4 pr-24 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm font-semibold focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all shadow-2xs"
                                       style="text-transform: none !important;">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center gap-1.5 pointer-events-none">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/70 dark:border-emerald-800/60 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Live
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Duty Days & Hours Picker Card -->
                        <div class="p-5 rounded-2xl bg-slate-50/90 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 space-y-4 shadow-2xs">
                            
                            <!-- Duty Days Header & Presets -->
                            <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-200/70 dark:border-slate-800">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Duty Days & Time Picker
                                </span>
                                <div class="flex flex-wrap gap-1.5 text-[11px]">
                                    <button type="button" @click="selectPresetDays('weekdays')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 text-slate-600 dark:text-slate-300 font-semibold cursor-pointer transition-colors">Mon - Fri</button>
                                    <button type="button" @click="selectPresetDays('mon-sat')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 text-slate-600 dark:text-slate-300 font-semibold cursor-pointer transition-colors">Mon - Sat</button>
                                    <button type="button" @click="selectPresetDays('all')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-emerald-500 text-slate-600 dark:text-slate-300 font-semibold cursor-pointer transition-colors">All 7 Days</button>
                                    <button type="button" @click="selectPresetDays('clear')" class="px-2.5 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-rose-400 text-rose-600 dark:text-rose-400 font-semibold cursor-pointer transition-colors">Clear</button>
                                </div>
                            </div>

                            <!-- Duty Days Selection -->
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Duty Days:</span>
                                    <span class="text-xs font-bold" :class="formattedDaysText === 'None' ? 'text-rose-500 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'" x-text="formattedDaysText"></span>
                                </div>

                                <!-- Day Pills -->
                                <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                                    <template x-for="(isActive, day) in days" :key="day">
                                        <button type="button" @click="toggleDay(day)"
                                                :class="isActive 
                                                    ? 'bg-emerald-600 text-white font-bold shadow-xs border-emerald-600 ring-2 ring-emerald-500/20' 
                                                    : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750 font-medium'"
                                                class="py-2 px-1 text-center border rounded-xl text-xs transition-all focus:outline-none cursor-pointer select-none">
                                            <span x-text="day"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Duty Time In & Duty Time Out -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2 border-t border-slate-200/70 dark:border-slate-800">
                                <!-- Duty Time In -->
                                <div class="relative" @click.outside="openTimeIn = false">
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Duty Time In
                                    </label>
                                    <div @click="openTimeIn = !openTimeIn; openTimeOut = false"
                                         role="button" tabindex="0"
                                         class="w-full h-12 flex items-center justify-between px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all cursor-pointer select-none"
                                         :class="openTimeIn ? 'border-emerald-500 ring-2 ring-emerald-500/20' : ''">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span x-text="format12Hour(timeIn)"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80" @click.stop>
                                                <button type="button" @click.stop="setTimeIn(undefined, undefined, 'AM')"
                                                        :class="timeInPeriod === 'AM' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                        class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">AM</button>
                                                <button type="button" @click.stop="setTimeIn(undefined, undefined, 'PM')"
                                                        :class="timeInPeriod === 'PM' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                        class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">PM</button>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openTimeIn ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Time In Popover -->
                                    <div x-show="openTimeIn" 
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                                         style="display: none;"
                                         class="absolute left-0 bottom-full mb-2 z-50 w-72 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-3 backdrop-blur-md space-y-2.5">
                                        
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100 dark:border-slate-700">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Time:</span>
                                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400" x-text="format12Hour(timeIn)"></span>
                                            </div>
                                            <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                <button type="button" @click="setTimeIn(undefined, undefined, 'AM')"
                                                        :class="timeInPeriod === 'AM' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                        class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">AM</button>
                                                <button type="button" @click="setTimeIn(undefined, undefined, 'PM')"
                                                        :class="timeInPeriod === 'PM' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                        class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">PM</button>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Hour</div>
                                            <div class="grid grid-cols-6 gap-1">
                                                <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="'in-h-' + h">
                                                    <button type="button" @click="setTimeIn(h, undefined, undefined)"
                                                            :class="timeInHour === h 
                                                                ? 'bg-emerald-600 text-white shadow-xs font-bold border border-emerald-500' 
                                                                : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-slate-600 hover:text-emerald-600 dark:hover:text-emerald-300 border border-slate-200 dark:border-slate-600'"
                                                            class="py-1 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                            x-text="h"></button>
                                                </template>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Minute</div>
                                            <div class="grid grid-cols-4 gap-1">
                                                <template x-for="m in ['00', '15', '30', '45']" :key="'in-m-' + m">
                                                    <button type="button" @click="setTimeIn(undefined, m, undefined)"
                                                            :class="timeInMinute === m 
                                                                ? 'bg-emerald-600 text-white shadow-xs font-bold border border-emerald-500' 
                                                                : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-slate-600 hover:text-emerald-600 dark:hover:text-emerald-300 border border-slate-200 dark:border-slate-600'"
                                                            class="py-1 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                            x-text="':' + m"></button>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700">
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Quick Presets</div>
                                            <div class="flex flex-wrap gap-1">
                                                <button type="button" @click="setTimeIn(7, '00', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">7:00 AM</button>
                                                <button type="button" @click="setTimeIn(8, '00', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">8:00 AM</button>
                                                <button type="button" @click="setTimeIn(8, '30', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">8:30 AM</button>
                                                <button type="button" @click="setTimeIn(9, '00', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">9:00 AM</button>
                                                <button type="button" @click="setTimeIn(1, '00', 'PM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">1:00 PM</button>
                                            </div>
                                        </div>

                                        <button type="button" @click="openTimeIn = false"
                                                class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-xs cursor-pointer">
                                            Done
                                        </button>
                                    </div>
                                </div>

                                <!-- Duty Time Out -->
                                <div class="relative" @click.outside="openTimeOut = false">
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                        Duty Time Out
                                    </label>
                                    <div @click="openTimeOut = !openTimeOut; openTimeIn = false"
                                         role="button" tabindex="0"
                                         class="w-full h-12 flex items-center justify-between px-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all cursor-pointer select-none"
                                         :class="openTimeOut ? 'border-emerald-500 ring-2 ring-emerald-500/20' : ''">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span x-text="format12Hour(timeOut)"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80" @click.stop>
                                                <button type="button" @click.stop="setTimeOut(undefined, undefined, 'AM')"
                                                        :class="timeOutPeriod === 'AM' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                        class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">AM</button>
                                                <button type="button" @click.stop="setTimeOut(undefined, undefined, 'PM')"
                                                        :class="timeOutPeriod === 'PM' ? 'bg-emerald-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                        class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">PM</button>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openTimeOut ? 'rotate-180 text-emerald-600 dark:text-emerald-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Time Out Popover -->
                                    <div x-show="openTimeOut" 
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                                         style="display: none;"
                                         class="absolute right-0 bottom-full mb-2 z-50 w-72 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl p-3 backdrop-blur-md space-y-2.5">
                                        
                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100 dark:border-slate-700">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Time:</span>
                                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400" x-text="format12Hour(timeOut)"></span>
                                            </div>
                                            <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                <button type="button" @click="setTimeOut(undefined, undefined, 'AM')"
                                                        :class="timeOutPeriod === 'AM' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                        class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">AM</button>
                                                <button type="button" @click="setTimeOut(undefined, undefined, 'PM')"
                                                        :class="timeOutPeriod === 'PM' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                        class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">PM</button>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Hour</div>
                                            <div class="grid grid-cols-6 gap-1">
                                                <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="'out-h-' + h">
                                                    <button type="button" @click="setTimeOut(h, undefined, undefined)"
                                                            :class="timeOutHour === h 
                                                                ? 'bg-emerald-600 text-white shadow-xs font-bold border border-emerald-500' 
                                                                : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-slate-600 hover:text-emerald-600 dark:hover:text-emerald-300 border border-slate-200 dark:border-slate-600'"
                                                            class="py-1 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                            x-text="h"></button>
                                                </template>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Minute</div>
                                            <div class="grid grid-cols-4 gap-1">
                                                <template x-for="m in ['00', '15', '30', '45']" :key="'out-m-' + m">
                                                    <button type="button" @click="setTimeOut(undefined, m, undefined)"
                                                            :class="timeOutMinute === m 
                                                                ? 'bg-emerald-600 text-white shadow-xs font-bold border border-emerald-500' 
                                                                : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-emerald-50 dark:hover:bg-slate-600 hover:text-emerald-600 dark:hover:text-emerald-300 border border-slate-200 dark:border-slate-600'"
                                                            class="py-1 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                            x-text="':' + m"></button>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700">
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Quick Presets</div>
                                            <div class="flex flex-wrap gap-1">
                                                <button type="button" @click="setTimeOut(12, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">12:00 PM</button>
                                                <button type="button" @click="setTimeOut(1, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">1:00 PM</button>
                                                <button type="button" @click="setTimeOut(5, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">5:00 PM</button>
                                                <button type="button" @click="setTimeOut(6, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">6:00 PM</button>
                                                <button type="button" @click="setTimeOut(8, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-emerald-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">8:00 PM</button>
                                            </div>
                                        </div>

                                        <button type="button" @click="openTimeOut = false"
                                                class="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-xs cursor-pointer">
                                            Done
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Reflected Indicator -->
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-200/70 dark:border-slate-800 text-xs">
                                <span :class="scheduleText === 'No Schedule Set' ? 'text-rose-500 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'" class="shrink-0 flex items-center">
                                    <template x-if="scheduleText === 'No Schedule Set'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </template>
                                    <template x-if="scheduleText !== 'No Schedule Set'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </template>
                                </span>
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Reflecting schedule:</span>
                                <span :class="scheduleText === 'No Schedule Set' ? 'font-bold text-red-600 dark:text-red-400' : 'font-bold text-teal-700 dark:text-teal-300'" x-text="scheduleText"></span>
                            </div>
                        </div>

                        <!-- Save Schedule Changes Action -->
                        <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" 
                                    @click="document.getElementById('profile-info-form').submit()"
                                    class="h-12 px-6 rounded-xl font-semibold text-sm text-white bg-[#08795a] hover:bg-[#065f46] active:scale-[0.99] transition-all cursor-pointer shadow-sm">
                                Save schedule changes
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Locked Schedule Notice for non-admin -->
                    <div class="space-y-4 max-w-xl">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Assigned Schedule
                            </label>
                            <input type="text" value="{{ $user->formatted_schedule ?? ($user->schedule ?: 'No Schedule Set') }}" readonly disabled
                                   class="h-12 w-full px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100/80 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 text-sm font-normal cursor-not-allowed select-none">
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-700/60 flex items-start gap-3">
                            <div class="w-7 h-7 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                <strong class="font-semibold text-slate-700 dark:text-slate-300">Schedule Managed by Admin:</strong> Your assigned clinic schedule is configured by administrative personnel. Contact the administrator's office to request schedule updates.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

        </main>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminSchedulePicker', (initialSchedule) => window.adminSchedulePicker(initialSchedule));
});

window.adminSchedulePicker = function(initialSchedule) {
    return {
        days: { Mon: true, Tue: true, Wed: true, Thu: true, Fri: true, Sat: false, Sun: false },
        timeIn: '08:00',
        timeOut: '17:00',
        openTimeIn: false,
        openTimeOut: false,
        scheduleText: '',

        init() {
            this.parseInitial(initialSchedule);
            if (!initialSchedule || initialSchedule === 'No Schedule Set') {
                this.scheduleText = 'No Schedule Set';
            } else {
                this.scheduleText = this.formattedSchedule || initialSchedule;
            }
            this.updateSchedule();
        },

        parseInitial(str) {
            if (!str || str === 'No Schedule Set' || str === 'None') {
                const daysList = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                daysList.forEach(d => this.days[d] = false);
                return;
            }
            const daysList = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            if (str.includes('Mon - Fri') || str.includes('Mon-Fri')) {
                daysList.forEach(d => this.days[d] = ['Mon','Tue','Wed','Thu','Fri'].includes(d));
            } else if (str.includes('Mon - Sun') || str.includes('Mon-Sun') || str.includes('Everyday')) {
                daysList.forEach(d => this.days[d] = true);
            } else if (str.includes('Mon - Sat') || str.includes('Mon-Sat')) {
                daysList.forEach(d => this.days[d] = d !== 'Sun');
            } else {
                let anyFound = false;
                daysList.forEach(d => {
                    if (str.includes(d)) {
                        this.days[d] = true;
                        anyFound = true;
                    } else {
                        this.days[d] = false;
                    }
                });
                if (!anyFound) {
                    daysList.forEach(d => this.days[d] = false);
                }
            }

            const timeMatches = [...str.matchAll(/(\d{1,2}):(\d{2})\s*(AM|PM)?/gi)];
            if (timeMatches.length >= 2) {
                this.timeIn = this.convertTo24(timeMatches[0][1], timeMatches[0][2], timeMatches[0][3]);
                this.timeOut = this.convertTo24(timeMatches[1][1], timeMatches[1][2], timeMatches[1][3]);
            } else if (timeMatches.length === 1) {
                this.timeIn = this.convertTo24(timeMatches[0][1], timeMatches[0][2], timeMatches[0][3]);
            }
        },

        convertTo24(h, m, ap) {
            let hour = parseInt(h, 10);
            if (ap) {
                ap = ap.toUpperCase();
                if (ap === 'PM' && hour !== 12) hour += 12;
                if (ap === 'AM' && hour === 12) hour = 0;
            }
            return `${String(hour).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
        },

        format12Hour(time24) {
            if (!time24) return '';
            let [h, m] = time24.split(':').map(Number);
            const ap = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${String(m).padStart(2, '0')} ${ap}`;
        },

        toggleDay(day) {
            this.days[day] = !this.days[day];
            this.updateSchedule();
        },

        selectPresetDays(type) {
            const daysList = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            if (type === 'weekdays') {
                daysList.forEach(d => this.days[d] = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'].includes(d));
            } else if (type === 'all') {
                daysList.forEach(d => this.days[d] = true);
            } else if (type === 'mon-sat') {
                daysList.forEach(d => this.days[d] = d !== 'Sun');
            } else if (type === 'clear') {
                daysList.forEach(d => this.days[d] = false);
            }
            this.updateSchedule();
        },

        get timeInHour() {
            let [h] = this.timeIn.split(':').map(Number);
            return h % 12 || 12;
        },
        get timeInMinute() {
            let [, m] = this.timeIn.split(':');
            return m || '00';
        },
        get timeInPeriod() {
            let [h] = this.timeIn.split(':').map(Number);
            return h >= 12 ? 'PM' : 'AM';
        },
        get timeOutHour() {
            let [h] = this.timeOut.split(':').map(Number);
            return h % 12 || 12;
        },
        get timeOutMinute() {
            let [, m] = this.timeOut.split(':');
            return m || '00';
        },
        get timeOutPeriod() {
            let [h] = this.timeOut.split(':').map(Number);
            return h >= 12 ? 'PM' : 'AM';
        },

        setTimeIn(h, m, p) {
            let hour12 = this.timeInHour;
            let minute = this.timeInMinute;
            let period = this.timeInPeriod;

            if (h !== undefined) hour12 = h;
            if (m !== undefined) minute = m;
            if (p !== undefined) period = p;

            let h24 = parseInt(hour12, 10);
            if (period === 'PM' && h24 !== 12) h24 += 12;
            if (period === 'AM' && h24 === 12) h24 = 0;

            this.timeIn = `${String(h24).padStart(2, '0')}:${minute}`;
            this.updateSchedule();
        },

        setTimeOut(h, m, p) {
            let hour12 = this.timeOutHour;
            let minute = this.timeOutMinute;
            let period = this.timeOutPeriod;

            if (h !== undefined) hour12 = h;
            if (m !== undefined) minute = m;
            if (p !== undefined) period = p;

            let h24 = parseInt(hour12, 10);
            if (period === 'PM' && h24 !== 12) h24 += 12;
            if (period === 'AM' && h24 === 12) h24 = 0;

            this.timeOut = `${String(h24).padStart(2, '0')}:${minute}`;
            this.updateSchedule();
        },

        get formattedDaysText() {
            const selected = Object.keys(this.days).filter(d => this.days[d]);
            if (selected.length === 0) return 'None';
            if (selected.length === 7) return 'Mon - Sun';
            if (selected.length === 5 && ['Mon','Tue','Wed','Thu','Fri'].every(d => this.days[d])) return 'Mon - Fri';
            if (selected.length === 6 && ['Mon','Tue','Wed','Thu','Fri','Sat'].every(d => this.days[d])) return 'Mon - Sat';
            return selected.join(', ');
        },

        get formattedSchedule() {
            const selected = Object.keys(this.days).filter(d => this.days[d]);
            if (selected.length === 0) return 'No Schedule Set';
            const timePart = `${this.format12Hour(this.timeIn)} - ${this.format12Hour(this.timeOut)}`;
            return `${this.formattedDaysText} • ${timePart}`;
        },

        get schedulePayload() {
            const selected = Object.keys(this.days).filter(d => this.days[d]);
            if (selected.length === 0) return '';
            return JSON.stringify(selected.map(day => ({
                day: day,
                time_in: this.timeIn,
                time_out: this.timeOut
            })));
        },

        onManualInput(val) {
            this.scheduleText = val;
            window.dispatchEvent(new CustomEvent('admin-schedule-updated', { detail: { schedule: val, payload: '' } }));
        },

        updateSchedule() {
            const sched = this.formattedSchedule;
            this.scheduleText = sched;
            window.dispatchEvent(new CustomEvent('admin-schedule-updated', { detail: { schedule: sched, payload: this.schedulePayload } }));
        }
    };
};
</script>

@include('partials.image-cropper')
@endsection
