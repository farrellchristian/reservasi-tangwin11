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

        $query = DB::table('reservation_slots')
            ->join('reservation_slot_employee', 'reservation_slots.id_slot', '=', 'reservation_slot_employee.id_slot')
            ->where('reservation_slots.day_of_week', $dayName)
            ->where('reservation_slots.is_active', 1)
            ->where('reservation_slots.id_store', $storeId) // Filter slot berdasarkan id_store
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
                ->where('status', '!=', 'expired')
                ->where(function ($q) use ($employeeId) {
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
            'payment_method' => 'required|in:qris,bank_transfer',
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

            $reservation = new Reservation();
            $reservation->id_store = $request->store_id; // Menggunakan Dinamis Store ID 
            $reservation->customer_name = $request->customer_name;
            $reservation->customer_phone = $request->customer_phone;
            $reservation->customer_email = $request->customer_email;
            $reservation->booking_date = $request->date;
            $reservation->booking_time = $request->time;
            $reservation->id_service = $service->id_service;
            $reservation->id_employee = $request->capster_id;
            $reservation->status = 'pending';
            $reservation->notes = $request->notes;
            $reservation->save();

            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = config('midtrans.is_sanitized');
            Config::$is3ds = config('midtrans.is_3ds');

            $orderId = 'BOOK-' . $reservation->id_reservation . '-' . time();
            $grossAmount = (int)$service->price;

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
                ]
            ];

            if ($request->payment_method == 'qris') {
                $params['payment_type'] = 'qris';
                $params['qris'] = ['acquirer' => 'gopay'];
            }
            elseif ($request->payment_method == 'bank_transfer') {
                $params['payment_type'] = 'bank_transfer';
                $params['bank_transfer'] = ['bank' => 'bca'];
            }

            $response = CoreApi::charge($params);

            DB::commit();

            $resultData = [
                'status' => 'success',
                'order_id' => $orderId,
                'payment_type' => $request->payment_method,
                'reservation_id' => $reservation->id_reservation,
                'amount' => $grossAmount,
            ];

            if ($request->payment_method == 'qris') {
                $resultData['qr_image_url'] = $response->actions[0]->url ?? null;
                $resultData['expiration_time'] = $response->expiry_time ?? null;
            }
            elseif ($request->payment_method == 'bank_transfer') {
                $resultData['va_number'] = $response->va_numbers[0]->va_number ?? null;
                $resultData['bank'] = 'BCA';
                $resultData['expiration_time'] = $response->expiry_time ?? null;
            }

            return response()->json($resultData);
        }
        catch (\Exception $e) {
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
                }
                else {
                    $isPaid = true;
                }
            }
            else if ($transactionStatus == 'settlement') {
                $isPaid = true;
            }
            else if ($transactionStatus == 'pending') {
                $isPaid = false;
            }
            else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
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
                        }
                        catch (\Exception $e) {
                            Log::error("Gagal men-dispatch job email invoice: " . $e->getMessage());
                        }
                    }
                // --- SELESAI PROSES EMAIL ---
                }

                return response()->json(['status' => 'paid']);
            }

            return response()->json(['status' => 'pending']);
        }
        catch (\Exception $e) {
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
                        }
                        catch (\Exception $e) {
                            Log::error('Midtrans Webhook: Gagal dispatch email: ' . $e->getMessage());
                        }
                    }
                }
            }
            elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                // Pembayaran GAGAL/EXPIRED
                if ($reservation->status === 'pending') {
                    $reservation->status = 'expired';
                    $reservation->save();
                    Log::info('Midtrans Webhook: Reservasi #' . $reservationId . ' diubah ke expired');
                }
            }
            elseif ($transactionStatus == 'pending') {
                Log::info('Midtrans Webhook: Pembayaran masih pending untuk order ' . $orderId);
            }

            return response()->json(['message' => 'Notification processed']);
        }
        catch (\Exception $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Internal error'], 500);
        }
    }
}
