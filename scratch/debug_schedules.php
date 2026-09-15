<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AFTER FIX - Debug Schedule Status ===\n";
echo "Server Time: " . now()->format('Y-m-d H:i:s (D)') . "\n\n";

$doctors = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->get();
foreach ($doctors as $doc) {
    echo "Doctor: {$doc->name}\n";
    echo "  Status column: {$doc->status}\n";
    echo "  schedule_override: " . ($doc->schedule_override ?? 'NULL') . "\n";
    echo "  is_present: " . ($doc->is_present ? 'YES ✓' : 'NO ✗') . "\n";
    $docScheds = $doc->practitionerSchedules;
    foreach ($docScheds as $ds) {
        $isNow = ($ds->day_of_week === now()->format('D') && $ds->time_in <= now()->format('H:i:s') && $ds->time_out >= now()->format('H:i:s'));
        echo "  -> {$ds->day_of_week}: {$ds->time_in} - {$ds->time_out}" . ($isNow ? ' ← ACTIVE NOW' : '') . "\n";
    }
    echo "\n";
}

// Simulate homepage query
$now = now();
$today = $now->format('D');
$currentTime = $now->format('H:i:s');

$homepageDoctors = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])
    ->with('practitionerSchedules')
    ->where(function ($q) use ($today, $currentTime) {
        $q->whereIn('status', ['Online', 'Occupied'])
          ->orWhereHas('practitionerSchedules', function ($schedQ) use ($today, $currentTime) {
              $schedQ->where('day_of_week', $today)
                     ->where('time_in', '<=', $currentTime)
                     ->where('time_out', '>=', $currentTime);
          });
    })
    ->where(function ($q) {
        $q->whereNull('schedule_override')
          ->orWhere('schedule_override', '!=', 'manual_offline');
    })
    ->get();

echo "=== Homepage Query Results ===\n";
echo "Doctors on homepage: " . $homepageDoctors->count() . "\n";
foreach ($homepageDoctors as $d) {
    echo "  ✓ {$d->name} (status: {$d->status}, is_present: " . ($d->is_present ? 'YES' : 'NO') . ")\n";
}
