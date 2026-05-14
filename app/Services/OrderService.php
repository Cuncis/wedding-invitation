<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function __construct(private readonly PricingService $pricing)
    {
    }

    /**
     * Create or reuse a pending order for the invitation.
     *
     * Reuses an existing non-expired pending order to prevent duplicates.
     * Locks the pricing snapshot at the moment of checkout.
     *
     * @throws RuntimeException when already paid or total === 0
     */
    public function createOrder(Invitation $invitation): Order
    {
        if ($invitation->status === Invitation::STATUS_ACTIVE) {
            throw new RuntimeException('Invitation sudah aktif dan tidak dapat dibayar ulang.');
        }

        $pricing = $this->pricing->calculateFromInvitation($invitation);

        if ($pricing['total'] <= 0) {
            throw new RuntimeException('Pilih tema terlebih dahulu sebelum checkout.');
        }

        return DB::transaction(function () use ($invitation, $pricing): Order {
            // Reuse existing pending, non-expired order
            $existing = $invitation->orders()
                ->where('status', Order::STATUS_PENDING)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if ($existing) {
                return $existing;
            }

            $order = $invitation->orders()->create([
                'order_number' => Order::generateNumber(),
                'user_id' => $invitation->user_id,
                'theme_price' => $pricing['theme_price'],
                'addon_price' => $pricing['addon_price'],
                'animation_price' => $pricing['animation_price'],
                'total_amount' => $pricing['total'],
                'status' => Order::STATUS_PENDING,
                'snapshot' => $pricing,
                'expires_at' => now()->addHours(24),
            ]);

            // Mark invitation as 'pending' while awaiting payment
            $invitation->update(['status' => Invitation::STATUS_PENDING]);

            return $order;
        });
    }

    /**
     * Mark an order as paid. Also flips the invitation to active.
     * Publishing the slug is handled by InvitationPublisher listener.
     */
    public function markAsPaid(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order->update(['status' => Order::STATUS_PAID]);
            $order->invitation->update(['status' => Invitation::STATUS_ACTIVE]);
        });
    }

    /**
     * Expire pending orders past their expires_at timestamp.
     * Resets the invitation back to draft so the user can retry.
     */
    public function expireOldOrders(): int
    {
        $expired = Order::query()
            ->where('status', Order::STATUS_PENDING)
            ->where('expires_at', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            return 0;
        }

        DB::transaction(function () use ($expired): void {
            foreach ($expired as $order) {
                $order->update(['status' => Order::STATUS_FAILED]);

                if ($order->invitation && $order->invitation->status === Invitation::STATUS_PENDING) {
                    $order->invitation->update(['status' => Invitation::STATUS_DRAFT]);
                }
            }
        });

        return $expired->count();
    }
}
