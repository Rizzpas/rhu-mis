<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RHU - Silang | Queue Slip #{{ $visit->queue_number }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { margin: 0; padding: 0; font-family: monospace; }
            .no-print { display: none; }
            .print-only { display: block; }
            /* Standard 80mm thermal paper width usually maps to about 300px roughly */
            .slip-container { width: 100%; max-width: 300px; margin: 0 auto; box-shadow: none; border: none; }
        }
        @media screen {
            body { background-color: #f3f4f6; display: flex; justify-content: center; padding: 2rem; font-family: 'Inter', sans-serif; }
            .slip-container { background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 100%; max-width: 320px; }
        }
    </style>
</head>
<body>

    <div class="slip-container text-center">
        <!-- Header -->
        <div class="mb-4 pb-4 border-b border-gray-400 border-dashed">
            <h1 class="font-bold text-lg leading-tight uppercase">RHU Dasmariñas</h1>
            <p class="text-xs text-gray-600 dark:text-gray-400">Patient Registration Slip</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $visit->created_at->format('M d, Y h:i A') }}</p>
        </div>

        <!-- Queue Number -->
        <div class="my-6">
            <p class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Queue Number</p>
            <p class="text-5xl font-black text-gray-900 dark:text-white tracking-tighter">{{ $visit->queue_number }}</p>
            <p class="text-xs font-bold mt-2 bg-gray-200 inline-block px-3 py-1 rounded-full uppercase">{{ $visit->patient->classification ?? 'General' }}</p>
        </div>

        <!-- Patient Info -->
        <div class="mb-6 pb-4 border-b border-gray-400 border-dashed text-left">
            <!-- Patient Name removed per request -->
            <div>
                <span class="text-xs text-gray-500 dark:text-gray-400 block">Assigned To</span>
                @if($visit->doctor_id)
                    <span class="font-bold text-sm">Dr. {{ $visit->doctor->name }}</span>
                @elseif($visit->nurse_id)
                    <span class="font-bold text-sm">Nurse {{ $visit->nurse->name }}</span>
                @else
                    <span class="font-bold text-sm italic text-gray-400">Pending Triage</span>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="text-xs text-gray-500 dark:text-gray-400 italic">
            <p>Please wait for your queue number to be called.</p>
            <p class="mt-2 text-center">*** End of Slip ***</p>
        </div>

        <!-- Screen-only Actions -->
        <div class="mt-8 gap-3 flex flex-col no-print">
            <!-- Printing disabled per user request -->
            <!-- <button onclick="window.print()" class="w-full bg-slate-800 text-white py-2 rounded font-bold hover:bg-slate-700">🖨️ Print Slip</button> -->
            <a href="{{ route('frontdesk.registration.index') }}" class="w-full bg-teal-100 text-teal-800 py-2 rounded font-bold text-center hover:bg-teal-200">← Back to Registration</a>
        </div>
    </div>

</body>
</html>
