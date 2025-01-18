<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvinceController;

// Rute untuk halaman utama
Route::get('/', function () {
    return view('map');
})->name('home');

// Rute lain
Route::get('/provinces', [ProvinceController::class, 'index']);
Route::get('/hujan', function () {
    return view('hujan');
})->name('hujan');

Route::get('/air', function () {
    return view('air');
})->name('air');

Route::get('/tanah', function () {
    return view('tanah');
})->name('tanah');

