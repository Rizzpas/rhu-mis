<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            ['name' => 'Paracetamol', 'generic_name' => 'Acetaminophen', 'form' => 'Tablet'],
            ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin', 'form' => 'Capsule'],
            ['name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'form' => 'Tablet'],
            ['name' => 'Mefenamic Acid', 'generic_name' => 'Mefenamic Acid', 'form' => 'Capsule'],
            ['name' => 'Cetirizine', 'generic_name' => 'Cetirizine', 'form' => 'Tablet'],
            ['name' => 'Loratadine', 'generic_name' => 'Loratadine', 'form' => 'Tablet'],
            ['name' => 'Salbutamol', 'generic_name' => 'Albuterol', 'form' => 'Syrup'],
            ['name' => 'Omeprazole', 'generic_name' => 'Omeprazole', 'form' => 'Capsule'],
            ['name' => 'Losartan', 'generic_name' => 'Losartan potassium', 'form' => 'Tablet'],
            ['name' => 'Amlodipine', 'generic_name' => 'Amlodipine besylate', 'form' => 'Tablet'],
            ['name' => 'Metformin', 'generic_name' => 'Metformin hydrochloride', 'form' => 'Tablet'],
            ['name' => 'Ascorbic Acid (Vitamin C)', 'generic_name' => 'Ascorbic Acid', 'form' => 'Tablet'],
            ['name' => 'Cefalexin', 'generic_name' => 'Cephalexin', 'form' => 'Capsule'],
            ['name' => 'Azithromycin', 'generic_name' => 'Azithromycin', 'form' => 'Tablet'],
            ['name' => 'Lagundi', 'generic_name' => 'Vitex negundo', 'form' => 'Syrup'],
        ];

        foreach ($medicines as $med) {
            \App\Models\Medicine::updateOrCreate(
                ['name' => $med['name']],
                $med
            );
        }
    }
}
