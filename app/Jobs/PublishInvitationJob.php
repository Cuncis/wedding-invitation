<?php

namespace App\Jobs;

use App\Models\Invitation;
use App\Services\InvitationPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishInvitationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $invitationId)
    {
    }

    public function handle(InvitationPublisher $publisher): void
    {
        $invitation = Invitation::find($this->invitationId);

        if (! $invitation) {
            return;
        }

        $publisher->publish($invitation);
    }
}
