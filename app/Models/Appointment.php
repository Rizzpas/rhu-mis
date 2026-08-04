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
    ];

    protected $casts = [
        'dob' => 'date',
        'preferred_date' => 'datetime',
        'data_privacy_agreed' => 'boolean',
        'philhealth_number' => 'encrypted',
        'guardian_philhealth' => 'encrypted',
    ];

    use \Illuminate\Database\Eloquent\Prunable;

    public function prunable()
    {
        return static::where('status', 'cancelled')
            ->where('updated_at', '<=', now()->subHours(12));
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
