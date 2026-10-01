<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use \App\Traits\Auditable, \App\Traits\Sterilizable, \Illuminate\Database\Eloquent\Prunable, SoftDeletes;

    // Centralized pediatric age cutoff (inclusive). A patient is pediatric if age <= 12
    public const MAX_PEDIATRIC_AGE = 12;

    protected $fillable = [
        'patient_id', 'first_name', 'middle_name', 'last_name', 'suffix', 'sex', 'dob', 'civil_status',
        'blood_type', 'known_allergies', 'address', 'house_no', 'street', 'building', 'barangay', 'city_province',
        'philhealth_number', 'education', 'religion', 'occupation', 'mothers_maiden_name',
        'classification', 'email', 'contact_number',
        'guardian_name', 'guardian_first_name', 'guardian_middle_name', 'guardian_last_name', 'guardian_suffix',
        'guardian_relation', 'guardian_contact', 'guardian_philhealth',
        'expires_at', 'next_followup_date', 'previous_doctor_id',
    ];

    protected $sterilizable = [
        'first_name', 'middle_name', 'last_name', 'suffix', 'address', 'house_no', 'street', 'building',
        'barangay', 'city_province', 'religion', 'occupation', 'mothers_maiden_name',
        'guardian_name', 'guardian_first_name', 'guardian_middle_name', 'guardian_last_name', 'guardian_suffix',
    ];

    protected $casts = [
        'dob' => 'date',
        'expires_at' => 'datetime',
        'philhealth_number' => 'encrypted',
        'guardian_philhealth' => 'encrypted',
        'next_followup_date' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'patient_id';
    }

    protected static function booted()
    {
        static::creating(function ($patient) {
            if (empty($patient->patient_id)) {
                $year = now()->year;

                \Illuminate\Support\Facades\DB::transaction(function () use ($patient, $year) {
                    $latest = self::whereYear('created_at', $year)
                        ->lockForUpdate()
                        ->orderBy('id', 'desc')
                        ->first();

                    $sequence = $latest ? intval(substr($latest->patient_id, -5)) + 1 : 1;
                    $patient->patient_id = sprintf('RHU-%04d-%05d', $year, $sequence);
                });
            }
        });

        static::saving(function ($patient) {
            // Automatically push expiration 10 years from now whenever touched/saved
            $patient->expires_at = now()->addYears(10);

            // ── Auto-correct classification based on DOB ──────────────
            // PWD is not age-based, so skip auto-correction for that classification
            if ($patient->dob && $patient->classification !== 'PWD') {
                $age = \Carbon\Carbon::parse($patient->dob)->age;

                if ($age <= self::MAX_PEDIATRIC_AGE && $patient->classification !== 'Pediatric') {
                    $patient->classification = 'Pediatric';
                } elseif ($age > self::MAX_PEDIATRIC_AGE && $age < 60 && $patient->classification === 'Pediatric') {
                    $patient->classification = 'Regular Adult';
                } elseif ($age >= 60 && $patient->classification !== 'Senior Citizen') {
                    $patient->classification = 'Senior Citizen';
                } elseif ($age > self::MAX_PEDIATRIC_AGE && $age < 60 && $patient->classification === 'Senior Citizen') {
                    $patient->classification = 'Regular Adult';
                }
            }

            // ── Auto-populate / normalize barangay from address if missing ───
            $patient->barangay = self::normalizeBarangay($patient->barangay, $patient->address);
        });
    }

    public const SILANG_BARANGAYS = [
        'Biga I', 'Biga II', 'Biga 1', 'Biga 2', 'Biga Ii',
        'Kalubkob', 'Bulihan', 'Lucsuhin', 'Balite I', 'Balite II', 'Balite 1', 'Balite 2',
        'Acacia', 'Adlas', 'Anahaw I', 'Anahaw II', 'Balubad', 'Banaba',
        'Batas', 'Biluso', 'Bucal', 'Buho', 'Cabangaan', 'Carmen', 'Hoyo',
        'Hukay', 'Iba', 'Inchican', 'Ipil I', 'Ipil II', 'Kaong', 'Lalaan I',
        'Lalaan II', 'Litlit', 'Lumil', 'Maguyam', 'Malabag', 'Malaking Tatyao',
        'Mataas Na Burol', 'Munting Ilog', 'Narra I', 'Narra II', 'Narra III',
        'Paligawan', 'Pasong Langka', 'Pooc I', 'Pooc II', 'Pulong Bunga',
        'Pulong Saging', 'Puting Kahoy', 'Sabutan', 'San Miguel I', 'San Miguel II',
        'San Vicente I', 'San Vicente II', 'Santol', 'Tartaria', 'Tibig', 'Toledo',
        'Tubuan I', 'Tubuan II', 'Tubuan III', 'Ulat', 'Yakal',
        'Barangay I', 'Barangay II', 'Barangay III', 'Barangay IV', 'Barangay V',
    ];

    public static function normalizeBarangay(?string $barangay, ?string $address = null): ?string
    {
        $brgy = trim($barangay ?? '');
        if (!$brgy && !empty($address)) {
            $known = self::SILANG_BARANGAYS;
            usort($known, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));

            foreach ($known as $kb) {
                if (preg_match('/\b' . preg_quote($kb, '/') . '\b/i', $address)) {
                    $brgy = $kb;
                    break;
                }
            }
        }

        if ($brgy) {
            $normalized = preg_replace_callback('/\b(I|Ii|Iii|Iv|V)\b/i', fn($m) => strtoupper($m[0]), ucwords(strtolower($brgy)));
            $normalized = preg_replace('/\b1\b/', 'I', $normalized);
            $normalized = preg_replace('/\b2\b/', 'II', $normalized);
            return $normalized;
        }

        return $barangay;
    }

    /**
     * Get the prunable model query.
     */
    public function prunable()
    {
        return static::whereNotNull('expires_at')->where('expires_at', '<=', now());
    }

    public function getFullNameAttribute()
    {
        if ($this->middle_name) {
            return "{$this->first_name} {$this->middle_name} {$this->last_name}";
        }

        return "{$this->first_name} {$this->last_name}";
    }

    public function getGuardianFullNameAttribute()
    {
        if ($this->guardian_first_name || $this->guardian_last_name) {
            return trim("{$this->guardian_first_name} {$this->guardian_middle_name} {$this->guardian_last_name} {$this->guardian_suffix}");
        }

        return $this->guardian_name;
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id', 'patient_id');
    }

    public function medicalCases()
    {
        return $this->hasMany(MedicalCase::class, 'patient_id', 'patient_id')->orderBy('closed_at', 'desc');
    }

    public function getMaskedPhilhealthNumberAttribute()
    {
        $phn = $this->classification === 'Pediatric' ? ($this->guardian_philhealth ?: $this->philhealth_number) : $this->philhealth_number;
        if (! $phn) {
            return null;
        }

        return preg_replace('/(\d{2})-(\d{5})(\d{4})-(\d{1})/', '$1-*****$3-$4', $phn);
    }

    public function getIsFollowUpAttribute()
    {
        $latestFollowUp = Consultation::where('patient_id', $this->patient_id)
            ->where('is_followup_needed', true)
            ->whereNotNull('followup_date')
            ->whereNull('followup_completed_at') // Only unfulfilled follow-ups
            ->orderBy('consultation_date', 'desc')
            ->first();

        if ($latestFollowUp) {
            $followUpDate = \Carbon\Carbon::parse($latestFollowUp->followup_date)->startOfDay();
            if (\Carbon\Carbon::today()->subMonths(3)->lte($followUpDate)) {
                return true;
            }
        }

        return false;
    }

    public function isPediatric(): bool
    {
        return $this->dob && $this->dob->age <= self::MAX_PEDIATRIC_AGE;
    }
}
