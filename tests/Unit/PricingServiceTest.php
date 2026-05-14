<?php

namespace Tests\Unit;

use App\Models\Addon;
use App\Models\AnimationPack;
use App\Models\Theme;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PricingService();
    }

    public function test_format_returns_rupiah_string(): void
    {
        $this->assertSame('Rp 99.000', PricingService::format(99000));
        $this->assertSame('Rp 0', PricingService::format(0));
        $this->assertSame('Rp 1.500.000', PricingService::format(1500000));
    }

    public function test_calculate_with_no_inputs_returns_zero(): void
    {
        $result = $this->service->calculate(null, [], null);

        $this->assertSame(0, $result['total']);
        $this->assertNull($result['theme']);
        $this->assertSame([], $result['addons']);
    }

    public function test_calculate_includes_theme_price(): void
    {
        $theme = Theme::factory()->create(['base_price' => 99000, 'is_active' => true]);

        $result = $this->service->calculate($theme->id);

        $this->assertSame(99000, $result['theme_price']);
        $this->assertSame(99000, $result['total']);
    }

    public function test_calculate_includes_active_addons_only(): void
    {
        $active   = Addon::factory()->create(['price' => 29000, 'is_active' => true]);
        $inactive = Addon::factory()->create(['price' => 10000, 'is_active' => false]);

        $result = $this->service->calculate(null, [$active->id, $inactive->id]);

        $this->assertSame(29000, $result['addon_price']);
        $this->assertCount(1, $result['addons']);
    }

    public function test_calculate_uses_free_animation_when_none_specified(): void
    {
        AnimationPack::factory()->create(['key' => AnimationPack::KEY_FREE, 'price' => 0, 'is_active' => true]);

        $result = $this->service->calculate(null, [], null);

        $this->assertSame(0, $result['animation_price']);
        $this->assertNotNull($result['animation']);
        $this->assertSame(AnimationPack::KEY_FREE, $result['animation']['key']);
    }

    public function test_calculate_includes_specified_animation_price(): void
    {
        $pack = AnimationPack::factory()->create(['price' => 49000, 'is_active' => true]);

        $result = $this->service->calculate(null, [], $pack->id);

        $this->assertSame(49000, $result['animation_price']);
    }

    public function test_calculate_totals_all_components(): void
    {
        $theme   = Theme::factory()->create(['base_price' => 99000, 'is_active' => true]);
        $addon   = Addon::factory()->create(['price' => 29000, 'is_active' => true]);
        $pack    = AnimationPack::factory()->create(['price' => 49000, 'is_active' => true]);

        $result = $this->service->calculate($theme->id, [$addon->id], $pack->id);

        $this->assertSame(99000 + 29000 + 49000, $result['total']);
        $this->assertSame('Rp 177.000', $result['total_formatted']);
    }
}
