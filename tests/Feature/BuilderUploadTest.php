<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuilderUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable R2 in tests so uploadDisk() falls back to 'public'
        Config::set('filesystems.disks.r2.bucket', null);
    }

    public function test_unauthenticated_user_cannot_upload_couple_photo(): void
    {
        $invitation = Invitation::factory()->create();

        $this->postJson("/builder/{$invitation->id}/couple/upload", [
            'photo' => UploadedFile::fake()->image('groom.jpg', 400, 400),
            'role' => 'groom',
        ])->assertUnauthorized();
    }

    public function test_non_owner_cannot_upload_couple_photo(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create();

        $this->actingAs($otherUser)
            ->postJson("/builder/{$invitation->id}/couple/upload", [
                'photo' => UploadedFile::fake()->image('bride.jpg', 400, 400),
                'role' => 'bride',
            ])->assertForbidden();
    }

    public function test_owner_can_upload_groom_photo_to_public_disk(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $invitation = Invitation::factory()->for($user)->create();

        $file = UploadedFile::fake()->image('groom.jpg', 400, 400);

        $response = $this->actingAs($user)
            ->postJson("/builder/{$invitation->id}/couple/upload", [
                'photo' => $file,
                'role' => 'groom',
            ]);

        $response->assertOk()
            ->assertJsonStructure(['url'])
            ->assertJsonPath('url', fn (string $url) => str_contains($url, 'couple/'.$invitation->id.'/groom/'));

        Storage::disk('public')->assertExists(
            'couple/'.$invitation->id.'/groom/'.$this->extractFilename($response->json('url')),
        );
    }

    public function test_owner_can_upload_bride_photo_to_public_disk(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $invitation = Invitation::factory()->for($user)->create();

        $file = UploadedFile::fake()->image('bride.jpg', 400, 400);

        $response = $this->actingAs($user)
            ->postJson("/builder/{$invitation->id}/couple/upload", [
                'photo' => $file,
                'role' => 'bride',
            ]);

        $response->assertOk()
            ->assertJsonStructure(['url']);

        Storage::disk('public')->assertExists(
            'couple/'.$invitation->id.'/bride/'.$this->extractFilename($response->json('url')),
        );
    }

    public function test_couple_photo_upload_validates_role(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $invitation = Invitation::factory()->for($user)->create();

        $this->actingAs($user)
            ->postJson("/builder/{$invitation->id}/couple/upload", [
                'photo' => UploadedFile::fake()->image('test.jpg', 400, 400),
                'role' => 'invalid',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors(['role']);
    }

    public function test_couple_photo_upload_validates_file_type(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $invitation = Invitation::factory()->for($user)->create();

        $this->actingAs($user)
            ->postJson("/builder/{$invitation->id}/couple/upload", [
                'photo' => UploadedFile::fake()->create('doc.pdf', 100),
                'role' => 'groom',
            ])->assertUnprocessable()
            ->assertJsonValidationErrors(['photo']);
    }

    /**
     * Extract the filename (UUID.jpg) from a storage URL.
     */
    private function extractFilename(string $url): string
    {
        return basename(parse_url($url, PHP_URL_PATH));
    }
}
