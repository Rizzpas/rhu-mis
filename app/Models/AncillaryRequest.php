<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AncillaryRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_id',
        'type',
        'test_name',
        'status',
        'remarks',
        'result_file_path',
        'result_data',
        'completed_by',
        'completed_at',
        'archived_at',
        'archived_reason',
        'specimen_collected_at',
        'specimen_collected_by',
        'processing_started_at',
        'rejection_reason',
        'rejected_by',
        'rejected_at',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'is_amended',
        'amendment_reason',
        'amended_by',
        'amended_at',
        'previous_result_data',
        'parent_id',
        'is_repeat',
    ];

    protected $casts = [
        'result_data' => 'array',
        'previous_result_data' => 'array',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
        'specimen_collected_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amended_at' => 'datetime',
        'is_amended' => 'boolean',
        'is_repeat' => 'boolean',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'specimen_collected_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function amender()
    {
        return $this->belongsTo(User::class, 'amended_by');
    }

    public function parentRequest()
    {
        return $this->belongsTo(AncillaryRequest::class, 'parent_id');
    }

    public function repeatRequests()
    {
        return $this->hasMany(AncillaryRequest::class, 'parent_id');
    }

    public function getResultFileUrlAttribute(): ?string
    {
        if (! $this->result_file_path) {
            return null;
        }

        return route('ancillary.file', $this);
    }
}
