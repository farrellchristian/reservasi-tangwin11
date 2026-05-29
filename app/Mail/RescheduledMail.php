<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class RescheduledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $oldReservation;
    public $newReservation;

    public function __construct(Reservation $oldReservation, Reservation $newReservation)
    {
        $this->oldReservation = $oldReservation;
        $this->newReservation = $newReservation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jadwal Reservasi Anda Telah Diubah - Tangwin Cut',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reschedule_confirmed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
