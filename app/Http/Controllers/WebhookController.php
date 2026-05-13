<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $result = $this->paymentService->handleWebhook($request->all());

        return response()->json(['processed' => $result]);
    }
}
