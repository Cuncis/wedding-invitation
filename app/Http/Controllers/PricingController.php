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
        $data = $request->validate([
            'theme_id' => ['nullable', 'integer', 'exists:themes,id'],
            'addon_ids' => ['nullable', 'array'],
            'addon_ids.*' => ['integer', 'exists:addons,id'],
            'animation_pack_id' => ['nullable', 'integer', 'exists:animation_packs,id'],
        ]);

        $result = $this->pricingService->calculate(
            themeId: isset($data['theme_id']) ? (int) $data['theme_id'] : null,
            addonIds: $data['addon_ids'] ?? [],
            animationPackId: isset($data['animation_pack_id']) ? (int) $data['animation_pack_id'] : null,
        );

        return response()->json($result);
    }
}
