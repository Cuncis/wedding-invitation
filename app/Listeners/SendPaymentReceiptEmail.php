<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Notifications\PaymentReceiptNotification;

class SendPaymentReceiptEmail
{
    public function handle(PaymentSucceeded $event): void
    {
        $order = $event->payment->order;
        $user = $order?->user;

        if (! $user) {
            return;
        }

        $user->notify(new PaymentReceiptNotification($event->payment));
    }
}
