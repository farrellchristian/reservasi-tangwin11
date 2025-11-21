<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\CoreApi;

class BookingController extends Controller
{
    public function showBookingForm()
    {
        $services = Service::all();
        $capsters = Employee::activeCapster()->get();
        return view('booking.wizard', compact('services', 'capsters'));
    }

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
                ->where('status', '!=', 'expired')
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

    // --- FUNGSI 1: PROSES BOOKING ---
    public function processBooking(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id_service',
            'capster_id' => 'nullable|exists:employees,id_employee',
            'date' => 'required|date',
            'time' => 'required',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $service = Service::find($request->service_id);

        try {
            DB::beginTransaction();

            $reservation = new Reservation();
            $reservation->id_store = 2; 
            $reservation->customer_name = $request->customer_name;
            $reservation->customer_phone = $request->customer_phone;
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
                'order_id' => $orderId, // <--- INI PENTING UNTUK POLLING
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

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // --- FUNGSI 2: CEK STATUS (POLLING) ---
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
                // Update status reservasi di database
                $parts = explode('-', $orderId);
                $reservationId = $parts[1]; // Ambil ID dari BOOK-{ID}-TIMESTAMP

                $reservation = Reservation::find($reservationId);
                if($reservation) {
                    $reservation->status = 'approved';
                    $reservation->save();
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
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        return $days[$englishDay] ?? 'Senin';
    }
}