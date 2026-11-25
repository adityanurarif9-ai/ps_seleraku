<?php

use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RentalReservationController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::post('/selo/chat', \App\Http\Controllers\SeloController::class)->middleware('throttle:30,1,selo-')->name('selo.answer');

Route::redirect('/login/admin', '/admin/login');
Route::get('/', [SessionController::class, 'landing']);

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleLoginController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');
    Route::view('/register', 'register')->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->middleware('throttle:5,1,register-');
    Route::get('/auth/google/link', [GoogleLoginController::class, 'linkForm'])->name('google.link');
    Route::post('/auth/google/link', [GoogleLoginController::class, 'link'])->middleware('throttle:5,1,google-link-');
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:10,1,login-');
});

Route::middleware('auth')->group(function () {

    Route::view('/home-page', 'home-page')->name('home-page');
    Route::view('/home page', 'home-page');

    Route::view('/page-ke2', 'page-ke2')->name('page-ke2');
    Route::view('/page ke2', 'page-ke2');

    Route::view('/sewa-harian', 'sewa-harian')->name('sewa-harian');
    Route::view('/sewa harian', 'sewa-harian');

    Route::view('/menu-fnb', 'menu-fnb')->name('menu-fnb');
    Route::view('/menu fnb', 'menu-fnb');

    Route::get('/rental-saya', [RentalReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservasi/baru', [RentalReservationController::class, 'create'])->name('reservations.create');
    Route::get('/reservasi/ketersediaan', [RentalReservationController::class, 'availability'])->middleware('throttle:60,1,rental-availability-')->name('reservations.availability');
    Route::post('/reservasi', [RentalReservationController::class, 'store'])->middleware('throttle:10,1,rental-create-')->name('reservations.store');
    Route::get('/reservasi/{reservation}', [RentalReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservasi/{reservation}/status', [RentalReservationController::class, 'status'])->middleware('throttle:60,1,rental-status-')->name('reservations.status');
    Route::post('/reservasi/{reservation}/batal', [RentalReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservasi/{reservation}/tambah-waktu', [RentalReservationController::class, 'extend'])->middleware('throttle:10,1,rental-extension-')->name('reservations.extend');

    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
