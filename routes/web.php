<?php

use App\Http\Controllers\AdminMesinLabController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaboranHasilUjiController;
use App\Http\Controllers\LaboranSopController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PengajuanUjiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaboranAntreanController;

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

// Modul Pengajuan (PBI-07, PBI-08, PBI-09 - Giri Pulung Nugroho)
Route::middleware('auth')->group(function () {
    Route::get('/pengajuan/buat', [PengajuanUjiController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [PengajuanUjiController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{nomorTiket}', [PengajuanUjiController::class, 'show'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengajuan.show');
    Route::get('/pengajuan/{nomorTiket}/resi/unduh', [PengajuanUjiController::class, 'downloadResi'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengajuan.resi.download');
    Route::get('/pengajuan/{nomorTiket}/resi/preview', [PengajuanUjiController::class, 'previewResi'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengajuan.resi.preview');
    Route::get('/pengajuan/{nomorTiket}/resi/cetak', [PengajuanUjiController::class, 'printResi'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengajuan.resi.print');
});

Route::get('/pengajuan/{nomorTiket}/unduh-lhu', [HomeController::class, 'downloadHasil'])
    ->where('nomorTiket', '[A-Za-z0-9\-_]+')
    ->middleware('auth')
    ->name('pemohon.pengujian.download');


// PBI-10: Validasi penerimaan fisik dan progres pengujian Laboran
Route::prefix('laboran')->name('laboran.')->middleware(['auth', 'laboran'])->group(function () {
    Route::get('/antrean', [LaboranAntreanController::class, 'index'])
        ->name('antrean.index');

    Route::post('/antrean/{nomorTiket}/terima-fisik', [LaboranAntreanController::class, 'terimaFisik'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('antrean.terima-fisik');

    Route::post('/antrean/{nomorTiket}/proses-mesin', [LaboranAntreanController::class, 'prosesMesin'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('antrean.proses-mesin');
});


// PBI-13 (UC-13 / SC-13): Kelola Unggahan & Revisi Dokumen Hasil Uji oleh Laboran
Route::prefix('laboran')->name('laboran.')->middleware(['auth', 'laboran'])->group(function () {
    Route::get('/pengujian', [LaboranHasilUjiController::class, 'index'])->name('pengujian.index');
    Route::post('/pengujian/{nomorTiket}/unggah-hasil', [LaboranHasilUjiController::class, 'upload'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengujian.upload');
    Route::get('/pengujian/{nomorTiket}/unduh-hasil', [LaboranHasilUjiController::class, 'download'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengujian.download');
});

// Manajemen Pengguna oleh Administrator
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/pengguna', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

// PBI-04 (SPK-4): Kelola Katalog Mesin dan Status Operasional oleh Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('mesin', AdminMesinLabController::class)->except(['show']);
    Route::patch('/mesin/{mesin}/toggle', [AdminMesinLabController::class, 'toggleStatus'])->name('mesin.toggle');
});

// PBI-06 (SPK-6): Kelola Teks SOP Penyiapan Sampel oleh Laboran(HELMI)
Route::prefix('laboran')->name('laboran.')->middleware(['auth', 'laboran'])->group(function () {
    Route::get('/sop', [LaboranSopController::class, 'index'])->name('sop.index');
    Route::get('/sop/{mesin}/edit', [LaboranSopController::class, 'edit'])->name('sop.edit');
    Route::put('/sop/{mesin}', [LaboranSopController::class, 'update'])->name('sop.update');
    Route::delete('/sop/{mesin}', [LaboranSopController::class, 'destroy'])->name('sop.destroy');
});