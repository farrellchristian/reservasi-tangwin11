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

    public function build()
    {
        return $this->subject('Booking Confirmed - Tangwin Cut Studio')
                    ->view('emails.payment_success')
                    ->attachData($this->pdfOutput, 'Invoice-TangwinCut.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}