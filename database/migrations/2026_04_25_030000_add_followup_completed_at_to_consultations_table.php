<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds a `followup_completed_at` timestamp to mark when a follow-up
     * cycle has been fulfilled (i.e., the patient returned for their
     * follow-up visit). This preserves the historical record while
     * preventing the follow-up flag from persisting after completion.
     */
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->timestamp('followup_completed_at')->nullable()->after('followup_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn('followup_completed_at');
        });
    }
};
