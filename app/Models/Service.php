<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image_path', 'steps'];

    protected $casts = [
        'steps' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
