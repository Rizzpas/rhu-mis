<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medicine_batches', function (Blueprint $table) {
            if (!Schema::hasColumn('medicine_batches', 'status')) {
                $table->enum('status', ['active', 'depleted', 'disposed', 'quarantined'])
                    ->default('active')
                    ->after('original_quantity');
            }
            if (!Schema::hasColumn('medicine_batches', 'disposed_at')) {
                $table->timestamp('disposed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('medicine_batches', 'disposed_by')) {
                $table->unsignedBigInteger('disposed_by')->nullable()->after('disposed_at');
                $table->foreign('disposed_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
            if (!Schema::hasColumn('medicine_batches', 'disposal_reason')) {
                $table->string('disposal_reason', 100)->nullable()->after('disposed_by');
            }
            if (!Schema::hasColumn('medicine_batches', 'disposal_notes')) {
                $table->text('disposal_notes')->nullable()->after('disposal_reason');
            }
        });

        Schema::table('medicines', function (Blueprint $table) {
            if (!Schema::hasColumn('medicines', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_batches', function (Blueprint $table) {
            if (Schema::hasColumn('medicine_batches', 'disposed_by')) {
                $table->dropForeign(['disposed_by']);
                $table->dropColumn('disposed_by');
            }
            $columnsToDrop = [];
            foreach (['status', 'disposed_at', 'disposal_reason', 'disposal_notes'] as $col) {
                if (Schema::hasColumn('medicine_batches', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::table('medicines', function (Blueprint $table) {
            if (Schema::hasColumn('medicines', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
