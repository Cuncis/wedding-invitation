<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();

            // Owner
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Selected theme — set at order time, can change before payment
            $table->foreignId('theme_id')
                ->constrained();

            // Selected animation pack — nullable until chosen
            $table->foreignId('animation_pack_id')
                ->nullable()
                ->constrained('animation_packs')
                ->nullOnDelete();

            // Public URL slug — e.g. /budi-ani-2025
            // Null until invitation is published after payment
            $table->string('slug')->unique()->nullable();

            // Lifecycle status
            // draft   = being built, not paid
            // active  = paid, published, live
            // expired = past expires_at date
            $table->enum('status', ['draft', 'active', 'expired'])->default('draft');

            // Set when payment is confirmed and invitation goes live
            $table->timestamp('published_at')->nullable();

            // 1 year from published_at — after this the URL shows an expiry page
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
