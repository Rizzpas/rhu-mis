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
        'schedule_override_at',
        'schedule_override_expires_at',
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
            'schedule_override_at' => 'datetime',
            'schedule_override_expires_at' => 'datetime',
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
     * Whether the user is currently online and available (not busy).
     *
     * Computed on every read by App\Services\StaffPresence from the current time,
     * the practitioner schedule and any unexpired manual override. The stored
     * `status` column is NOT trusted for presence (it is only a cache + busy flag).
     */
    public function getIsPresentAttribute(): bool
    {
        return \App\Services\StaffPresence::effectiveStatus($this) === 'Online';
    }

    /**
     * Effective presence: 'Online' | 'Occupied' | 'Offline'.
     */
    public function getEffectiveStatusAttribute(): string
    {
        return \App\Services\StaffPresence::effectiveStatus($this);
    }

    /**
     * Query scope: users who are effectively online right now (including Occupied).
     */
    public function scopeEffectivelyOnline($query)
    {
        return \App\Services\StaffPresence::applyOnlineScope($query);
    }

    /**
     * Query scope to filter only present (online and not busy) users.
     * SQL mirror of getIsPresentAttribute().
     */
    public function scopePresent($query)
    {
        $query->where(function ($q) {
            $q->whereNull('status')
              ->orWhereNotIn('status', ['Occupied', 'occupied', 'Seminar', 'seminar', 'in meeting', 'on seminar']);
        });

        return \App\Services\StaffPresence::applyOnlineScope($query);
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

        // Full URL or Data URI support
        if (str_starts_with($this->avatar_path, 'data:') || str_starts_with($this->avatar_path, 'http://') || str_starts_with($this->avatar_path, 'https://')) {
            return $this->avatar_path;
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

        // Check if S3 / Cloud Object Storage is used and has the file
        try {
            if (config('filesystems.default') === 's3' && \Illuminate\Support\Facades\Storage::disk('s3')->exists($cleanPath)) {
                return \Illuminate\Support\Facades\Storage::disk('s3')->url($cleanPath);
            }
        } catch (\Throwable $e) {
            // Ignore storage connection issues
        }

        // If file does NOT exist on disk, return null so blade views cleanly fallback to initials
        return null;
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
