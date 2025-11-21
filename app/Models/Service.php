<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services'; // Nama tabel di database
    protected $primaryKey = 'id_service'; // Primary key tabel kamu
    protected $guarded = []; // Agar semua kolom aman diisi
}