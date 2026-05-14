<?php

namespace App\Services;

use App\Models\Addon;
use App\Models\AnimationPack;
use App\Models\Invitation;
use App\Models\Theme;

class PricingService
{
    /**
     * Single source of truth for all price calculations.
     *
     * @param  int|null  $themeId
     * @param  array<int>  $addonIds
     * @param  int|null  $animationPackId
     * @return array{
     *   theme: ?array{id:int,name:string,price:int},
     *   addons: array<int, array{id:int,name:string,key:string,price:int}>,
     *   animation: ?array{id:int,name:string,key:string,price:int},
     *   theme_price: int,
     *   addon_price: int,
     *   animation_price: int,
     *   total: int,
     *   total_formatted: string,
     * }
     */
    public function calculate(?int $themeId, array $addonIds = [], ?int $animationPackId = null): array
    {
        // Theme (optional — null when nothing selected yet)
        $theme = $themeId ? Theme::active()->find($themeId) : null;
        $themePrice = $theme ? (int) $theme->base_price : 0;
        $themeData = $theme ? [
            'id' => (int) $theme->id,
            'name' => $theme->name,
            'price' => (int) $theme->base_price,
        ] : null;

        // Addons — ignore inactive
        $addonIds = array_values(array_unique(array_map('intval', $addonIds)));
        $addons = $addonIds
            ? Addon::active()->whereIn('id', $addonIds)->get()
            : collect();
        $addonPrice = (int) $addons->sum('price');
        $addonsData = $addons->map(fn(Addon $a) => [
            'id' => (int) $a->id,
            'name' => $a->name,
            'key' => $a->key,
            'price' => (int) $a->price,
        ])->values()->all();

        // Animation pack — default to free tier when none specified
        $animation = $animationPackId
            ? AnimationPack::active()->find($animationPackId)
            : AnimationPack::active()->where('key', AnimationPack::KEY_FREE)->first();
        $animationPrice = $animation ? (int) $animation->price : 0;
        $animationData = $animation ? [
            'id' => (int) $animation->id,
            'name' => $animation->name,
            'key' => $animation->key,
            'price' => (int) $animation->price,
        ] : null;

        $total = $themePrice + $addonPrice + $animationPrice;

        return [
            'theme' => $themeData,
            'addons' => $addonsData,
            'animation' => $animationData,
            'theme_price' => $themePrice,
            'addon_price' => $addonPrice,
            'animation_price' => $animationPrice,
            'total' => $total,
            'total_formatted' => self::format($total),
        ];
    }

    /**
     * Calculate pricing from an Invitation's saved config.
     */
    public function calculateFromInvitation(Invitation $invitation): array
    {
        $config = $invitation->config;

        return $this->calculate(
            themeId: $config?->theme_id,
            addonIds: $config?->addon_ids ?? [],
            animationPackId: $config?->animation_pack_id,
        );
    }

    /**
     * Format an integer Rupiah amount as 'Rp 99.000'.
     */
    public static function format(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
