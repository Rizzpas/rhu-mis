<?php

namespace Database\Seeders;

use App\Models\FacilityUnit;
use Illuminate\Database\Seeder;

class FacilityUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'name' => 'Main Health Center',
                'slug' => 'main-health-center',
                'description' => 'Comprehensive primary check-ups, diagnostic services, chronic disease management, and general medical consultations for all ages.',
                'category' => 'General Medicine',
                'operating_hours' => 'Monday - Friday: 8:00 AM - 5:00 PM',
                'contact_number' => '(046) 414-0879',
                'location' => 'Main RHU Building, Ground Floor',
                'services_offered' => [
                    'General Medical Consultations',
                    'Pediatric Health Assessment',
                    'Adult & Senior Care Management',
                    'Preventive Wellness Screening',
                    'Medical Certificate Issuance',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Lying-in Clinic & Birthing Facility',
                'slug' => 'lying-in-clinic',
                'description' => '24/7 dedicated maternal healthcare, safe normal spontaneous delivery, prenatal & postnatal check-ups, and essential newborn care.',
                'category' => 'Maternity & Child Health',
                'operating_hours' => '24 Hours / 7 Days a Week',
                'contact_number' => '(046) 414-0880',
                'location' => 'Maternal Wing, 1st Floor',
                'services_offered' => [
                    '24/7 Normal Spontaneous Delivery',
                    'Prenatal and Postnatal Consultations',
                    'Newborn Screening & Hearing Test',
                    'Lactation & Breastfeeding Counseling',
                    'Family Planning & Reproductive Health',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'OB-GYN Unit',
                'slug' => 'ob-gyn-unit',
                'description' => 'Specialized obstetrics and gynecological care for women of reproductive age, high-risk pregnancy monitoring, and cervical cancer screening.',
                'category' => 'Women\'s Health',
                'operating_hours' => 'Monday - Friday: 8:00 AM - 4:00 PM',
                'contact_number' => '(046) 414-0881',
                'location' => 'Specialty Clinic Wing, Room 104',
                'services_offered' => [
                    'Obstetric & Gynecological Examination',
                    'High-Risk Pregnancy Evaluation',
                    'Pap Smear & Visual Inspection with Acetic Acid (VIA)',
                    'Pre-marital Counseling',
                    'Adolescent Reproductive Health',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Dental Clinic',
                'slug' => 'dental-clinic',
                'description' => 'Oral health evaluation, emergency tooth extractions, preventive dental prophylaxis, fluoride applications, and oral hygiene education.',
                'category' => 'Dental Care',
                'operating_hours' => 'Monday - Friday: 8:00 AM - 5:00 PM',
                'contact_number' => '(046) 414-0882',
                'location' => 'Dental Section, 2nd Floor',
                'services_offered' => [
                    'Dental Examination & Consultation',
                    'Permanent & Deciduous Tooth Extraction',
                    'Oral Prophylaxis (Cleaning)',
                    'Topical Fluoride Application',
                    'Community Oral Health Education',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'TB DOTS Facility',
                'slug' => 'tb-dots-facility',
                'description' => 'National Tuberculosis Program (NTP) accredited center providing free GeneXpert sputum testing, daily directly observed therapy, and monitoring.',
                'category' => 'Infectious Diseases',
                'operating_hours' => 'Monday - Friday: 8:00 AM - 3:00 PM',
                'contact_number' => '(046) 414-0883',
                'location' => 'Infectious Disease Wing (Isolated Entrance)',
                'services_offered' => [
                    'GeneXpert & Sputum AFB Microscopy',
                    'Free Category I & II TB Medications',
                    'Directly Observed Treatment Short-course (DOTS)',
                    'Contact Tracing & Preventive Therapy',
                    'Monthly Sputum Follow-up Monitoring',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Animal Bite Treatment Center (ABTC)',
                'slug' => 'animal-bite-center',
                'description' => 'DOH-certified animal bite center providing post-exposure rabies prophylaxis (PEP), wound assessment, anti-tetanus immunization, and rabies education.',
                'category' => 'Emergency & Immunization',
                'operating_hours' => 'Monday - Friday: 8:00 AM - 5:00 PM',
                'contact_number' => '(046) 414-0884',
                'location' => 'ABTC Pavilion, East Wing',
                'services_offered' => [
                    'Animal Bite Wound Assessment & Washing',
                    'Anti-Rabies Vaccine (Purified Chick Embryo/Vero Cell)',
                    'Rabies Immune Globulin (RIG) Administration',
                    'Tetanus Toxoid & Anti-Tetanus Serum',
                    'Responsible Pet Ownership Counseling',
                ],
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($units as $unit) {
            FacilityUnit::updateOrCreate(
                ['slug' => $unit['slug']],
                $unit
            );
        }
    }
}
