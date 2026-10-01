<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    protected $fillable = [
        'consultation_id',
        'patient_id',
        'doctor_id',
        'status',
        'dispensed_by',
        'dispensed_at',
        'pharmacist_notes',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'expired_at',
        'expires_at',
    ];

    protected $casts = [
        'dispensed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expired_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'partially_dispensed']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['dispensed', 'partially_dispensed', 'cancelled', 'expired']);
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? false;
    }

    public function outstandingTotal(): int
    {
        return $this->items->sum(function ($item) {
            return max(($item->quantity ?? 0) - ($item->dispensed_quantity ?? 0), 0);
        });
    }
}