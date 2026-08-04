<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'TB DOTS',
                'description' => 'Comprehensive Tuberculosis treatment and monitoring program providing direct observation therapy.',
                'steps' => [
                    ['title' => 'Sputum Collection', 'description' => 'Submit sputum samples for laboratory analysis.'],
                    ['title' => 'Diagnosis & Enrollment', 'description' => 'Review results and enrollment in the NTP program.'],
                    ['title' => 'Treatment', 'description' => 'Daily medication intake under direct supervision.'],
                    ['title' => 'Monitoring', 'description' => 'Monthly follow-up checkups and testing.'],
                ],
            ],
            [
                'name' => 'Lying-In Clinic',
                'description' => 'Safe, affordable, and quality maternal and newborn care services for low-risk deliveries.',
                'steps' => [
                    ['title' => 'Prenatal Visits', 'description' => 'Complete at least 4 standardized prenatal checkups.'],
                    ['title' => 'Labor Admission', 'description' => 'Admission upon onset of active labor.'],
                    ['title' => 'Delivery', 'description' => 'Assisted normal delivery by skilled health professionals.'],
                    ['title' => 'Postpartum Care', 'description' => 'Observation and care for mother and baby for 24 hours.'],
                ],
            ],
            [
                'name' => 'Family Planning',
                'description' => 'Provision of modern family planning methods and counseling services.',
                'steps' => [
                    ['title' => 'Registration', 'description' => 'Fill out Family Planning Form 1.'],
                    ['title' => 'Counseling', 'description' => 'One-on-one session to discuss FP methods.'],
                    ['title' => 'Method Selection', 'description' => 'Choose preferred method (Pills, Injectables, IUD, etc.).'],
                    ['title' => 'Dispensing', 'description' => 'Administration of chosen method.'],
                ],
            ],
            [
                'name' => 'Health / Sanitary Card',
                'description' => 'Issuance of health certificates for employment and business permit requirements.',
                'steps' => [
                    ['title' => 'Laboratory Tests', 'description' => 'Submit X-ray, Urinalysis, and Stool Exam results.'],
                    ['title' => 'Payment', 'description' => 'Pay corresponding fees at the Treasury Office.'],
                    ['title' => 'Seminar', 'description' => 'Attend Food Handlers Seminar (if applicable).'],
                    ['title' => 'Release', 'description' => 'Claim signed Health/Sanitary Card.'],
                ],
            ],
            [
                'name' => 'Dental Services',
                'description' => 'Oral health examinations, tooth extractions, and preventive dental care.',
                'steps' => [
                    ['title' => 'Consultation', 'description' => 'Dental examination and history taking.'],
                    ['title' => 'Procedure', 'description' => 'Extraction or temporary filling as assessed.'],
                    ['title' => 'Medication', 'description' => 'Prescription of antibiotics or pain relievers if needed.'],
                ],
            ],
            [
                'name' => 'General Consultation',
                'description' => 'Diagnosis and treatment of common illnesses for all age groups.',
                'steps' => [
                    ['title' => 'Triage', 'description' => 'Vital signs taking and initial interview.'],
                    ['title' => 'Doctor Consultation', 'description' => 'Medical assessment and physical examination.'],
                    ['title' => 'Management', 'description' => 'Prescription, treatment, or referral to higher facility.'],
                ],
            ],
        ];

        foreach ($services as $service) {
            \App\Models\Service::firstOrCreate(
                ['name' => $service['name']],
                [
                    'slug' => \Illuminate\Support\Str::slug($service['name']),
                    'description' => $service['description'],
                    'image_path' => null, // Placeholder or null
                    'steps' => $service['steps'],
                ]
            );
        }
    }
}
