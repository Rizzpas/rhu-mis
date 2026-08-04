<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use \App\Traits\Auditable, \App\Traits\Sterilizable;

    protected $sterilizable = [
        'diagnosis', 'prescription', 'medical_notes', 'followup_reason',
    ];

    protected $touches = ['patient'];

    protected $fillable = [
        'patient_id', 'doctor_id', 'nurse_id', 'pre_triage_id', 'consultation_date',
        'queue_number', 'status', 'severity', 'diagnosis',
        'prescription',
        'medical_notes',
        'is_followup_needed',
        'followup_date',
        'followup_reason',
        'followup_doctor_id',
        'followup_completed_at',
        'blood_pressure',
        'temperature',
        'weight',
        'height',
        'heart_rate',
        'respiratory_rate',
        'pulse_rate',
        'spo2',
        'consultation_start_time', 'consultation_end_time',
    ];

    protected $casts = [
        'consultation_date' => 'date',
        'consultation_start_time' => 'datetime',
        'consultation_end_time' => 'datetime',
        'is_followup_needed' => 'boolean',
        'followup_date' => 'date',
        'followup_completed_at' => 'datetime',
    ];

    public function preTriage()
    {
        return $this->belongsTo(PreTriage::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function ancillaryRequests()
    {
        return $this->hasMany(AncillaryRequest::class);
    }

    public function prescriptionRecord()
    {
        return $this->hasOne(Prescription::class);
    }
}
