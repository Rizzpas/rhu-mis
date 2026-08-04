<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'medicine_id',
        'batch_id',
        'action',
        'quantity_changed',
        'remarks',
        'performed_by',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function batch()
    {
        return $this->belongsTo(MedicineBatch::class, 'batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
