<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_units', function (Blueprint $table) {
            if (!Schema::hasColumn('facility_units', 'operating_hours_structured')) {
                $table->json('operating_hours_structured')->nullable()->after('operating_hours');
            }
            if (!Schema::hasColumn('facility_units', 'contacts_structured')) {
                $table->json('contacts_structured')->nullable()->after('contact_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facility_units', function (Blueprint $table) {
            if (Schema::hasColumn('facility_units', 'operating_hours_structured')) {
                $table->dropColumn('operating_hours_structured');
            }
            if (Schema::hasColumn('facility_units', 'contacts_structured')) {
                $table->dropColumn('contacts_structured');
            }
        });
    }
};
