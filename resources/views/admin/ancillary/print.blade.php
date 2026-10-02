<x-print-layout 
    title="Official Diagnostic Examination Report"
    subtitle="Department of Pathology, Clinical Laboratory & Radiological Sciences"
    :period="$requests->count() > 1 ? $requests->count() . ' Diagnostic Reports' : ($requests->first()->test_name ?? 'Diagnostic Report')"
    :generatedBy="auth()->check() ? auth()->user()->name : 'Authorized Medical Personnel'"
>
    @foreach($requests as $reqIdx => $req)
        @include('partials.diagnostic-report-body', [
            'req' => $req,
            'patient' => $patient,
            'isLoopLast' => $loop->last,
        ])
    @endforeach
</x-print-layout>
