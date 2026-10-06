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

    /**
     * Role → short department code used as the Staff ID prefix.
     */
    public const STAFF_ID_ROLE_CODES = [
        'super_admin' => 'SAD',
        'admin' => 'ADM',
        'regular_doctor' => 'DOC',
        'pedia_doctor' => 'PED',
        'clinical_nurse' => 'NRS',
        'vitals_nurse' => 'VTN',
        'information_desk' => 'IFD',
        'laboratory' => 'LAB',
        'radiology' => 'RAD',
        'pharmacy' => 'PHM',
    ];

    /**
     * Characters used for the random Staff ID suffix (ambiguous 0/O/1/I/L removed).
     */
    protected const STAFF_ID_ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->staff_id)) {
                $user->staff_id = static::generateStaffId($user->role, now()->format('ym'));
            }
        });
    }

    /**
     * Generate a unique Staff ID in the format {ROLE}-{YYMM}-{XXXX}, e.g. DOC-2610-7K3P.
     * ROLE = department code, YYMM = year + month the staff joined, XXXX = random unique suffix.
     */
    public static function generateStaffId(?string $role, ?string $period = null): string
    {
        $code = self::STAFF_ID_ROLE_CODES[$role ?? ''] ?? 'STF';
        $period = $period ?? now()->format('ym');
        $alphabet = self::STAFF_ID_ALPHABET;

        do {
            $suffix = '';
            for ($i = 0; $i < 4; $i++) {
                $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $candidate = "{$code}-{$period}-{$suffix}";
        } while (static::withTrashed()->where('staff_id', $candidate)->exists());

        return $candidate;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
     * Get the clean, un-prefixed full name normalized to First Middle Last order.
     */
    public function getCleanFullNameAttribute()
    {
        $rawName = $this->name ?? '';
        $cleanName = trim(preg_replace('/^(Dr\.|Dr|Doc|Doctor|Nurse|MedTech|RadTech)\s+/i', '', $rawName));

        // If stored as "LastName, FirstName MiddleName", normalize to "FirstName MiddleName LastName"
        if (str_contains($cleanName, ',')) {
            $parts = explode(',', $cleanName, 2);
            $lastName = trim($parts[0]);
            $firstMiddle = trim($parts[1] ?? '');
            $cleanName = trim($firstMiddle . ' ' . $lastName);
        }

        return $cleanName;
    }

    /**
     * Get the standardized and formatted name based on the user's role.
     * Formats names in First name > Middle > Last name order (e.g., "Dr. First Middle Last").
     */
    public function getFormattedNameAttribute()
    {
        $cleanName = $this->clean_full_name;
        $role = $this->role ?? '';

        // Only format clinical staff with their professional prefix
        if (str_contains($role, 'doctor') || str_contains($role, 'nurse') || $role === 'laboratory' || $role === 'radiology') {
            if (str_contains($role, 'doctor')) {
                return 'Dr. '.$cleanName;
            } elseif (str_contains($role, 'nurse')) {
                return 'Nurse '.$cleanName;
            } elseif ($role === 'laboratory') {
                return 'MedTech '.$cleanName;
            } elseif ($role === 'radiology') {
                return 'RadTech '.$cleanName;
            }
        }

        return $cleanName;
    }

    /**
     * Get the initials (first + last) of the clean name for avatars.
     */
    public function getInitialsAttribute()
    {
        $cleanName = $this->clean_full_name;
        $parts = preg_split('/\s+/', $cleanName);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr(end($parts), 0, 1));
        }

        return strtoupper(substr($cleanName, 0, 2));
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

        // If manually set to 'Present', they are physically in clinic
        if (in_array($status, ['present'])) {
            return true;
        }

        // If manually set to 'Online', check recent activity (generous 60-min window)
        if (in_array($status, ['online']) && $this->isActive(60)) {
            return true;
        }

        // Auto-online via schedule: check if they have an active schedule slot RIGHT NOW
        // This is the key fix: doctors can still be "present" if their schedule says they should be working now
        $now = now();
        $dayOfWeek = $now->format('D');
        $timeNow = $now->format('H:i:s');

        $hasActiveScheduleSlot = $this->relationLoaded('practitionerSchedules')
            ? $this->practitionerSchedules->contains(function ($s) use ($dayOfWeek, $timeNow) {
                return $s->day_of_week === $dayOfWeek
                    && $s->time_in <= $timeNow
                    && $s->time_out >= $timeNow;
            })
            : $this->practitionerSchedules()
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

        // Either: manually Present (on-duty)
        // Or: Online with activity in last 60 minutes
        // Or: has an active schedule slot right now
        $activeSince = $now->copy()->subMinutes(60);

        $query->where(function ($q) use ($dayOfWeek, $time, $activeSince) {
            // Status explicitly Present (on duty)
            $q->whereIn('status', ['Present', 'present'])
            // OR manually online with recent heartbeat
            ->orWhere(function ($inner) use ($activeSince) {
                $inner->whereIn('status', ['Online', 'online'])
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
