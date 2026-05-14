<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\MidtransGateway;
use App\Services\Payment\PaymentGateway;
use App\Services\Payment\PaymentResult;

class PaymentService
{
    /** @var array<string, PaymentGateway> */
    private array $gateways = [];

    public function __construct(private readonly MidtransGateway $midtrans)
    {
        $this->gateways['midtrans'] = $midtrans;
    }

    public function gateway(string $name = 'midtrans'): PaymentGateway
    {
        return $this->gateways[$name]
            ?? throw new \InvalidArgumentException("Unknown payment gateway: {$name}");
    }

    /**
     * Initiate a payment — calls gateway, creates Payment record, returns PaymentResult.
     */
    public function initiate(Order $order, string $gatewayName = 'midtrans'): PaymentResult
    {
        $gateway = $this->gateway($gatewayName);
        $result = $gateway->createPayment($order);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => $gatewayName,
            'external_id' => $result->externalId,
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_PENDING,
            'raw_payload' => ['snap_token' => $result->snapToken],
        ]);

        return $result;
    }
}
