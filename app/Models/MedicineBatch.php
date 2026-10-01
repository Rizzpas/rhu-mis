<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineBatch extends Model
{
    protected $fillable = [
        'medicine_id',
        'batch_number',
        'expiration_date',
        'quantity',
        'original_quantity',
        'status',
        'disposed_at',
        'disposed_by',
        'disposal_reason',
        'disposal_notes',
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'disposed_at' => 'datetime',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function disposer()
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDisposed($query)
    {
        return $query->where('status', 'disposed');
    }
}
