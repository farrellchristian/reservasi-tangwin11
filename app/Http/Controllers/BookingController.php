<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Store;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\PaymentSuccessMail;

class BookingController extends Controller
{
    public function showBookingForm()
    {
        // Ambil semua Toko/Cabang yang aktif
        $stores = Store::where('is_active', 1)->get();
        // Ambil data layanan & kapster untuk difilter via frontend
        $services = Service::all();
        $capsters = Employee::activeCapster()->get();

        return view('booking.wizard', compact('stores', 'services', 'capsters'));
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id_store',
            'date' => 'required|date',
            'employee_id' => 'nullable|integer'
        ]);

        $date = $request->date;
        $employeeId = $request->employee_id;
        $storeId = $request->store_id;
        $dayName = $this->getDayNameIndonesian($date);

        // Ambil Unique Slots dari reservation_slots yang memiliki setidaknya satu Stylist ditugaskan
        $query = DB::table('reservation_slots')
            ->where('reservation_slots.day_of_week', $dayName)
            ->where('reservation_slots.is_active', 1)
            ->where('reservation_slots.id_store', $storeId)
            ->whereExists(function ($q) use ($employeeId) {
                $q->select(DB::raw(1))
                    ->from('reservation_slot_employee')
                    ->whereColumn('reservation_slot_employee.id_slot', 'reservation_slots.id_slot');
                
                if ($employeeId) {
                    $q->where('reservation_slot_employee.id_employee', $employeeId);
                }
            })
            ->select('reservation_slots.id_slot', 'reservation_slots.slot_time', 'reservation_slots.quota');

        $slots = $query->orderBy('slot_time', 'asc')->get();
        $availableSlots = [];

        foreach ($slots as $slot) {
            // Tentukan Limit Quota
            // Jika stylist spesifik dipilih: limit = 1 (1 stylist hanya bisa melayani 1 pelanggan per jam)
            // Jika "Bebas/Siapa Saja": limit = quota (total kapasitas slot = jumlah stylist yang bertugas)
            if ($employeeId) {
                $limitQuota = 1;
            } else {
                $limitQuota = $slot->quota;
            }

            // Normalisasi format waktu (misal "09:00:00" atau "09:00" jadi "09:00")
            $formattedSlotTime = date('H:i', strtotime($slot->slot_time));

            $bookedCount = DB::table('reservations')
                ->where('booking_date', $date)
                ->where('booking_time', 'like', $formattedSlotTime . '%')
                ->where('id_store', $storeId) // Filter berdasarkan store agar akurat
                ->where('status', '!=', 'canceled')
                ->where('status', '!=', 'expired')
                ->where(function ($q) use ($employeeId) {
                    if ($employeeId) {
                        $q->where('id_employee', $employeeId);
                    }
                })
                ->count();

            // Tambahkan semua slot, tandai sebagai is_full jika kuota habis
            $isPast = false;
            $slotDateTime = Carbon::parse($date . ' ' . $slot->slot_time);
            if ($slotDateTime->isPast()) {
                $isPast = true;
            }

            $slot->formatted_time = date('H:i', strtotime($slot->slot_time));
            $slot->is_past = $isPast;
            $slot->is_full = ($bookedCount >= $limitQuota);
            $availableSlots[] = $slot;
        }

        return response()->json([
            'day' => $dayName,
            'slots' => $availableSlots
        ]);
    }

    public function processBooking(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id_store',
            'service_id' => 'required|exists:services,id_service',
            'capster_id' => 'nullable|exists:employees,id_employee',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'payment_method' => 'required|in:qris,bank_transfer,cash',
        ]);

        // Verifikasi bahwa Service benar-benar dari Store yang dipilih
        $service = Service::where('id_service', $request->service_id)
            ->where('id_store', $request->store_id)
            ->first();

        if (!$service) {
            return response()->json(['status' => 'error', 'message' => 'Layanan yang dipilih tidak tersedia di cabang ini.'], 400);
        }

        // Verifikasi Capster jika dipilih
        if ($request->capster_id) {
            $capster = Employee::where('id_employee', $request->capster_id)
                ->where('id_store', $request->store_id)
                ->first();

            if (!$capster) {
                return response()->json(['status' => 'error', 'message' => 'Stylist yang dipilih tidak bertugas di cabang ini.'], 400);
            }
        }

        try {
            DB::beginTransaction();

            // === RACE CONDITION PROTECTION ===
            // Cek ulang ketersediaan slot di dalam transaksi dengan lock
            $dayName = $this->getDayNameIndonesian($request->date);
            $formattedReqTime = date('H:i', strtotime($request->time));

            $slotDateTime = Carbon::parse($request->date . ' ' . $request->time);
            if ($slotDateTime->isPast()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Waktu slot yang dipilih sudah berlalu. Silakan pilih waktu lain.'
                ], 400);
            }

            $slot = DB::table('reservation_slots')
                ->where('day_of_week', $dayName)
                ->where('slot_time', 'like', $formattedReqTime . '%')
                ->where('id_store', $request->store_id)
                ->where('is_active', 1)
                ->first();

            if (!$slot) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Slot waktu yang dipilih tidak tersedia.'
                ], 409);
            }

            // Lock baris reservasi yang relevan agar proses lain menunggu
            // Filter by employee if specific capster is chosen
            $bookedQuery = DB::table('reservations')
                ->where('booking_date', $request->date)
                ->where('booking_time', 'like', $formattedReqTime . '%')
                ->where('id_store', $request->store_id)
                ->where('status', '!=', 'canceled')
                ->where('status', '!=', 'expired')
                ->lockForUpdate();

            if ($request->capster_id) {
                $bookedCount = (clone $bookedQuery)->where('id_employee', $request->capster_id)->count();
                // 1 stylist hanya bisa melayani 1 pelanggan per jam
                if ($bookedCount >= 1) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Maaf, stylist yang dipilih baru saja terisi oleh pelanggan lain. Silakan pilih waktu atau stylist lain.'
                    ], 409);
                }
                $assignedCapsterId = $request->capster_id;
            } else {
                // "Bebas / Siapa Saja" logic
                // 1. Get all employees assigned to this slot
                $assignedEmployees = DB::table('reservation_slot_employee')
                    ->where('id_slot', $slot->id_slot)
                    ->pluck('id_employee')
                    ->toArray();

                if (empty($assignedEmployees)) {
                    DB::rollBack();
                    return response()->json(['status' => 'error', 'message' => 'Tidak ada stylist yang bertugas pada jam ini.'], 409);
                }

                // 2. Count current bookings for each employee at this time
                $bookingsPerEmployee = (clone $bookedQuery)
                    ->select('id_employee', DB::raw('count(*) as total'))
                    ->whereIn('id_employee', $assignedEmployees)
                    ->groupBy('id_employee')
                    ->get()
                    ->keyBy('id_employee');

                // 3. Find available employees
                $availableEmployees = [];
                foreach ($assignedEmployees as $empId) {
                    $booked = isset($bookingsPerEmployee[$empId]) ? $bookingsPerEmployee[$empId]->total : 0;
                    // 1 stylist hanya bisa melayani 1 pelanggan per jam
                    if ($booked < 1) {
                        $availableEmployees[] = $empId;
                    }
                }

                if (empty($availableEmployees)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Maaf, semua stylist pada jam ini baru saja penuh. Silakan pilih waktu lain.'
                    ], 409);
                }

                // 4. Randomly pick one of the available employees
                $assignedCapsterId = $availableEmployees[array_rand($availableEmployees)];
            }
            // === END RACE CONDITION PROTECTION ===

            $reservation = new Reservation();
            $reservation->id_store = $request->store_id;
            $reservation->customer_name = $request->customer_name;
            $reservation->customer_phone = $request->customer_phone;
            $reservation->customer_email = $request->customer_email;
            $reservation->booking_date = $request->date;
            $reservation->booking_time = $request->time;
            $reservation->id_service = $service->id_service;
            $reservation->id_employee = $assignedCapsterId; // Gunakan capster yang sudah ditugaskan
            $reservation->status = 'pending';
            $reservation->payment_type = $request->payment_method;
            $reservation->notes = $request->notes;
            $reservation->save();

            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');

            $orderId = 'BOOK-' . $reservation->id_reservation . '-' . time();
            $grossAmount = (int) $service->price;

            $params = [
                'payment_type' => '',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email, // Kirim email ke Midtrans juga
                ],
                'item_details' => [
                    [
                        'id' => $service->id_service,
                        'price' => $grossAmount,
                        'quantity' => 1,
                        'name' => substr($service->service_name, 0, 50),
                    ]
                ],
                'expiry' => [
                    'start_time' => date("Y-m-d H:i:s O"),
                    'unit' => 'minute',
                    'duration' => 10
                ],
            ];

            // === CASH: Skip Midtrans, langsung konfirmasi ===
            if ($request->payment_method == 'cash') {
                $reservation->status = 'approved';
                $reservation->save();

                // Dispatch email konfirmasi langsung
                if ($reservation->customer_email) {
                    try {
                        \App\Jobs\ProcessPaymentSuccessEmail::dispatch($reservation);
                        Log::info("Cash booking approved & email job dispatched for: " . $reservation->customer_email);
                    } catch (\Exception $e) {
                        Log::error("Gagal dispatch email untuk Cash booking: " . $e->getMessage());
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'payment_type' => 'cash',
                    'order_id' => $orderId,
                    'reservation_id' => $reservation->id_reservation,
                    'amount' => $grossAmount,
                ]);
            }

            // === ONLINE PAYMENT: Proses via Midtrans Snap ===
            unset($params['payment_type']); // Snap API does not strictly require payment_type at the root for general open
            
            if ($request->payment_method == 'qris') {
                $params['enabled_payments'] = ['gopay', 'other_qris', 'shopeepay'];
            } elseif ($request->payment_method == 'bank_transfer') {
                $params['enabled_payments'] = ['bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'permata_va', 'cimb_va'];
            }

            // Dapatkan token SNAP
            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'order_id' => $orderId,
                'payment_type' => $request->payment_method,
                'reservation_id' => $reservation->id_reservation,
                'amount' => $grossAmount,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // --- FUNGSI UTAMA: CEK STATUS, GENERATE PDF, & KIRIM EMAIL ---
    public function checkPaymentStatus(Request $request)
    {
        $orderId = $request->order_id;

        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');

            $status = \Midtrans\Transaction::status($orderId);
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status;

            $isPaid = false;

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // Challenge
                } else {
                    $isPaid = true;
                }
            } else if ($transactionStatus == 'settlement') {
                $isPaid = true;
            } else if ($transactionStatus == 'pending') {
                $isPaid = false;
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                return response()->json(['status' => 'failed']);
            }

            if ($isPaid) {
                $parts = explode('-', $orderId);
                $reservationId = $parts[1];

                $reservation = Reservation::with(['service', 'employee'])->find($reservationId);

                // Cek agar tidak kirim email double (Hanya jika status sebelumnya BUKAN approved)
                if ($reservation && $reservation->status !== 'approved') {
                    $reservation->status = 'approved';
                    $reservation->save();

                    // --- MULAI PROSES EMAIL OTOMATIS (DI LATAR BELAKANG) ---
                    if ($reservation->customer_email) {
                        try {
                            // Dispatch pekerjaan kirim email ke Queue
                            \App\Jobs\ProcessPaymentSuccessEmail::dispatch($reservation);
                            Log::info("Job email invoice di-dispatch untuk: " . $reservation->customer_email);
                        } catch (\Exception $e) {
                            Log::error("Gagal men-dispatch job email invoice: " . $e->getMessage());
                        }
                    }
                    // --- SELESAI PROSES EMAIL ---
                }

                return response()->json(['status' => 'paid']);
            }

            return response()->json(['status' => 'pending']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function getDayNameIndonesian($dateString)
    {
        $englishDay = date('l', strtotime($dateString));
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        return $days[$englishDay] ?? 'Senin';
    }

    // --- WEBHOOK: Midtrans Notification Handler (Production) ---
    public function handleNotification(Request $request)
    {
        Log::info('Midtrans Webhook diterima', $request->all());

        try {
            $serverKey = config('midtrans.server_key');
            $notification = $request->all();

            // 1. Verifikasi Signature Hash (Keamanan)
            $orderId = $notification['order_id'];
            $statusCode = $notification['status_code'];
            $grossAmount = $notification['gross_amount'];
            $signatureKey = $notification['signature_key'] ?? '';

            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($signatureKey !== $expectedSignature) {
                Log::warning('Midtrans Webhook: Signature tidak valid untuk order ' . $orderId);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            // 2. Proses Status Transaksi
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? 'accept';

            // Ambil reservation ID dari order_id (format: BOOK-{ID}-{TIMESTAMP})
            $parts = explode('-', $orderId);
            if (count($parts) < 2) {
                Log::error('Midtrans Webhook: Format order_id tidak valid: ' . $orderId);
                return response()->json(['message' => 'Invalid order_id format'], 400);
            }
            $reservationId = $parts[1];
            $reservation = Reservation::with(['service', 'employee'])->find($reservationId);

            if (!$reservation) {
                Log::error('Midtrans Webhook: Reservasi tidak ditemukan: ' . $reservationId);
                return response()->json(['message' => 'Reservation not found'], 404);
            }

            // 3. Update status berdasarkan notification
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($transactionStatus == 'capture' && $fraudStatus == 'challenge') {
                    Log::info('Midtrans Webhook: Transaksi CHALLENGE untuk order ' . $orderId);
                    return response()->json(['message' => 'Transaction challenged']);
                }

                // Pembayaran BERHASIL - Update status & kirim email
                if ($reservation->status !== 'approved') {
                    $reservation->status = 'approved';
                    $reservation->save();

                    Log::info('Midtrans Webhook: Reservasi #' . $reservationId . ' diubah ke approved');

                    // Kirim email invoice
                    if ($reservation->customer_email) {
                        try {
                            \App\Jobs\ProcessPaymentSuccessEmail::dispatch($reservation);
                            Log::info('Midtrans Webhook: Job email invoice di-dispatch untuk ' . $reservation->customer_email);
                        } catch (\Exception $e) {
                            Log::error('Midtrans Webhook: Gagal dispatch email: ' . $e->getMessage());
                        }
                    }
                }
            } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // Pembayaran GAGAL/EXPIRED
                if ($reservation->status === 'pending') {
                    $reservation->status = 'expired';
                    $reservation->save();
                    Log::info('Midtrans Webhook: Reservasi #' . $reservationId . ' diubah ke expired');
                }
            } elseif ($transactionStatus == 'pending') {
                Log::info('Midtrans Webhook: Pembayaran masih pending untuk order ' . $orderId);
            }

            return response()->json(['message' => 'Notification processed']);
        } catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal error'], 500);
        }
    }
}
