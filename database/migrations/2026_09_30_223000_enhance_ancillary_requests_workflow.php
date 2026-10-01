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
            DB::statement("ALTER TABLE `ancillary_requests` MODIFY COLUMN `status` ENUM('Pending', 'Specimen Collected', 'In Progress', 'Done', 'Cancelled', 'Rejected') NOT NULL DEFAULT 'Pending'");
        }

        Schema::table('ancillary_requests', function (Blueprint $table) {
            // Specimen collection & processing timestamps
            if (!Schema::hasColumn('ancillary_requests', 'specimen_collected_at')) {
                $table->timestamp('specimen_collected_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('ancillary_requests', 'specimen_collected_by')) {
                $table->unsignedBigInteger('specimen_collected_by')->nullable()->after('specimen_collected_at');
                $table->foreign('specimen_collected_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('ancillary_requests', 'processing_started_at')) {
                $table->timestamp('processing_started_at')->nullable()->after('specimen_collected_by');
            }

            // Rejection fields
            if (!Schema::hasColumn('ancillary_requests', 'rejection_reason')) {
                $table->string('rejection_reason')->nullable()->after('processing_started_at');
            }
            if (!Schema::hasColumn('ancillary_requests', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('rejection_reason');
                $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('ancillary_requests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }

            // Cancellation fields
            if (!Schema::hasColumn('ancillary_requests', 'cancellation_reason')) {
                $table->string('cancellation_reason')->nullable()->after('rejected_at');
            }
            if (!Schema::hasColumn('ancillary_requests', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancellation_reason');
                $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('ancillary_requests', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }

            // Amendment & Correction fields
            if (!Schema::hasColumn('ancillary_requests', 'is_amended')) {
                $table->boolean('is_amended')->default(false)->after('cancelled_at');
            }
            if (!Schema::hasColumn('ancillary_requests', 'amendment_reason')) {
                $table->text('amendment_reason')->nullable()->after('is_amended');
            }
            if (!Schema::hasColumn('ancillary_requests', 'amended_by')) {
                $table->unsignedBigInteger('amended_by')->nullable()->after('amendment_reason');
                $table->foreign('amended_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('ancillary_requests', 'amended_at')) {
                $table->timestamp('amended_at')->nullable()->after('amended_by');
            }
            if (!Schema::hasColumn('ancillary_requests', 'previous_result_data')) {
                $table->json('previous_result_data')->nullable()->after('amended_at');
            }

            // Repeat / Re-order linkage
            if (!Schema::hasColumn('ancillary_requests', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('previous_result_data');
                $table->foreign('parent_id')->references('id')->on('ancillary_requests')->nullOnDelete();
            }
            if (!Schema::hasColumn('ancillary_requests', 'is_repeat')) {
                $table->boolean('is_repeat')->default(false)->after('parent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ancillary_requests', function (Blueprint $table) {
            if (Schema::hasColumn('ancillary_requests', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('ancillary_requests', 'is_repeat')) {
                $table->dropColumn('is_repeat');
            }
            if (Schema::hasColumn('ancillary_requests', 'amended_by')) {
                $table->dropForeign(['amended_by']);
                $table->dropColumn('amended_by');
            }
            if (Schema::hasColumn('ancillary_requests', 'is_amended')) {
                $table->dropColumn(['is_amended', 'amendment_reason', 'amended_at', 'previous_result_data']);
            }
            if (Schema::hasColumn('ancillary_requests', 'cancelled_by')) {
                $table->dropForeign(['cancelled_by']);
                $table->dropColumn('cancelled_by');
            }
            if (Schema::hasColumn('ancillary_requests', 'cancellation_reason')) {
                $table->dropColumn(['cancellation_reason', 'cancelled_at']);
            }
            if (Schema::hasColumn('ancillary_requests', 'rejected_by')) {
                $table->dropForeign(['rejected_by']);
                $table->dropColumn('rejected_by');
            }
            if (Schema::hasColumn('ancillary_requests', 'rejection_reason')) {
                $table->dropColumn(['rejection_reason', 'rejected_at']);
            }
            if (Schema::hasColumn('ancillary_requests', 'specimen_collected_by')) {
                $table->dropForeign(['specimen_collected_by']);
                $table->dropColumn('specimen_collected_by');
            }
            if (Schema::hasColumn('ancillary_requests', 'specimen_collected_at')) {
                $table->dropColumn(['specimen_collected_at', 'processing_started_at']);
            }
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `ancillary_requests` MODIFY COLUMN `status` ENUM('Pending', 'Done') NOT NULL DEFAULT 'Pending'");
        }
    }
};
