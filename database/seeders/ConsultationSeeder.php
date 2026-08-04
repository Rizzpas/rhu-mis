<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConsultationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We assume UserSeeder and PatientSeeder have run before this.
        $patient = Patient::where('first_name', 'Dan Irylle')->where('last_name', 'Isuga')->first();
        $doctor = User::where('email', 'doctor3@rhu.gov.ph')->first();
        $nurse = User::where('email', 'nurse1@rhu.gov.ph')->first();

        // 1st Consultation (Doctor)
        if ($patient && $doctor) {
            Consultation::firstOrCreate(
                ['queue_number' => 'ADU-001'],
                [
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'consultation_date' => '2026-03-12',
                    'temperature' => '37',
                    'heart_rate' => '80',
                    'respiratory_rate' => '16',
                    'pulse_rate' => '80',
                    'blood_pressure' => '110/90',
                    'weight' => '70',
                    'height' => '174',
                    'bmi' => '23.12',
                    'spo2' => '98',
                    'past_medical_history' => 'N/A',
                    'medicine_taken' => 'N/A',
                    'symptoms' => 'Cough, Migraine',
                    'status' => 'queued',
                ]
            );
        }

        // 2nd Consultation (Nurse)
        if ($patient && $nurse) {
            Consultation::firstOrCreate(
                ['queue_number' => 'ADU-002'],
                [
                    'patient_id' => $patient->id,
                    'nurse_id' => $nurse->id,
                    'consultation_date' => '2026-03-12',
                    'temperature' => '37',
                    'heart_rate' => '80',
                    'respiratory_rate' => '16',
                    'pulse_rate' => '80',
                    'blood_pressure' => '110/90',
                    'weight' => '70',
                    'height' => '174',
                    'bmi' => '23.12',
                    'spo2' => '98',
                    'past_medical_history' => 'N/A',
                    'medicine_taken' => 'N/A',
                    'symptoms' => 'Cough, Migraine',
                    'status' => 'queued',
                ]
            );
        }
    }
}
