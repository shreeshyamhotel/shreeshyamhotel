<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\LoginController;

// Auth Routes (Guest accessible)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Protected Routes (Required Authentication)
Route::middleware('auth')->group(function () {
    // Logout Action
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rooms Management
    Route::resource('rooms', RoomController::class);

    // Guest Check-in & Directory
    Route::get('/checkin', [GuestController::class, 'showCheckinForm'])->name('checkin.form');
    Route::post('/checkin', [GuestController::class, 'checkin'])->name('checkin.store');
    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('/guests/lookup', [GuestController::class, 'lookup'])->name('guests.lookup');
    Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');

    // Checkout & Billing
    Route::get('/checkout/{stayRecord}', [BillingController::class, 'showCheckoutForm'])->name('checkout.form');
    Route::post('/checkout/{stayRecord}', [BillingController::class, 'checkout'])->name('checkout.store');
    Route::post('/checkout/{stayRecord}/extra-charge', [BillingController::class, 'addExtraCharge'])->name('checkout.extra-charge');
    Route::get('/invoice/{stayRecord}', [BillingController::class, 'invoice'])->name('checkout.invoice');

    // Advance Bookings
    Route::get('/bookings', [GuestController::class, 'bookingsIndex'])->name('bookings.index');
    Route::get('/bookings/create', function() {
        return redirect()->route('checkin.form', ['entry_type' => 'booking']);
    })->name('bookings.create');
    Route::post('/bookings/{stayRecord}/checkin', [GuestController::class, 'checkinBooking'])->name('bookings.checkin');
    Route::post('/bookings/{stayRecord}/cancel', [GuestController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::get('/bookings/{stayRecord}/edit', [GuestController::class, 'editBookingForm'])->name('bookings.edit');
    Route::post('/bookings/{stayRecord}/update', [GuestController::class, 'updateBooking'])->name('bookings.update');

    // Reports
    Route::get('/reports/monthly', [BillingController::class, 'monthlyReport'])->name('reports.monthly');

    // Room Availability
    Route::get('/rooms/{room}/availability', [RoomController::class, 'availability'])->name('rooms.availability');
});
