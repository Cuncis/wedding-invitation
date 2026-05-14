<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\InvitationPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPublisherTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishes_invitation_with_unique_slug(): void
    {
        $publisher  = app(InvitationPublisher::class);
        $invitation = Invitation::factory()->create([
            'groom_name' => 'Budi',
            'bride_name' => 'Sari',
            'status'     => Invitation::STATUS_PENDING,
        ]);

        $result = $publisher->publish($invitation);

        $this->assertSame('budi-sari', $result->slug);
        $this->assertSame(Invitation::STATUS_ACTIVE, $result->status);
        $this->assertNotNull($result->published_at);
        $this->assertNotNull($result->expires_at);
        $this->assertTrue($result->expires_at->isAfter(now()->addMonths(11)));
    }

    public function test_generates_suffixed_slug_when_name_already_taken(): void
    {
        Invitation::factory()->create([
            'groom_name'   => 'Budi',
            'bride_name'   => 'Sari',
            'slug'         => 'budi-sari',
            'status'       => Invitation::STATUS_ACTIVE,
            'published_at' => now(),
            'expires_at'   => now()->addYear(),
        ]);

        $publisher  = app(InvitationPublisher::class);
        $invitation = Invitation::factory()->create([
            'groom_name' => 'Budi',
            'bride_name' => 'Sari',
        ]);

        $result = $publisher->publish($invitation);

        $this->assertSame('budi-sari-2', $result->slug);
    }

    public function test_is_idempotent_when_already_published(): void
    {
        $publisher  = app(InvitationPublisher::class);
        $invitation = Invitation::factory()->create([
            'slug'         => 'already-set',
            'status'       => Invitation::STATUS_ACTIVE,
            'published_at' => now()->subDay(),
            'expires_at'   => now()->addYear(),
        ]);

        $result = $publisher->publish($invitation);

        $this->assertSame('already-set', $result->slug);
        $this->assertSame(
            $invitation->published_at->toDateTimeString(),
            $result->published_at->toDateTimeString()
        );
    }
}
