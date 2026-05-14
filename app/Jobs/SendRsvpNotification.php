<?php

namespace App\Jobs;

use App\Models\RsvpResponse;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendRsvpNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public readonly RsvpResponse $rsvp) {}

    public function handle(NotificationService $notification): void
    {
        $invitation = $this->rsvp->invitation()->with('user')->first();

        if (! $invitation) {
            return;
        }

        $phone = $invitation->user?->whatsapp;

        if (! $phone) {
            return;
        }

        $attendanceLabel = match ($this->rsvp->attendance) {
            'hadir'       => '✅ Hadir',
            'tidak_hadir' => '❌ Tidak Hadir',
            default       => '🤔 Mungkin Hadir',
        };

        $message = "📩 *RSVP Baru!*\n\n"
            . "Tamu: *{$this->rsvp->guest_name}*\n"
            . "Kehadiran: {$attendanceLabel}\n"
            . "Jumlah: {$this->rsvp->pax} orang\n";

        if ($this->rsvp->message) {
            $message .= "Pesan: _{$this->rsvp->message}_\n";
        }

        $message .= "\nUndangan: {$invitation->coupleNames()}";

        $notification->sendWhatsApp($phone, $message);
    }
}
