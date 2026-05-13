<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification
{
    public function handle(PaymentSucceeded $event): void
    {
        Log::info('WhatsApp notification queued for paid order.', [
            'payment_id' => $event->payment->id,
            'order_id' => $event->payment->order_id,
        ]);
    }
}
