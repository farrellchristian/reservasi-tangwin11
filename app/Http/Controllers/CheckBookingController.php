<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\Store;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\RescheduledMail;

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
        // Atau format baru (TWC-YYYYMM-NNN)
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

        if (!$reservationId && !str_starts_with(strtoupper($orderId), 'TWC-')) {
            return back()->with('error', 'Format Nomor Reservasi / Order ID tidak valid.')->withInput();
        }

        $reservation = Reservation::with(['service', 'employee', 'store'])
            ->where(function($q) use ($reservationId, $orderId) {
                if ($reservationId) {
                    $q->where('id_reservation', $reservationId);
                }
                $q->orWhere('booking_number', strtoupper($orderId));
            })
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

    // Tampilkan halaman reschedule
    public function reschedule(Request $request)
    {
        $request->validate([
            'id_reservation' => 'required|exists:reservations,id_reservation',
        ]);

        $reservation = Reservation::with(['service', 'employee', 'store'])
            ->where('id_reservation', $request->id_reservation)
            ->first();

        if (!$reservation || $reservation->status !== 'approved') {
            return redirect()->route('booking.check.form')
                ->with('error', 'Reservasi tidak ditemukan atau tidak dalam status yang dapat di-reschedule.');
        }

        return view('booking.reschedule', compact('reservation'));
    }

    // Proses reschedule: cancel booking lama, buat booking baru
    public function processReschedule(Request $request)
    {
        $request->validate([
            'id_reservation' => 'required|exists:reservations,id_reservation',
            'new_date'       => 'required|date|after_or_equal:today',
            'new_time'       => 'required|date_format:H:i',
        ], [
            'new_date.required'          => 'Tanggal baru wajib dipilih.',
            'new_date.after_or_equal'    => 'Tanggal tidak boleh di masa lalu.',
            'new_time.required'          => 'Waktu baru wajib dipilih.',
        ]);

        $oldReservation = Reservation::with(['service', 'employee', 'store'])
            ->where('id_reservation', $request->id_reservation)
            ->first();

        if (!$oldReservation || $oldReservation->status !== 'approved') {
            return back()->with('error', 'Reservasi tidak dapat di-reschedule. Pastikan statusnya sudah lunas/approved.');
        }

        $newDate    = $request->new_date;
        $newTime    = $request->new_time;
        $storeId    = $oldReservation->id_store;
        $employeeId = $oldReservation->id_employee;
        $serviceId  = $oldReservation->id_service;

        try {
            DB::beginTransaction();

            // 1. Dapatkan nama hari dari tanggal baru
            $dayMap = [
                'Sunday'    => 'Minggu', 'Monday'  => 'Senin', 'Tuesday'  => 'Selasa',
                'Wednesday' => 'Rabu',   'Thursday' => 'Kamis', 'Friday'   => 'Jumat',
                'Saturday'  => 'Sabtu',
            ];
            $dayName = $dayMap[date('l', strtotime($newDate))] ?? 'Senin';
            $formattedTime = date('H:i', strtotime($newTime));

            // 2. Validasi slot tidak di masa lalu
            $slotDateTime = Carbon::parse($newDate . ' ' . $newTime);
            if ($slotDateTime->isPast()) {
                DB::rollBack();
                return back()->with('error', 'Waktu yang dipilih sudah berlalu. Silakan pilih waktu lain.');
            }

            // 3. Pastikan slot aktif tersedia di toko ini
            $slot = DB::table('reservation_slots')
                ->where('day_of_week', $dayName)
                ->where('slot_time', 'like', $formattedTime . '%')
                ->where('id_store', $storeId)
                ->where('is_active', 1)
                ->first();

            if (!$slot) {
                DB::rollBack();
                return back()->with('error', 'Slot waktu yang dipilih tidak tersedia. Silakan pilih waktu lain.');
            }

            // 4. Cek ketersediaan slot (dengan lock untuk menghindari race condition)
            $bookedQuery = DB::table('reservations')
                ->whereNull('deleted_at')
                ->where('booking_date', $newDate)
                ->where('booking_time', 'like', $formattedTime . '%')
                ->where('id_store', $storeId)
                ->where('status', '!=', 'canceled')
                ->where('status', '!=', 'expired')
                ->where('status', '!=', 'refunded')
                ->lockForUpdate();

            if ($employeeId) {
                // Pastikan stylist masih aktif dan show_on_reservation = 1
                $stylistExists = Employee::where('id_employee', $employeeId)
                    ->whereNull('deleted_at')
                    ->where('show_on_reservation', 1)
                    ->exists();
                if (!$stylistExists) {
                    DB::rollBack();
                    return back()->with('error', 'Maaf, stylist Anda tidak tersedia untuk reservasi online saat ini. Silakan hubungi admin atau pilih stylist lain.');
                }

                // Stylist spesifik: limit 1 per slot
                $bookedCount = (clone $bookedQuery)->where('id_employee', $employeeId)->count();
                if ($bookedCount >= 1) {
                    DB::rollBack();
                    return back()->with('error', 'Maaf, stylist Anda tidak tersedia di waktu tersebut. Silakan pilih waktu lain.');
                }
                $assignedEmployeeId = $employeeId;
            } else {
                // Siapa saja: cari stylist yang masih available (harus aktif dan show_on_reservation)
                $assignedEmployees = DB::table('reservation_slot_employee')
                    ->join('employees', 'employees.id_employee', '=', 'reservation_slot_employee.id_employee')
                    ->where('reservation_slot_employee.id_slot', $slot->id_slot)
                    ->whereNull('employees.deleted_at')
                    ->where('employees.show_on_reservation', 1)
                    ->pluck('reservation_slot_employee.id_employee')
                    ->toArray();

                if (empty($assignedEmployees)) {
                    DB::rollBack();
                    return back()->with('error', 'Tidak ada stylist yang bertugas pada jam tersebut.');
                }

                $bookingsPerEmployee = (clone $bookedQuery)
                    ->select('id_employee', DB::raw('count(*) as total'))
                    ->whereIn('id_employee', $assignedEmployees)
                    ->groupBy('id_employee')
                    ->get()
                    ->keyBy('id_employee');

                $available = [];
                foreach ($assignedEmployees as $empId) {
                    $booked = isset($bookingsPerEmployee[$empId]) ? $bookingsPerEmployee[$empId]->total : 0;
                    if ($booked < 1) $available[] = $empId;
                }

                if (empty($available)) {
                    DB::rollBack();
                    return back()->with('error', 'Semua stylist sudah penuh di waktu tersebut. Silakan pilih waktu lain.');
                }

                $assignedEmployeeId = $available[array_rand($available)];
            }

            // 5. Cancel booking lama
            $oldReservation->status = 'canceled';
            $oldReservation->save();

            // 6. Buat booking baru (inherit data lama, ganti date/time/employee)
            $newReservation = new Reservation();
            $newReservation->id_store        = $storeId;
            $newReservation->id_service      = $serviceId;
            $newReservation->id_employee     = $assignedEmployeeId;
            $newReservation->customer_name   = $oldReservation->customer_name;
            $newReservation->customer_phone  = $oldReservation->customer_phone;
            $newReservation->customer_email  = $oldReservation->customer_email;
            $newReservation->booking_date    = $newDate;
            $newReservation->booking_time    = $newTime;
            $newReservation->payment_type    = $oldReservation->payment_type;
            $newReservation->notes           = $oldReservation->notes;
            $newReservation->status          = 'approved'; // langsung approved, tidak perlu bayar ulang
            $newReservation->save();

            DB::commit();

            // 7. Kirim email notifikasi reschedule
            if ($newReservation->customer_email) {
                try {
                    $newReservation->load(['service', 'employee', 'store']);
                    Mail::to($newReservation->customer_email)
                        ->queue(new RescheduledMail($oldReservation, $newReservation));
                    Log::info("Email reschedule di-queue untuk: " . $newReservation->customer_email);
                } catch (\Exception $e) {
                    Log::error("Gagal queue email reschedule: " . $e->getMessage());
                }
            }

            return redirect()->route('booking.check.form')
                ->with('success', 'Jadwal berhasil diubah! Booking baru #' . str_pad($newReservation->id_reservation, 5, '0', STR_PAD_LEFT) . ' telah dikonfirmasi. Silakan cek email Anda.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Reschedule error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
