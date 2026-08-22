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
@endphp

@extends($layout)

@section('header', 'Profile Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Profile Information -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Profile Information</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Update your account's profile information and email address.</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <div class="space-y-4">
                    <!-- Avatar Upload -->
                    <div x-data="{ 
                        photoName: null, 
                        photoPreview: null,
                        isDragging: false,
                        handleAvatarFile(file) {
                            if (!file || !file.type.startsWith('image/')) return;
                            const input = document.getElementById('profile_avatar_input');
                            $store.imageCropper.open(file, {
                                aspectRatio: 1,
                                circular: true,
                                subtitle: 'Square crop (1:1) — Profile Avatar',
                                onApply: (blob, previewUrl) => {
                                    this.photoPreview = previewUrl;
                                    this.photoName = file.name;
                                    setCroppedFile(input, blob, file.name || 'avatar.jpg');
                                }
                            });
                        }
                    }">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Avatar</label>
                        <div class="flex items-center space-x-6">
                            <!-- Avatar Preview with Hover Camera Overlay -->
                            <div @click="document.getElementById('profile_avatar_input').click()" 
                                 class="h-20 w-20 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 relative border-2 border-teal-500 shadow-md cursor-pointer group flex-shrink-0">
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="h-full w-full object-cover" alt="Avatar Preview">
                                </template>
                                <template x-if="!photoPreview">
                                    @if(auth()->user()->avatar_url)
                                        <img class="h-full w-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="Avatar">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-400 font-bold text-2xl">
                                            {{ auth()->user()->initials }}
                                        </div>
                                    @endif
                                </template>
                                <div class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                            </div>

                            <!-- Drag & Drop Dropzone for Avatar -->
                            <div class="flex-1">
                                <input type="file" id="profile_avatar_input" name="avatar" accept="image/*" class="hidden"
                                    @change="if ($event.target.files.length) handleAvatarFile($event.target.files[0])">
                                
                                <div @dragover.prevent="isDragging = true"
                                     @dragleave.prevent="isDragging = false"
                                     @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) handleAvatarFile($event.dataTransfer.files[0])"
                                     @click="document.getElementById('profile_avatar_input').click()"
                                     :class="isDragging ? 'border-teal-500 bg-teal-50/50 dark:bg-teal-950/20 ring-2 ring-teal-500/20' : 'border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-750'"
                                     class="border-2 border-dashed rounded-xl px-4 py-3 text-center cursor-pointer transition-all flex items-center justify-center gap-3">
                                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <div class="text-left">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200" x-text="photoName || 'Drag & drop avatar here, or browse'"></p>
                                        <p class="text-[10px] text-slate-400">1:1 square crop • Max 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('avatar') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 outline-none shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ 
                        originalEmail: '{{ $user->email }}',
                        currentEmail: '{{ old('email', $user->email) }}',
                        otpSent: false,
                        otpVerified: false,
                        otp: '',
                        error: '',
                        message: '',
                        loading: false,
                        sendOtp() {
                            if(this.currentEmail === this.originalEmail) return;
                            this.loading = true;
                            this.error = '';
                            this.message = '';
                            fetch('{{ route('profile.email-otp.send') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ email: this.currentEmail })
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.loading = false;
                                if(data.success) {
                                    this.otpSent = true;
                                    this.message = 'OTP sent to your new email. Please check your inbox.';
                                } else {
                                    this.error = data.message || 'Error sending OTP.';
                                }
                            })
                            .catch(err => {
                                this.loading = false;
                                this.error = 'Failed to send OTP. Check console.';
                                console.error(err);
                            });
                        },
                        verifyOtp() {
                            if(!this.otp) return;
                            this.loading = true;
                            this.error = '';
                            this.message = '';
                            fetch('{{ route('profile.email-otp.verify') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ email: this.currentEmail, otp: this.otp })
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.loading = false;
                                if(data.success) {
                                    this.otpVerified = true;
                                    this.message = 'Email verified successfully! You can now save changes.';
                                } else {
                                    this.error = data.message || 'Invalid OTP.';
                                }
                            })
                            .catch(err => {
                                this.loading = false;
                                this.error = 'Failed to verify OTP.';
                            });
                        }
                    }">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                        <div class="flex gap-2 items-start mt-1">
                            <input type="email" name="email" id="email" x-model="currentEmail" :readonly="otpSent && !otpVerified" class="block w-full rounded-md border border-gray-300 px-3 py-2 outline-none shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 transition-colors dark:text-white" :class="{'bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed': otpSent && !otpVerified}">
                            <template x-if="currentEmail !== originalEmail && !otpVerified && !otpSent">
                                <button type="button" @click="sendOtp" :disabled="loading" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold rounded-md shadow-sm transition-colors whitespace-nowrap">
                                    <span x-text="loading ? 'Sending...' : 'Verify Email'"></span>
                                </button>
                            </template>
                            <template x-if="otpVerified">
                                <span class="px-4 py-2 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 font-bold text-sm rounded-md border border-green-200 dark:border-green-800 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Verified
                                </span>
                            </template>
                        </div>
                        
                        <!-- OTP Input Area -->
                        <div x-show="otpSent && !otpVerified" x-transition style="display: none;" class="mt-3 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700 flex flex-col gap-2">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Enter 6-digit OTP</label>
                            <div class="flex gap-2">
                                <input type="text" x-model="otp" maxlength="6" class="block w-full rounded-md border border-gray-300 px-3 py-2 outline-none shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 text-center tracking-widest font-mono text-lg dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="••••••">
                                <button type="button" @click="verifyOtp" :disabled="loading || otp.length < 6" class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-md shadow-sm disabled:opacity-50 transition-colors">
                                    <span x-text="loading ? '...' : 'Confirm'"></span>
                                </button>
                            </div>
                            <button type="button" @click="otpSent = false; otp = ''; error = ''; message = '';" class="text-xs text-slate-500 hover:text-teal-600 text-left mt-1 underline">Change email / resend</button>
                        </div>

                        <!-- Feedback Messages -->
                        <p x-show="error" x-text="error" style="display: none;" class="mt-2 text-sm text-red-600 font-semibold"></p>
                        <p x-show="message" x-text="message" style="display: none;" :class="otpVerified ? 'text-green-600' : 'text-teal-600'" class="mt-2 text-sm font-semibold"></p>
                        @error('email') <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Password -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Update Password</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Ensure your account is using a long, random password to stay secure.</p>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('password.update') }}" x-data="{
                password: '',
                confirmPassword: '',
                showCurrent: false,
                showNew: false,
                showConfirm: false,
                get hasMinLength() { return this.password.length >= 8; },
                get hasUppercase() { return /[A-Z]/.test(this.password); },
                get hasNumber() { return /[0-9]/.test(this.password); },
                get hasSpecial() { return /[^A-Za-z0-9]/.test(this.password); },
                get strength() {
                    let score = 0;
                    if (this.hasMinLength) score++;
                    if (this.hasUppercase) score++;
                    if (this.hasNumber) score++;
                    if (this.hasSpecial) score++;
                    return score;
                },
                get strengthLabel() {
                    if (this.password.length === 0) return '';
                    if (this.strength <= 1) return 'Weak';
                    if (this.strength === 2) return 'Fair';
                    if (this.strength === 3) return 'Good';
                    return 'Strong';
                },
                get strengthColor() {
                    if (this.password.length === 0) return 'bg-gray-200 dark:bg-gray-700';
                    if (this.strength <= 1) return 'bg-red-500';
                    if (this.strength === 2) return 'bg-yellow-500';
                    if (this.strength === 3) return 'bg-blue-500';
                    return 'bg-green-500';
                },
                get strengthTextColor() {
                    if (this.strength <= 1) return 'text-red-500';
                    if (this.strength === 2) return 'text-yellow-500';
                    if (this.strength === 3) return 'text-blue-500';
                    return 'text-green-500';
                },
                get match() {
                    if (this.confirmPassword.length === 0) return null;
                    return this.password === this.confirmPassword;
                }
            }">
                @csrf
                @method('put')

                <div class="space-y-5">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                        <div class="relative mt-1">
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" id="current_password" class="block w-full rounded-md border border-gray-300 px-3 py-2 outline-none shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-10">
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" tabindex="-1">
                                <svg x-show="!showCurrent" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showCurrent" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <div class="flex justify-between items-end">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                            <span x-text="strengthLabel" class="text-xs font-bold" :class="strengthTextColor"></span>
                        </div>
                        <div class="relative mt-1">
                            <input :type="showNew ? 'text' : 'password'" name="password" id="password" x-model="password" class="block w-full rounded-md border border-gray-300 px-3 py-2 outline-none shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-10">
                            <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" tabindex="-1">
                                <svg x-show="!showNew" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showNew" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>

                        <!-- Strength Meter Bar -->
                        <div class="mt-3 space-y-2">
                            <div class="flex gap-1.5">
                                <template x-for="i in 4" :key="i">
                                    <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                         :class="i <= strength ? strengthColor : 'bg-gray-200 dark:bg-gray-700'"></div>
                                </template>
                            </div>

                            <!-- Password Criteria Checklist -->
                            <div x-show="password.length > 0" x-transition class="grid grid-cols-2 gap-x-4 gap-y-1 mt-2">
                                <div class="flex items-center gap-1.5 text-xs" :class="hasMinLength ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="hasMinLength" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        <path x-show="!hasMinLength" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                    </svg>
                                    <span class="font-medium">8+ characters</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs" :class="hasUppercase ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="hasUppercase" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        <path x-show="!hasUppercase" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                    </svg>
                                    <span class="font-medium">Uppercase letter</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs" :class="hasNumber ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="hasNumber" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        <path x-show="!hasNumber" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                    </svg>
                                    <span class="font-medium">Number (0-9)</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-xs" :class="hasSpecial ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="hasSpecial" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        <path x-show="!hasSpecial" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                    </svg>
                                    <span class="font-medium">Special char (!@#$)</span>
                                </div>
                            </div>
                        </div>
                        @error('password', 'updatePassword') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <div class="flex justify-between items-end">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                            <span x-show="match === false" class="text-xs font-bold text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Passwords do not match
                            </span>
                            <span x-show="match === true" class="text-xs font-bold text-green-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Passwords match!
                            </span>
                        </div>
                        <div class="relative mt-1">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" x-model="confirmPassword" class="block w-full rounded-md border border-gray-300 px-3 py-2 outline-none shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white pr-10" :class="{'border-red-300 focus:border-red-500 focus:ring-red-500': match === false, 'border-green-300 focus:border-green-500 focus:ring-green-500': match === true}">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" tabindex="-1">
                                <svg x-show="!showConfirm" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        :class="(!hasMinLength || !hasUppercase || !hasNumber || !hasSpecial || !match) ? 'bg-gray-400 hover:bg-gray-400' : 'bg-teal-600 hover:bg-teal-700'"
                        :disabled="!hasMinLength || !hasUppercase || !hasNumber || !hasSpecial || !match">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('partials.image-cropper')
@endsection
