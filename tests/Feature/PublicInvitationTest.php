<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpublished_invitation_returns_404(): void
    {
        $invitation = Invitation::factory()->create(['slug' => 'budi-sari-draft']);

        $this->get('/budi-sari-draft')->assertNotFound();
    }

    public function test_published_invitation_returns_200(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'budi-sari-pub']);

        $this->get('/budi-sari-pub')
            ->assertOk()
            ->assertSee($invitation->groom_name)
            ->assertSee($invitation->bride_name);
    }

    public function test_view_count_increments_on_each_visit(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'budi-view-test', 'view_count' => 0]);

        $this->get('/budi-view-test');
        $this->get('/budi-view-test');

        $this->assertDatabaseHas('invitations', [
            'id'         => $invitation->id,
            'view_count' => 2,
        ]);
    }
}
