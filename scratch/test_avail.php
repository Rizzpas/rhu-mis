<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$doc = App\Models\User::where('role', 'pedia_doctor')->first();
echo "Doctor: " . ($doc ? $doc->name : 'None') . " (ID: " . ($doc ? $doc->id : 'None') . ")\n";
echo "PractitionerSchedules count: " . ($doc ? $doc->practitionerSchedules()->count() : 0) . "\n";

$allSchedules = App\Models\PractitionerSchedule::all();
echo "All schedules in DB:\n";
foreach ($allSchedules as $s) {
    $u = App\Models\User::find($s->user_id);
    echo "  User #{$s->user_id} (" . ($u ? $u->name : 'Unknown') . " - " . ($u ? $u->role : 'Unknown') . "): {$s->day_of_week} {$s->time_in} - {$s->time_out}\n";
}
