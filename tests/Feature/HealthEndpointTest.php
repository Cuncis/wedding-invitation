<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonFragment(['status' => 'ok'])
            ->assertJsonStructure(['status', 'checks', 'timestamp']);
    }

    public function test_health_endpoint_includes_database_check(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonPath('checks.database', 'ok');
    }

    public function test_health_endpoint_is_publicly_accessible(): void
    {
        $this->getJson('/health')->assertOk();
    }
}
