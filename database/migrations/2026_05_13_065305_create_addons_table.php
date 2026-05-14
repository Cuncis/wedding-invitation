<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // "Music Player"
            $table->string('key')->unique();               // "music_player" (used in config JSON)
            $table->text('description')->nullable();
            $table->string('icon')->nullable();            // emoji or heroicon name
            $table->unsignedInteger('price');              // in Rupiah
            $table->enum('category', [
                'media',      // music, gallery
                'interactive',// RSVP, countdown, maps
                'social',     // digital gift, share
                'utility',    // live streaming, etc.
            ])->default('utility');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
