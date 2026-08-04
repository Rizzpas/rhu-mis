<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ComplexAnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Main Announcement
        $announcement = Announcement::create([
            'title' => 'Annual Health Caravan 2026',
            'subheading' => 'Bringing Healthcare Closer to You',
            'event_date' => Carbon::now()->addDays(15),
            'content' => "We are thrilled to announce the upcoming Annual Health Caravan! This year's theme focuses on preventative care and community wellness. 
            
            Our team of doctors, nurses, and volunteers will be visiting every barangay to provide free medical check-ups, dental services, and health education seminars. We encourage everyone to participate and take advantage of these free services.",
            'image_path' => null, // We'll let the UI handle the "No Image" placeholder or you can upload one manually later
            'is_published' => true,
            'display_type' => 'list', // Start with List view
        ]);

        // 2. Add Dynamic Sections (Image + Content Pairs)

        // Section 1
        $announcement->images()->create([
            'image_path' => null,
            'content' => 'Dental Services: Free cleaning and tooth extraction will be available for children and adults. Our dental bus is fully equipped to handle minor procedures.',
            'sort_order' => 0,
        ]);

        // Section 2
        $announcement->images()->create([
            'image_path' => null,
            'content' => 'Medical Check-ups: General consultation, blood pressure monitoring, and blood sugar testing will be conducted by our resident physicians.',
            'sort_order' => 1,
        ]);

        // Section 3
        $announcement->images()->create([
            'image_path' => null,
            'content' => 'Medicine Distribution: Free vitamins and maintenance medicines will be distributed to eligible senior citizens and children.',
            'sort_order' => 2,
        ]);

        // Section 4
        $announcement->images()->create([
            'image_path' => null,
            'content' => 'Health Seminars: Join our interactive sessions on nutrition, family planning, and mental health awareness.',
            'sort_order' => 3,
        ]);

        // Section 5
        $announcement->images()->create([
            'image_path' => null,
            'content' => 'Grand Finale: A Zumba marathon and feeding program will conclude the week-long caravan.',
            'sort_order' => 4,
        ]);
    }
}
