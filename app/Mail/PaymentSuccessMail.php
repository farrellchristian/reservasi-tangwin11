<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment; // Penting buat PDF
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $pdfOutput;

    // Kita terima data Reservasi & File PDF mentah dari Controller
    public function __construct(Reservation $reservation, $pdfOutput)
    {
        $this->reservation = $reservation;
        $this->pdfOutput = $pdfOutput;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmed - Tangwin Cut Studio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_success', // Nama file view body email tadi
        );
    }

    // Di sini kita tempelkan PDF-nya
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfOutput, 'Invoice-TangwinCut.pdf')
                ->withMime('application/pdf'),
        ];
    }
}