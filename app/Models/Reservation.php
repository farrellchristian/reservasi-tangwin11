<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';
    
    // Kita wajib kasih tau Laravel kalau primary key-nya bukan 'id'
    protected $primaryKey = 'id_reservation'; 

    // Izinkan semua kolom diisi (mass assignment)
    protected $guarded = [];
}