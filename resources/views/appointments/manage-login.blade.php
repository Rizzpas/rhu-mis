@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-transparent flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Manage Appointment</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Enter your details to reschedule or cancel your booking.
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100 dark:border-gray-700">
            <form action="{{ route('appointment.login') }}" method="POST" class="space-y-6">
                @csrf
                 
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Reference Number') }}</label>
                    <div class="mt-1">
                        <input id="reference_number" name="reference_number" type="text" required 
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm uppercase"
                            placeholder="APT-XXXXXXX">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email Address') }}</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" required 
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"
                            placeholder="you@gmail.com">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        {{ __('Access Appointment') }}
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <a href="{{ route('welcome') }}" class="text-sm font-medium text-teal-600 hover:text-teal-500">
                    &larr; {{ __('Back to Home') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
