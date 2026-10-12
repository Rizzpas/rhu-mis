<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Manual presence overrides (`schedule_override` = manual_online | manual_offline)
 * now carry a creation timestamp and an expiry so they lapse on their own and the
 * schedule-based status takes over again. See App\Services\StaffPresence.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('schedule_override_at')->nullable()->after('schedule_override');
            $table->timestamp('schedule_override_expires_at')->nullable()->after('schedule_override_at');
        });

        // Legacy overrides were written without an expiry and under the old semantics,
        // so they cannot be interpreted safely. Drop them; the schedule takes over.
        DB::table('users')->whereNotNull('schedule_override')->update(['schedule_override' => null]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['schedule_override_at', 'schedule_override_expires_at']);
        });
    }
};
