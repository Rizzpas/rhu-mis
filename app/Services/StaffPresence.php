<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Single source of truth for staff online/offline presence.
 *
 * Presence is COMPUTED on every read from (current time, schedule, active override)
 * instead of being trusted from the stored `users.status` column. The `status` column
 * is only a denormalized cache (kept in sync by `staff:sync-schedule-status`) plus the
 * "Occupied" busy flag.
 *
 * Rules (all comparisons in config('app.timezone')):
 *   1. Schedule-based: online while  time_in <= now < time_out  on a scheduled day.
 *   2. Manual offline override: offline until the start of the next scheduled shift.
 *   3. Manual online override (outside schedule): online until the doctor switches
 *      offline / logs out (or the session goes idle), or the next shift starts and
 *      the schedule takes over.
 *   Priority: active override > schedule.
 */
class StaffPresence
{
    public const OVERRIDE_ONLINE = 'manual_online';
    public const OVERRIDE_OFFLINE = 'manual_offline';

    /** A manual-online session with no heartbeat for this long counts as logged out. */
    public const IDLE_MINUTES = 30;

    public const BUSY_STATUSES = ['occupied', 'seminar', 'in meeting', 'on seminar'];

    public static function timezone(): string
    {
        return config('app.timezone', 'Asia/Manila');
    }

    public static function now(): Carbon
    {
        return Carbon::now(self::timezone());
    }

    protected static function at(?CarbonInterface $at): Carbon
    {
        return $at ? Carbon::instance($at)->setTimezone(self::timezone()) : self::now();
    }

    /** Normalize "08:00" / "08:00:00" to "08:00:00" for safe string comparison. */
    public static function normalizeTime(?string $time): string
    {
        $time = trim((string) $time);

        return strlen($time) === 5 ? $time.':00' : substr($time, 0, 8);
    }

    protected static function schedules(User $user): Collection
    {
        return $user->relationLoaded('practitionerSchedules')
            ? $user->practitionerSchedules
            : $user->practitionerSchedules()->get();
    }

    /** Start inclusive, end exclusive. */
    public static function isWithinSchedule(User $user, ?CarbonInterface $at = null): bool
    {
        $at = self::at($at);
        $day = $at->format('D');
        $time = $at->format('H:i:s');

        return self::schedules($user)->contains(function ($s) use ($day, $time) {
            return $s->day_of_week === $day
                && self::normalizeTime($s->time_in) <= $time
                && $time < self::normalizeTime($s->time_out);
        });
    }

    /** The first scheduled shift start strictly after $after, or null if no schedule. */
    public static function nextShiftStart(User $user, ?CarbonInterface $after = null): ?Carbon
    {
        $after = self::at($after);
        $schedules = self::schedules($user);

        if ($schedules->isEmpty()) {
            return null;
        }

        for ($i = 0; $i <= 7; $i++) {
            $date = $after->copy()->startOfDay()->addDays($i);
            $next = $schedules
                ->where('day_of_week', $date->format('D'))
                ->map(fn ($s) => $date->copy()->setTimeFromTimeString(self::normalizeTime($s->time_in)))
                ->filter(fn (Carbon $start) => $start->gt($after))
                ->sort()
                ->first();

            if ($next) {
                return $next;
            }
        }

        return null;
    }

    /** Returns OVERRIDE_ONLINE / OVERRIDE_OFFLINE if an unexpired override exists, else null. */
    public static function activeOverride(User $user, ?CarbonInterface $at = null): ?string
    {
        $at = self::at($at);
        $override = $user->schedule_override;

        if (! in_array($override, [self::OVERRIDE_ONLINE, self::OVERRIDE_OFFLINE], true)) {
            return null;
        }

        $expiresAt = $user->schedule_override_expires_at;
        if ($expiresAt && $expiresAt->lte($at)) {
            return null;
        }

        // Manual online lasts only while the doctor's session is alive (heartbeat every 60s).
        if ($override === self::OVERRIDE_ONLINE) {
            $lastSeen = $user->last_activity_at;
            if (! $lastSeen || $lastSeen->lt($at->copy()->subMinutes(self::IDLE_MINUTES))) {
                return null;
            }
        }

        return $override;
    }

    public static function isDemoMode(): bool
    {
        return SiteSetting::get('demo_mode') === '1';
    }

    /** Effective online state (includes "Occupied", which is online-but-busy). */
    public static function isOnline(User $user, ?CarbonInterface $at = null): bool
    {
        $override = self::activeOverride($user, $at);

        if ($override === self::OVERRIDE_OFFLINE) {
            return false;
        }
        if ($override === self::OVERRIDE_ONLINE) {
            return true;
        }
        if (self::isDemoMode()) {
            return true;
        }

        $schedules = self::schedules($user);
        if ($schedules->isNotEmpty()) {
            return self::isWithinSchedule($user, $at);
        }

        // Fallback for accounts without any practitioner schedule defined (e.g. test stubs):
        return in_array(strtolower($user->status ?? ''), ['online', 'present', 'active', 'in office'], true);
    }

    /** "Occupied" is a busy flag on top of online; it only counts while the session is alive. */
    public static function isBusy(User $user, ?CarbonInterface $at = null): bool
    {
        if (! in_array(strtolower($user->status ?? ''), self::BUSY_STATUSES, true)) {
            return false;
        }

        $lastSeen = $user->last_activity_at;

        return $lastSeen && $lastSeen->gte(self::at($at)->subMinutes(self::IDLE_MINUTES));
    }

    /** 'Online' | 'Occupied' | 'Offline' */
    public static function effectiveStatus(User $user, ?CarbonInterface $at = null): string
    {
        if (! self::isOnline($user, $at)) {
            return 'Offline';
        }

        return self::isBusy($user, $at) ? 'Occupied' : 'Online';
    }

    /**
     * Apply a status the user (or an admin) chose manually.
     * $status is a normalized 'Online' | 'Occupied' | 'Offline'.
     *
     * @return array<string, mixed> attributes to persist
     */
    public static function manualStatusAttributes(User $user, string $status, ?CarbonInterface $at = null): array
    {
        $at = self::at($at);

        if ($status === 'Offline') {
            return [
                'status' => 'Offline',
                'schedule_override' => self::OVERRIDE_OFFLINE,
                'schedule_override_at' => $at,
                // Stays offline until the next shift starts; null = no schedule → until switched online.
                'schedule_override_expires_at' => self::nextShiftStart($user, $at),
                'last_activity_at' => null,
            ];
        }

        $attributes = [
            'status' => $status === 'Occupied' ? 'Occupied' : 'Online',
            'last_activity_at' => $at,
        ];

        if (self::isWithinSchedule($user, $at)) {
            // The schedule already makes them online → just remove any override.
            return $attributes + self::clearedOverride();
        }

        return $attributes + [
            'schedule_override' => self::OVERRIDE_ONLINE,
            'schedule_override_at' => $at,
            // When the next shift starts the schedule takes over again.
            'schedule_override_expires_at' => self::nextShiftStart($user, $at),
        ];
    }

    public static function setManualStatus(User $user, string $status, ?CarbonInterface $at = null): void
    {
        $user->forceFill(self::manualStatusAttributes($user, $status, $at))->save();
    }

    /** Called on login: opening the account outside schedule makes the user online. */
    public static function handleLogin(User $user, ?CarbonInterface $at = null): void
    {
        $at = self::at($at);

        if (self::isWithinSchedule($user, $at)) {
            // Schedule governs; a manual-offline override chosen during this shift is respected.
            $user->forceFill([
                'last_activity_at' => $at,
                'status' => self::isOnline($user, $at) ? 'Online' : 'Offline',
            ])->save();

            return;
        }

        self::setManualStatus($user, 'Online', $at);
    }

    /** Called on heartbeat / active portal use: keeps session alive and activates online outside schedule. */
    public static function handleAccountActive(User $user, ?CarbonInterface $at = null): void
    {
        $at = self::at($at);

        // If user manually switched offline and override is still active, respect it
        if ($user->schedule_override === self::OVERRIDE_OFFLINE && self::activeOverride($user, $at) === self::OVERRIDE_OFFLINE) {
            $user->forceFill(['last_activity_at' => $at])->save();

            return;
        }

        // If outside schedule, active presence in account brings them online (rule 3)
        if (! self::isWithinSchedule($user, $at) && ! self::isDemoMode()) {
            if ($user->schedule_override !== self::OVERRIDE_ONLINE) {
                self::setManualStatus($user, 'Online', $at);

                return;
            }
        }

        $user->forceFill(['last_activity_at' => $at])->save();
    }

    /** Called on logout: ends a manual-online session; the schedule takes over. */
    public static function handleLogout(User $user, ?CarbonInterface $at = null): void
    {
        $at = self::at($at);
        $attributes = ['last_activity_at' => null];

        if ($user->schedule_override === self::OVERRIDE_ONLINE) {
            $attributes += self::clearedOverride();
        }

        $user->forceFill($attributes);
        $user->forceFill(['status' => self::effectiveStatus($user, $at)])->save();
    }

    /** @return array<string, null> */
    public static function clearedOverride(): array
    {
        return [
            'schedule_override' => null,
            'schedule_override_at' => null,
            'schedule_override_expires_at' => null,
        ];
    }

    /**
     * SQL equivalent of isOnline() (excluding busy filtering), for query scopes.
     */
    public static function applyOnlineScope(Builder $query, ?CarbonInterface $at = null): Builder
    {
        $at = self::at($at);
        $now = $at->format('Y-m-d H:i:s');
        $day = $at->format('D');
        $time = $at->format('H:i:s');
        $idleCutoff = $at->copy()->subMinutes(self::IDLE_MINUTES)->format('Y-m-d H:i:s');
        $demo = self::isDemoMode();

        return $query->where(function ($q) use ($now, $day, $time, $idleCutoff, $demo) {
            // (a) active manual-online override (unexpired, session alive)
            $q->where(function ($q) use ($now, $idleCutoff) {
                $q->where('schedule_override', self::OVERRIDE_ONLINE)
                    ->where(fn ($q) => $q->whereNull('schedule_override_expires_at')
                        ->orWhere('schedule_override_expires_at', '>', $now))
                    ->where('last_activity_at', '>=', $idleCutoff);
            })
            // (b) no active manual-offline override, and the schedule (or demo mode) says online
            ->orWhere(function ($q) use ($now, $day, $time, $demo) {
                $q->where(function ($q) use ($now) {
                    $q->whereNull('schedule_override')
                        ->orWhere('schedule_override', '!=', self::OVERRIDE_OFFLINE)
                        ->orWhere(fn ($q) => $q->whereNotNull('schedule_override_expires_at')
                            ->where('schedule_override_expires_at', '<=', $now));
                });

                if (! $demo) {
                    $q->where(function ($q) use ($day, $time) {
                        $q->whereHas('practitionerSchedules', function ($s) use ($day, $time) {
                            $s->where('day_of_week', $day)
                                ->where('time_in', '<=', $time)
                                ->where('time_out', '>', $time);
                        })->orWhere(function ($q) {
                            $q->whereDoesntHave('practitionerSchedules')
                              ->whereIn('status', ['Online', 'online', 'Present', 'present', 'active', 'in office']);
                        });
                    });
                }
            });
        });
    }
}
