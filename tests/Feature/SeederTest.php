<?php

namespace Tests\Feature;

use App\Models\Addon;
use App\Models\AnimationPack;
use App\Models\Theme;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_themes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('themes', 3);
        $this->assertDatabaseHas('themes', ['slug' => 'elegant-rose']);
        $this->assertDatabaseHas('themes', ['slug' => 'modern-minimalist']);
        $this->assertDatabaseHas('themes', ['slug' => 'floral-garden']);
    }

    public function test_database_seeder_creates_addons(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('addons', 8);
        $this->assertDatabaseHas('addons', ['key' => 'music_player']);
        $this->assertDatabaseHas('addons', ['key' => 'rsvp_form', 'price' => 0]);
    }

    public function test_database_seeder_creates_animation_packs(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('animation_packs', 3);
        $this->assertDatabaseHas('animation_packs', ['key' => AnimationPack::KEY_FREE, 'price' => 0]);
        $this->assertDatabaseHas('animation_packs', ['key' => AnimationPack::KEY_PREMIUM]);
    }

    public function test_database_seeder_creates_admin_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@undanganmu.com',
            'role'  => User::ROLE_ADMIN,
        ]);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('themes', 3);
        $this->assertDatabaseCount('addons', 8);
        $this->assertDatabaseCount('animation_packs', 3);
    }
}
