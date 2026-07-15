<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');          // topbar, hero, footer, about, steps, faq, privacy
            $table->string('key')->unique();  // clinic_hours, hero_title, etc.
            $table->longText('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, json, image
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
