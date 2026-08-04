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
    ];

    protected $casts = [
        'result_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
