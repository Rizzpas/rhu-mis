<?php

// Quick diagnostic script for dashboard KPIs
$total = \App\Models\Consultation::count();
$withStartTime = \App\Models\Consultation::whereNotNull('consultation_start_time')->count();
$followUpNeeded = \App\Models\Consultation::where('is_followup_needed', true)->count();
$thisMonth = \App\Models\Consultation::where('created_at', '>=', now()->startOfMonth())->count();

echo "Total Consultations: {$total}\n";
echo "With consultation_start_time: {$withStartTime}\n";
echo "Follow-Up Needed: {$followUpNeeded}\n";
echo "This Month: {$thisMonth}\n";

if ($withStartTime > 0) {
    $avg = \App\Models\Consultation::whereNotNull('consultation_start_time')
        ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait')
        ->value('avg_wait');
    echo "Avg Wait (min): {$avg}\n";
}
