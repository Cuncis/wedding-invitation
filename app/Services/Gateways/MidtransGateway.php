<?php

namespace App\Services\Gateways;

use App\Models\Order;

class MidtransGateway
{
    public function createCharge(Order $order): array
    {
        return [
            'reference' => 'MID-' . $order->id . '-' . now()->timestamp,
            'gross_amount' => $order->total_amount,
            'status' => 'pending',
        ];
    }
}
