<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Consultation;
use App\Models\MedicalCase;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $completedConsultations = Consultation::with('preTriage')
            ->where('status', 'completed')
            ->get();

        foreach ($completedConsultations as $c) {
            $existing = MedicalCase::where('consultation_id', $c->id)->first();
            if (!$existing) {
                $datePrefix = $c->created_at ? $c->created_at->format('Ymd') : now()->format('Ymd');
                $caseNumber = 'CASE-' . $datePrefix . '-' . str_pad($c->id, 5, '0', STR_PAD_LEFT);
                
                // If by any chance case number exists, add consultation ID suffix
                if (MedicalCase::where('case_number', $caseNumber)->exists()) {
                    $caseNumber = 'CASE-' . $datePrefix . '-' . str_pad($c->id, 5, '0', STR_PAD_LEFT) . '-C' . $c->id;
                }

                $diagnosis = $c->diagnosis;
                if (!$diagnosis || trim($diagnosis) === '') {
                    $diagnosis = $c->medical_notes ? trim(str_replace(['[Auto-closed at End of Day]', '[Auto-Closed At End Of Day]'], '', $c->medical_notes)) : '';
                    if (empty($diagnosis)) {
                        $diagnosis = 'Clinical Follow-up & Evaluation';
                    }
                }

                $vitals = [
                    'bp' => $c->preTriage->blood_pressure ?? $c->blood_pressure ?? null,
                    'temp' => $c->preTriage->temperature ?? $c->temperature ?? null,
                    'wt' => $c->preTriage->weight ?? $c->weight ?? null,
                    'ht' => $c->preTriage->height ?? $c->height ?? null,
                    'hr' => $c->preTriage->heart_rate ?? $c->heart_rate ?? null,
                    'rr' => $c->preTriage->respiratory_rate ?? $c->respiratory_rate ?? null,
                    'pr' => $c->preTriage->pulse_rate ?? $c->pulse_rate ?? null,
                    'spo2' => $c->preTriage->spo2 ?? $c->spo2 ?? ($c->preTriage->oxygen_saturation ?? null),
                ];

                MedicalCase::create([
                    'case_number' => $caseNumber,
                    'patient_id' => $c->patient_id,
                    'consultation_id' => $c->id,
                    'pre_triage_id' => $c->pre_triage_id,
                    'diagnosis' => $diagnosis,
                    'prescription' => $c->prescription ?: '[]',
                    'vitals_snapshot' => $vitals,
                    'closed_at' => $c->consultation_end_time ?? $c->updated_at ?? now(),
                    'created_at' => $c->created_at ?? now(),
                    'updated_at' => $c->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructive reverse needed for backfilled cases
    }
};
