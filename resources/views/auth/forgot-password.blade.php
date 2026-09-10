<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHU - Silang | Forgot Password</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; letter-spacing: -0.025em; }
        [x-cloak] { display: none !important; }
    </style>
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#faf8f2] dark:!bg-[#081a1c] flex items-center justify-center min-h-screen relative font-sans text-slate-900 dark:text-white selection:bg-teal-500 selection:text-white p-4 transition-colors duration-200">
    
    {{-- Global Aura Gradient: "Frosted Jade" Background --}}
    <div class="aura-bg" aria-hidden="true">
        <div class="aura-layer-1"></div>
        <div class="aura-layer-2"></div>
        <div class="aura-layer-3"></div>
        <div class="aura-layer-4"></div>
    </div>
    
    <div class="absolute top-6 left-6 md:top-8 md:left-10 flex items-center gap-3 z-50">
        <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo" class="w-10 h-10 md:w-12 md:h-12 drop-shadow-md">
        <div class="font-black text-slate-800 dark:text-slate-100 leading-tight tracking-tight text-base md:text-lg hidden sm:block transition-colors">
            RHU <span class="text-teal-600 dark:text-teal-400">SILANG</span>
        </div>
    </div>

    <div class="absolute top-6 right-6 md:top-8 md:right-10 z-50">
        <button id="theme-toggle" type="button"
            class="relative inline-flex h-7 w-[48px] shrink-0 cursor-pointer items-center justify-start rounded-full border-2 border-transparent bg-slate-200 dark:bg-slate-700 transition-colors duration-200 ease-in-out shadow-sm">
            <span class="sr-only">Toggle theme</span>
            <span
                class="pointer-events-none relative inline-flex h-5 w-5 transform items-center justify-center rounded-full bg-white dark:bg-slate-900 shadow ring-0 transition duration-200 ease-in-out translate-x-0.5 dark:translate-x-[22px]">
                <svg id="theme-toggle-dark-icon" class="hidden w-3.5 h-3.5 text-slate-700"
                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="hidden w-3.5 h-3.5 text-amber-400"
                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                        fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
            </span>
        </button>
    </div>

    <div class="w-full max-w-[900px] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-teal-900/10 dark:shadow-teal-900/30 overflow-hidden flex flex-col md:flex-row m-4 z-10 border border-slate-100 dark:border-slate-700 transition-colors duration-200">
        
        <!-- Left Banner -->
        <div class="w-full md:w-5/12 bg-gradient-to-br from-teal-600 to-emerald-800 dark:from-teal-800 dark:to-slate-900 p-10 text-white flex flex-col justify-center relative overflow-hidden hidden md:flex">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-teal-900/40 blur-3xl"></div>
            
            <div class="relative z-10 text-center flex flex-col items-center">
                <div class="w-24 h-24 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center p-2 mb-8 border border-white/20 shadow-xl">
                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight mb-4 leading-tight">
                    Reset your <br/>
                    <span class="text-teal-200 dark:text-teal-400">Password</span>
                </h1>
                <p class="text-teal-50 dark:text-slate-300 text-sm leading-relaxed opacity-90 max-w-xs mx-auto transition-colors">
                    Follow the instructions to regain access to your account securely.
                </p>
            </div>
            
            <div class="absolute bottom-6 left-0 w-full text-center z-10 text-xs text-teal-200 dark:text-teal-500 font-medium opacity-80">
                &copy; {{ date('Y') }} RHU Silang, Cavite.
            </div>
        </div>

        <!-- Right Form Area -->
        <div class="w-full md:w-7/12 p-8 md:p-12 bg-white dark:bg-slate-800 relative flex flex-col justify-center transition-colors duration-200"
             x-data="{ 
                 step: 1, 
                 email: '', 
                 otp: '', 
                 password: '', 
                 password_confirmation: '', 
                 loading: false, 
                 error: '', 
                 message: '',
                 get strength() {
                    let score = 0;
                    if (this.password.length >= 8) score++;
                    if (/[A-Z]/.test(this.password)) score++;
                    if (/[0-9]/.test(this.password)) score++;
                    if (/[^A-Za-z0-9]/.test(this.password)) score++;
                    return score;
                 },
                 get strengthLabel() {
                    if (this.password.length === 0) return '';
                    if (this.strength <= 1) return 'Weak';
                    if (this.strength === 2 || this.strength === 3) return 'Fair';
                    return 'Strong';
                 },
                 get strengthColor() {
                    if (this.password.length === 0) return 'bg-gray-200 dark:bg-gray-700';
                    if (this.strength <= 1) return 'bg-red-500';
                    if (this.strength === 2 || this.strength === 3) return 'bg-yellow-500';
                    return 'bg-green-500';
                 },
                 get match() {
                    if (this.password_confirmation.length === 0) return null;
                    return this.password === this.password_confirmation;
                 },
                 sendOtp() {
                     this.loading = true;
                     this.error = '';
                     this.message = '';
                     fetch('{{ route('staff.password.send-otp') }}', {
                         method: 'POST',
                         headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                         body: JSON.stringify({ email: this.email })
                     })
                     .then(res => res.json())
                     .then(data => {
                         this.loading = false;
                         if(data.success) {
                             this.step = 2;
                             this.message = 'OTP sent to your email.';
                         } else {
                             this.error = data.message || 'Error sending OTP.';
                         }
                     })
                     .catch(err => {
                         this.loading = false;
                         this.error = 'Failed to send OTP. Invalid email.';
                     });
                 }
             }">
            
            <div class="md:hidden flex justify-center mb-6">
                <div class="w-20 h-20 bg-teal-50 dark:bg-slate-700 rounded-full flex items-center justify-center p-2 border border-teal-100 dark:border-slate-600 shadow-sm transition-colors">
                    <svg class="w-10 h-10 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <!-- Step 1: Request OTP -->
            <div x-show="step === 1" x-transition>
                <div class="mb-8 md:text-left text-center">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-1 transition-colors">Forgot Password?</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium transition-colors">Enter your email address and we'll send you an OTP.</p>
                </div>

                <form @submit.prevent="sendOtp" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 transition-colors">Email Address</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-teal-600 dark:group-focus-within:text-teal-400">
                                <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-600 dark:group-focus-within:text-teal-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input type="email" x-model="email" required autofocus placeholder="name@example.com"
                                class="block w-full pl-11 pr-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm shadow-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all bg-slate-50 dark:bg-slate-700 focus:bg-white dark:focus:bg-slate-800 text-slate-800 dark:text-white font-medium">
                        </div>
                        <p x-show="error" x-text="error" class="text-red-500 text-xs mt-1.5 block font-bold"></p>
                    </div>

                    <button type="submit" :disabled="loading" class="w-full relative flex justify-center items-center py-3.5 px-4 border border-transparent text-sm font-extrabold rounded-xl text-white bg-teal-600 hover:bg-teal-700 dark:bg-teal-700 dark:hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-lg shadow-teal-500/30 hover:shadow-xl hover:shadow-teal-500/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wide mt-2">
                        <span x-text="loading ? 'Sending...' : 'Send OTP'"></span>
                        <svg x-show="!loading" class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                    
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                            <svg class="mr-1.5 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Login
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Step 2: Reset Password -->
            <div x-show="step === 2" style="display: none;" x-transition>
                <div class="mb-8 md:text-left text-center">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-1 transition-colors">Reset Password</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm font-medium transition-colors" x-text="message"></p>
                </div>

                <form method="POST" action="{{ route('staff.password.reset') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="email" :value="email">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">6-Digit OTP</label>
                        <input type="text" name="otp" x-model="otp" required maxlength="6" placeholder="••••••"
                            class="block w-full py-3 text-center tracking-widest font-mono text-xl border border-slate-200 dark:border-slate-600 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-bold">
                        @error('otp') <span class="text-red-500 text-xs mt-1.5 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-1.5">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">New Password</label>
                            <span x-text="strengthLabel" class="text-xs font-bold" :class="{
                                'text-red-500': strength <= 1,
                                'text-yellow-500': strength === 2 || strength === 3,
                                'text-green-500': strength === 4
                            }"></span>
                        </div>
                        <input type="password" name="password" x-model="password" required placeholder="••••••••"
                            class="block w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm shadow-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50 dark:bg-slate-700 focus:bg-white dark:focus:bg-slate-800 text-slate-800 dark:text-white font-medium">
                        
                        <div class="h-1.5 w-full bg-slate-200 dark:bg-slate-600 rounded-full mt-2 overflow-hidden flex">
                            <div class="h-full transition-all duration-300" :class="strengthColor" :style="'width: ' + (password.length === 0 ? 0 : Math.max(25, strength * 25)) + '%'"></div>
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1.5 block font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-1.5">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Confirm Password</label>
                            <span x-show="match === false" class="text-xs font-bold text-red-500">Passwords do not match</span>
                            <span x-show="match === true" class="text-xs font-bold text-green-500">Passwords match!</span>
                        </div>
                        <input type="password" name="password_confirmation" x-model="password_confirmation" required placeholder="••••••••"
                            class="block w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm shadow-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 bg-slate-50 dark:bg-slate-700 focus:bg-white dark:focus:bg-slate-800 text-slate-800 dark:text-white font-medium">
                    </div>

                    <button type="submit" :disabled="strength < 4 || match !== true || otp.length < 6" class="w-full relative flex justify-center items-center py-3.5 px-4 border border-transparent text-sm font-extrabold rounded-xl text-white bg-teal-600 hover:bg-teal-700 dark:bg-teal-700 dark:hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-lg shadow-teal-500/30 disabled:opacity-50 disabled:shadow-none hover:shadow-xl hover:shadow-teal-500/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wide mt-4">
                        Reset Password
                    </button>
                    
                    <div class="mt-4 text-center">
                        <button type="button" @click="step = 1; otp = ''; password = ''; password_confirmation = ''; message = '';" class="inline-flex items-center text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-teal-600 transition-colors">
                            Change Email Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (themeToggleDarkIcon && themeToggleLightIcon) {
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            var themeToggleBtn = document.getElementById('theme-toggle');

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    themeToggleDarkIcon.classList.toggle('hidden');
                    themeToggleLightIcon.classList.toggle('hidden');

                    if (localStorage.getItem('color-theme')) {
                        if (localStorage.getItem('color-theme') === 'light') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                    } else {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
