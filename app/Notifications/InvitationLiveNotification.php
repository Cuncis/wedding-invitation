<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationLiveNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Invitation $invitation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Invitation Is Live')
            ->line('Your invitation has been published and is now publicly accessible.')
            ->line('Slug: ' . $this->invitation->slug);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'slug' => $this->invitation->slug,
            'published_at' => $this->invitation->published_at,
        ];
    }
}
