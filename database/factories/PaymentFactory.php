<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id'       => Order::factory(),
            'gateway'        => 'midtrans',
            'external_id'    => 'UND-' . date('Y') . '-' . $this->faker->unique()->numerify('#####'),
            'payment_method' => null,
            'status'         => Payment::STATUS_PENDING,
            'amount'         => 99000,
            'raw_payload'    => [],
            'paid_at'        => null,
        ];
    }

    public function settled(): static
    {
        return $this->state([
            'status'  => Payment::STATUS_SETTLEMENT,
            'paid_at' => now(),
        ]);
    }
}
