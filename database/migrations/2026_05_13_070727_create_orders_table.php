<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Orders represent a payment intent for an invitation.
     *
     * Flow:
     *   1. User configures invitation (picks theme, addons, animation)
     *   2. User clicks "Checkout" → OrderService creates a pending order
     *   3. User pays via Midtrans → webhook fires → order marked 'paid'
     *   4. PaymentSucceeded event fires → invitation published
     *
     * One invitation can have multiple orders if a previous attempt failed/expired.
     * Only one order per invitation should ever reach 'paid' status.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('invitation_id')
                ->constrained()
                ->cascadeOnDelete();

            // Human-readable order number, e.g. UND-20250101-0001
            $table->string('order_number', 30)->unique();

            // Total in Rupiah — calculated by PricingService and locked at order creation
            // Do NOT recalculate from current prices — use this locked value
            $table->unsignedInteger('total_amount');

            // Itemized breakdown snapshot — JSON so we have a receipt forever
            // { "theme": 149000, "addons": { "music-player": 29000 }, "animation": 49000 }
            $table->json('price_breakdown')->nullable();

            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');

            // Order expires after 24h if unpaid — a scheduled job cleans these up
            $table->timestamp('expires_at');

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['invitation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
