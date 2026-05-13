<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function create(?int $userId, array $payload): Order
    {
        return Order::create([
            ...$payload,
            'user_id' => $userId,
            'status' => $payload['status'] ?? 'pending',
        ]);
    }
}
