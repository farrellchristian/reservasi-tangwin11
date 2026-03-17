<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Refund;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\RefundRequestedMail;

class CheckBookingController extends Controller
{
    // Menampilkan halaman form pencarian
    public function index()
    {
        return view('booking.check');
    }

    // Memproses pencarian reservasi
    public function search(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'contact' => 'required|string', // Bisa email atau nomor WA
        ], [
            'order_id.required' => 'Nomor Reservasi/Order ID wajib diisi.',
            'contact.required' => 'Email atau Nomor WhatsApp wajib diisi.',
        ]);

        $orderId = trim($request->order_id);
        $contact = trim($request->contact);

        // Ekstrak ID Reservasi dari format Invoice (INV-00061 atau INV-61)
        // Atau jika user hanya memasukkan ID Reservasi langsung (61)
        // Atau format lama Midtrans (BOOK-{ID}-{TIMESTAMP})
        $reservationId = null;
        if (str_starts_with(strtoupper($orderId), 'INV-')) {
            $reservationId = (int) str_replace('INV-', '', strtoupper($orderId));
        } elseif (str_starts_with(strtoupper($orderId), 'BOOK-')) {
            $parts = explode('-', $orderId);
            if (count($parts) >= 2 && is_numeric($parts[1])) {
                $reservationId = (int) $parts[1];
            }
        } elseif (is_numeric($orderId)) {
            $reservationId = (int) $orderId;
        }

        if (!$reservationId) {
            return back()->with('error', 'Format Nomor Reservasi / Order ID tidak valid.')->withInput();
        }

        $reservation = Reservation::with(['service', 'employee', 'store'])
            ->where('id_reservation', $reservationId)
            ->where(function ($query) use ($contact) {
                $query->where('customer_phone', $contact)
                    ->orWhere('customer_email', $contact);
            })
            ->first();

        if (!$reservation) {
            return back()->with('error', 'Reservasi tidak ditemukan atau data kontak tidak cocok.')->withInput();
        }

        // Tampilkan halaman detail jika ditemukan
        return view('booking.detail', compact('reservation'));
    }

    // Memproses pembatalan dan refund
    public function cancel(Request $request)
    {
        $request->validate([
            'id_reservation' => 'required|exists:reservations,id_reservation',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'cancel_reason' => 'required|string',
        ]);

        $reservation = Reservation::where('id_reservation', $request->id_reservation)->first();

        // Validasi Status dan Waktu
        if ($reservation->status !== 'approved') {
            return back()->with('error', 'Reservasi ini tidak dapat dibatalkan karena statusnya belum lunas/approved.');
        }

        $bookingDate = Carbon::parse($reservation->booking_date);
        $today = Carbon::today();

        if ($today->diffInDays($bookingDate, false) <= 0) {
            return back()->with('error', 'Pembatalan maksimal dilakukan H-1 (Satu hari sebelum jadwal).');
        }

        try {
            DB::beginTransaction();

            // 1. Ubah status reservasi
            $reservation->status = 'refund_requested';
            $reservation->save();

            // 2. Simpan Data Refund
            $refund = Refund::create([
                'id_reservation' => $reservation->id_reservation,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'cancel_reason' => $request->cancel_reason,
                'amount' => $reservation->service->price ?? 0,
                'status' => 'pending'
            ]);

            DB::commit();

            // 3. Kirim Email Notifikasi (Background / Queue jika worker jalan)
            if ($reservation->customer_email) {
                try {
                    Mail::to($reservation->customer_email)->send(new RefundRequestedMail($reservation, $refund));
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email refund_requested ke " . $reservation->customer_email . ": " . $e->getMessage());
                }
            }

            return back()->with('success', 'Permintaan pembatalan dan pengajuan refund telah berhasil dikirim. Admin kami akan segera memproses dana Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
