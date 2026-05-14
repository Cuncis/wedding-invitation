<?php

namespace Tests\Feature;

use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(NotificationService::class);
    }

    public function test_send_whatsapp_returns_true_on_success(): void
    {
        Config::set('services.fonnte.token', 'test-token');

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $result = $this->service->sendWhatsApp('628123456789', 'Hello!');

        $this->assertTrue($result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request->hasHeader('Authorization', 'test-token')
                && $request['target'] === '628123456789'
                && $request['message'] === 'Hello!'
                && $request['countryCode'] === '62';
        });
    }

    public function test_send_whatsapp_returns_false_on_http_failure(): void
    {
        Config::set('services.fonnte.token', 'test-token');

        Http::fake([
            'api.fonnte.com/*' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        $result = $this->service->sendWhatsApp('628123456789', 'Hello!');

        $this->assertFalse($result);
    }

    public function test_send_whatsapp_returns_false_when_token_not_configured(): void
    {
        Config::set('services.fonnte.token', null);

        $result = $this->service->sendWhatsApp('628123456789', 'Hello!');

        $this->assertFalse($result);

        Http::assertNothingSent();
    }
}
