<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    public function create(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $result = $this->paymentService->initiate($order);

        return response()->json([
            'snap_token' => $result->snapToken,
            'external_id' => $result->externalId,
            'redirect_url' => $result->redirectUrl,
        ], 201);
    }
}
