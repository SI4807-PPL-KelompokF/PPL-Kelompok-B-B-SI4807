<?php

use App\Http\Controllers\AdminFakultasProdiController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaboranHasilUjiController;
use App\Http\Controllers\LaboranValidasiKtmController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PengajuanUjiController;
use App\Http\Controllers\ProfileController;
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

// PBI-01 (UC-01): Profil Akademik & Validasi Identitas KTM — Pemohon (Syalfa Oktaviana)
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::get('/profil/prodi', [ProfileController::class, 'prodiByFakultas'])->name('profil.prodi');
    Route::get('/profil/identitas/preview', [ProfileController::class, 'previewIdentitas'])->name('profil.identitas.preview');
});

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

    // Validasi Identitas & KTM Pemohon oleh Laboran (PBI-01 - Syalfa)
    Route::get('/validasi-ktm', [LaboranValidasiKtmController::class, 'index'])->name('validasi.ktm.index');
    Route::post('/validasi-ktm/{user}/setuju', [LaboranValidasiKtmController::class, 'approve'])->name('validasi.ktm.approve');
    Route::post('/validasi-ktm/{user}/tolak', [LaboranValidasiKtmController::class, 'reject'])->name('validasi.ktm.reject');
    Route::get('/validasi-ktm/{user}/preview', [LaboranValidasiKtmController::class, 'previewKtm'])->name('validasi.ktm.preview');
});

// Manajemen Pengguna oleh Administrator
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/pengguna', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // PBI-03 (UC-04): Kelola Master Data Fakultas dan Program Studi (Syalfa)
    Route::get('/master-fakultas-prodi', [AdminFakultasProdiController::class, 'index'])->name('master.fakultas-prodi.index');
    Route::post('/fakultas', [AdminFakultasProdiController::class, 'storeFakultas'])->name('fakultas.store');
    Route::put('/fakultas/{fakultas}', [AdminFakultasProdiController::class, 'updateFakultas'])->name('fakultas.update');
    Route::delete('/fakultas/{fakultas}', [AdminFakultasProdiController::class, 'destroyFakultas'])->name('fakultas.destroy');

    Route::post('/prodi', [AdminFakultasProdiController::class, 'storeProdi'])->name('prodi.store');
    Route::put('/prodi/{prodi}', [AdminFakultasProdiController::class, 'updateProdi'])->name('prodi.update');
    Route::delete('/prodi/{prodi}', [AdminFakultasProdiController::class, 'destroyProdi'])->name('prodi.destroy');
});

// Endpoint API untuk ambil daftar prodi berdasarkan fakultas
Route::get('/api/fakultas/{fakultasId}/prodi', [AdminFakultasProdiController::class, 'getProdiByFakultas'])->name('api.fakultas.prodi');
