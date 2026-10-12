<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoLogoutInactiveStaff extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'staff:auto-logout {--minutes=30 : Minutes of inactivity before auto-logout}';

    /**
     * The console command description.
     */
    protected $description = 'Mark staff as "Offline" if they have been inactive for the specified duration.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = Carbon::now()->subMinutes($minutes);

        // Idle staff (no heartbeat within the cutoff) are treated as logged out:
        // their manual-online session ends and the schedule-based status takes over.
        $inactiveStaff = User::whereIn('role', [
                'regular_doctor', 'pedia_doctor',
                'clinical_nurse', 'vitals_nurse',
                'information_desk', 'laboratory', 'radiology', 'pharmacy',
            ])
            ->where(function ($q) {
                $q->where('schedule_override', \App\Services\StaffPresence::OVERRIDE_ONLINE)
                  ->orWhereIn('status', ['Occupied', 'occupied']);
            })
            ->where(function ($q) use ($cutoff) {
                $q->where('last_activity_at', '<', $cutoff)
                    ->orWhereNull('last_activity_at');
            })
            ->with('practitionerSchedules')
            ->get();

        $count = $inactiveStaff->count();

        foreach ($inactiveStaff as $user) {
            $lastActivity = $user->last_activity_at?->toIso8601String() ?? 'Never';

            \App\Services\StaffPresence::handleLogout($user);

            \App\Models\AuditLog::record('Auto Logout (Idle)', $user, [
                'last_activity' => $lastActivity,
                'idle_threshold' => "{$minutes} minutes",
            ]);
        }

        if ($count > 0) {
            $this->info("Auto-logged out {$count} inactive staff member(s).");
        } else {
            $this->info('No inactive staff found.');
        }

        return self::SUCCESS;
    }
}
