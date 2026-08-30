<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityUnit extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'category', 'operating_hours',
        'contact_number', 'location', 'image_path', 'is_active',
        'services_offered', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'services_offered' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
