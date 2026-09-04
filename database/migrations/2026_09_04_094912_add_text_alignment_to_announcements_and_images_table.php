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
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('content_align', 20)->default('left')->after('content');
        });

        Schema::table('announcement_images', function (Blueprint $table) {
            $table->string('text_align', 20)->default('left')->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('content_align');
        });

        Schema::table('announcement_images', function (Blueprint $table) {
            $table->dropColumn('text_align');
        });
    }
};
