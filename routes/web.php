<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\FoodOrderController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RentalReservationController;
use App\Http\Controllers\SeloController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TestPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

Route::post('/language', function (Request $request) {
    $data = $request->validate(['locale' => ['required', Rule::in(['id', 'en'])]]);
    $request->session()->put('locale', $data['locale']);
    $previous = url()->previous();
    $target = parse_url($previous, PHP_URL_SCHEME) === parse_url(url('/'), PHP_URL_SCHEME)
        && parse_url($previous, PHP_URL_HOST) === parse_url(url('/'), PHP_URL_HOST)
        && parse_url($previous, PHP_URL_PORT) === parse_url(url('/'), PHP_URL_PORT) ? $previous : route('login');

    return redirect()->to($target, 303)->header('Cache-Control', 'no-store, private');
})->middleware('throttle:30,1,language-')->name('language.update');

Route::post('/selo/chat', SeloController::class)->middleware('throttle:30,1,selo-')->name('selo.answer');

Route::view('/tentang', 'site.about')->name('tentang');
Route::view('/cara-sewa', 'site.how-to')->name('cara-sewa');
Route::view('/kontak', 'site.contact')->name('kontak');

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

Route::get('/demo-bayar/{payment}', [TestPaymentController::class, 'scan'])->middleware(['signed', 'throttle:60,1,scan-demo-'])->name('payments.scan');
Route::post('/demo-bayar/{payment}', [TestPaymentController::class, 'scanPay'])->middleware(['signed', 'throttle:20,1,scan-demo-pay-'])->name('payments.scan-pay');
Route::middleware('auth')->group(function () {
    Route::get('/pembayaran/{payment}/status', [TestPaymentController::class, 'status'])->middleware('throttle:120,1,payment-status-')->name('payments.status');
    Route::get('/reservasi/{reservation}/struk.pdf', [ReceiptController::class, 'rental'])->middleware('throttle:20,1,receipt-')->name('receipts.rental');
    Route::get('/pesanan-makanan/{order}/struk.pdf', [ReceiptController::class, 'food'])->middleware('throttle:20,1,receipt-')->name('receipts.food');
    Route::get('/pembayaran/{payment}', [TestPaymentController::class, 'show'])->name('payments.show');
    Route::post('/pembayaran/reservasi/{reservation}', [TestPaymentController::class, 'reservation'])->name('payments.reservation');
    Route::post('/pembayaran/kedai/{order}', [TestPaymentController::class, 'food'])->name('payments.food');
    Route::post('/pembayaran/{payment}/uji', [TestPaymentController::class, 'simulate'])->middleware('throttle:20,1,payment-test-')->name('payments.simulate');
    Route::get('/pesanan-makanan', [FoodOrderController::class, 'index'])->name('food-orders.index');
    Route::post('/pesanan-makanan', [FoodOrderController::class, 'store'])->middleware('throttle:10,1,food-order-')->name('food-orders.store');
    Route::post('/pesanan-makanan/{order}/batal', [FoodOrderController::class, 'cancel'])->name('food-orders.cancel');

    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang', [CartController::class, 'store'])->middleware('throttle:60,1,cart-')->name('cart.store');
    Route::patch('/keranjang/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/keranjang/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

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
