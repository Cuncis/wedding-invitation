<?php

namespace Tests\Feature;

use App\Jobs\ProcessPaymentWebhook;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\MidtransGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    private function buildPayload(Order $order, string $transactionStatus = 'settlement'): array
    {
        $grossAmount = (string) $order->total_amount . '.00';
        $statusCode  = '200';
        $serverKey   = config('midtrans.server_key', '');
        $signature   = hash('sha512', $order->order_number . $statusCode . $grossAmount . $serverKey);

        return [
            'order_id'           => $order->order_number,
            'transaction_status' => $transactionStatus,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signature,
            'payment_type'       => 'bank_transfer',
            'fraud_status'       => 'accept',
        ];
    }

    public function test_rejects_webhook_with_invalid_signature(): void
    {
        $order   = Order::factory()->create();
        Payment::factory()->for($order)->create(['external_id' => $order->order_number]);

        $this->postJson('/api/webhooks/midtrans', [
            'order_id'      => $order->order_number,
            'signature_key' => 'bad-signature',
            'status_code'   => '200',
            'gross_amount'  => '99000.00',
        ])->assertStatus(401);
    }

    public function test_dispatches_job_on_valid_webhook(): void
    {
        Queue::fake();

        $order   = Order::factory()->create();
        Payment::factory()->for($order)->create(['external_id' => $order->order_number]);

        $this->postJson('/api/webhooks/midtrans', $this->buildPayload($order))
            ->assertStatus(200);

        Queue::assertPushed(ProcessPaymentWebhook::class);
    }

    public function test_returns_200_even_when_payment_record_not_found(): void
    {
        Queue::fake();

        $order = Order::factory()->create();

        $this->postJson('/api/webhooks/midtrans', $this->buildPayload($order))
            ->assertStatus(200);

        Queue::assertNothingPushed();
    }
}
