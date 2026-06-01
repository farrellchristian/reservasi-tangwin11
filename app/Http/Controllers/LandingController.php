<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Store;
use App\Models\Announcement;

class LandingController extends Controller
{
    public function index()
    {
        // Ambil semua toko yang aktif beserta service-nya
        $stores = Store::whereNull('deleted_at')
            ->with(['services' => function ($query) {
                $query->whereNull('deleted_at');
            }])
            ->get()
            ->filter(fn($store) => $store->services->isNotEmpty());

        // Ambil data pegawai yang jabatannya Capster & Aktif (pakai fungsi scope di Model tadi)
        $capsters = Employee::activeCapster()->get();

        // Ambil pengumuman aktif
        $announcement = Announcement::where('is_active', true)->first();

        // Kirim data ke tampilan 'welcome'
        return view('welcome', compact('stores', 'capsters', 'announcement'));
    }
}