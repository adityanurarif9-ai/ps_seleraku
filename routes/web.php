<?php

use App\Http\Controllers\SessionController;
use App\Models\CatalogItem;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $items = CatalogItem::query()->orderBy('id')->get();

        return view('catalog', [
            'consoles' => $items->where('category', 'Konsol'),
            'menu' => $items->where('category', '!=', 'Konsol'),
        ]);
    })->name('catalog');

    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
});
