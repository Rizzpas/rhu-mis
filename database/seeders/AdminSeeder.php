<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default administrator accounts.
     * These are the bootstrap accounts needed to log in and manage the system.
     */
    public function run(): void
    {
        // Super Admin (RHU Head / Municipal Health Officer)
        User::updateOrCreate(
            ['email' => 'super_admin@rhu.gov.ph'],
            [
                'name' => 'RHU Super Admin',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'status' => 'Present',
            ]
        );

        // Regular Admin
        User::updateOrCreate(
            ['email' => 'admin@rhu.gov.ph'],
            [
                'name' => 'RHU Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'Present',
            ]
        );

        $this->command->info('✅ Admin accounts seeded:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'super_admin@rhu.gov.ph', 'password'],
                ['Admin', 'admin@rhu.gov.ph', 'password'],
            ]
        );
        $this->command->warn('⚠️  Change these passwords immediately after first login!');
    }
}
