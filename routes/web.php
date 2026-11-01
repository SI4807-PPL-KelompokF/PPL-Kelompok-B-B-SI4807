<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaboranHasilUjiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// PBI-13 (UC-13 / SC-13): Kelola Unggahan & Revisi Dokumen Hasil Uji oleh Laboran
Route::prefix('laboran')->name('laboran.')->middleware('auth')->group(function () {
    Route::get('/pengujian', [LaboranHasilUjiController::class, 'index'])->name('pengujian.index');
    Route::post('/pengujian/{nomorTiket}/unggah-hasil', [LaboranHasilUjiController::class, 'upload'])->name('pengujian.upload');
    Route::get('/pengujian/{nomorTiket}/unduh-hasil', [LaboranHasilUjiController::class, 'download'])->name('pengujian.download');
});
