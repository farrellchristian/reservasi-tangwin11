<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class CompleteExpiredReservations extends Command
{
    /**
     * Nama command artisan.
     */
    protected $signature = 'reservations:complete-expired';

    /**
     * Deskripsi command.
     */
    protected $description = 'Auto-expire pending reservations & Auto-complete approved reservations yang jadwalnya sudah terlewat';

    public function handle(): void
    {
        // 1. AUTO-EXPIRE PENDING (Tidak Dibayar > 10 menit)
        $expiredReservations = Reservation::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes(10))
            ->get();

        $expireCount = 0;
        if ($expiredReservations->isNotEmpty()) {
            foreach ($expiredReservations as $reservation) {
                $reservation->status = 'expired';
                $reservation->save();
                $expireCount++;
                Log::info("Reservation #{$reservation->id_reservation} ({$reservation->customer_name}) di-expire otomatis karena tidak ada pembayaran.");
            }
        }

        // 2. AUTO-COMPLETE APPROVED (Jadwal Sudah Terlewat)
        $now = now();
        $date = $now->toDateString();
        $time = $now->toTimeString(); // 'H:i:s'

        $completedReservations = Reservation::where('status', 'approved')
            ->where(function ($query) use ($date, $time) {
                $query->where('booking_date', '<', $date)
                      ->orWhere(function ($q) use ($date, $time) {
                          $q->where('booking_date', '=', $date)
                            ->where('booking_time', '<=', $time);
                      });
            })
            ->get();

        $completeCount = 0;
        if ($completedReservations->isNotEmpty()) {
            foreach ($completedReservations as $reservation) {
                $reservation->status = 'completed';
                $reservation->save();
                $completeCount++;
                Log::info("Reservation #{$reservation->id_reservation} ({$reservation->customer_name}) di-complete otomatis karena jadwal {$reservation->booking_date} {$reservation->booking_time} sudah lewat.");
            }
        }

        $this->info("Berhasil expire {$expireCount} reservasi & complete {$completeCount} reservasi.");
    }
}
