<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // Menampilkan Halaman Wizard Booking
    public function showBookingForm()
    {
        // Ambil semua layanan
        $services = Service::all();
        
        // Ambil capster aktif
        $capsters = Employee::activeCapster()->get();

        return view('booking.wizard', compact('services', 'capsters'));
    }

    // Nanti kita tambahkan fungsi untuk cek slot & simpan data di langkah berikutnya
}