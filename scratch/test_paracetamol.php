<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\MedicalCase;
use Illuminate\Http\Request;

auth()->login(User::first());

$case7 = MedicalCase::with('patient')->where('prescription', 'like', '%Paracetamol%')->first();
if ($case7) {
    echo "Found case with Paracetamol: Case #{$case7->id}, Patient: {$case7->patient->full_name}\n";
    $req = Request::create(route('admin.patients.print', ['patient' => $case7->patient, 'cases' => $case7->id]), 'GET');
    $resp = app()->handle($req);
    $content = $resp->getContent();
    echo "Contains table header 'Medication & Formulation': " . (str_contains($content, 'Medication & Formulation') ? 'YES' : 'NO') . "\n";
    echo "Contains Paracetamol: " . (str_contains($content, 'Paracetamol') ? 'YES' : 'NO') . "\n";
    echo "Contains raw JSON: " . (str_contains($content, '[{"medicine"') ? 'YES (BAD)' : 'NO (GOOD)') . "\n";
} else {
    echo "No case found with Paracetamol.\n";
}
