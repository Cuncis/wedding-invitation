<?php

namespace App\Jobs;

use App\Events\PaymentSucceeded;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public readonly int $paymentId)
    {
    }

    public function handle(OrderService $orderService): void
    {
        $payment = Payment::with('order')->find($this->paymentId);

        if (! $payment) {
            return;
        }

        // Idempotency: skip if already settled
        if ($payment->status === Payment::STATUS_SETTLEMENT) {
            return;
        }

        $resolvedStatus = $this->resolveStatus($payment->raw_payload ?? []);

        $payment->update([
            'status'         => match ($resolvedStatus) {
                'paid'    => Payment::STATUS_SETTLEMENT,
                'failed'  => Payment::STATUS_CANCEL,
                default   => Payment::STATUS_PENDING,
            },
            'payment_method' => $payment->raw_payload['payment_type'] ?? null,
            'paid_at'        => $resolvedStatus === 'paid' ? now() : null,
        ]);

        if ($resolvedStatus === 'paid' && $payment->order) {
            $orderService->markAsPaid($payment->order);
            PaymentSucceeded::dispatch($payment->fresh());
        }
    }

    private function resolveStatus(array $payload): string
    {
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? '';

        if ($transactionStatus === 'settlement') {
            return 'paid';
        }

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            return 'paid';
        }

        if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            return 'failed';
        }

        return 'pending';
    }
}
