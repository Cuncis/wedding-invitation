<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('invitation_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')
                ->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('theme_id')->nullable()->constrained();
            $table->foreignId('animation_pack_id')->nullable()->constrained();
            $table->json('sections')->nullable();   // ordered visible sections
            $table->json('colors')->nullable();     // primary, secondary, accent
            $table->json('typography')->nullable(); // font_heading, font_body
            $table->json('content')->nullable();   // all text content per section
            $table->json('addon_ids')->nullable(); // array of selected addon IDs
            $table->json('music')->nullable();    // {url, autoplay, title}
            $table->json('maps')->nullable();     // {lat, lng, label, embed_url}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_configs');
    }
};
