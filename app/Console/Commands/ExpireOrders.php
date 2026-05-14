<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class ExpireOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Expire pending orders that passed their expiry time and reset invitations to draft';

    public function handle(OrderService $orderService): int
    {
        $count = $orderService->expireOldOrders();

        $this->info("Expired {$count} order(s).");

        return self::SUCCESS;
    }
}
