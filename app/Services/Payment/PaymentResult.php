<?php

namespace App\Services\Payment;

readonly class PaymentResult
{
    public function __construct(
        public string $snapToken,
        public string $externalId,
        public string $redirectUrl,
        public string $status = 'pending',
    ) {
    }
}
