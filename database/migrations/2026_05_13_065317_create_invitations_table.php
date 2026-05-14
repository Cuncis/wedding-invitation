<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique()->nullable();        // public URL slug — null until paid
            $table->string('groom_name');
            $table->string('bride_name');
            $table->dateTime('event_date')->nullable();
            $table->string('event_venue')->nullable();
            $table->enum('status', [
                'draft',       // being built, no payment
                'pending',     // order created, awaiting payment
                'active',      // paid and published
                'expired',     // past expires_at date
            ])->default('draft');
            $table->timestamp('published_at')->nullable();    // set after payment
            $table->timestamp('expires_at')->nullable();       // 1 year from publish
            $table->unsignedBigInteger('view_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
