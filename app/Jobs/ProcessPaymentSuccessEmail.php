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

    /**
     * Jumlah maksimal percobaan jika job gagal.
     * Job akan dicoba 3x sebelum masuk ke failed_jobs.
     */
    public int $tries = 3;

    /**
     * Jeda waktu (detik) antar percobaan: 30s, 60s, 120s.
     */
    public array $backoff = [30, 60, 120];

    /**
     * Batas waktu eksekusi job (detik).
     */
    public int $timeout = 60;

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
     * CATATAN: Jangan tangkap Exception di sini agar Laravel Queue
     * tahu job gagal dan bisa melakukan retry otomatis.
     */
    public function handle(): void
    {
        // 1. Generate PDF dari View
        $pdf = Pdf::loadView('pdf.invoice', ['reservation' => $this->reservation]);
        $pdfOutput = $pdf->output();

        // 2. Kirim Email dengan Attachment
        Mail::to($this->reservation->customer_email)->send(new PaymentSuccessMail($this->reservation, $pdfOutput));

        Log::info("Email invoice terkirim ke: " . $this->reservation->customer_email);
    }

    /**
     * Dipanggil setelah semua percobaan habis dan job benar-benar gagal.
     */
    public function failed(\Throwable $exception): void
    {
        $email = $this->reservation->customer_email ?? 'unknown';
        $name  = $this->reservation->customer_name  ?? 'unknown';

        Log::error("Job email GAGAL PERMANEN untuk {$name} ({$email}) | Error: " . $exception->getMessage());
    }
}
