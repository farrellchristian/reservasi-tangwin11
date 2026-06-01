<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id_employee';
    protected $guarded = [];

    // Fungsi bantuan untuk mengambil hanya pegawai yang jabatannya 'Capster' dan masih aktif.
    public function scopeActiveCapster($query)
    {
        return $query->where('position', 'Capster')
            ->whereNull('deleted_at');
    }

    // Hanya mengambil pegawai yang diizinkan tampil di web reservasi (booking).
    public function scopeShowOnReservation($query)
    {
        return $query->where('show_on_reservation', 1);
    }
}