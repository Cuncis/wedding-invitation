<?php

namespace Tests\Feature;

use App\Models\AnimationPack;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\Theme;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_checkout(): void
    {
        $invitation = Invitation::factory()->create();

        $this->get(route('checkout.show', $invitation))
            ->assertRedirect(route('login'));
    }

    public function test_owner_can_view_checkout_page(): void
    {
        $user = User::factory()->create();
        $theme = Theme::factory()->create(['base_price' => 99000, 'is_active' => true]);
        AnimationPack::factory()->create(['key' => AnimationPack::KEY_FREE, 'price' => 0, 'is_active' => true]);

        $invitation = Invitation::factory()
            ->for($user)
            ->withConfig(themeId: $theme->id)
            ->create();

        $this->actingAs($user)
            ->get(route('checkout.show', $invitation))
            ->assertOk()
            ->assertSee('Rp 99.000');
    }

    public function test_checkout_creates_order_and_marks_invitation_pending(): void
    {
        $user = User::factory()->create();
        $theme = Theme::factory()->create(['base_price' => 99000, 'is_active' => true]);
        AnimationPack::factory()->create(['key' => AnimationPack::KEY_FREE, 'price' => 0, 'is_active' => true]);

        $invitation = Invitation::factory()
            ->for($user)
            ->withConfig(themeId: $theme->id)
            ->create();

        $this->actingAs($user)
            ->post(route('checkout.store', $invitation))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'invitation_id' => $invitation->id,
            'status'        => Order::STATUS_PENDING,
            'total_amount'  => 99000,
        ]);

        $this->assertDatabaseHas('invitations', [
            'id'     => $invitation->id,
            'status' => Invitation::STATUS_PENDING,
        ]);
    }

    public function test_checkout_reuses_existing_pending_order(): void
    {
        $user = User::factory()->create();
        $theme = Theme::factory()->create(['base_price' => 49000, 'is_active' => true]);
        AnimationPack::factory()->create(['key' => AnimationPack::KEY_FREE, 'price' => 0, 'is_active' => true]);

        $invitation = Invitation::factory()
            ->for($user)
            ->withConfig(themeId: $theme->id)
            ->create();

        $service = app(OrderService::class);

        $first  = $service->createOrder($invitation);
        $second = $service->createOrder($invitation);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Order::count());
    }
}
