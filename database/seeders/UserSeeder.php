<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (\App\Models\User::where('email', 'super_admin@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Super Admin (RHU Head)',
                'email' => 'super_admin@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
            ]);
        }
        if (\App\Models\User::where('email', 'admin@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        if (\App\Models\User::where('email', 'frontdesk@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Information Desk',
                'email' => 'frontdesk@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'information_desk',
            ]);
        }

        if (\App\Models\User::where('email', 'nurse1@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Nurse Joy',
                'email' => 'nurse1@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'clinical_nurse',
            ]);
        }

        if (\App\Models\User::where('email', 'nurse_station@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Vitals Nurse Station (Shared)',
                'email' => 'nurse_station@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'vitals_nurse',
            ]);
        }

        // Add dummy Doctors replacing old Doctors
        if (\App\Models\User::where('email', 'doctor1@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Dr. Anna Smith',
                'email' => 'doctor1@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'pedia_doctor',
                'status' => 'Present',
                'schedule' => 'Mon-Fri 8AM-5PM',
            ]);
        }

        if (\App\Models\User::where('email', 'doctor2@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Dr. Robert Davis',
                'email' => 'doctor2@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'regular_doctor',
                'status' => 'Online',
                'schedule' => 'Mon-Thu 9AM-4PM',
            ]);
        }

        if (\App\Models\User::where('email', 'doctor3@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Dr. Sarah Johnson',
                'email' => 'doctor3@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'regular_doctor',
                'status' => 'Offline',
                'schedule' => 'Tue, Thu, Sat',
            ]);
        }

        if (\App\Models\User::where('email', 'pharmacy@rhu.gov.ph')->doesntExist()) {
            \App\Models\User::factory()->create([
                'name' => 'Pharmacist Staff',
                'email' => 'pharmacy@rhu.gov.ph',
                'password' => bcrypt('password'),
                'role' => 'pharmacy',
            ]);
        }
    }
}
