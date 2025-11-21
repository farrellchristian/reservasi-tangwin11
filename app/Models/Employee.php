<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id_employee';
    protected $guarded = [];
    
    // Fungsi bantuan untuk mengambil hanya pegawai yang jabatannya 'Capster' dan Aktif
    public function scopeActiveCapster($query)
    {
        return $query->where('position', 'Capster')->where('is_active', 1);
    }
}