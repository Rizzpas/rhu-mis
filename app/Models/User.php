<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
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
                $formattedName = $lastName . ', ' . $firstName;
            } else {
                $formattedName = $cleanName;
            }
            
            // Re-apply correct prefix
            if (str_contains($role, 'doctor')) {
                return 'Dr. ' . $formattedName;
            } elseif (str_contains($role, 'nurse')) {
                return 'Nurse ' . $formattedName;
            } elseif ($role === 'laboratory') {
                return 'MedTech ' . $formattedName;
            } elseif ($role === 'radiology') {
                return 'RadTech ' . $formattedName;
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
            return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
        }
        return strtoupper(substr($cleanName, 0, 1));
    }

    /**
     * Check if the user is currently active (has activity within the threshold).
     */
    public function isActive(int $minutes = 10): bool
    {
        if (!$this->last_activity_at) {
            return false;
        }

        return $this->last_activity_at->greaterThan(now()->subMinutes($minutes));
    }

    /**
     * Dynamically determine if the user is present based on schedule and heartbeat.
     */
    public function getIsPresentAttribute(): bool
    {
        if ($this->status === 'Out of Office' || $this->status === 'Seminar') {
            return false;
        }

        $isDemoMode = \App\Models\SiteSetting::get('demo_mode') === '1';
        $activeMinutes = $isDemoMode ? 720 : 10;

        if (!$this->isActive($activeMinutes)) {
            return false;
        }

        $now = now();
        $dayOfWeek = $now->format('D');
        $timeNow = $now->format('H:i:s');

        // If Demo Mode is ON, we don't care about their schedule at all. 
        // If they are logged in (active heartbeat), they are present.
        if ($isDemoMode) {
            return true;
        }

        // If manually set to 'Present', they are present as long as they are active.
        if ($this->status === 'Present') {
            return true;
        }

        $query = $this->practitionerSchedules()
                      ->where('day_of_week', $dayOfWeek)
                      ->where('time_in', '<=', $timeNow)
                      ->where('time_out', '>=', $timeNow);

        return $query->exists();
    }

    /**
     * Query scope to filter only present users.
     */
    public function scopePresent($query)
    {
        $now = now();
        $dayOfWeek = $now->format('D');
        $time = $now->format('H:i:s');
        
        $isDemoMode = \App\Models\SiteSetting::get('demo_mode') === '1';

        $query->whereNotIn('status', ['Out of Office', 'Seminar']);

        if ($isDemoMode) {
            // In demo mode, everyone who isn't Out of Office/Seminar is present
            // We ignore last_activity_at to prevent timeouts during presentations
            return $query;
        }

        // For non-demo mode, check if they are manually marked 'Present' OR if they are scheduled + recently active
        $activeSince = $now->copy()->subMinutes(10);
        
        $query->where(function ($q) use ($dayOfWeek, $time, $activeSince) {
            // Either they have a manual 'Present' status...
            $q->where('status', 'Present')
              // ...or they match their defined schedule AND have been active recently
              ->orWhere(function($sq) use ($dayOfWeek, $time, $activeSince) {
                  $sq->where('last_activity_at', '>=', $activeSince)
                     ->whereHas('practitionerSchedules', function ($scheduleQuery) use ($dayOfWeek, $time) {
                         $scheduleQuery->where('day_of_week', $dayOfWeek)
                           ->where('time_in', '<=', $time)
                           ->where('time_out', '>=', $time);
                     });
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
            $key = $sched->time_in . '-' . $sched->time_out;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'days' => [],
                    'time_in' => $sched->time_in,
                    'time_out' => $sched->time_out
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
        if (!$this->avatar_path) {
            return null;
        }
        return asset('storage/' . $this->avatar_path);
    }
}
