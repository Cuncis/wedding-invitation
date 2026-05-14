<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // "Elegant Rose"
            $table->string('slug')->unique();              // "elegant-rose" (blade template key)
            $table->text('description')->nullable();
            $table->string('preview_image')->nullable();   // path to preview screenshot
            $table->unsignedInteger('base_price');          // in Rupiah, e.g. 99000
            $table->json('default_colors')->nullable();    // default palette for this theme
            $table->json('default_fonts')->nullable();     // {heading, body}
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
