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

    public function getVitalsSnapshotAttribute($value): array
    {
        return $this->normalizeVitals($value);
    }

    public function setVitalsSnapshotAttribute($value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['vitals_snapshot'] = json_encode($decoded ?? $value);
        } elseif (is_null($value)) {
            $this->attributes['vitals_snapshot'] = null;
        } else {
            $this->attributes['vitals_snapshot'] = json_encode($value);
        }
    }

    public function normalizeVitals($value): array
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            if (is_string($decoded)) {
                $doubleDecoded = json_decode($decoded, true);
                if (is_array($doubleDecoded)) {
                    return $doubleDecoded;
                }
            }
        }

        return (array) $value;
    }

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
