<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'medicine_name',
        'dosage',
        'frequency',
        'duration',
        'quantity',
        'dispensed_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'dispensed_quantity' => 'integer',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function getOutstandingQuantityAttribute(): int
    {
        return max(($this->quantity ?? 0) - ($this->dispensed_quantity ?? 0), 0);
    }
}