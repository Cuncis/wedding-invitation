<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Services\InvitationPublisher;

class PublishInvitationAfterPayment
{
    public function __construct(private readonly InvitationPublisher $publisher)
    {
    }

    public function handle(PaymentSucceeded $event): void
    {
        $invitation = $event->payment->order?->invitation;

        if (! $invitation) {
            return;
        }

        $this->publisher->publish($invitation);
    }
}
