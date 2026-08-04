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

        // 2. Fix stale follow-up consultations
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
