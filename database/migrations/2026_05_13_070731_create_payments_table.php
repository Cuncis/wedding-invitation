<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained();
            $table->enum('gateway', ['midtrans', 'xendit', 'doku']);
            $table->string('external_id')->nullable();  // Midtrans order_id / Xendit invoice_id
            $table->string('payment_method')->nullable();// gopay, bank_transfer, qris, etc.
            $table->enum('status', [
                'pending',
                'settlement',
                'expire',
                'cancel',
                'deny',
                'refund'
            ])->default('pending');
            $table->unsignedInteger('amount');
            $table->json('raw_payload')->nullable();  // store full webhook payload
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('external_id');             // queried on every webhook
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
