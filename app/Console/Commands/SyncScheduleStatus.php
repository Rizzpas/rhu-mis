<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PractitionerSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncScheduleStatus extends Command
{
    protected $signature = 'staff:sync-schedule-status';

    protected $description = 'Automatically set staff Online/Offline based on their practitioner schedule.';

    public function handle(): int
    {
        $now = Carbon::now();
        $dayOfWeek = $now->format('D'); // Mon, Tue, Wed, etc.
        $currentTime = $now->format('H:i:s');

        // 1. Find staff whose schedule starts NOW — bring them online
        //    Only if they are currently Offline and NOT manually overridden to offline
        $staffToGoOnline = User::where('status', 'Offline')
            ->where(function ($q) {
                $q->whereNull('schedule_override')
                  ->orWhere('schedule_override', '!=', 'manual_offline');
            })
            ->whereHas('practitionerSchedules', function ($q) use ($dayOfWeek, $currentTime) {
                $q->where('day_of_week', $dayOfWeek)
                  ->where('time_in', '<=', $currentTime)
                  ->where('time_out', '>', $currentTime);
            })
            ->get();

        foreach ($staffToGoOnline as $user) {
            $user->update([
                'status' => 'Online',
                'last_activity_at' => now(),
                'schedule_override' => null, // Clear any override since schedule is active
            ]);

            $this->line("→ {$user->name} set to Online (schedule started)");
        }

        // 2. Clear manual_offline override when a NEW schedule slot starts
        //    This handles: "I went offline on Monday, my next slot is Friday 10AM"
        //    When Friday 10AM arrives, the override is cleared and they go online
        $staffWithOverride = User::where('schedule_override', 'manual_offline')
            ->whereHas('practitionerSchedules', function ($q) use ($dayOfWeek, $currentTime) {
                $q->where('day_of_week', $dayOfWeek)
                  ->where('time_in', '<=', $currentTime)
                  ->where('time_out', '>', $currentTime);
            })
            ->get();

        foreach ($staffWithOverride as $user) {
            $user->update([
                'status' => 'Online',
                'last_activity_at' => now(),
                'schedule_override' => null,
            ]);

            $this->line("→ {$user->name} override cleared, set to Online (new schedule slot)");
        }

        // 3. Find staff whose schedule has ENDED — bring them offline
        //    Only staff who are currently Online and have NO remaining schedule slots for today
        $onlineStaff = User::whereIn('status', ['Online', 'Occupied'])
            ->whereIn('role', [
                'regular_doctor', 'pedia_doctor',
                'clinical_nurse', 'vitals_nurse',
                'information_desk', 'laboratory', 'radiology', 'pharmacy',
            ])
            ->whereHas('practitionerSchedules') // Only staff who HAVE schedules at all
            ->get();

        foreach ($onlineStaff as $user) {
            // Check if they have ANY active schedule slot right now
            $hasActiveSlot = $user->practitionerSchedules()
                ->where('day_of_week', $dayOfWeek)
                ->where('time_in', '<=', $currentTime)
                ->where('time_out', '>', $currentTime)
                ->exists();

            if (! $hasActiveSlot) {
                // Check if they had a schedule today that has already ended
                $hadScheduleToday = $user->practitionerSchedules()
                    ->where('day_of_week', $dayOfWeek)
                    ->where('time_out', '<=', $currentTime)
                    ->exists();

                if ($hadScheduleToday) {
                    // Check if there's a future slot today they haven't reached yet
                    $hasFutureSlotToday = $user->practitionerSchedules()
                        ->where('day_of_week', $dayOfWeek)
                        ->where('time_in', '>', $currentTime)
                        ->exists();

                    // If they manually went online, keep them online until shift truly ends
                    if ($user->schedule_override === 'manual_online' && $hasFutureSlotToday) {
                        continue;
                    }

                    // If no more slots today, set to offline
                    if (! $hasFutureSlotToday) {
                        $user->update([
                            'status' => 'Offline',
                            'last_activity_at' => null,
                            'schedule_override' => null,
                        ]);

                        $this->line("→ {$user->name} set to Offline (schedule ended)");
                    }
                }
            }
        }

        $this->info('Schedule status sync completed.');

        return self::SUCCESS;
    }
}
