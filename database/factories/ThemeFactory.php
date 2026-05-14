<?php

namespace Database\Factories;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ThemeFactory extends Factory
{
    protected $model = Theme::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name'           => ucwords($name),
            'slug'           => Str::slug($name),
            'description'    => $this->faker->sentence(),
            'preview_image'  => null,
            'base_price'     => $this->faker->randomElement([0, 49000, 99000, 149000]),
            'default_colors' => ['primary' => '#e11d48', 'secondary' => '#f43f5e'],
            'default_fonts'  => ['heading' => 'Playfair Display', 'body' => 'Inter'],
            'is_active'      => true,
            'sort_order'     => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
