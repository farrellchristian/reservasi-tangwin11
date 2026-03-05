<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Reservation;
use App\Mail\PaymentSuccessMail;

class ProcessPaymentSuccessEmail implements ShouldQueue
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new job instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. Generate PDF dari View
            $pdf = Pdf::loadView('pdf.invoice', ['reservation' => $this->reservation]);
            $pdfOutput = $pdf->output(); // Ambil output mentah PDF-nya

            // 2. Kirim Email dengan Attachment
            Mail::to($this->reservation->customer_email)->send(new PaymentSuccessMail($this->reservation, $pdfOutput));

            Log::info("Email invoice terkirim ke: " . $this->reservation->customer_email);
        } catch (\Exception $e) {
            Log::error("Gagal kirim email invoice via Job: " . $e->getMessage());
        }
    }
}
