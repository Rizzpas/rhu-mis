<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `medicine_batches` MODIFY COLUMN `status` ENUM('active', 'depleted', 'disposed', 'quarantined', 'expired') NOT NULL DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            // Revert any expired batches to active or disposed before narrowing enum
            DB::statement("UPDATE `medicine_batches` SET `status` = 'disposed' WHERE `status` = 'expired'");
            DB::statement("ALTER TABLE `medicine_batches` MODIFY COLUMN `status` ENUM('active', 'depleted', 'disposed', 'quarantined') NOT NULL DEFAULT 'active'");
        }
    }
};
