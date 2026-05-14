<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('animation_packs', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // "Premium"
            $table->string('key')->unique();    // "free" | "standard" | "premium"
            $table->text('description')->nullable();
            $table->json('features')->nullable();  // list of animation features
            $table->unsignedInteger('price');   // 0 for free tier
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animation_packs');
    }
};
