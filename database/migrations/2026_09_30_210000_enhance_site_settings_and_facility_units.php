<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure `group` in site_settings has default value 'general'
        try {
            DB::statement("ALTER TABLE `site_settings` MODIFY COLUMN `group` VARCHAR(255) NOT NULL DEFAULT 'general'");
        } catch (\Throwable $e) {
            // Fallback for sqlite / testing environments
            if (Schema::hasTable('site_settings')) {
                Schema::table('site_settings', function (Blueprint $table) {
                    $table->string('group')->default('general')->change();
                });
            }
        }

        // 2. Insert standard default settings if not present
        $defaults = [
            'topbar_republic' => ['group' => 'topbar', 'value' => 'Republic of the Philippines', 'type' => 'text'],
            'topbar_province' => ['group' => 'topbar', 'value' => 'Province of Cavite', 'type' => 'text'],
            'topbar_municipality' => ['group' => 'topbar', 'value' => 'Municipality of Silang', 'type' => 'text'],
            'clinic_hours' => ['group' => 'topbar', 'value' => 'Mon - Fri | 8:00 AM - 5:00 PM', 'type' => 'text'],
            'emergency_hotlines' => ['group' => 'topbar', 'value' => '911 | (046) 432-1234', 'type' => 'text'],
            'mission_headline' => ['group' => 'about', 'value' => 'Advancing Municipal Health with Integrity & Care', 'type' => 'text'],
            'mission_subheadline' => ['group' => 'about', 'value' => 'The primary public healthcare authority of the Municipality of Silang, Cavite — dedicated to providing responsive, equitable, and professional medical services to every constituent across all 64 barangays.', 'type' => 'textarea'],
            'mission_title' => ['group' => 'about', 'value' => 'Delivering Responsive, Equitable Public Healthcare', 'type' => 'text'],
            'vision_headline' => ['group' => 'about', 'value' => 'A healthy, resilient, and empowered Silang served by modern healthcare integrity.', 'type' => 'text'],
        ];

        foreach ($defaults as $key => $data) {
            if (!DB::table('site_settings')->where('key', $key)->exists()) {
                DB::table('site_settings')->insert([
                    'key' => $key,
                    'group' => $data['group'],
                    'value' => $data['value'],
                    'type' => $data['type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Normalize facility_units categories
        if (Schema::hasTable('facility_units')) {
            $categoryMap = [
                'General Health' => 'General Medicine',
                'General' => 'General Medicine',
                'Dental' => 'Dental Care',
                'Maternity' => 'Maternity & Child Health',
                'Infectious Disease' => 'Infectious Diseases',
                'Emergency' => 'Emergency & Immunization',
            ];

            foreach ($categoryMap as $old => $new) {
                DB::table('facility_units')->where('category', $old)->update(['category' => $new]);
            }
        }
    }

    public function down(): void
    {
        // Revert defaults if necessary
    }
};
