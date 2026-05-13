<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function __construct(private readonly PricingService $pricingService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'base_price' => ['nullable', 'integer', 'min:0'],
            'addon_prices' => ['nullable', 'array'],
            'addon_prices.*' => ['integer', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0'],
        ]);

        $total = $this->pricingService->calculate(
            (int) $request->integer('base_price', 0),
            (array) $request->input('addon_prices', []),
            (int) $request->integer('discount', 0)
        );

        return response()->json(['total' => $total]);
    }
}
