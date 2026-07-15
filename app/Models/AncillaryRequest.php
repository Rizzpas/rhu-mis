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
        'result_file_path'
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
