<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPaymentWebhook;
use App\Models\Payment;
use App\Services\Payment\MidtransGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(private readonly MidtransGateway $gateway)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (!$this->gateway->verifyWebhook($request)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $externalId = $this->gateway->resolveExternalId($request);

        $payment = Payment::where('external_id', $externalId)->first();

        if ($payment) {
            $payment->update(['raw_payload' => $request->all()]);
            ProcessPaymentWebhook::dispatch($payment->id);
        }

        return response()->json(['status' => 'ok']);
    }
}
