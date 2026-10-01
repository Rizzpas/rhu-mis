<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Announcement;
use App\Models\User;
use App\Models\AncillaryRequest;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\PreTriage;

echo "Announcements trashed: " . Announcement::onlyTrashed()->count() . "\n";
echo "Staff trashed: " . User::onlyTrashed()->count() . "\n";
echo "Ancillary archived/rejected/cancelled: " . AncillaryRequest::where(function($q) {
    $q->whereNotNull('archived_at')
      ->orWhereIn('status', ['Cancelled', 'Rejected']);
})->count() . "\n";
echo "Appointments cancelled/no_show: " . Appointment::whereIn('status', ['cancelled', 'no_show'])->count() . "\n";
echo "Prescriptions cancelled/expired: " . Prescription::whereIn('status', ['cancelled', 'expired'])->count() . "\n";
echo "PreTriages cancelled: " . PreTriage::where('status', 'cancelled')->count() . "\n";
