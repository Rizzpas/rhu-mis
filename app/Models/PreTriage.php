<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreTriage extends Model
{
    use \App\Traits\Auditable, \App\Traits\Sterilizable;

    protected $sterilizable = [
        'first_name', 'middle_name', 'last_name', 'suffix', 'patient_name', 'chief_complaint', 'medicine_taken', 'known_allergies',
    ];

    protected $fillable = [
        'patient_name',
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'dob',
        'blood_pressure',
        'temperature',
        'weight',
        'height',
        'heart_rate',
        'respiratory_rate',
        'pulse_rate',
        'oxygen_saturation',
        'spo2',
        'chief_complaint',
        'past_medical_history',
        'medicine_taken',
        'known_allergies',
        'symptoms',
        'classification',
        'recorded_by',
        'patient_id',
        'appointment_id',
        'status',
        'encoding_duration_seconds',
        'is_emergency',
    ];

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
