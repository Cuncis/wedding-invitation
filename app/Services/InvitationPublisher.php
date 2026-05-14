<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationLiveNotification;
use Illuminate\Support\Str;

class InvitationPublisher
{
    public function __construct(private readonly NotificationService $notification)
    {
    }

    /**
     * Idempotent — skips if invitation is already published.
     * Generates a unique slug, sets status=active, published_at, expires_at=+1year.
     * Optionally sends WhatsApp + email to $notifyUser after publishing.
     */
    public function publish(Invitation $invitation, ?User $notifyUser = null): Invitation
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

        if ($notifyUser) {
            $notifyUser->notify(new InvitationLiveNotification($invitation->refresh()));

            if ($notifyUser->whatsapp) {
                $waMessage = "🎉 Undangan *{$invitation->coupleNames()}* sudah aktif!\n\n"
                    . "🔗 Link undangan:\n" . url('/' . $slug) . "\n\n"
                    . "Link ini aktif selama 1 tahun. Selamat!";

                $this->notification->sendWhatsApp($notifyUser->whatsapp, $waMessage);
            }
        }

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
