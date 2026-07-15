<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHU - Silang | System Portal Login</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <!-- Modern sans-serif font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    <!-- Dark Mode Init Script -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Alpine.js for show/hide functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-slate-50 dark:bg-slate-900 flex items-center justify-center min-h-screen relative font-sans text-slate-900 dark:text-white selection:bg-teal-500 selection:text-white p-4 transition-colors duration-200">
    
    @include('partials.skeleton-login')

    <!-- Logo in upper left -->
    <div class="absolute top-6 left-6 md:top-8 md:left-10 flex items-center gap-3 z-50">
        <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Logo" class="w-10 h-10 md:w-12 md:h-12 drop-shadow-md">
        <div class="font-black text-slate-800 dark:text-slate-100 leading-tight tracking-tight text-base md:text-lg hidden sm:block transition-colors">
            RHU <span class="text-teal-600 dark:text-teal-400">SILANG</span>
        </div>
    </div>

    <!-- Login Container -->
    <div class="w-full max-w-[900px] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl shadow-teal-900/10 dark:shadow-teal-900/30 overflow-hidden flex flex-col md:flex-row m-4 z-10 border border-slate-100 dark:border-slate-700 transition-colors duration-200">
        
        <!-- Left Banner (Branding) -->
        <div class="w-full md:w-5/12 bg-gradient-to-br from-teal-600 to-emerald-800 dark:from-teal-800 dark:to-slate-900 p-10 text-white flex flex-col justify-center relative overflow-hidden hidden md:flex">
            <!-- decorative circles -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-teal-900/40 blur-3xl"></div>
            
            <div class="relative z-10 text-center flex flex-col items-center">
                <div class="w-24 h-24 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center p-2 mb-8 border border-white/20 shadow-xl">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Seal" class="w-full h-full object-contain drop-shadow-md">
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight mb-4 leading-tight">
                    Welcome to the <br/>
                    <span class="text-teal-200 dark:text-teal-400">System Portal</span>
                </h1>
                <p class="text-teal-50 dark:text-slate-300 text-sm leading-relaxed opacity-90 max-w-xs mx-auto transition-colors">
                    Secure access for Administrators, Doctors, Nurses, and Laboratory Staff of the Rural Health Unit of Silang, Cavite.
                </p>
            </div>
            
            <div class="absolute bottom-6 left-0 w-full text-center z-10 text-xs text-teal-200 dark:text-teal-500 font-medium opacity-80">
                &copy; {{ date('Y') }} RHU Silang, Cavite.
            </div>
        </div>

        <!-- Right Form Area -->
        <div class="w-full md:w-7/12 p-8 md:p-12 bg-white dark:bg-slate-800 relative flex flex-col justify-center transition-colors duration-200">
            
            <!-- Mobile Logo (shows only on small screens inside form) -->
            <div class="md:hidden flex justify-center mb-6">
                <div class="w-20 h-20 bg-teal-50 dark:bg-slate-700 rounded-full flex items-center justify-center p-2 border border-teal-100 dark:border-slate-600 shadow-sm transition-colors">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="RHU Seal" class="w-full h-full object-contain">
                </div>
            </div>

            <div class="mb-8 md:text-left text-center">
                <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-1 transition-colors">Sign In to your account</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium transition-colors">Enter your credentials to securely access the portal.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 transition-colors">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-teal-600 dark:group-focus-within:text-teal-400">
                            <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-600 dark:group-focus-within:text-teal-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" required autofocus placeholder="name@example.com"
                            class="block w-full pl-11 pr-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm shadow-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all bg-slate-50 dark:bg-slate-700 focus:bg-white dark:focus:bg-slate-800 text-slate-800 dark:text-white font-medium">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1.5 block font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5 transition-colors">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-teal-600 dark:group-focus-within:text-teal-400">
                            <svg class="h-5 w-5 text-slate-400 dark:text-slate-500 group-focus-within:text-teal-600 dark:group-focus-within:text-teal-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" name="password" id="password" required placeholder="••••••••"
                            class="block w-full pl-11 pr-12 py-3 border border-slate-200 dark:border-slate-600 rounded-xl text-sm shadow-sm placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all bg-slate-50 dark:bg-slate-700 focus:bg-white dark:focus:bg-slate-800 text-slate-800 dark:text-white font-medium">
                        
                        <!-- Toggle Password Button -->
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 dark:text-slate-500 hover:text-teal-600 dark:hover:text-teal-400 focus:outline-none transition-colors">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1.5 block font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Forgot Password -->
                <div class="flex items-center justify-end pt-1">
                    <a href="{{ route('staff.password.request') }}" class="text-xs text-teal-600 dark:text-teal-400 hover:text-teal-800 dark:hover:text-teal-300 font-bold text-right transition-colors underline-offset-2 hover:underline">
                        Forgot your password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full relative flex justify-center items-center py-3.5 px-4 border border-transparent text-sm font-extrabold rounded-xl text-white bg-teal-600 hover:bg-teal-700 dark:bg-teal-700 dark:hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-lg shadow-teal-500/30 hover:shadow-xl hover:shadow-teal-500/40 transform hover:-translate-y-0.5 transition-all duration-200 uppercase tracking-wide mt-2">
                    Login
                    <svg class="ml-2 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
                
                <!-- Back to home -->
                <div class="mt-6 text-center">
                    <a href="/" class="inline-flex items-center text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                        <svg class="mr-1.5 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Home
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>