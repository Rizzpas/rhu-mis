<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send 24-hour advance email reminders to patients with upcoming appointments scheduled for tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $upcomingAppointments = Appointment::whereIn('status', ['approved', 'rescheduled'])
            ->whereDate('preferred_date', $tomorrow)
            ->whereNull('reminder_sent_at')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        $this->info("Found {$upcomingAppointments->count()} appointment(s) scheduled for tomorrow ({$tomorrow}) pending reminders.");

        $sentCount = 0;
        $failedCount = 0;

        foreach ($upcomingAppointments as $appointment) {
            try {
                Mail::to($appointment->email)->send(new AppointmentReminderMail($appointment));
                $appointment->update(['reminder_sent_at' => now()]);
                $sentCount++;
                $this->line("  [SENT] Ref: {$appointment->reference_number} -> {$appointment->email}");
            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("  [FAILED] Ref: {$appointment->reference_number} ({$appointment->email}): " . $e->getMessage());
            }
        }

        if ($sentCount > 0) {
            AuditLog::record("Automated Scheduler: Dispatched {$sentCount} appointment reminder email(s) for {$tomorrow}");
        }

        $this->info("Completed. Sent: {$sentCount}, Failed: {$failedCount}");

        return Command::SUCCESS;
    }
}
