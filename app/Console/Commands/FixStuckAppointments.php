<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Consultation;
use Illuminate\Console\Command;

class FixStuckAppointments extends Command
{
    protected $signature = 'fix:stuck-appointments';
    protected $description = 'Fix appointments stuck in registered/triaged status and stale follow-up flags';

    public function handle()
    {
        // 1. Fix stuck appointments from past dates
        $stuck = Appointment::whereIn('status', ['registered', 'triaged'])
            ->whereDate('preferred_date', '<', now()->toDateString())
            ->get();

        $this->info("Found {$stuck->count()} stuck appointment(s) from past dates.");

        foreach ($stuck as $appointment) {
            $this->line("  ID={$appointment->id} | {$appointment->first_name} {$appointment->last_name} | status={$appointment->status} | follow_up=" . ($appointment->is_follow_up ? 'YES' : 'NO'));
            $appointment->update(['status' => 'done']);
            $this->info("    -> Updated to 'done'");
        }

        // 2. Auto-close stale consultations from past dates
        $staleConsultations = Consultation::whereIn('status', ['queued', 'active', 'awaiting_results', 'results_ready'])
            ->whereDate('consultation_date', '<', now()->toDateString())
            ->get();

        $this->info("\nChecking {$staleConsultations->count()} stale consultation(s) from past dates...");

        foreach ($staleConsultations as $c) {
            if ($c->consultation_start_time) {
                // Doctor had started this consultation; mark completed with end timestamp
                $c->update([
                    'status' => 'completed',
                    'consultation_end_time' => $c->updated_at ?? now(),
                    'medical_notes' => trim(($c->medical_notes ?? '') . ' [Auto-closed at End of Day]'),
                ]);
                if ($c->preTriage) {
                    $c->preTriage->update(['status' => 'completed']);
                }

                $existingCase = \App\Models\MedicalCase::where('consultation_id', $c->id)->first();
                if (!$existingCase) {
                    $datePrefix = $c->created_at ? $c->created_at->format('Ymd') : now()->format('Ymd');
                    $caseNumber = 'CASE-' . $datePrefix . '-' . str_pad($c->id, 5, '0', STR_PAD_LEFT);
                    if (\App\Models\MedicalCase::where('case_number', $caseNumber)->exists()) {
                        $caseNumber .= '-C' . $c->id;
                    }
                    \App\Models\MedicalCase::create([
                        'case_number' => $caseNumber,
                        'patient_id' => $c->patient_id,
                        'consultation_id' => $c->id,
                        'pre_triage_id' => $c->pre_triage_id,
                        'diagnosis' => $c->diagnosis ?: 'Clinical Follow-up & Evaluation',
                        'prescription' => $c->prescription ?: '[]',
                        'vitals_snapshot' => [
                            'bp' => $c->preTriage->blood_pressure ?? $c->blood_pressure ?? null,
                            'temp' => $c->preTriage->temperature ?? $c->temperature ?? null,
                            'wt' => $c->preTriage->weight ?? $c->weight ?? null,
                            'ht' => $c->preTriage->height ?? $c->height ?? null,
                            'hr' => $c->preTriage->heart_rate ?? $c->heart_rate ?? null,
                            'rr' => $c->preTriage->respiratory_rate ?? $c->respiratory_rate ?? null,
                            'pr' => $c->preTriage->pulse_rate ?? $c->pulse_rate ?? null,
                            'spo2' => $c->preTriage->spo2 ?? $c->spo2 ?? ($c->preTriage->oxygen_saturation ?? null),
                        ],
                        'closed_at' => $c->consultation_end_time ?? now(),
                    ]);
                }

                $this->line("  Consultation #{$c->id} (started): Marked as 'completed' and MedicalCase archived");
            } else {
                // Uncalled / unattended by doctor
                $c->update([
                    'status' => 'cancelled',
                    'medical_notes' => trim(($c->medical_notes ?? '') . ' [Unattended at End of Day]'),
                ]);
                $this->line("  Consultation #{$c->id} (unstarted): Marked as 'cancelled'");
            }

            // Sync linked queue
            \App\Models\Queue::where('patient_id', $c->patient_id)
                ->where('queue_number', $c->queue_number)
                ->whereDate('created_at', $c->consultation_date)
                ->update(['status' => $c->consultation_start_time ? 'Completed' : 'Cancelled']);
        }

        // 3. Mark past claimed pre-triages whose consultations are completed
        $claimedPreTriages = \App\Models\PreTriage::where('status', 'claimed')
            ->whereDate('created_at', '<', now()->toDateString())
            ->get();

        foreach ($claimedPreTriages as $pt) {
            $consultation = Consultation::where('pre_triage_id', $pt->id)->first();
            if ($consultation && in_array($consultation->status, ['completed', 'done'])) {
                $pt->update(['status' => 'completed']);
            }
        }

        // 4. Resolve lingering past queues
        $lingeringQueues = \App\Models\Queue::whereIn('status', ['Waiting', 'Calling'])
            ->whereDate('created_at', '<', now()->toDateString())
            ->update(['status' => 'Cancelled']);

        if ($lingeringQueues > 0) {
            $this->info("Cancelled $lingeringQueues lingering queue ticket(s) from past dates.");
        }

        // 5. Fix stale follow-up consultations
        $patients = Consultation::where('is_followup_needed', true)
            ->whereNull('followup_completed_at')
            ->where('status', 'completed')
            ->pluck('patient_id')
            ->unique();

        $this->info("\nChecking {$patients->count()} patient(s) with stale follow-ups...");

        foreach ($patients as $patientId) {
            $followup = Consultation::where('patient_id', $patientId)
                ->where('is_followup_needed', true)
                ->whereNull('followup_completed_at')
                ->orderBy('consultation_date', 'desc')
                ->first();

            if (!$followup) continue;

            $laterVisit = Consultation::where('patient_id', $patientId)
                ->where('status', 'completed')
                ->where('id', '>', $followup->id)
                ->exists();

            if ($laterVisit) {
                $this->line("  Patient {$patientId}: Follow-up #{$followup->id} has a later visit -> marking as fulfilled");
                Consultation::where('patient_id', $patientId)
                    ->where('is_followup_needed', true)
                    ->whereNull('followup_completed_at')
                    ->update(['followup_completed_at' => now()]);
            }
        }

        $this->info("\nDone!");
        return 0;
    }
}
