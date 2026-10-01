<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SITE SETTINGS ===\n";
foreach (\App\Models\SiteSetting::all() as $s) {
    echo "{$s->key} | group: {$s->group} | type: {$s->type}\n";
}

echo "\n=== FACILITY UNITS ===\n";
foreach (\App\Models\FacilityUnit::all() as $f) {
    echo "ID: {$f->id} | Name: {$f->name} | Slug: {$f->slug} | Category: {$f->category} | Is Active: " . ($f->is_active ? 'YES' : 'NO') . "\n";
    echo "  Hours: {$f->operating_hours}\n";
    echo "  Contact: {$f->contact_number}\n";
    echo "  Services: " . json_encode($f->services) . "\n";
}
