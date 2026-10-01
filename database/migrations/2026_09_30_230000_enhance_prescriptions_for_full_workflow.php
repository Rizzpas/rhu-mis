<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `prescriptions` MODIFY COLUMN `status` ENUM('pending', 'partially_dispensed', 'dispensed', 'cancelled', 'expired') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'dispensed_by')) {
                $table->unsignedBigInteger('dispensed_by')->nullable()->after('status');
                $table->foreign('dispensed_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('prescriptions', 'dispensed_at')) {
                $table->timestamp('dispensed_at')->nullable()->after('dispensed_by');
            }
            if (!Schema::hasColumn('prescriptions', 'pharmacist_notes')) {
                $table->text('pharmacist_notes')->nullable()->after('dispensed_at');
            }
            if (!Schema::hasColumn('prescriptions', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('pharmacist_notes');
                $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('prescriptions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
            if (!Schema::hasColumn('prescriptions', 'cancellation_reason')) {
                $table->string('cancellation_reason', 500)->nullable()->after('cancelled_at');
            }
            if (!Schema::hasColumn('prescriptions', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('cancellation_reason');
            }
            if (!Schema::hasColumn('prescriptions', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->index()->after('expired_at');
            }
        });

        Schema::table('prescription_items', function (Blueprint $table) {
            if (!Schema::hasColumn('prescription_items', 'dispensed_quantity')) {
                $table->integer('dispensed_quantity')->nullable()->default(0)->after('quantity');
            }
        });

        // Backfill dispensed_quantity for already dispensed prescriptions
        DB::statement("
            UPDATE `prescription_items` pi
            JOIN `prescriptions` p ON pi.prescription_id = p.id
            SET pi.dispensed_quantity = pi.quantity
            WHERE p.status = 'dispensed'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, normalize any rows in new statuses back to 'pending' before narrowing the enum
        DB::statement("
            UPDATE `prescriptions`
            SET `status` = 'pending'
            WHERE `status` IN ('partially_dispensed', 'cancelled', 'expired')
        ");

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `prescriptions` MODIFY COLUMN `status` ENUM('pending', 'dispensed') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('prescription_items', function (Blueprint $table) {
            if (Schema::hasColumn('prescription_items', 'dispensed_quantity')) {
                $table->dropColumn('dispensed_quantity');
            }
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            if (Schema::hasColumn('prescriptions', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
            if (Schema::hasColumn('prescriptions', 'expired_at')) {
                $table->dropColumn('expired_at');
            }
            if (Schema::hasColumn('prescriptions', 'cancellation_reason')) {
                $table->dropColumn('cancellation_reason');
            }
            if (Schema::hasColumn('prescriptions', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            if (Schema::hasColumn('prescriptions', 'cancelled_by')) {
                $table->dropForeign(['cancelled_by']);
                $table->dropColumn('cancelled_by');
            }
            if (Schema::hasColumn('prescriptions', 'pharmacist_notes')) {
                $table->dropColumn('pharmacist_notes');
            }
            if (Schema::hasColumn('prescriptions', 'dispensed_at')) {
                $table->dropColumn('dispensed_at');
            }
            if (Schema::hasColumn('prescriptions', 'dispensed_by')) {
                $table->dropForeign(['dispensed_by']);
                $table->dropColumn('dispensed_by');
            }
        });
    }
};