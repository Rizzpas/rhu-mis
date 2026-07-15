<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoLogoutInactiveStaff extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'staff:auto-logout {--minutes=30 : Minutes of inactivity before auto-logout}';

    /**
     * The console command description.
     */
    protected $description = 'Mark staff as "Out of Office" if they have been inactive for the specified duration.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = Carbon::now()->subMinutes($minutes);

        // Find clinical staff who are "Present" but haven't had any activity within the cutoff
        $inactiveStaff = User::present()
            ->whereIn('role', [
                'regular_doctor', 'pedia_doctor',
                'clinical_nurse', 'vitals_nurse',
                'information_desk', 'laboratory', 'radiology',
            ])
            ->where(function ($q) use ($cutoff) {
                $q->where('last_activity_at', '<', $cutoff)
                  ->orWhereNull('last_activity_at');
            })
            ->get();

        $count = $inactiveStaff->count();

        foreach ($inactiveStaff as $user) {
            $user->update(['status' => 'Out of Office']);

            \App\Models\AuditLog::record('Auto Logout (Idle)', $user, [
                'last_activity' => $user->last_activity_at?->toIso8601String() ?? 'Never',
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
