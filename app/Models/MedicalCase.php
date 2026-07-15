<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalCase extends Model
{
    protected $fillable = [
        'case_number',
        'patient_id',
        'consultation_id',
        'pre_triage_id',
        'diagnosis',
        'prescription',
        'vitals_snapshot',
        'closed_at',
    ];

    protected $casts = [
        'vitals_snapshot' => 'array',
        'closed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function preTriage()
    {
        return $this->belongsTo(PreTriage::class);
    }
}
