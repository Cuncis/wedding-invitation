<?php

namespace App\Services;

use App\Models\Invitation;

class InvitationPublisher
{
    public function publish(Invitation $invitation): Invitation
    {
        $invitation->forceFill([
            'is_published' => true,
            'published_at' => now(),
        ])->save();

        return $invitation->refresh();
    }
}
