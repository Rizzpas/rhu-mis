<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes, \Illuminate\Database\Eloquent\Prunable, \App\Traits\Auditable, \App\Traits\Sterilizable;
    
    protected $fillable = [
        'patient_id', 'first_name', 'middle_name', 'last_name', 'suffix', 'sex', 'dob', 'civil_status', 
        'blood_type', 'known_allergies', 'address', 'house_no', 'street', 'building', 'barangay', 'city_province',
        'philhealth_number', 'education', 'religion', 'occupation', 'mothers_maiden_name', 
        'classification', 'email', 'contact_number', 
        'guardian_name', 'guardian_first_name', 'guardian_middle_name', 'guardian_last_name', 'guardian_suffix',
        'guardian_relation', 'guardian_contact', 'guardian_philhealth', 
        'expires_at', 'next_followup_date', 'previous_doctor_id'
    ];

    protected $sterilizable = [
        'first_name', 'middle_name', 'last_name', 'suffix', 'address', 'house_no', 'street', 'building', 
        'barangay', 'city_province', 'religion', 'occupation', 'mothers_maiden_name', 
        'guardian_name', 'guardian_first_name', 'guardian_middle_name', 'guardian_last_name', 'guardian_suffix'
    ];

    protected $casts = [
        'dob' => 'date',
        'expires_at' => 'datetime',
        'philhealth_number' => 'encrypted',
        'guardian_philhealth' => 'encrypted',
    ];
    public function getRouteKeyName()
    {
        return 'patient_id';
    }

    protected static function booted()
    {
        static::creating(function ($patient) {
            if (empty($patient->patient_id)) {
                $year = now()->year;
                
                \Illuminate\Support\Facades\DB::transaction(function () use ($patient, $year) {
                    $latest = self::whereYear('created_at', $year)
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->first();
                        
                    $sequence = $latest ? intval(substr($latest->patient_id, -5)) + 1 : 1;
                    $patient->patient_id = sprintf('RHU-%04d-%05d', $year, $sequence);
                });
            }
        });

        static::saving(function ($patient) {
            // Automatically push expiration 10 years from now whenever touched/saved
            $patient->expires_at = now()->addYears(10);
        });
    }

    /**
     * Get the prunable model query.
     */
    public function prunable()
    {
        return static::whereNotNull('expires_at')->where('expires_at', '<=', now());
    }

    public function getFullNameAttribute()
    {
        if ($this->middle_name) {
            return "{$this->first_name} {$this->middle_name} {$this->last_name}";
        }
        return "{$this->first_name} {$this->last_name}";
    }

    public function getGuardianFullNameAttribute()
    {
        if ($this->guardian_first_name || $this->guardian_last_name) {
            return trim("{$this->guardian_first_name} {$this->guardian_middle_name} {$this->guardian_last_name} {$this->guardian_suffix}");
        }
        return $this->guardian_name;
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id', 'patient_id');
    }

    public function medicalCases()
    {
        return $this->hasMany(MedicalCase::class, 'patient_id', 'patient_id');
    }

    public function getMaskedPhilhealthNumberAttribute()
    {
        $phn = $this->classification === 'Pediatric' ? ($this->guardian_philhealth ?: $this->philhealth_number) : $this->philhealth_number;
        if (!$phn) return null;
        return preg_replace('/(\d{2})-(\d{5})(\d{4})-(\d{1})/', '$1-*****$3-$4', $phn);
    }

    public function getIsFollowUpAttribute()
    {
        $latestFollowUp = Consultation::where('patient_id', $this->patient_id)
            ->where('is_followup_needed', true)
            ->whereNotNull('followup_date')
            ->whereNull('followup_completed_at') // Only unfulfilled follow-ups
            ->orderBy('consultation_date', 'desc')
            ->first();

        if ($latestFollowUp) {
            $followUpDate = \Carbon\Carbon::parse($latestFollowUp->followup_date)->startOfDay();
            if (\Carbon\Carbon::today()->subMonths(3)->lte($followUpDate)) {
                return true;
            }
        }
        return false;
    }
}
