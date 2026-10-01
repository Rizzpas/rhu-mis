<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityUnit extends Model
{
    public const CATEGORIES = [
        'General Medicine',
        'Maternity & Child Health',
        'Women\'s Health',
        'Dental Care',
        'Infectious Diseases',
        'Emergency & Immunization',
        'Diagnostic & Laboratory',
    ];

    public const PREDEFINED_SERVICES = [
        'General Consultation',
        'Pediatric Care',
        'Maternal & Prenatal Care',
        'Normal Delivery / Birthing',
        'Newborn Screening',
        'Post-Partum Care',
        'Dental Consultation & Cleaning',
        'Tooth Extraction',
        'TB DOTS Screening & GeneXpert',
        'Directly Observed TB Therapy',
        'Animal Bite Assessment',
        'Anti-Rabies Vaccination',
        'Tetanus Toxoid Injection',
        'Routine Child Immunization',
        'Family Planning & Counseling',
        'Laboratory & Diagnostic Testing',
        'Pharmacy & Medicine Dispensing',
        'Wound Care & Minor Surgery',
    ];

    protected $fillable = [
        'name', 'slug', 'description', 'category', 'operating_hours',
        'operating_hours_structured', 'contact_number', 'contacts_structured',
        'location', 'image_path', 'is_active', 'services_offered', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'services_offered' => 'array',
        'operating_hours_structured' => 'array',
        'contacts_structured' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    protected $appends = ['image_url', 'is_main_center'];

    public function getIsMainCenterAttribute(): bool
    {
        return $this->slug === 'main-health-center';
    }

    public function getContactListAttribute(): array
    {
        if (!empty($this->contacts_structured) && is_array($this->contacts_structured)) {
            return array_map(function ($c) {
                $raw = is_array($c) ? ($c['number'] ?? '') : (string) $c;
                $digits = preg_replace('/[^\d+]/', '', $raw);
                return [
                    'label' => is_array($c) ? ($c['label'] ?? 'Contact') : 'Contact',
                    'number' => $raw,
                    'tel' => $digits,
                ];
            }, $this->contacts_structured);
        }

        if (!empty($this->contact_number)) {
            $digits = preg_replace('/[^\d+]/', '', $this->contact_number);
            return [[
                'label' => 'Main Line',
                'number' => $this->contact_number,
                'tel' => $digits,
            ]];
        }

        return [];
    }

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
