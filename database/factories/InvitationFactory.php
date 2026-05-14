<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        $groom = $this->faker->firstName('male');
        $bride = $this->faker->firstName('female');

        return [
            'user_id'    => User::factory(),
            'slug'       => Str::slug("{$groom}-{$bride}-" . $this->faker->unique()->numerify('####')),
            'groom_name' => $groom,
            'bride_name' => $bride,
            'event_date' => now()->addMonths(3),
            'event_venue'=> $this->faker->address(),
            'status'     => Invitation::STATUS_DRAFT,
            'view_count' => 0,
        ];
    }

    public function withConfig(?int $themeId = null, array $addonIds = [], ?int $animationPackId = null): static
    {
        return $this->afterCreating(function (Invitation $invitation) use ($themeId, $addonIds, $animationPackId): void {
            $invitation->config()->create([
                'theme_id'          => $themeId,
                'animation_pack_id' => $animationPackId,
                'addon_ids'         => $addonIds,
            ]);
        });
    }
}
