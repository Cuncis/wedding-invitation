<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function createPayment(Order $order): PaymentResult;

    public function verifyWebhook(Request $request): bool;

    public function resolveStatus(Request $request): string;

    public function resolveExternalId(Request $request): string;
}
