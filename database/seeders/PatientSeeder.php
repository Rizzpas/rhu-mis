<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::firstOrCreate(
            ['first_name' => 'Dan Irylle', 'last_name' => 'Isuga'],
            [
                'middle_name' => 'Sotomayor',
                'sex' => 'Male',
                'civil_status' => 'Single',
                'blood_type' => 'O+',
                'dob' => '2004-12-30',
                'address' => 'BLK 82 LOT 18, Biga II, Silang, Cavite',
                'contact_number' => '09910285503',
                'philhealth_number' => '12-312312312-3',
                'mothers_maiden_name' => 'Ritchelle C. Sotomayor',
                'occupation' => 'Student',
                'education' => 'College Undergraduate',
                'religion' => 'Iglesia ni Cristo',
                'classification' => 'Regular Adult',
            ]
        );
    }
}
