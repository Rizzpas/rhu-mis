<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessAppointmentNoShows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:process-no-shows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process past-due unattended appointments by marking them as no-show and auto-cancelling expired pending bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // 1. Transition past-due approved/rescheduled/arrived appointments that never completed to 'no_show'
        $missedAppointments = Appointment::whereIn('status', ['approved', 'rescheduled', 'arrived'])
            ->whereDate('preferred_date', '<', $today)
            ->get();

        $noShowCount = 0;
        foreach ($missedAppointments as $apt) {
            $apt->update(['status' => 'no_show']);
            $noShowCount++;
            $this->line("  [NO-SHOW] Ref: {$apt->reference_number} | Patient: {$apt->first_name} {$apt->last_name} | Date: {$apt->preferred_date}");
        }

        if ($noShowCount > 0) {
            AuditLog::record("Automated EOD: Marked {$noShowCount} unattended appointment(s) as no-show");
            $this->info("Successfully marked {$noShowCount} unattended appointment(s) as no-show.");
        } else {
            $this->info("No past-due unattended approved appointments found.");
        }

        // 2. Auto-cancel past-due pending bookings that were never confirmed
        $expiredPending = Appointment::where('status', 'pending')
            ->whereDate('preferred_date', '<', $today)
            ->get();

        $expiredCount = 0;
        foreach ($expiredPending as $pendingApt) {
            $pendingApt->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Expired - Scheduled date passed without confirmation',
                'cancelled_at' => now(),
            ]);
            $expiredCount++;
            $this->line("  [EXPIRED PENDING] Ref: {$pendingApt->reference_number} | Patient: {$pendingApt->first_name} {$pendingApt->last_name} | Date: {$pendingApt->preferred_date}");
        }

        if ($expiredCount > 0) {
            AuditLog::record("Automated EOD: Cancelled {$expiredCount} expired pending appointment(s)");
            $this->info("Successfully cancelled {$expiredCount} expired pending appointment(s).");
        } else {
            $this->info("No past-due pending appointments to expire.");
        }

        return Command::SUCCESS;
    }
}
