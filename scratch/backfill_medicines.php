<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PrescriptionItem;
use App\Models\Medicine;

foreach (PrescriptionItem::whereNull('medicine_id')->get() as $item) {
    $clean = trim(preg_replace('/\s*\([^)]*\)$/', '', $item->medicine_name));
    $clean = trim(preg_replace('/\s+\d+mg\b/i', '', $clean));
    $med = Medicine::where('name', $item->medicine_name)
        ->orWhere('name', 'like', $clean . '%')
        ->first();
    echo $item->id . ': ' . $item->medicine_name . ' -> ' . ($med ? $med->name . ' (id: ' . $med->id . ')' : 'NOT FOUND') . PHP_EOL;
    if ($med) {
        $item->update(['medicine_id' => $med->id]);
    }
}
