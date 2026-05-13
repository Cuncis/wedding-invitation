<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animation_packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Tier determines which GSAP/CSS animations are unlocked
            // free     = fade-in only (CSS, no library needed)
            // standard = scroll reveal animations (GSAP ScrollTrigger)
            // premium  = particle effects + envelope open (GSAP + custom)
            $table->enum('tier', ['free', 'standard', 'premium'])->default('free');
            // Price in Rupiah integer — free tier = 0
            $table->unsignedInteger('price')->default(0);
            $table->text('description')->nullable();
            // URL of a short preview video/gif shown in the builder
            $table->string('preview_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animation_packs');
    }
};
