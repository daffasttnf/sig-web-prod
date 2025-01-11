<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProvinceController;



Route::get('/', function () {
    return view('welcome');
});


Route::get('/provinces', [ProvinceController::class, 'index']);


Route::get('/map', function () {
    return view('map');
});
