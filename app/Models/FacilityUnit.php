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

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            $clean = ltrim(str_replace(['uploads/', 'storage/'], '', $this->image_path), '/');
            if (file_exists(public_path('uploads/' . $clean))) {
                return asset('uploads/' . $clean);
            }
            if (file_exists(public_path($this->image_path))) {
                return asset($this->image_path);
            }
        }

        $defaultFacilityImage = 'assets/images/facilities/' . $this->slug . '.jpg';
        if (file_exists(public_path($defaultFacilityImage))) {
            return asset($defaultFacilityImage);
        }

        return asset('assets/images/rhu-facility.jpg');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
