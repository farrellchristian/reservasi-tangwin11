<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $table = 'services'; // Nama tabel di database
    protected $primaryKey = 'id_service'; // Primary key tabel kamu
    protected $guarded = []; // Agar semua kolom aman diisi
}