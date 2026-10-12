<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\StaffPresence;
use Illuminate\Console\Command;

/**
 * Keeps the denormalized `users.status` column in sync with the COMPUTED presence
 * (App\Services\StaffPresence) and clears expired manual overrides.
 *
 * Presence correctness does not depend on this job: every read path computes the
 * effective status from time + schedule + override. This job only keeps the stored
 * column (used by admin lists / audit) from drifting. Scheduled every minute.
 */
class SyncScheduleStatus extends Command
{
    protected $signature = 'staff:sync-schedule-status';

    protected $description = 'Sync stored staff status with schedule-based presence and expire manual overrides.';

    public function handle(): int
    {
        $now = StaffPresence::now();
        $changed = 0;

        User::whereIn('role', [
            'regular_doctor', 'pedia_doctor',
            'clinical_nurse', 'vitals_nurse',
            'information_desk', 'laboratory', 'radiology', 'pharmacy',
        ])
            ->with('practitionerSchedules')
            ->chunkById(200, function ($users) use ($now, &$changed) {
                foreach ($users as $user) {
                    $attributes = [];

                    // Drop overrides that are no longer active (expired or idle manual-online).
                    if ($user->schedule_override !== null && StaffPresence::activeOverride($user, $now) === null) {
                        $attributes += StaffPresence::clearedOverride();
                        $user->forceFill($attributes);
                    }

                    $effective = StaffPresence::effectiveStatus($user, $now);
                    if ($user->status !== $effective) {
                        $attributes['status'] = $effective;
                    }

                    if ($attributes !== []) {
                        $user->forceFill($attributes)->save();
                        $changed++;
                        $this->line("→ {$user->name}: {$effective}");
                    }
                }
            });

        $this->info("Schedule status sync completed ({$changed} updated).");

        return self::SUCCESS;
    }
}
