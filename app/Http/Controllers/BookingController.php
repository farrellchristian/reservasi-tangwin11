<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Reservation; // Pastikan Model ini ada (kita buat di langkah awal)
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

class BookingController extends Controller
{
    // 1. Menampilkan Halaman Wizard
    public function showBookingForm()
    {
        $services = Service::all();
        $capsters = Employee::activeCapster()->get();
        return view('booking.wizard', compact('services', 'capsters'));
    }

    // 2. API: Cek Slot Tersedia
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'employee_id' => 'nullable|integer'
        ]);

        $date = $request->date;
        $employeeId = $request->employee_id;
        $dayName = $this->getDayNameIndonesian($date);

        $query = DB::table('reservation_slots')
            ->join('reservation_slot_employee', 'reservation_slots.id_slot', '=', 'reservation_slot_employee.id_slot')
            ->where('reservation_slots.day_of_week', $dayName)
            ->where('reservation_slots.is_active', 1)
            ->select('reservation_slots.id_slot', 'reservation_slots.slot_time', 'reservation_slots.quota');

        if ($employeeId) {
            $query->where('reservation_slot_employee.id_employee', $employeeId);
        }

        $slots = $query->orderBy('slot_time', 'asc')->get();
        $availableSlots = [];

        foreach ($slots as $slot) {
            $bookedCount = DB::table('reservations')
                ->where('booking_date', $date)
                ->where('booking_time', $slot->slot_time)
                ->where('status', '!=', 'canceled')
                ->where(function($q) use ($employeeId) {
                    if ($employeeId) {
                        $q->where('id_employee', $employeeId);
                    }
                })
                ->count();

            if ($bookedCount < $slot->quota) {
                $slot->formatted_time = date('H:i', strtotime($slot->slot_time));
                $availableSlots[] = $slot;
            }
        }

        return response()->json([
            'day' => $dayName,
            'slots' => $availableSlots
        ]);
    }

    // 3. PROSES BOOKING & MIDTRANS (INI YANG BARU)
    public function processBooking(Request $request)
    {
        // Validasi Input
        $request->validate([
            'service_id' => 'required|exists:services,id_service',
            'capster_id' => 'nullable|exists:employees,id_employee', // Boleh null kalau "Siapa Saja"
            'date' => 'required|date',
            'time' => 'required',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        // Ambil data Service untuk tahu harganya
        $service = Service::find($request->service_id);

        try {
            DB::beginTransaction();

            // 1. Simpan ke Database
            $reservation = new Reservation();
            $reservation->id_store = 2; // ID 2 = Syuhada (Sesuai screenshot awal)
            $reservation->customer_name = $request->customer_name;
            $reservation->customer_phone = $request->customer_phone;
            $reservation->booking_date = $request->date;
            $reservation->booking_time = $request->time; // Format '10:00'
            $reservation->id_service = $service->id_service;
            $reservation->id_employee = $request->capster_id; // Bisa null
            $reservation->status = 'pending'; // Status awal pending bayar
            $reservation->notes = $request->notes;
            $reservation->save();

            // 2. Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');

            // 3. Siapkan Parameter Transaksi Midtrans
            // Order ID kita buat unik: BOOK-{ID_RESERVASI}-{TIMESTAMP}
            $orderId = 'BOOK-' . $reservation->id_reservation . '-' . time();

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $service->price, // Harga harus integer
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                ],
                'item_details' => [
                    [
                        'id' => $service->id_service,
                        'price' => (int) $service->price,
                        'quantity' => 1,
                        'name' => substr($service->service_name, 0, 50), // Midtrans max 50 chars name
                    ]
                ]
            ];

            // 4. Minta Snap Token ke Midtrans
            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            // 5. Kembalikan Token ke Frontend
            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'reservation_id' => $reservation->id_reservation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Helper
    private function getDayNameIndonesian($dateString)
    {
        $englishDay = date('l', strtotime($dateString));
        $days = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        return $days[$englishDay] ?? 'Senin';
    }
}