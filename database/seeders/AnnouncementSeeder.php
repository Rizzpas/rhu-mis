<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Announcement::firstOrCreate(
            ['title' => 'Free Anti-Rabies Vaccination'],
            [
                'content' => 'The RHU will conduct free anti-rabies vaccination on October 25, 2024 at the Barangay Hall.',
                'image_path' => null,
                'is_published' => true,
            ]
        );

        \App\Models\Announcement::firstOrCreate(
            ['title' => 'Dengue Awareness Month'],
            [
                'content' => 'Join us in our fight against Dengue. Clean your surroundings and report any cases immediately.',
                'image_path' => null,
                'is_published' => true,
            ]
        );
    }
}
