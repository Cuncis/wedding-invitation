<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

class RsvpResponseFactory extends Factory
{
    protected $model = RsvpResponse::class;

    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'guest_name'    => fake()->name(),
            'guest_phone'   => fake()->optional()->numerify('08##########'),
            'attendance'    => fake()->randomElement(['hadir', 'tidak_hadir', 'mungkin']),
            'pax'           => fake()->numberBetween(1, 5),
            'message'       => fake()->optional()->sentence(),
        ];
    }

    public function hadir(): static
    {
        return $this->state(['attendance' => 'hadir']);
    }
}
