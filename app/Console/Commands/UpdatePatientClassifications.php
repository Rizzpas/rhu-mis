<?php

namespace App\Console\Commands;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePatientClassifications extends Command
{
    protected $signature = 'patients:update-classifications';

    protected $description = 'Automatically update patient classifications based on current age derived from DOB.';

    public function handle(): int
    {
        $updated = 0;

        // ── Pediatric → Regular Adult (age > 12, excluding PWD) ──────
        $pediatricAged = Patient::where('classification', 'Pediatric')
            ->whereNotNull('dob')
            ->whereDate('dob', '<', Carbon::now()->subYears(Patient::MAX_PEDIATRIC_AGE + 1)->toDateString())
            ->get();

        foreach ($pediatricAged as $patient) {
            $age = Carbon::parse($patient->dob)->age;
            if ($age > Patient::MAX_PEDIATRIC_AGE) {
                $newClassification = $age >= 60 ? 'Senior Citizen' : 'Regular Adult';
                $patient->classification = $newClassification;
                $patient->saveQuietly(); // Skip events to avoid redundant processing
                $this->line("→ {$patient->patient_id} ({$patient->first_name} {$patient->last_name}): Pediatric → {$newClassification} (age {$age})");
                $updated++;
            }
        }

        // ── Regular Adult → Senior Citizen (age >= 60) ────────────────
        $adultAged = Patient::where('classification', 'Regular Adult')
            ->whereNotNull('dob')
            ->whereDate('dob', '<', Carbon::now()->subYears(60)->toDateString())
            ->get();

        foreach ($adultAged as $patient) {
            $age = Carbon::parse($patient->dob)->age;
            if ($age >= 60) {
                $patient->classification = 'Senior Citizen';
                $patient->saveQuietly();
                $this->line("→ {$patient->patient_id} ({$patient->first_name} {$patient->last_name}): Regular Adult → Senior Citizen (age {$age})");
                $updated++;
            }
        }

        $this->info("Classification update completed. {$updated} patient(s) updated.");

        return self::SUCCESS;
    }
}
