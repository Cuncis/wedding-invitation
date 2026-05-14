<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number'    => 'UND-' . date('Y') . '-' . $this->faker->unique()->numerify('#####'),
            'user_id'         => User::factory(),
            'invitation_id'   => Invitation::factory(),
            'theme_price'     => 99000,
            'addon_price'     => 0,
            'animation_price' => 0,
            'total_amount'    => 99000,
            'status'          => Order::STATUS_PENDING,
            'snapshot'        => [],
            'expires_at'      => now()->addHours(24),
        ];
    }

    public function paid(): static
    {
        return $this->state(['status' => Order::STATUS_PAID]);
    }
}
