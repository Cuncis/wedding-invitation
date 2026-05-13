<?php

namespace App\Services;

class PricingService
{
    public function calculate(int $basePrice, array $addonPrices = [], int $discount = 0): int
    {
        $addonTotal = array_sum(array_map('intval', $addonPrices));
        $subtotal = $basePrice + $addonTotal;

        return max($subtotal - $discount, 0);
    }
}
