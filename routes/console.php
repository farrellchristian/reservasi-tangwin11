<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan pengecekan setiap menit untuk mengubah status menjadi 'completed' jika jadwal sudah lewat
Schedule::command('reservations:complete-expired')->everyMinute();
