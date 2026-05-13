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
        return (new MailMessage)
            ->subject('Payment Receipt')
            ->line('Your payment has been successfully received.')
            ->line('Reference: ' . ($this->payment->gateway_transaction_id ?? '-'))
            ->line('Amount: Rp ' . number_format((int) $this->payment->amount, 0, ',', '.'));
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
