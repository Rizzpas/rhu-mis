<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Top Bar ─────────────────────────────────────────
            ['group' => 'topbar', 'key' => 'clinic_hours', 'value' => 'Mon - Fri | 8:00 AM - 5:00 PM', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'emergency_hotlines', 'value' => '911 | (046) 432-1234', 'type' => 'text'],

            // ── Hero Section ────────────────────────────────────
            ['group' => 'hero', 'key' => 'hero_badge_text', 'value' => 'Welcome to RHU Portal', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_title_line1', 'value' => 'Accessible', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_title_highlight', 'value' => 'Healthcare', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_title_line2', 'value' => 'for Every Citizen.', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'hero_description', 'value' => 'The Rural Health Unit is the primary gateway for medical services in our city. We provide digital triage, scheduling, and diagnostic referrals.', 'type' => 'textarea'],
            ['group' => 'hero', 'key' => 'hero_image', 'value' => 'assets/images/hero.jpg', 'type' => 'image'],
            ['group' => 'hero', 'key' => 'carousel_hero_title', 'value' => 'RHU Silang, Cavite', 'type' => 'text'],
            ['group' => 'hero', 'key' => 'carousel_hero_subtitle', 'value' => 'Providing Quality Healthcare for All Citizens', 'type' => 'text'],

            // ── Footer ──────────────────────────────────────────
            ['group' => 'footer', 'key' => 'footer_address_line1', 'value' => 'M.H del Pilar St.', 'type' => 'text'],
            ['group' => 'footer', 'key' => 'footer_address_line2', 'value' => 'Silang, Cavite', 'type' => 'text'],
            ['group' => 'footer', 'key' => 'footer_phone', 'value' => '(046) 414-0209', 'type' => 'text'],
            ['group' => 'footer', 'key' => 'footer_email', 'value' => 'contact@silang.gov.ph', 'type' => 'text'],

            // ── About (Mission & Vision) ────────────────────────
            ['group' => 'about', 'key' => 'mission_statement', 'value' => '"To provide responsive, equitable, and quality primary healthcare services to all citizens. We commit to transparency and excellence by utilizing modern management systems to eliminate barriers to health access and pharmaceutical needs."', 'type' => 'textarea'],
            ['group' => 'about', 'key' => 'vision_statement', 'value' => '"A healthy and empowered community served by a world-class Rural Health Unit that champions technological advancement and medical integrity for the well-being of every family, ensuring that quality healthcare is a reliable and efficient right for every citizen."', 'type' => 'textarea'],

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
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
