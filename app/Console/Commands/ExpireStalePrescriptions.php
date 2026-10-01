<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Prescription;
use App\Models\User;
use App\Notifications\NewPrescriptionNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ExpireStalePrescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prescriptions:expire-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire stale prescriptions that have passed their expiry date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // Find prescriptions that are pending or partially_dispensed and have expired
        $expiredPrescriptions = Prescription::whereIn('status', ['pending', 'partially_dispensed'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->with(['patient', 'doctor'])
            ->get();

        $expiredCount = 0;
        foreach ($expiredPrescriptions as $prescription) {
            $prescription->update([
                'status' => 'expired',
                'expired_at' => $now,
            ]);

            $expiredCount++;
            $this->line("  [EXPIRED] Rx #{$prescription->id} | Patient: {$prescription->patient?->first_name} {$prescription->patient?->last_name} | Expires: {$prescription->expires_at}");
        }

        if ($expiredCount > 0) {
            AuditLog::record("Automated EOD: Expired {$expiredCount} stale prescription(s)");

            // Notify prescribers about expired prescriptions
            foreach ($expiredPrescriptions as $prescription) {
                if ($prescription->doctor) {
                    Notification::send($prescription->doctor, new NewPrescriptionNotification(
                        $prescription->patient?->first_name . ' ' . $prescription->patient?->last_name,
                        $prescription->doctor_id,
                        $prescription->id,
                        'expired'
                    ));
                }
            }

            $this->info("Successfully expired {$expiredCount} stale prescription(s).");
        } else {
            $this->info("No stale prescriptions to expire.");
        }

        return Command::SUCCESS;
    }
}