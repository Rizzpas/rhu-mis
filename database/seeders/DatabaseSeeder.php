<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * For a fresh/clean start, this only seeds:
     * 1. Admin users (super_admin + admin)
     * 2. Site settings (CMS content)
     * 3. Medicine catalogue (for prescriptions autocomplete)
     *
     * All clinical data (patients, appointments, consultations) is left
     * empty so the system can be populated via the UI.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,         // Super Admin + Admin accounts
            SiteSettingsSeeder::class,  // CMS content defaults
            MedicineSeeder::class,      // Medicine catalogue
        ]);
    }
}
