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

        $payment = $this->paymentService->createForOrder($order);

        return response()->json($payment, 201);
    }
}
