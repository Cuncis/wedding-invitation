<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class MidtransGateway implements PaymentGateway
{
    public function __construct()
    {
        MidtransConfig::$serverKey    = config('midtrans.server_key');
        MidtransConfig::$clientKey    = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized  = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds        = config('midtrans.is_3ds');
    }

    public function createPayment(Order $order): PaymentResult
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->user?->name ?? 'Customer',
                'email'      => $order->user?->email ?? '',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return new PaymentResult(
            snapToken:   $snapToken,
            externalId:  $order->order_number,
            redirectUrl: 'https://app' . (config('midtrans.is_production') ? '' : '.sandbox') . '.midtrans.com/snap/v2/vtweb/' . $snapToken,
        );
    }

    public function verifyWebhook(Request $request): bool
    {
        $orderId      = $request->input('order_id', '');
        $statusCode   = $request->input('status_code', '');
        $grossAmount  = $request->input('gross_amount', '');
        $serverKey    = config('midtrans.server_key', '');
        $signature    = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($signature, (string) $request->input('signature_key', ''));
    }

    public function resolveStatus(Request $request): string
    {
        $transactionStatus = $request->input('transaction_status', '');
        $fraudStatus       = $request->input('fraud_status', '');

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

    public function resolveExternalId(Request $request): string
    {
        return (string) $request->input('order_id', '');
    }
}
