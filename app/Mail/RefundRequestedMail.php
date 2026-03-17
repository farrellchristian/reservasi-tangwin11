<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\Refund;

class RefundRequestedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reservation;
    public $refund;

    public function __construct(Reservation $reservation, Refund $refund)
    {
        $this->reservation = $reservation;
        $this->refund = $refund;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Refund Diterima - Tangwin Cut Studio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.refund_requested',
            with: [
                'reservation' => $this->reservation,
                'refund' => $this->refund,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
