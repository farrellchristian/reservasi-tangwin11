<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BookingController;

Route::get('/', [LandingController::class, 'index'])->name('home');

// Route Halaman Booking
Route::get('/booking', [BookingController::class, 'showBookingForm'])->name('booking.form');

// Route Cek Pesanan
Route::get('/cek-pesanan', [App\Http\Controllers\CheckBookingController::class, 'index'])->name('booking.check.form');
Route::post('/cek-pesanan', [App\Http\Controllers\CheckBookingController::class, 'search'])->name('booking.check.process');
Route::post('/cek-pesanan/cancel', [App\Http\Controllers\CheckBookingController::class, 'cancel'])->name('booking.check.cancel');

// Route : Untuk mengambil data slot (Ajax)
Route::get('/booking/slots', [BookingController::class, 'getAvailableSlots'])->name('booking.slots');

// Route Proses Simpan & Bayar (POST)
Route::post('/booking/process', [BookingController::class, 'processBooking'])->name('booking.process');

// Route Cek Status Pembayaran (Polling)
Route::get('/booking/check-status', [BookingController::class, 'checkPaymentStatus'])->name('booking.check');
