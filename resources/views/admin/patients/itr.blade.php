<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITR - {{ $patient->full_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #fff;
            color: #000;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        @media print {
            body { background: white !important; }
            .print-hidden { display: none !important; }
            .page-break { page-break-before: always; }
            .avoid-break { page-break-inside: avoid; }
            @page { margin: 1cm; size: auto; }
        }
        .itr-container {
            max-w-4xl;
            margin: 0 auto;
            padding: 2rem;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            position: relative;
        }
        .header img {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
        }
        .section-title {
            background-color: #f3f4f6 !important;
            padding: 0.5rem;
            font-weight: bold;
            border: 1px solid #d1d5db;
            margin-top: 1.5rem;
            text-transform: uppercase;
            font-size: 0.875rem;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #d1d5db;
            padding: 1rem;
        }
        .info-item {
            font-size: 0.875rem;
        }
        .info-label {
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .case-block {
            border: 1px solid #000;
            margin-bottom: 2rem;
        }
        .case-header {
            background-color: #e5e7eb !important;
            padding: 0.5rem 1rem;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .case-body {
            padding: 1rem;
        }
        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
            background: #f9fafb !important;
            padding: 0.5rem;
            border: 1px dashed #9ca3af;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Print Action Bar -->
    <div class="print-hidden bg-gray-100 p-4 flex justify-between items-center border-b">
        <div>
            <p class="font-bold">Individual Treatment Record (ITR)</p>
            <p class="text-sm text-gray-600">Please use the browser print dialog to save as PDF or print.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.close()" class="px-4 py-2 bg-gray-300 text-gray-700 font-bold rounded">Close</button>
            <button onclick="window.print()" class="px-4 py-2 bg-teal-600 text-white font-bold rounded shadow">Print Document</button>
        </div>
    </div>

    <div class="itr-container">
        
        <!-- Header -->
        <div class="header">
            <h1 class="text-2xl font-black uppercase">Rural Health Unit - Silang</h1>
            <p class="text-sm font-bold uppercase tracking-widest text-gray-600">Individual Treatment Record</p>
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        </div>

        <!-- Patient Demographics -->
        <div class="section-title">Patient Demographics</div>
        <div class="info-grid">
            <div class="info-item col-span-2">
                <div class="info-label">Full Name</div>
                <div>{{ $patient->full_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Patient ID</div>
                <div class="font-mono">{{ $patient->patient_id }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Classification</div>
                <div>{{ $patient->classification }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Date of Birth</div>
                <div>{{ \Carbon\Carbon::parse($patient->dob)->format('F d, Y') }} ({{ \Carbon\Carbon::parse($patient->dob)->age }} yrs)</div>
            </div>
            <div class="info-item">
                <div class="info-label">Sex</div>
                <div>{{ $patient->sex }}</div>
            </div>
            <div class="info-item col-span-2">
                <div class="info-label">Address</div>
                <div>{{ $patient->address ?: 'N/A' }}</div>
            </div>
        </div>

        <!-- Cases Loop -->
        @forelse($cases as $case)
            <div class="case-block avoid-break">
                <div class="case-header">
                    <span>Date: {{ $case->created_at->format('F d, Y') }}</span>
                    <span>Type: {{ ucfirst($case->type) }}</span>
                </div>
                <div class="case-body">
                    
                    @if($case->blood_pressure || $case->temperature || $case->heart_rate || $case->respiratory_rate)
                    <div class="vitals-grid">
                        <div><span class="info-label">BP:</span> {{ $case->blood_pressure ?: '--' }}</div>
                        <div><span class="info-label">Temp:</span> {{ $case->temperature ?: '--' }} °C</div>
                        <div><span class="info-label">HR:</span> {{ $case->heart_rate ?: '--' }} bpm</div>
                        <div><span class="info-label">RR:</span> {{ $case->respiratory_rate ?: '--' }} cpm</div>
                        <div><span class="info-label">Height:</span> {{ $case->height ?: '--' }} cm</div>
                        <div><span class="info-label">Weight:</span> {{ $case->weight ?: '--' }} kg</div>
                        <div><span class="info-label">SpO2:</span> {{ $case->spo2 ?: '--' }} %</div>
                    </div>
                    @endif

                    <div class="mb-4">
                        <div class="info-label">Chief Complaint / Symptoms</div>
                        <p class="text-sm mt-1">{{ $case->complaint ?: 'None recorded.' }}</p>
                    </div>

                    @if($case->diagnosis)
                    <div class="mb-4">
                        <div class="info-label">Diagnosis / Clinical Impression</div>
                        <p class="text-sm mt-1 font-bold">{{ $case->diagnosis }}</p>
                    </div>
                    @endif

                    @if($case->prescription || $case->treatment_plan)
                    <div class="mb-4">
                        <div class="info-label">Treatment Plan & Prescriptions</div>
                        <p class="text-sm mt-1">{{ $case->prescription ?: 'No prescription recorded.' }}</p>
                        @if($case->treatment_plan)
                            <p class="text-sm mt-2">{{ $case->treatment_plan }}</p>
                        @endif
                    </div>
                    @endif

                    @if($case->lab_requests)
                    <div class="mb-4">
                        <div class="info-label">Laboratory / Diagnostics Requested</div>
                        <p class="text-sm mt-1">{{ $case->lab_requests }}</p>
                    </div>
                    @endif

                    <div class="mt-8 flex justify-end">
                        <div class="text-center w-64">
                            <div class="border-b border-black mb-1 h-8"></div>
                            <div class="info-label">{{ $case->doctor ? $case->doctor->formatted_name : 'Attending Physician' }}</div>
                            <div class="text-[10px]">Signature over Printed Name</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                No clinical records found for the selected criteria.
            </div>
        @endforelse

        <div class="text-center text-xs text-gray-400 mt-8 avoid-break">
            <p>*** END OF MEDICAL RECORD ***</p>
            <p>Printed on {{ now()->format('F d, Y h:i A') }}</p>
        </div>

    </div>

</body>
</html>
