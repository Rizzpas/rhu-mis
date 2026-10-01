<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use \App\Traits\Sterilizable, HasFactory;

    protected $sterilizable = [
        'first_name', 'middle_name', 'last_name', 'suffix', 'address', 'house_no', 'street', 'building',
        'barangay', 'city_province', 'religion', 'occupation', 'mothers_maiden_name',
        'guardian_name', 'guardian_first_name', 'guardian_middle_name', 'guardian_last_name', 'guardian_suffix',
        'complaint',
    ];

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'contact_number',
        'sex',
        'dob',
        'civil_status',
        'blood_type',
        'address',
        'house_no',
        'street',
        'building',
        'barangay',
        'city_province',
        'philhealth_number',
        'education',
        'religion',
        'occupation',
        'mothers_maiden_name',
        'classification',
        'type',
        'preferred_date',
        'preferred_time',
        'data_privacy_agreed',
        'reference_number',
        'status',
        'complaint',
        'guardian_name',
        'guardian_first_name',
        'guardian_middle_name',
        'guardian_last_name',
        'guardian_suffix',
        'guardian_relation',
        'guardian_contact',
        'guardian_philhealth',
        'is_follow_up',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'reminder_sent_at',
    ];

    protected $casts = [
        'dob' => 'date',
        'preferred_date' => 'datetime',
        'data_privacy_agreed' => 'boolean',
        'philhealth_number' => 'encrypted',
        'guardian_philhealth' => 'encrypted',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($apt) {
            $apt->barangay = \App\Models\Patient::normalizeBarangay($apt->barangay, $apt->address);
        });
    }

    use \Illuminate\Database\Eloquent\Prunable;

    public function prunable()
    {
        // Retain cancelled records for 30 days before automated pruning
        return static::where('status', 'cancelled')
            ->where('updated_at', '<=', now()->subDays(30));
    }

    public function cancelledBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'cancelled_by');
    }

    public function getGuardianFullNameAttribute()
    {
        if ($this->guardian_first_name || $this->guardian_last_name) {
            return trim("{$this->guardian_first_name} {$this->guardian_middle_name} {$this->guardian_last_name} {$this->guardian_suffix}");
        }

        return $this->guardian_name;
    }

    public function getMaskedPhilhealthNumberAttribute()
    {
        if (! $this->philhealth_number) {
            return null;
        }

        return preg_replace('/(\d{2})-(\d{5})(\d{4})-(\d{1})/', '$1-*****$3-$4', $this->philhealth_number);
    }
}
