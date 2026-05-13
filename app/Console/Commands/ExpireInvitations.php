<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use Illuminate\Console\Command;

class ExpireInvitations extends Command
{
    protected $signature = 'invitations:expire';

    protected $description = 'Unpublish invitations after their event date has passed';

    public function handle(): int
    {
        $count = Invitation::where('is_published', true)
            ->whereDate('event_date', '<', now()->toDateString())
            ->update(['is_published' => false]);

        $this->info("Expired {$count} invitation(s).");

        return self::SUCCESS;
    }
}
