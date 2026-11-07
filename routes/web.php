<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Seluruh logika bisnis ditempatkan pada Controller & Service sesuai
| standar arsitektur Telyuji (tanpa Route Closure).
|
*/

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/lacak-sampel/{nomorTiket}', [LandingController::class, 'track'])
    ->where('nomorTiket', '[A-Za-z0-9\-_]+')
    ->middleware('throttle:60,1')
    ->name('public.track');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
