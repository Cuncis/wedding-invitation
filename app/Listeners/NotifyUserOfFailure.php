<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use Illuminate\Support\Facades\Log;

class NotifyUserOfFailure
{
    public function handle(PaymentFailed $event): void
    {
        Log::warning('Payment failed.', [
            'payment_id' => $event->payment->id,
            'order_id' => $event->payment->order_id,
            'reason' => $event->reason,
        ]);
    }
}
