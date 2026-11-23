<?php

use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\SessionController;
use App\Models\CatalogItem;
use Illuminate\Support\Facades\Route;

Route::redirect('/login/admin', '/admin/login');

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleLoginController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    $renderCatalog = function () {
        $items = CatalogItem::query()->orderBy('id')->get();

        return view('catalog', [
            'consoles' => $items->where('category', 'Konsol'),
            'menu' => $items->where('category', '!=', 'Konsol'),
        ]);
    };

    Route::redirect('/', '/home-page');
    Route::get('/catalog', $renderCatalog)->name('catalog');

    Route::view('/home-page', 'home-page')->name('home-page');
    Route::view('/home page', 'home-page');

    Route::view('/page-ke2', 'page-ke2')->name('page-ke2');
    Route::view('/page ke2', 'page-ke2');

    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
