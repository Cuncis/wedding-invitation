<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceiptNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invitation = $this->payment->order?->invitation;
        $couple = $invitation?->coupleNames() ?? 'Undangan Digital';
        $amount = 'Rp ' . number_format((int) $this->payment->amount, 0, ',', '.');
        $ref = $this->payment->gateway_transaction_id ?? $this->payment->external_id ?? '-';
        $invitationUrl = $invitation?->slug ? url('/' . $invitation->slug) : null;

        $mail = (new MailMessage)
            ->subject("Pembayaran Berhasil — Undangan {$couple}")
            ->greeting('Halo!')
            ->line("Pembayaran untuk undangan **{$couple}** telah berhasil dikonfirmasi.")
            ->line("**Referensi:** {$ref}")
            ->line("**Total:** {$amount}")
            ->line("**Metode:** " . ($this->payment->payment_method ?? 'Online Payment'));

        if ($invitationUrl) {
            $mail->action('Lihat Undangan Saya', $invitationUrl);
        }

        return $mail
            ->line('Undangan Anda kini aktif dan dapat diakses oleh tamu.')
            ->salutation('Terima kasih — Tim Undangan Digital');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'order_id' => $this->payment->order_id,
            'status' => $this->payment->status,
        ];
    }
}
