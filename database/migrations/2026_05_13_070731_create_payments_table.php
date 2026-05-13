<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Payments record every gateway transaction attempt for an order.
     *
     * Kept separate from orders so we can:
     *   - Store multiple attempts per order (user retries)
     *   - Store the full raw gateway response for debugging/audit
     *   - Swap payment gateways in future without touching orders table
     *
     * Webhook flow:
     *   POST /webhooks/midtrans
     *     → verify signature
     *     → save raw payload to raw_response
     *     → dispatch ProcessPaymentJob
     *     → return 200 immediately (Midtrans needs fast response)
     *
     *   ProcessPaymentJob:
     *     → update this row's status
     *     → update parent order status
     *     → fire PaymentSucceeded / PaymentFailed event
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // Which gateway processed this payment
            $table->string('gateway', 30)->default('midtrans');

            // Midtrans transaction ID — e.g. "mid-1234567890"
            // Null until gateway responds
            $table->string('gateway_transaction_id')->nullable()->index();

            // Midtrans Snap token — used to open the payment popup
            // Stored so we can reuse it without re-creating the transaction
            $table->string('snap_token')->nullable();

            // Payment method chosen by user — populated by webhook
            // e.g. "gopay", "bank_transfer", "credit_card", "qris"
            $table->string('payment_type', 30)->nullable();

            // Mirrors Midtrans status: pending, settlement, deny, cancel, expire, failure
            $table->string('status', 30)->default('pending');

            // Full webhook payload — NEVER discard this, needed for disputes
            $table->json('raw_response')->nullable();

            // When gateway confirmed the payment settled
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
