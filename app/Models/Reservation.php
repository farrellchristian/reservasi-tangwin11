<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'id_reservation'; 
    protected $guarded = [];

    // --- TAMBAHKAN RELASI INI AGAR TIDAK ERROR ---
    
    // 1. Hubungan ke Service (Layanan)
    public function service()
    {
        return $this->belongsTo(Service::class, 'id_service', 'id_service');
    }

    // 2. Hubungan ke Employee (Capster)
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_employee', 'id_employee');
    }

    // 3. Hubungan ke Store (Cabang) - Opsional tapi bagus ada
    public function store()
    {
        // Asumsi kamu punya model Store, kalau belum ada function ini bisa dihapus/diabaikan
        // return $this->belongsTo(Store::class, 'id_store', 'id_store');
    }
}