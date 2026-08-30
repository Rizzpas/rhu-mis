<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ancillary_requests', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('completed_at');
            $table->string('archived_reason')->nullable()->after('archived_at'); // no_show, expired, manual
        });
    }

    public function down(): void
    {
        Schema::table('ancillary_requests', function (Blueprint $table) {
            $table->dropColumn(['archived_at', 'archived_reason']);
        });
    }
};
