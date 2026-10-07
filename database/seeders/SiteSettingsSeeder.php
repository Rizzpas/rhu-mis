<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Top Bar ─────────────────────────────────────────
            ['group' => 'topbar', 'key' => 'topbar_republic', 'value' => 'Republic of the Philippines', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_province', 'value' => 'Province of Cavite', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_municipality', 'value' => 'Municipality of Silang', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'clinic_hours', 'value' => 'Mon - Fri | 8:00 AM - 5:00 PM', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'emergency_hotlines', 'value' => '911 | (046) 432-1234', 'type' => 'text'],

            // ── Hero Section ────────────────────────────────────
            ['group' => 'hero', 'key' => 'hero_badge_text', 'value' => 'Municipality of Silang', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_title_line1', 'value' => 'Accessible', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_title_highlight', 'value' => 'Public Healthcare', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_title_line2', 'value' => 'for Every Silang Constituent.', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_description', 'value' => 'The Rural Health Unit is the official municipal healthcare gateway of Silang, Cavite. We provide online appointments, digital triage, doctor consultations, and primary diagnostic referrals.', 'type' => 'textarea'],
            ['group' => 'hero', 'key' => 'hero_image', 'value' => 'assets/images/hero.jpg', 'type' => 'image'],
            ['group' => 'hero', 'key' => 'carousel_hero_title', 'value' => 'RHU Silang, Cavite', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'carousel_hero_subtitle', 'value' => 'Providing Responsive & Quality Healthcare for All Constituents', 'type' => 'text'],

            // ── Footer ──────────────────────────────────────────
            ['group' => 'footer', 'key' => 'footer_address_line1', 'value' => 'M.H del Pilar St.', 'type' => 'text'],
            ['group' => 'footer', 'key' => 'footer_address_line2', 'value' => 'Silang, Cavite', 'type' => 'text'],
            ['group' => 'footer', 'key' => 'footer_phone', 'value' => '(046) 432-1234', 'type' => 'text'],
            ['group' => 'footer', 'key' => 'footer_email', 'value' => 'contact@silang.gov.ph', 'type' => 'text'],

            // ── About (Mission, Vision & Public Service Charter) ───
            ['group' => 'about', 'key' => 'mission_statement', 'value' => 'To provide responsive, equitable, and quality primary healthcare services to all citizens of Silang. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs.', 'type' => 'textarea'],
            ['group' => 'about', 'key' => 'vision_statement', 'value' => 'A healthy, resilient, and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable right for every citizen.', 'type' => 'textarea'],
            ['group' => 'about', 'key' => 'guiding_principles', 'value' => json_encode([
                ['number' => '01', 'title' => 'Compassionate Care', 'description' => 'Treating every patient with dignity, empathy, and dedicated professional attention.'],
                ['number' => '02', 'title' => 'Digital Innovation', 'description' => 'Streamlining triage, clinical schedules, and patient records with modern MIS solutions.'],
                ['number' => '03', 'title' => 'Transparency & Ethics', 'description' => 'Upholding absolute accountability in pharmacy inventories and healthcare governance.'],
                ['number' => '04', 'title' => 'Universal Inclusivity', 'description' => 'Guaranteeing barrier-free medical access for all constituents regardless of status.']
            ]), 'type' => 'json'],

            // ── Pharmacy Settings ─────────────────────────────────────
            ['group' => 'pharmacy', 'key' => 'pharmacy.prescription_expiry_days', 'value' => '3', 'type' => 'integer'],

            // ── Step-by-Step Process (Per Unit) ─────────────────────────────
            ['group' => 'steps', 'key' => 'steps_data_main-health-center', 'value' => json_encode([
                ['title' => 'Check-in & Enrollment', 'description' => 'Visit the Information Desk to check in. New patients are enrolled in the system, while existing records are retrieved instantly.'],
                ['title' => 'Vitals & Screening', 'description' => 'Staff will record your weight, BP, and temperature. Results are encoded directly into your record for the doctor\'s review.'],
                ['title' => 'Evaluation', 'description' => 'Once called, meet your Doctor or Nurse. They will assess your history, provide a diagnosis, and issue an e-prescription.'],
                ['title' => 'Pharmacy / Exit', 'description' => 'Receive referrals for lab tests if needed. Finally, proceed to the RHU Pharmacy to claim your prescribed medication.'],
            ]), 'type' => 'json'],
            ['group' => 'steps', 'key' => 'steps_data_lying-in-clinic', 'value' => json_encode([
                ['title' => 'Admission', 'description' => 'Present your prenatal record book and valid ID at the admission desk. Initial assessment will be conducted immediately.'],
                ['title' => 'Labor Monitoring', 'description' => 'You will be transferred to the labor room where midwives and nurses will monitor your contractions and fetal heart rate.'],
                ['title' => 'Delivery', 'description' => 'Safe delivery facilitated by our trained personnel in a sterile environment.'],
                ['title' => 'Post-partum Care', 'description' => 'Rest in our recovery room. We provide newborn screening and essential post-natal care instructions before discharge.'],
            ]), 'type' => 'json'],
            ['group' => 'steps', 'key' => 'steps_data_dental-clinic', 'value' => json_encode([
                ['title' => 'Registration', 'description' => 'Log your details at the dental reception area. Present your priority number.'],
                ['title' => 'Initial Assessment', 'description' => 'The dental aide will ask about your dental history and current concerns.'],
                ['title' => 'Dental Procedure', 'description' => 'The dentist will perform the necessary procedure (extraction, filling, oral prophylaxis, etc.).'],
                ['title' => 'Prescription & Advice', 'description' => 'Receive post-procedure care instructions and medication prescriptions if needed.'],
            ]), 'type' => 'json'],
            ['group' => 'steps', 'key' => 'steps_data_tb-dots-facility', 'value' => json_encode([
                ['title' => 'Screening', 'description' => 'Consult with the TB DOTS coordinator regarding your symptoms. A sputum test may be requested.'],
                ['title' => 'Diagnostics', 'description' => 'Submit your sputum sample to the laboratory. X-ray referrals may also be provided.'],
                ['title' => 'Treatment Enrollment', 'description' => 'If positive, you will be enrolled in the TB DOTS program and assigned a treatment partner.'],
                ['title' => 'Medication Collection', 'description' => 'Regularly visit the facility to take your medication under the direct observation of our health workers.'],
            ]), 'type' => 'json'],
            ['group' => 'steps', 'key' => 'steps_data_animal-bite-center', 'value' => json_encode([
                ['title' => 'Wound Washing', 'description' => 'Immediately wash the bite/scratch area with soap and running water for 15 minutes before proceeding to the center.'],
                ['title' => 'Assessment', 'description' => 'The center nurse will assess the category of the bite/scratch.'],
                ['title' => 'Vaccination', 'description' => 'Receive the first dose of the anti-rabies vaccine (and ERIG if Category III).'],
                ['title' => 'Follow-up Schedule', 'description' => 'You will be given a vaccination card with dates for your subsequent doses. Do not miss these dates!'],
            ]), 'type' => 'json'],

            // ── FAQ ─────────────────────────────────────────────
            ['group' => 'faq', 'key' => 'faq_items', 'value' => json_encode([
                ['question' => 'What are your operating hours?', 'answer' => 'We are open Monday to Friday, from 8:00 AM to 5:00 PM. Emergency services are available 24/7 at the main facility.'],
                ['question' => 'Do I need an appointment for a check-up?', 'answer' => 'Appointments are highly recommended for specialized clinics (like Dental, Prenatal, and Pediatrics) to ensure you are served promptly. However, we accept walk-ins for general consultations, subject to doctor availability.'],
                ['question' => 'Is the anti-rabies vaccination free?', 'answer' => 'Yes, anti-rabies vaccines are generally free for the first few doses, subject to stock availability. Please check our Announcements page for stock updates.'],
                ['question' => 'What should I bring during my visit?', 'answer' => 'Please bring a valid ID. If you have a PhilHealth ID/MDR, Senior Citizen ID, or PWD ID, please present it at the admitting section.'],
            ]), 'type' => 'json'],

            // ── Privacy Policy ──────────────────────────────────
            ['group' => 'privacy', 'key' => 'privacy_intro', 'value' => 'The Rural Health Unit (RHU) is committed to protecting your personal information. By using our services, you understand and agree to the following:', 'type' => 'textarea'],
            ['group' => 'privacy', 'key' => 'privacy_items', 'value' => json_encode([
                'We collect personal data for medical records, appointment scheduling, and public health tracking.',
                'Your information is treated with strict confidentiality and is only accessible by authorized health personnel.',
                'We do not share your data with third parties unless required by law or for referral purposes with your consent.',
                'You have the right to access, correct, or request deletion of your data (subject to retention laws).',
            ]), 'type' => 'json'],
            ['group' => 'privacy', 'key' => 'privacy_footer', 'value' => 'For any privacy concerns, please contact our Data Protection Officer at the Municipal Hall.', 'type' => 'textarea'],

            // ── Organizational Structure & Leadership ───────────────────
            ['group' => 'organization', 'key' => 'org_kicker', 'value' => 'Leadership & Governance', 'type' => 'text'],
            ['group' => 'organization', 'key' => 'org_title', 'value' => 'Organizational Structure & Leadership', 'type' => 'text'],
            ['group' => 'organization', 'key' => 'org_subtitle', 'value' => 'The dedicated healthcare administrators, medical doctors, nurses, midwives, and diagnostic specialists of Rural Health Unit — Silang, Cavite.', 'type' => 'textarea'],
            ['group' => 'organization', 'key' => 'mho_badge', 'value' => 'Executive Head', 'type' => 'text'],
            ['group' => 'organization', 'key' => 'mho_subbadge', 'value' => 'Head of Agency', 'type' => 'text'],
            ['group' => 'organization', 'key' => 'mho_name', 'value' => 'Jericho Joshua E. Palay, MD', 'type' => 'text'],
            ['group' => 'organization', 'key' => 'mho_title', 'value' => 'Municipal Health Officer', 'type' => 'text'],
            ['group' => 'organization', 'key' => 'mho_oversight_title', 'value' => 'Executive Oversight:', 'type' => 'text'],
            ['group' => 'organization', 'key' => 'mho_oversight_desc', 'value' => 'Clinical governance, health policy, and public healthcare across all 64 barangays.', 'type' => 'textarea'],
            ['group' => 'organization', 'key' => 'mho_image', 'value' => '', 'type' => 'image'],

            ['group' => 'organization', 'key' => 'org_medical_officers', 'value' => json_encode([
                ['name' => 'Angel Casapao, MD', 'role' => 'Specialist I', 'initials' => 'AC'],
                ['name' => 'Michelle Mae Brofas, MD', 'role' => 'Medical Officer III', 'initials' => 'MB'],
                ['name' => 'Jebriel Allen Desacada, MD', 'role' => 'Medical Officer III', 'initials' => 'JD'],
                ['name' => 'Junee Elleigh Oway, MD', 'role' => 'Medical Officer II', 'initials' => 'JO']
            ]), 'type' => 'json'],

            ['group' => 'organization', 'key' => 'org_divisions', 'value' => json_encode([
                [
                    'id' => 'primary',
                    'number' => '1',
                    'title' => 'Primary Health',
                    'badge' => '28 Staff',
                    'subtitle' => 'Immunization (NIP), Animal Bite, TB DOTS, Disease Surveillance & Emergency Transport',
                    'accent' => 'emerald',
                    'units' => [
                        [
                            'title' => 'National Immunization (NIP)',
                            'category' => 'Immunization',
                            'lead_name' => 'Razelle Bendo, RN',
                            'lead_role' => 'Nurse II',
                            'members' => ['Merlita Leyban', 'Kyle Jaydee Buklatin']
                        ],
                        [
                            'title' => 'Animal Bite Treatment (ABTC)',
                            'category' => 'Specialized Clinic',
                            'lead_name' => 'Elaine Mae Bayacal, RN',
                            'lead_role' => 'Nurse',
                            'members' => ['Stanley Emelo', 'Maribel Ramos', 'Czar Ian Calaycay', 'Hafisudin Adil', 'Aiby Villavicencio']
                        ],
                        [
                            'title' => 'TB DOTS Clinic & Program',
                            'category' => 'Infectious Diseases',
                            'lead_name' => 'James Lee Ambojia, RN',
                            'lead_role' => 'Nurse II',
                            'members' => ['Edna Laureles', 'Nelson Malate', 'Neil Bryan Velando', 'Patricia Reyes']
                        ],
                        [
                            'title' => 'MESU & Disease Surveillance',
                            'category' => 'Epidemiology',
                            'lead_name' => 'Roniben Garde, RN, MAN',
                            'lead_role' => 'Nurse IV',
                            'members' => ['Elaine Mae Bayacal, RN (Nurse III)', 'Chaz Angelo Palumpon', 'Emiliano Asas']
                        ],
                        [
                            'title' => 'Non-Communicable Diseases',
                            'category' => 'Wellness & Lifestyle',
                            'lead_name' => 'Annaliza Marquina, RN',
                            'lead_role' => 'Nurse II',
                            'members' => ['Jenalyn De Castro, RN', 'Mon Christian Maneja, RN', 'Corazon Medina, RN', 'Narissa Agustin', 'Cecilia Amagan', 'Ivan Casapao']
                        ],
                        [
                            'title' => 'Medic Team & Transport',
                            'category' => 'Emergency Care',
                            'lead_name' => 'Edgar Bayan',
                            'lead_role' => 'Driver I',
                            'members' => ['Roniben Garde, RN, MAN', 'Mon Christian Maneja, RN', 'Redentor Mojica', 'Renato Loyola']
                        ]
                    ]
                ],
                [
                    'id' => 'maternal',
                    'number' => '2',
                    'title' => 'Maternal & Child Health',
                    'badge' => '24 Staff',
                    'subtitle' => 'Family Planning, Child Nutrition, BEmONC Birthing & 19 Licensed Barangay Midwives',
                    'accent' => 'teal',
                    'units' => [
                        [
                            'title' => 'Family Planning & Clinical Care',
                            'category' => 'Maternal Care',
                            'lead_name' => 'Tristan Voltaire Eguia, RN',
                            'lead_role' => 'Nurse II',
                            'members' => ['Razelle Bendo, RN (Maternal Health)', 'Maribel Ramos', 'Vanessa Erika Amon']
                        ],
                        [
                            'title' => 'Child & Adolescent Health',
                            'category' => 'Child Nutrition',
                            'lead_name' => 'Charlene Paggao, RN',
                            'lead_role' => 'Nutrition II',
                            'members' => ['Cyrus James Navarro, RN (Nurse I)', 'Jenalyn De Castro, RN (Adolescent Health)']
                        ]
                    ]
                ],
                [
                    'id' => 'ancillary',
                    'number' => '3',
                    'title' => 'Ancillary & Allied Health',
                    'badge' => '20 Staff',
                    'subtitle' => 'Dental Care, Pharmacy Supplies, Medical Laboratory, X-Ray & Public Sanitation',
                    'accent' => 'cyan',
                    'units' => [
                        [
                            'title' => 'Dental Clinic',
                            'category' => 'Oral Health',
                            'lead_name' => 'Sylvia Buen, DMD',
                            'lead_role' => 'Dentist III',
                            'members' => ['Marilou Galang']
                        ],
                        [
                            'title' => 'Pharmacy & Supplies',
                            'category' => 'Pharmacy',
                            'lead_name' => 'Hannah Mae Josue, RPh',
                            'lead_role' => 'Pharmacist III',
                            'members' => ['Mary Jane Anarna', 'Elmer Belardo', 'Noelyn Belen', 'Mark Anthony Sebastian']
                        ],
                        [
                            'title' => 'Laboratory & X-Ray',
                            'category' => 'Diagnostics',
                            'lead_name' => 'Evalyn Martin, RMT',
                            'lead_role' => 'MedTech III',
                            'members' => ['Benessie Madlangsakay, RMT (MedTech II)', 'Bettina Ramos, RMT', 'Diana Aquino, RMT', 'Celergene Pellerin, RRT (RadTech II)']
                        ],
                        [
                            'title' => 'Sanitation & Environment',
                            'category' => 'Public Health',
                            'lead_name' => 'Aileen Del Barrio',
                            'lead_role' => 'Inspector III',
                            'members' => ['Maria Florinda Gonzalez, RN (Inspector I)', 'Rhonna Rhezza Jose, RN, MAN']
                        ]
                    ]
                ],
                [
                    'id' => 'admin',
                    'number' => '4',
                    'title' => 'Administrative Staff',
                    'badge' => '3 Staff',
                    'subtitle' => 'Institutional Governance, Records Management, Procurement & Public Assistance',
                    'accent' => 'amber',
                    'units' => []
                ]
            ]), 'type' => 'json'],

            ['group' => 'organization', 'key' => 'org_midwives', 'value' => json_encode([
                ['name' => 'Maria Mendoza, RM', 'rank' => 'Midwife III'],
                ['name' => 'Zosima Aquino, RM', 'rank' => 'Midwife III'],
                ['name' => 'Nena Cotoner, RM', 'rank' => 'Midwife II'],
                ['name' => 'Engracia Dominguez, RM', 'rank' => 'Midwife II'],
                ['name' => 'Felilia Marino, RM', 'rank' => 'Midwife II'],
                ['name' => 'Evangeline Pulido, RM', 'rank' => 'Midwife II'],
                ['name' => 'Emma Yaya, RM', 'rank' => 'Midwife II'],
                ['name' => 'Charlene Gallardo, RM', 'rank' => 'Midwife II'],
                ['name' => 'Lara Vanessa Beaton, RM', 'rank' => 'Midwife II'],
                ['name' => 'Erlinda Videña, RM', 'rank' => 'Midwife II'],
                ['name' => 'Anabelle Revilla, RM', 'rank' => 'Midwife I'],
                ['name' => 'Anna Lissa Belardo, RM', 'rank' => 'Midwife I'],
                ['name' => 'Silvestina Loyola, RM', 'rank' => 'Midwife I'],
                ['name' => 'Ma. Dolores Lumagda, RM', 'rank' => 'Midwife I'],
                ['name' => 'Merwinda Ignas, RM', 'rank' => 'Midwife'],
                ['name' => 'Charo Halili, RM', 'rank' => 'Midwife'],
                ['name' => 'Andrea Lei Javier, RM', 'rank' => 'Midwife'],
                ['name' => 'Vanessa Erika Amon, RM', 'rank' => 'Midwife'],
                ['name' => 'Marisa Seran', 'rank' => 'Staff']
            ]), 'type' => 'json'],

            ['group' => 'organization', 'key' => 'org_admins', 'value' => json_encode([
                ['name' => 'Mark Anthony Sebastian', 'role' => 'Administrative Officer', 'initials' => 'MS'],
                ['name' => 'Jacqueline Hapin', 'role' => 'Administrative Support', 'initials' => 'JH'],
                ['name' => 'Apple Toledo', 'role' => 'Public Assistance & Records', 'initials' => 'AT']
            ]), 'type' => 'json'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
