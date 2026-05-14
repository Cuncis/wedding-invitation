<?php

namespace Database\Factories;

use App\Models\AnimationPack;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AnimationPackFactory extends Factory
{
    protected $model = AnimationPack::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name'        => ucwords($name),
            'key'         => Str::slug($name, '_'),
            'description' => $this->faker->sentence(),
            'features'    => ['Fade in', 'Slide up'],
            'price'       => $this->faker->randomElement([0, 49000, 99000]),
            'is_active'   => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
