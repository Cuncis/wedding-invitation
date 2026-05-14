<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpTest extends TestCase
{
    use RefreshDatabase;

    private function rsvpPayload(array $overrides = []): array
    {
        return array_merge([
            'guest_name' => 'Ahmad Fulan',
            'attendance' => 'hadir',
            'pax'        => 2,
        ], $overrides);
    }

    public function test_guest_can_submit_rsvp_to_published_invitation(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rsvp-test-1']);

        $this->post("/rsvp/rsvp-test-1", $this->rsvpPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('rsvp_responses', [
            'invitation_id' => $invitation->id,
            'guest_name'    => 'Ahmad Fulan',
            'attendance'    => 'hadir',
            'pax'           => 2,
        ]);
    }

    public function test_rsvp_to_unpublished_invitation_returns_404(): void
    {
        Invitation::factory()->create(['slug' => 'rsvp-draft-inv']);

        $this->post('/rsvp/rsvp-draft-inv', $this->rsvpPayload())
            ->assertNotFound();
    }

    public function test_rsvp_validates_required_fields(): void
    {
        $invitation = Invitation::factory()->published()->create(['slug' => 'rsvp-validate']);

        $this->post('/rsvp/rsvp-validate', [])
            ->assertSessionHasErrors(['guest_name', 'attendance']);
    }

    public function test_owner_can_view_rsvp_dashboard(): void
    {
        $user = User::factory()->create();
        $invitation = Invitation::factory()->for($user)->published()->create(['slug' => 'rsvp-dash']);

        $this->actingAs($user)
            ->get(route('rsvp.index', $invitation))
            ->assertOk()
            ->assertSee('Daftar RSVP');
    }

    public function test_non_owner_cannot_view_rsvp_dashboard(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->published()->create(['slug' => 'rsvp-other']);

        $this->actingAs($other)
            ->get(route('rsvp.index', $invitation))
            ->assertForbidden();
    }
}
