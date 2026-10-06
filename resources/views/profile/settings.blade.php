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
@endphp

@extends($layout)

@section('header', 'Profile Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12">
    
    <!-- Hero Profile & Avatar Drag-and-Drop Anywhere Container -->
    <div x-data="{ 
            photoName: null, 
            photoPreview: null,
            uploadError: null,
            isDragging: false,
            dragCounter: 0,
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
        @dragenter.prevent="dragCounter++; isDragging = true"
        @dragleave.prevent="dragCounter--; if (dragCounter <= 0) { isDragging = false; dragCounter = 0; }"
        @dragover.prevent
        @drop.prevent="isDragging = false; dragCounter = 0; if ($event.dataTransfer.files.length) handleAvatarFile($event.dataTransfer.files[0])"
        class="relative bg-white dark:bg-slate-800/95 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 dark:shadow-slate-950/40 border border-slate-200/80 dark:border-slate-700/80 overflow-hidden transition-all duration-200">

        <!-- Full-Container Drag & Drop Active Overlay -->
        <div x-show="isDragging" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-98"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-98"
             style="display: none;"
             class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-emerald-600/90 dark:bg-emerald-950/90 backdrop-blur-md border-2 border-dashed border-white/80 p-6 text-white text-center pointer-events-none shadow-2xl">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center mb-3 animate-bounce shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <h4 class="text-xl font-extrabold tracking-tight">Drop Image to Update Avatar</h4>
            <p class="text-sm text-emerald-100 mt-1 max-w-sm">Release your photo anywhere in this card to launch the square cropper</p>
        </div>

        <!-- Ambient Background Aura -->
        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-emerald-500/10 dark:bg-emerald-500/5 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-teal-500/10 dark:bg-teal-500/5 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 sm:gap-8">
            <!-- Left: Avatar & Identity -->
            <div class="flex flex-col sm:flex-row items-center sm:items-center gap-6 text-center sm:text-left">
                
                <!-- Avatar Visual with Camera Button -->
                <div class="relative group cursor-pointer" @click="document.getElementById('profile_avatar_input').click()" title="Click to change avatar or drag & drop image anywhere">
                    <div class="h-28 w-28 sm:h-32 sm:w-32 rounded-3xl overflow-hidden bg-slate-100 dark:bg-slate-700/80 relative ring-4 ring-emerald-500/30 dark:ring-emerald-400/20 shadow-xl shadow-emerald-900/10 dark:shadow-emerald-950/40 transition-transform duration-300 group-hover:scale-105">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" class="h-full w-full object-cover" alt="New Avatar Preview">
                        </template>
                        <template x-if="!photoPreview">
                            @if(auth()->user()->avatar_url)
                                <img class="h-full w-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-black text-3xl sm:text-4xl select-none">
                                    {{ auth()->user()->initials }}
                                </div>
                            @endif
                        </template>

                        <!-- Hover Overlay -->
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <svg class="w-7 h-7 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-[11px] font-bold uppercase tracking-wider">Change</span>
                        </div>
                    </div>

                    <!-- Floating Camera Badge -->
                    <button type="button" 
                            class="absolute -bottom-1 -right-1 w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white shadow-lg shadow-emerald-600/30 flex items-center justify-center ring-4 ring-white dark:ring-slate-800 group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- User Details -->
                @php
                    $rawStatus = strtolower(auth()->user()->status ?? '');
                    $initStatus = match(true) {
                        in_array($rawStatus, ['online', 'present', 'active', 'in office']) => 'Online',
                        in_array($rawStatus, ['occupied', 'seminar', 'on seminar', 'in meeting']) => 'Occupied',
                        default => 'Offline'
                    };
                @endphp
                <div class="space-y-2 flex flex-col justify-center"
                     x-data="{
                         status: '{{ $initStatus }}',
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
                         }
                     }">
                    <div>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border transition-all duration-200"
                              :class="{
                                  'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border-emerald-200/80 dark:border-emerald-800/80': status === 'Online',
                                  'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700': status === 'Offline',
                                  'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border-amber-200/80 dark:border-amber-800/80': status === 'Occupied'
                              }">
                            <span class="w-2 h-2 rounded-full transition-all"
                                  :class="{
                                      'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse': status === 'Online',
                                      'bg-slate-400 dark:bg-slate-500': status === 'Offline',
                                      'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.8)] animate-pulse': status === 'Occupied'
                                  }"></span>
                            <span x-text="status">{{ $initStatus }}</span>
                        </span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ auth()->user()->name }}
                    </h2>

                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-sm text-slate-500 dark:text-slate-400 font-medium">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                            {{ auth()->user()->email }}
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Verified Account
                        </span>
                    </div>

                    <!-- Direct Instant Duty Status Toggle (No confirmation required, instant update) -->
                    <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Duty Status:</span>
                        <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/70">
                            <!-- Online -->
                            <button type="button" 
                                    @click="setStatus('Online')"
                                    class="relative inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer"
                                    :class="status === 'Online' 
                                        ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm font-bold' 
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"
                                      :class="status === 'Online' ? 'shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'opacity-60'"></span>
                                <span>Online</span>
                            </button>

                            <!-- Offline -->
                            <button type="button" 
                                    @click="setStatus('Offline')"
                                    class="relative inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer"
                                    :class="status === 'Offline' 
                                        ? 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 shadow-sm font-bold' 
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                <span class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                <span>Offline</span>
                            </button>

                            <!-- Occupied -->
                            <button type="button" 
                                    @click="setStatus('Occupied')"
                                    class="relative inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold transition-all duration-150 cursor-pointer"
                                    :class="status === 'Occupied' 
                                        ? 'bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-sm font-bold' 
                                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'">
                                <span class="w-2 h-2 rounded-full bg-amber-500"
                                      :class="status === 'Occupied' ? 'shadow-[0_0_8px_rgba(245,158,11,0.8)]' : 'opacity-60'"></span>
                                <span>Occupied</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Dropzone Helper Box (Drop anywhere in container or click) -->
            <div class="w-full lg:w-80 flex flex-col justify-center">
                <div @click="document.getElementById('profile_avatar_input').click()"
                     class="group p-4 sm:p-5 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-600 hover:border-emerald-500 dark:hover:border-emerald-400 bg-slate-50/70 dark:bg-slate-900/40 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 cursor-pointer transition-all duration-200 text-center flex flex-col items-center justify-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="photoName || 'Drop avatar anywhere here'"></p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">or click to browse from device</p>
                    <div class="mt-2.5 inline-flex items-center gap-1.5 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider bg-emerald-100/60 dark:bg-emerald-900/30 px-2.5 py-1 rounded-lg">
                        <span>Square Crop • Max 2MB</span>
                    </div>
                </div>
                <template x-if="uploadError">
                    <p class="text-xs text-center text-rose-600 dark:text-rose-400 font-semibold mt-2 flex items-center justify-center gap-1.5" role="alert">
                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span x-text="uploadError"></span>
                    </p>
                </template>
                <template x-if="photoPreview && !uploadError">
                    <p class="text-xs text-center text-emerald-600 dark:text-emerald-400 font-semibold mt-2 flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        New photo selected (Click Save Changes below)
                    </p>
                </template>
            </div>
        </div>

        <!-- Hidden input connected to form -->
        <input type="file" id="profile_avatar_input" name="avatar" form="profile-info-form" accept=".jpeg,.jpg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden"
            @change="if ($event.target.files.length) handleAvatarFile($event.target.files[0])">
    </div>

    <!-- Main Forms Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        
        <!-- CARD 1: Profile Information -->
        <div class="bg-white dark:bg-slate-800/95 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 dark:shadow-slate-950/40 border border-slate-200/80 dark:border-slate-700/80 space-y-6">
            
            <!-- Card Header -->
            <div class="flex items-start gap-4 pb-6 border-b border-slate-100 dark:border-slate-700/60">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Profile & Account Information</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Update your display details and review locked administrative records.</p>
                </div>
            </div>

            <!-- Profile Form with Confirmation Modal -->
            <form id="profile-info-form" x-ref="profileForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" 
                  x-data="{ 
                      showConfirmModal: false, 
                      submitting: false, 
                      formName: '{{ addslashes($user->name) }}',
                      formEmail: '{{ addslashes($user->email) }}',
                      formSchedule: '{{ addslashes(old('schedule', $user->schedule ?? ($user->formatted_schedule ?: 'No Schedule Set'))) }}',
                      avatarPreview: null,
                      avatarName: null,
                      hasAvatar() {
                          const input = document.getElementById('profile_avatar_input');
                          return !!(this.avatarPreview || (input && input.files && input.files.length > 0));
                      }
                  }"
                  @profile-avatar-changed.window="avatarPreview = $event.detail.previewUrl; avatarName = $event.detail.fileName"
                  @admin-schedule-updated.window="formSchedule = $event.detail.schedule"
                  class="space-y-6">
                @csrf
                @method('patch')

                <!-- Personal & Account Details Form Grid -->
                <div class="space-y-4">
                    <!-- Full Name Field -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-bold @if($isAdminOrSuperAdmin) text-slate-700 dark:text-slate-300 @else text-slate-500 dark:text-slate-400 @endif uppercase tracking-wider">
                            Full Name @if($isAdminOrSuperAdmin) <span class="text-rose-500">*</span> @endif
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            @if($isAdminOrSuperAdmin)
                                <input type="text" name="name" id="name" x-model="formName" value="{{ old('name', $user->name) }}" required
                                       class="no-uppercase normal-case font-normal w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-900/60 text-slate-900 dark:text-white text-sm focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                       style="text-transform: none !important; font-weight: 400 !important;">
                            @else
                                <input type="text" value="{{ $user->name }}" readonly disabled
                                       class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-200/60 dark:bg-slate-900/90 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed select-none transition-all shadow-inner"
                                       style="text-transform: none !important; font-weight: 400 !important;">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </div>
                            @endif
                        </div>
                        @error('name') <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        
                        <!-- Role / Position -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold @if($isAdminOrSuperAdmin) text-slate-700 dark:text-slate-300 @else text-slate-500 dark:text-slate-400 @endif uppercase tracking-wider">
                                Assigned Role
                            </label>
                            @if(auth()->user()->can('promote-admin'))
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <select name="role" id="role"
                                            class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-900/60 text-slate-900 dark:text-white text-sm focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all appearance-none cursor-pointer"
                                            style="text-transform: none !important; font-weight: 400 !important;">
                                        <option value="super_admin" @selected(old('role', $user->role) === 'super_admin')>Super Administrator</option>
                                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrator</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            @else
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                        </svg>
                                    </div>
                                    <input type="text" value="{{ $roleTitle }}" readonly disabled
                                           class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-200/60 dark:bg-slate-900/90 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed select-none transition-all shadow-inner"
                                           style="text-transform: none !important; font-weight: 400 !important;">
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Staff / Employee ID -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Staff ID
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <input type="text" value="{{ $user->staff_id ?? '—' }}" readonly disabled
                                       class="font-mono font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-200/60 dark:bg-slate-900/90 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed select-none transition-all shadow-inner"
                                       style="font-weight: 400 !important;">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-bold @if($isAdminOrSuperAdmin) text-slate-700 dark:text-slate-300 @else text-slate-500 dark:text-slate-400 @endif uppercase tracking-wider">
                                Official Email Address @if($isAdminOrSuperAdmin) <span class="text-rose-500">*</span> @endif
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                    </svg>
                                </div>
                                @if($isAdminOrSuperAdmin)
                                    <input type="email" name="email" id="email" x-model="formEmail" value="{{ old('email', $user->email) }}" required
                                           class="no-uppercase normal-case font-normal w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-900/60 text-slate-900 dark:text-white text-sm focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                                           style="text-transform: none !important; font-weight: 400 !important;">
                                @else
                                    <input type="email" value="{{ $user->email }}" readonly disabled
                                           class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-200/60 dark:bg-slate-900/90 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed select-none transition-all shadow-inner"
                                           style="text-transform: none !important; font-weight: 400 !important;">
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    </div>
                                @endif
                            </div>
                            @error('email') <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Member Since -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Member Since
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="text" value="{{ $user->created_at ? $user->created_at->format('M d, Y') : 'Active' }}" readonly disabled
                                       class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-200/60 dark:bg-slate-900/90 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed select-none transition-all shadow-inner"
                                       style="text-transform: none !important; font-weight: 400 !important;">
                                <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Work Schedule -->
                        @if($isAdminOrSuperAdmin)
                            <div class="space-y-3 sm:col-span-2" x-data="adminSchedulePicker('{{ addslashes(old('schedule', $user->schedule ?? ($user->formatted_schedule ?: 'No Schedule Set'))) }}')">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <label for="schedule" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        Assigned Schedule
                                    </label>
                                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                        Reflected live via date & time picker below
                                    </span>
                                </div>

                                <!-- Schedule Input Field (Bound & Reflected Live) -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-teal-600 dark:text-teal-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="schedule" id="schedule" x-model="scheduleText"
                                           @input="onManualInput($event.target.value)"
                                           placeholder="e.g. Mon - Fri • 8:00 AM - 5:00 PM"
                                           class="no-uppercase normal-case font-semibold w-full pl-10 pr-24 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-sm focus:border-teal-500 dark:focus:border-teal-400 focus:ring-2 focus:ring-teal-500/20 transition-all shadow-2xs"
                                           style="text-transform: none !important;">
                                    
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5 pointer-events-none">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-teal-50 dark:bg-teal-950/60 border border-teal-200/70 dark:border-teal-800/60 text-[10px] font-bold text-teal-700 dark:text-teal-300">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500 animate-pulse"></span>
                                            Live
                                        </span>
                                    </div>
                                </div>
                                <input type="hidden" name="schedule_payload" :value="schedulePayload">
                                @error('schedule') <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold mt-1">{{ $message }}</p> @enderror

                                <!-- Interactive Duty Days & Hours Picker Card -->
                                <div class="p-4 rounded-2xl bg-slate-50/90 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 space-y-4 shadow-2xs">
                                    
                                    <!-- Duty Days Header & Presets -->
                                    <div class="flex items-center justify-between flex-wrap gap-2 pb-2 border-b border-slate-200/70 dark:border-slate-800">
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Duty Days & Time Picker
                                        </span>
                                        <div class="flex flex-wrap gap-1.5 text-[11px]">
                                            <button type="button" @click="selectPresetDays('weekdays')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-teal-500 text-slate-600 dark:text-slate-300 font-semibold cursor-pointer transition-colors">Mon - Fri</button>
                                            <button type="button" @click="selectPresetDays('mon-sat')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-teal-500 text-slate-600 dark:text-slate-300 font-semibold cursor-pointer transition-colors">Mon - Sat</button>
                                            <button type="button" @click="selectPresetDays('all')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-teal-500 text-slate-600 dark:text-slate-300 font-semibold cursor-pointer transition-colors">All 7 Days</button>
                                            <button type="button" @click="selectPresetDays('clear')" class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:border-rose-400 text-rose-600 dark:text-rose-400 font-semibold cursor-pointer transition-colors">Clear</button>
                                        </div>
                                    </div>

                                    <!-- Duty Days Selection -->
                                    <div class="space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Duty Days:</span>
                                            <span class="text-xs font-bold" :class="formattedDaysText === 'None' ? 'text-red-500 dark:text-red-400' : 'text-teal-600 dark:text-teal-400'" x-text="formattedDaysText"></span>
                                        </div>

                                        <!-- Day Pills -->
                                        <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                                            <template x-for="(isActive, day) in days" :key="day">
                                                <button type="button" @click="toggleDay(day)"
                                                        :class="isActive 
                                                            ? 'bg-teal-600 text-white font-bold shadow-xs border-teal-600 ring-2 ring-teal-500/20' 
                                                            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-750 font-medium'"
                                                        class="py-2 px-1 text-center border rounded-xl text-xs transition-all focus:outline-none cursor-pointer select-none">
                                                    <span x-text="day"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Section: Duty Time In & Duty Time Out -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2 border-t border-slate-200/70 dark:border-slate-800">
                                        <!-- Duty Time In -->
                                        <div class="relative" @click.outside="openTimeIn = false">
                                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                                                Duty Time In
                                            </label>
                                            <div @click="openTimeIn = !openTimeIn; openTimeOut = false"
                                                 role="button" tabindex="0"
                                                 class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer select-none"
                                                 :class="openTimeIn ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span x-text="format12Hour(timeIn)"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80" @click.stop>
                                                        <button type="button" @click.stop="setTimeIn(undefined, undefined, 'AM')"
                                                                :class="timeInPeriod === 'AM' ? 'bg-teal-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                                class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">AM</button>
                                                        <button type="button" @click.stop="setTimeIn(undefined, undefined, 'PM')"
                                                                :class="timeInPeriod === 'PM' ? 'bg-teal-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                                class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">PM</button>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openTimeIn ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                        <span class="text-xs font-black text-teal-600 dark:text-teal-400" x-text="format12Hour(timeIn)"></span>
                                                    </div>
                                                    <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                        <button type="button" @click="setTimeIn(undefined, undefined, 'AM')"
                                                                :class="timeInPeriod === 'AM' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                                class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">AM</button>
                                                        <button type="button" @click="setTimeIn(undefined, undefined, 'PM')"
                                                                :class="timeInPeriod === 'PM' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                                class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">PM</button>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Hour</div>
                                                    <div class="grid grid-cols-6 gap-1">
                                                        <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="'in-h-' + h">
                                                            <button type="button" @click="setTimeIn(h, undefined, undefined)"
                                                                    :class="timeInHour === h 
                                                                        ? 'bg-teal-600 text-white shadow-xs font-bold border border-teal-500' 
                                                                        : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-600'"
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
                                                                        ? 'bg-teal-600 text-white shadow-xs font-bold border border-teal-500' 
                                                                        : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-600'"
                                                                    class="py-1 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                    x-text="':' + m"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700">
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Quick Presets</div>
                                                    <div class="flex flex-wrap gap-1">
                                                        <button type="button" @click="setTimeIn(7, '00', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">7:00 AM</button>
                                                        <button type="button" @click="setTimeIn(8, '00', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">8:00 AM</button>
                                                        <button type="button" @click="setTimeIn(8, '30', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">8:30 AM</button>
                                                        <button type="button" @click="setTimeIn(9, '00', 'AM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">9:00 AM</button>
                                                        <button type="button" @click="setTimeIn(1, '00', 'PM'); openTimeIn = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">1:00 PM</button>
                                                    </div>
                                                </div>

                                                <button type="button" @click="openTimeIn = false"
                                                        class="w-full py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-all shadow-xs cursor-pointer">
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
                                                 class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-semibold shadow-2xs hover:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer select-none"
                                                 :class="openTimeOut ? 'border-teal-500 ring-2 ring-teal-500/20' : ''">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span x-text="format12Hour(timeOut)"></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80" @click.stop>
                                                        <button type="button" @click.stop="setTimeOut(undefined, undefined, 'AM')"
                                                                :class="timeOutPeriod === 'AM' ? 'bg-teal-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                                class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">AM</button>
                                                        <button type="button" @click.stop="setTimeOut(undefined, undefined, 'PM')"
                                                                :class="timeOutPeriod === 'PM' ? 'bg-teal-600 text-white font-bold shadow-2xs' : 'text-slate-500 dark:text-slate-400'"
                                                                class="px-1.5 py-0.5 rounded text-[10px] transition-all cursor-pointer">PM</button>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="openTimeOut ? 'rotate-180 text-teal-600 dark:text-teal-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                        <span class="text-xs font-black text-teal-600 dark:text-teal-400" x-text="format12Hour(timeOut)"></span>
                                                    </div>
                                                    <div class="flex p-0.5 rounded-lg bg-slate-100 dark:bg-slate-700/80 border border-slate-200/80 dark:border-slate-600/80">
                                                        <button type="button" @click="setTimeOut(undefined, undefined, 'AM')"
                                                                :class="timeOutPeriod === 'AM' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                                class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">AM</button>
                                                        <button type="button" @click="setTimeOut(undefined, undefined, 'PM')"
                                                                :class="timeOutPeriod === 'PM' ? 'bg-teal-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 font-medium'"
                                                                class="px-2 py-0.5 rounded text-xs transition-all cursor-pointer">PM</button>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Hour</div>
                                                    <div class="grid grid-cols-6 gap-1">
                                                        <template x-for="h in [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]" :key="'out-h-' + h">
                                                            <button type="button" @click="setTimeOut(h, undefined, undefined)"
                                                                    :class="timeOutHour === h 
                                                                        ? 'bg-teal-600 text-white shadow-xs font-bold border border-teal-500' 
                                                                        : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-600'"
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
                                                                        ? 'bg-teal-600 text-white shadow-xs font-bold border border-teal-500' 
                                                                        : 'bg-slate-100 dark:bg-slate-700/90 text-slate-800 dark:text-slate-100 hover:bg-teal-50 dark:hover:bg-slate-600 hover:text-teal-600 dark:hover:text-teal-300 border border-slate-200 dark:border-slate-600'"
                                                                    class="py-1 rounded-lg text-xs font-semibold text-center transition-all cursor-pointer"
                                                                    x-text="':' + m"></button>
                                                        </template>
                                                    </div>
                                                </div>

                                                <div class="pt-1.5 border-t border-slate-100 dark:border-slate-700">
                                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Quick Presets</div>
                                                    <div class="flex flex-wrap gap-1">
                                                        <button type="button" @click="setTimeOut(12, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">12:00 PM</button>
                                                        <button type="button" @click="setTimeOut(1, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">1:00 PM</button>
                                                        <button type="button" @click="setTimeOut(5, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">5:00 PM</button>
                                                        <button type="button" @click="setTimeOut(6, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">6:00 PM</button>
                                                        <button type="button" @click="setTimeOut(8, '00', 'PM'); openTimeOut = false" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700/90 hover:bg-teal-50 dark:hover:bg-slate-600 text-[10px] font-semibold text-slate-800 dark:text-slate-200 hover:text-teal-600 dark:hover:text-teal-300 transition-colors cursor-pointer border border-slate-200 dark:border-slate-600">8:00 PM</button>
                                                    </div>
                                                </div>

                                                <button type="button" @click="openTimeOut = false"
                                                        class="w-full py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-all shadow-xs cursor-pointer">
                                                    Done
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Live Reflected Schedule Badge / Indicator -->
                                    <div class="flex items-center gap-2 pt-2 border-t border-slate-200/70 dark:border-slate-800 text-xs">
                                        <span :class="scheduleText === 'No Schedule Set' ? 'text-red-500 dark:text-red-400' : 'text-teal-600 dark:text-teal-400'" class="shrink-0 flex items-center">
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
                            </div>
                        @else
                            <div class="space-y-1.5 sm:col-span-2">
                                <label for="schedule" class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    Assigned Schedule
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" value="{{ $user->formatted_schedule ?? ($user->schedule ?: 'No Schedule Set') }}" readonly disabled
                                           class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-200/60 dark:bg-slate-900/90 text-slate-500 dark:text-slate-400 text-sm cursor-not-allowed select-none transition-all shadow-inner"
                                           style="text-transform: none !important; font-weight: 400 !important;">
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    </div>
                                </div>
                            </div>
                        @endif

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
                    @endif
                </div>

                <!-- Save Changes CTA Button -->
                @if($isAdminOrSuperAdmin)
                    <div class="pt-2">
                        <button type="button" 
                                @click="showConfirmModal = true"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-[0.98] shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/30 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save Profile Changes</span>
                        </button>
                    </div>
                @else
                    <div class="pt-2" x-show="hasAvatar()">
                        <button type="button" 
                                @click="showConfirmModal = true"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 active:scale-[0.98] shadow-lg shadow-emerald-700/20 hover:shadow-emerald-700/30 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save Profile Photo</span>
                        </button>
                    </div>
                @endif

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
                         x-show="showConfirmModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden p-6 sm:p-7 space-y-5">
                        
                        <!-- Header / Icon -->
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

                        <!-- Summary Preview Box -->
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-700/60 space-y-2.5">
                            @if($isAdminOrSuperAdmin)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Updated Name:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="formName || '{{ $user->name }}'"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Official Email:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="formEmail || '{{ $user->email }}'"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Assigned Schedule:</span>
                                    <span :class="formSchedule === 'No Schedule Set' ? 'text-red-600 dark:text-red-400' : 'text-slate-800 dark:text-slate-200'" class="font-bold truncate max-w-[200px]" x-text="formSchedule"></span>
                                </div>
                            @else
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Name:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $user->name }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Avatar Upload:</span>
                                <template x-if="hasAvatar()">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-600 dark:text-emerald-400">
                                        <template x-if="avatarPreview">
                                            <img :src="avatarPreview" class="w-5 h-5 rounded-full object-cover ring-1 ring-emerald-500/50">
                                        </template>
                                        <span>New Photo Selected</span>
                                    </div>
                                </template>
                                <template x-if="!hasAvatar()">
                                    <span class="font-medium text-slate-400 dark:text-slate-500">Unchanged</span>
                                </template>
                            </div>
                            @if(!$isAdminOrSuperAdmin)
                                <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">Administrative Fields:</span>
                                    <span class="font-medium text-slate-500 dark:text-slate-400">Locked & Unchanged</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
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
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-700/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50">
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

        <!-- CARD 2: Security & Password -->
        <div class="bg-white dark:bg-slate-800/95 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 dark:shadow-slate-950/40 border border-slate-200/80 dark:border-slate-700/80 space-y-6">
            
            <!-- Card Header -->
            <div class="flex items-start gap-4 pb-6 border-b border-slate-100 dark:border-slate-700/60">
                <div class="w-12 h-12 rounded-2xl bg-teal-100 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Security & Password</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">Ensure your account uses a secure password (minimum 8 characters with numbers).</p>
                </div>
            </div>

            <!-- Password Form with Confirmation Modal -->
            <form id="password-update-form" x-ref="passwordForm" method="POST" action="{{ route('password.update') }}" x-data="{
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
            }" class="space-y-4">
                @csrf
                @method('put')

                <!-- Current Password -->
                <div class="space-y-1.5">
                    <label for="current_password" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Current Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="showCurrent ? 'text' : 'password'" name="current_password" id="current_password" required
                               class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-900/60 text-slate-900 dark:text-white text-sm focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                               style="text-transform: none !important; font-weight: 400 !important;">
                        <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" tabindex="-1">
                            <svg x-show="!showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showCurrent" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
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
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <input :type="showNew ? 'text' : 'password'" name="password" id="password" x-model="password" required
                               class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-900/60 text-slate-900 dark:text-white text-sm focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                               style="text-transform: none !important; font-weight: 400 !important;">
                        <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" tabindex="-1">
                            <svg x-show="!showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showNew" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>

                    <!-- Animated Strength Meter -->
                    <div class="space-y-2 pt-1">
                        <div class="flex gap-1.5">
                            <template x-for="i in 2" :key="i">
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                     :class="i <= strength ? strengthColor : 'bg-slate-200 dark:bg-slate-700'"></div>
                            </template>
                        </div>

                        <!-- Requirements Checklist -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <div class="flex items-center gap-1.5 text-[11px] font-medium" :class="hasMinLength ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path x-show="hasMinLength" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    <circle x-show="!hasMinLength" cx="12" cy="12" r="9" stroke-width="2"></circle>
                                </svg>
                                <span>8+ Characters</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-[11px] font-medium" :class="hasNumber ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500'">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Passwords do not match
                        </span>
                        <span x-show="match === true" class="text-xs font-bold text-emerald-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Passwords match!
                        </span>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" x-model="confirmPassword" required
                               class="no-uppercase normal-case font-normal w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-900/60 text-slate-900 dark:text-white text-sm focus:bg-white dark:focus:bg-slate-900 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/20 transition-all"
                               :class="{'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20': match === false, 'border-emerald-400 focus:border-emerald-500 focus:ring-emerald-500/20': match === true}"
                               style="text-transform: none !important; font-weight: 400 !important;">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" tabindex="-1">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Update Password Trigger Button -->
                <div class="pt-3">
                    <button type="button" 
                            @click="if (hasMinLength && hasNumber && match) showPassModal = true"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-sm text-white focus:outline-none transition-all cursor-pointer shadow-lg disabled:cursor-not-allowed disabled:opacity-50"
                            :class="(!hasMinLength || !hasNumber || !match) ? 'bg-slate-400 dark:bg-slate-700 shadow-none' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-emerald-700/20 active:scale-[0.98]'"
                            :disabled="!hasMinLength || !hasNumber || !match">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Update Password</span>
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
                         x-show="showPassModal"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden p-6 sm:p-7 space-y-5">
                        
                        <!-- Header / Icon -->
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

                        <!-- Action Buttons -->
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
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-xs font-bold bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-md shadow-emerald-700/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50">
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
            window.dispatchEvent(new CustomEvent('admin-schedule-updated', { detail: { schedule: val } }));
        },

        updateSchedule() {
            const sched = this.formattedSchedule;
            this.scheduleText = sched;
            window.dispatchEvent(new CustomEvent('admin-schedule-updated', { detail: { schedule: sched } }));
        }
    };
};
</script>

@include('partials.image-cropper')
@endsection
