<?php

namespace App\Services;

use App\Events\PaymentFailed;
use App\Events\PaymentSucceeded;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Gateways\MidtransGateway;

class PaymentService
{
    public function __construct(private readonly MidtransGateway $gateway)
    {
    }

    public function createForOrder(Order $order): Payment
    {
        $payload = $this->gateway->createCharge($order);

        return Payment::create([
            'order_id' => $order->id,
            'gateway' => 'midtrans',
            'amount' => $order->total_amount,
            'status' => 'pending',
            'gateway_transaction_id' => $payload['reference'] ?? null,
            'snap_token' => $payload['snap_token'] ?? null,
            'raw_response' => $payload,
        ]);
    }

    public function handleWebhook(array $payload): bool
    {
        $payment = Payment::query()
            ->where('gateway_transaction_id', '=', $payload['reference'] ?? null)
            ->first();

        if (! $payment) {
            return false;
        }

        $payment->status = (string) ($payload['status'] ?? 'failed');
        $payment->payment_type = $payload['payment_type'] ?? $payment->payment_type;
        $payment->raw_response = $payload;
        $payment->paid_at = $payment->status === 'paid' ? now() : null;
        $payment->save();

        if ($payment->status === 'paid') {
            PaymentSucceeded::dispatch($payment);

            return true;
        }

        PaymentFailed::dispatch($payment, (string) ($payload['message'] ?? 'Payment failed.'));

        return true;
    }
}
