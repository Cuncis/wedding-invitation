<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Payment $payment) {}

    public function envelope(): Envelope
    {
        $coupleName = $this->payment->order->invitation->coupleNames();

        return new Envelope(
            subject: "Pembayaran Berhasil — Undangan {$coupleName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-receipt',
        );
    }
}
