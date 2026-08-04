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
        Schema::table('ancillary_requests', function (Blueprint $table) {
            $table->json('result_data')->nullable()->after('remarks');
            $table->unsignedBigInteger('completed_by')->nullable()->after('result_data');
            $table->timestamp('completed_at')->nullable()->after('completed_by');

            $table->foreign('completed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ancillary_requests', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn(['result_data', 'completed_by', 'completed_at']);
        });
    }
};
