<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();      // UND-2025-00001
            $table->foreignId('user_id')->constrained();
            $table->foreignId('invitation_id')->constrained();
            $table->unsignedInteger('theme_price')->default(0);
            $table->unsignedInteger('addon_price')->default(0);
            $table->unsignedInteger('animation_price')->default(0);
            $table->unsignedInteger('total_amount');          // sum — locked at checkout
            $table->enum('status', [
                'pending',   // waiting for payment
                'paid',      // payment confirmed
                'failed',    // payment failed/expired
                'refunded',  // edge case
            ])->default('pending');
            $table->json('snapshot')->nullable();     // config snapshot at time of order
            $table->timestamp('expires_at')->nullable(); // order expires in 24h if unpaid
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
