<?php

namespace Database\Factories;

use App\Models\Addon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AddonFactory extends Factory
{
    protected $model = Addon::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'key' => Str::slug($name, '_'),
            'description' => $this->faker->sentence(),
            'icon' => 'heroicon-o-star',
            'price' => $this->faker->randomElement([0, 15000, 29000, 49000]),
            'category' => $this->faker->randomElement(['media', 'interactive', 'social', 'utility']),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
