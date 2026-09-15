<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'schedule_override',
        'avatar_path',
        'schedule',
        'last_activity_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity_at' => 'datetime',
        ];
    }

    public function consultationsAsNurse()
    {
        return $this->hasMany(Consultation::class, 'nurse_id');
    }

    public function consultationsAsDoctor()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    public function hasRole(...$roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get the standardized and formatted name based on the user's role.
     * Enforces formats like "Dr. Lastname, Firstname".
     */
    public function getFormattedNameAttribute()
    {
        $rawName = $this->name ?? '';
        $role = $this->role ?? '';

        // Strip out existing prefixes to prevent duplication
        $cleanName = trim(preg_replace('/^(Dr\.|Dr|Doc|Doctor|Nurse|MedTech)\s+/i', '', $rawName));

        // Only format clinical staff
        if (str_contains($role, 'doctor') || str_contains($role, 'nurse') || $role === 'laboratory' || $role === 'radiology') {
            // Attempt to format as "Last, First"
            $parts = explode(' ', $cleanName);
            if (count($parts) > 1) {
                $lastName = array_pop($parts);
                $firstName = implode(' ', $parts);
                $formattedName = $lastName.', '.$firstName;
            } else {
                $formattedName = $cleanName;
            }

            // Re-apply correct prefix
            if (str_contains($role, 'doctor')) {
                return 'Dr. '.$formattedName;
            } elseif (str_contains($role, 'nurse')) {
                return 'Nurse '.$formattedName;
            } elseif ($role === 'laboratory') {
                return 'MedTech '.$formattedName;
            } elseif ($role === 'radiology') {
                return 'RadTech '.$formattedName;
            }
        }

        return $cleanName;
    }

    /**
     * Get the initials (first + last) of the clean name for avatars.
     */
    public function getInitialsAttribute()
    {
        $cleanName = trim(preg_replace('/^(Dr\.|Dr|Doc|Doctor|Nurse|MedTech)\s+/i', '', $this->name ?? 'A'));
        $parts = preg_split('/\s+/', $cleanName);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr(end($parts), 0, 1));
        }

        return strtoupper(substr($cleanName, 0, 1));
    }

    /**
     * Check if the user is currently active (has activity within the threshold).
     */
    public function isActive(int $minutes = 10): bool
    {
        if (! $this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at->greaterThan(now()->subMinutes($minutes));
    }

    /**
     * Dynamically determine if the user is present based on schedule and heartbeat.
     * Priority order:
     *   1. Occupied/Seminar status → "occupied" (not present but shown differently on frontend)
     *   2. Manual offline override (schedule_override = 'manual_offline') → definitively offline
     *   3. Manually set to 'Online'/'Present' → present
     *   4. Within an active schedule slot → present (auto-online)
     *   5. Otherwise → offline
     */
    public function getIsPresentAttribute(): bool
    {
        $status = strtolower($this->status ?? '');

        // Occupied/Seminar are handled separately by the frontend (shown as "Occupied")
        if (in_array($status, ['occupied', 'seminar'])) {
            return false;
        }

        // If the user has manually overridden to offline, respect that
        if ($this->schedule_override === 'manual_offline') {
            return false;
        }

        $isDemoMode = \App\Models\SiteSetting::get('demo_mode') === '1';

        // Demo Mode: anyone who isn't manually offline/occupied is present
        if ($isDemoMode) {
            // In demo mode, only require a recent heartbeat (generous 12-hour window)
            return $this->isActive(720);
        }

        // If manually set to 'Online' or 'Present', they are present
        // (requires recent heartbeat to avoid ghost "online" users)
        if (in_array($status, ['online', 'present'])) {
            return $this->isActive(10);
        }

        // Auto-online via schedule: check if they have an active schedule slot RIGHT NOW
        // This is the key fix: doctors with status=Offline can still be "present" 
        // if their schedule says they should be working now
        $now = now();
        $dayOfWeek = $now->format('D');
        $timeNow = $now->format('H:i:s');

        $hasActiveScheduleSlot = $this->practitionerSchedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('time_in', '<=', $timeNow)
            ->where('time_out', '>=', $timeNow)
            ->exists();

        return $hasActiveScheduleSlot;
    }

    /**
     * Query scope to filter only present users.
     * Aligns with getIsPresentAttribute logic:
     *   - Excludes Occupied/Seminar status
     *   - Excludes manual_offline override
     *   - Includes manually Online/Present (with recent heartbeat)
     *   - Includes anyone within an active schedule slot (auto-online)
     */
    public function scopePresent($query)
    {
        $now = now();
        $dayOfWeek = $now->format('D');
        $time = $now->format('H:i:s');

        $isDemoMode = \App\Models\SiteSetting::get('demo_mode') === '1';

        // Always exclude Occupied/Seminar
        $query->whereNotIn('status', ['Occupied', 'occupied', 'Seminar', 'seminar']);

        // Always exclude manual offline overrides
        $query->where(function ($q) {
            $q->whereNull('schedule_override')
              ->orWhere('schedule_override', '!=', 'manual_offline');
        });

        if ($isDemoMode) {
            // In demo mode, everyone who isn't Occupied/manual_offline is present
            return $query;
        }

        // Either: manually Online/Present with recent heartbeat
        // Or: has an active schedule slot right now
        $activeSince = $now->copy()->subMinutes(10);

        $query->where(function ($q) use ($dayOfWeek, $time, $activeSince) {
            // Manually online with recent heartbeat
            $q->where(function ($inner) use ($activeSince) {
                $inner->whereIn('status', ['Online', 'online', 'Present', 'present'])
                      ->where('last_activity_at', '>=', $activeSince);
            })
            // OR has active schedule slot (no heartbeat required for schedule-based)
            ->orWhereHas('practitionerSchedules', function ($scheduleQuery) use ($dayOfWeek, $time) {
                $scheduleQuery->where('day_of_week', $dayOfWeek)
                    ->where('time_in', '<=', $time)
                    ->where('time_out', '>=', $time);
            });
        });

        return $query;
    }

    /**
     * Relationship to Practitioner Schedules
     */
    public function practitionerSchedules()
    {
        return $this->hasMany(PractitionerSchedule::class);
    }

    /**
     * Reconstruct the formatted schedule string from relational data.
     */
    public function getFormattedScheduleAttribute()
    {
        if ($this->practitionerSchedules->isEmpty()) {
            return null; // Keep fallback to null if empty
        }

        $schedules = $this->practitionerSchedules;

        // Group by time_in and time_out
        $grouped = [];
        foreach ($schedules as $sched) {
            $key = $sched->time_in.'-'.$sched->time_out;
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'days' => [],
                    'time_in' => $sched->time_in,
                    'time_out' => $sched->time_out,
                ];
            }
            $grouped[$key]['days'][] = $sched->day_of_week;
        }

        $strings = [];
        foreach ($grouped as $group) {
            $daysStr = implode(', ', $group['days']);
            $timeInStr = \Carbon\Carbon::parse($group['time_in'])->format('h:i A');
            $timeOutStr = \Carbon\Carbon::parse($group['time_out'])->format('h:i A');
            $strings[] = "$daysStr ($timeInStr - $timeOutStr)";
        }

        return implode('; ', $strings);
    }

    /**
     * Get the public URL for the avatar.
     */
    public function getAvatarUrlAttribute()
    {
        if (! $this->avatar_path) {
            return null;
        }

        $cleanPath = ltrim(str_replace(['uploads/', 'storage/'], '', $this->avatar_path), '/');

        // Check if file exists in public/uploads/
        if (file_exists(public_path('uploads/'.$cleanPath))) {
            return asset('uploads/'.$cleanPath);
        }

        // Check if file exists in storage/app/public/
        if (file_exists(storage_path('app/public/'.$cleanPath))) {
            @mkdir(public_path('uploads/'.dirname($cleanPath)), 0755, true);
            @copy(storage_path('app/public/'.$cleanPath), public_path('uploads/'.$cleanPath));

            return asset('uploads/'.$cleanPath);
        }

        return asset('uploads/'.$cleanPath);
    }

    /**
     * Conversations this user is a participant of.
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }
}
