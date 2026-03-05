<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';
    protected $primaryKey = 'id_store';
    protected $guarded = [];

    // Mengambil store yang aktif saja
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
