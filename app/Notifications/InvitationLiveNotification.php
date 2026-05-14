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
        $couple = $this->invitation->coupleNames();
        $url = url('/' . $this->invitation->slug);
        $expires = $this->invitation->expires_at?->isoFormat('D MMMM Y') ?? '-';

        return (new MailMessage)
            ->subject("Undangan {$couple} Sudah Aktif! 🎉")
            ->greeting('Selamat!')
            ->line("Undangan pernikahan **{$couple}** kini sudah aktif dan dapat diakses oleh tamu.")
            ->action('Buka Undangan Saya', $url)
            ->line("Link aktif hingga **{$expires}**.")
            ->line('Bagikan link ini kepada tamu undangan Anda.')
            ->salutation('Terima kasih — Tim Undangan Digital');
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
