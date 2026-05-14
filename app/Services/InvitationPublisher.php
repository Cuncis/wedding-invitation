<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationPublisher
{
    /**
     * Idempotent — skips if invitation is already published.
     * Generates a unique slug, sets status=active, published_at, expires_at=+1year.
     */
    public function publish(Invitation $invitation): Invitation
    {
        if ($invitation->published_at !== null) {
            return $invitation;
        }

        $slug = $this->generateUniqueSlug($invitation);

        $invitation->forceFill([
            'slug' => $slug,
            'status' => Invitation::STATUS_ACTIVE,
            'published_at' => now(),
            'expires_at' => now()->addYear(),
        ])->save();

        logger()->info("Invitation published: {$slug}", [
            'invitation_id' => $invitation->id,
            'couple' => $invitation->coupleNames(),
        ]);

        return $invitation->refresh();
    }

    private function generateUniqueSlug(Invitation $invitation): string
    {
        $base = Str::slug($invitation->groom_name . '-' . $invitation->bride_name);
        $slug = $base;
        $count = 2;

        while (Invitation::where('slug', $slug)->where('id', '!=', $invitation->id)->exists()) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
