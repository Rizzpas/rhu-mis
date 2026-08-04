<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Full Day (Today) - 20 slots
        $fullDate = Carbon::today();
        for ($i = 0; $i < 20; $i++) {
            Appointment::firstOrCreate(
                ['email' => "patient{$i}@gmail.com", 'preferred_date' => $fullDate],
                [
                    'first_name' => 'FullDay',
                    'last_name' => 'Patient'.$i,
                    'sex' => 'Male',
                    'dob' => '2018-05-10',
                    'address' => 'Barangay Test, Quezon City',
                    'classification' => 'Pediatric',
                    'type' => 'pedia',
                    'is_follow_up' => false,
                    'guardian_name' => 'John Patient',
                    'guardian_relation' => 'Mother',
                    'guardian_contact' => '09123456789',
                    'complaint' => 'Routine Checkup',
                    'status' => 'approved',
                    'data_privacy_agreed' => true,
                    'reference_number' => 'REF-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                ]
            );
        }

        // Limited Day (Day after tomorrow) - 15 slots
        $limitedDate = Carbon::tomorrow()->addDay();
        for ($i = 0; $i < 15; $i++) {
            Appointment::firstOrCreate(
                ['email' => "limited{$i}@gmail.com", 'preferred_date' => $limitedDate],
                [
                    'first_name' => 'Limited',
                    'last_name' => 'Patient'.$i,
                    'sex' => 'Female',
                    'dob' => '2020-01-15',
                    'address' => 'Barangay Test, Manila',
                    'classification' => 'Pediatric',
                    'type' => 'pedia',
                    'is_follow_up' => true,
                    'guardian_name' => 'Maria Patient',
                    'guardian_relation' => 'Father',
                    'guardian_contact' => '09123456789',
                    'complaint' => 'Immunization',
                    'status' => 'pending',
                    'data_privacy_agreed' => true,
                    'reference_number' => 'REF-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                ]
            );
        }

        // Available Day (3 days from now) - 5 slots
        $availableDate = Carbon::tomorrow()->addDays(2);
        for ($i = 0; $i < 5; $i++) {
            Appointment::firstOrCreate(
                ['email' => "avail{$i}@gmail.com", 'preferred_date' => $availableDate],
                [
                    'first_name' => 'Available',
                    'last_name' => 'Patient'.$i,
                    'sex' => 'Male',
                    'dob' => '2019-09-20',
                    'address' => 'Barangay Available, Makati',
                    'classification' => 'Pediatric',
                    'type' => 'pedia',
                    'is_follow_up' => false,
                    'guardian_name' => 'Lola Patient',
                    'guardian_relation' => 'Grandparent',
                    'guardian_contact' => '09123456789',
                    'complaint' => 'Checkup',
                    'status' => 'arrived',
                    'data_privacy_agreed' => true,
                    'reference_number' => 'REF-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                ]
            );
        }
    }
}
