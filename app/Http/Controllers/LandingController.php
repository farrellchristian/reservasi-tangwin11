<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Employee;

class LandingController extends Controller
{
    public function index()
    {
        // Ambil semua data layanan
        $services = Service::all(); 
        
        // Ambil data pegawai yang jabatannya Capster & Aktif (pakai fungsi scope di Model tadi)
        $capsters = Employee::activeCapster()->get();

        // Kirim data ke tampilan 'welcome'
        return view('welcome', compact('services', 'capsters'));
    }
}