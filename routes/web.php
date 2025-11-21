<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BookingController;

Route::get('/', [LandingController::class, 'index'])->name('home');

// Route Halaman Booking (UBAH BAGIAN INI)
Route::get('/booking', [BookingController::class, 'showBookingForm'])->name('booking.form');