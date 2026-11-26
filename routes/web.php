<?php

use App\Http\Controllers\AdminFakultasProdiController;
use App\Http\Controllers\AdminMesinLabController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaboranAntreanController;
use App\Http\Controllers\LaboranHasilUjiController;
use App\Http\Controllers\LaboranSopController;
<<<<<<< HEAD
use App\Http\Controllers\LaboranSuspendController;
=======
>>>>>>> origin/main
use App\Http\Controllers\LaboranValidasiKtmController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PengajuanUjiController;
use App\Http\Controllers\ProfileController;
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

// =========================================================================
// 1. RUTE PUBLIK & AUTENTIKASI
// =========================================================================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/lacak-sampel/{nomorTiket}', [LandingController::class, 'track'])
    ->where('nomorTiket', '[A-Za-z0-9\-_]+')
    ->middleware('throttle:60,1')
    ->name('public.track');

Route::get('/api/fakultas/{fakultasId}/prodi', [AdminFakultasProdiController::class, 'getProdiByFakultas'])
    ->name('api.fakultas.prodi');

Auth::routes();

// =========================================================================
// 2. RUTE PENGGUNA TERAUTENTIKASI (PEMOHON & UMUM)
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // PBI-01: Profil Akademik & Validasi Identitas KTM
    Route::get('/profil', [ProfileController::class, 'show'])->name('profil.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::get('/profil/prodi', [ProfileController::class, 'prodiByFakultas'])->name('profil.prodi');
    Route::get('/profil/identitas/preview', [ProfileController::class, 'previewIdentitas'])->name('profil.identitas.preview');

<<<<<<< HEAD
// Modul Pengajuan (PBI-07, PBI-08, PBI-09 - Giri Pulung Nugroho)
Route::middleware(['auth', 'not_suspended'])->group(function () {
=======
    // PBI-07, PBI-08, PBI-09, & PBI-14: Pengajuan, Resi E-Ticket, Timeline, & Unduh LHU
>>>>>>> origin/main
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
    Route::get('/pengajuan/{nomorTiket}/unduh-lhu', [HomeController::class, 'downloadHasil'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pemohon.pengujian.download');
});

// =========================================================================
// 3. RUTE OPERASIONAL LABORAN (PBI-01, PBI-06, PBI-10, PBI-13/14/15)
// =========================================================================
Route::prefix('laboran')->name('laboran.')->middleware(['auth', 'laboran'])->group(function () {
    // PBI-10: Validasi penerimaan fisik/digital & progres pengujian
    Route::get('/antrean', [LaboranAntreanController::class, 'index'])->name('antrean.index');
    Route::post('/antrean/{nomorTiket}/terima-fisik', [LaboranAntreanController::class, 'terimaFisik'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('antrean.terima-fisik');
    Route::post('/antrean/{nomorTiket}/verifikasi-digital', [LaboranAntreanController::class, 'verifikasiDigital'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('antrean.verifikasi-digital');
    Route::post('/antrean/{nomorTiket}/proses-mesin', [LaboranAntreanController::class, 'prosesMesin'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('antrean.proses-mesin');

    // PBI-13/14/15: Unggah & Revisi Dokumen Hasil Uji (LHU)
    Route::get('/pengujian', [LaboranHasilUjiController::class, 'index'])->name('pengujian.index');
    Route::post('/pengujian/{nomorTiket}/unggah-hasil', [LaboranHasilUjiController::class, 'upload'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengujian.upload');
    Route::get('/pengujian/{nomorTiket}/unduh-hasil', [LaboranHasilUjiController::class, 'download'])
        ->where('nomorTiket', '[A-Za-z0-9\-_]+')
        ->name('pengujian.download');

    // PBI-01: Validasi Identitas & KTM Pemohon
    Route::get('/validasi-ktm', [LaboranValidasiKtmController::class, 'index'])->name('validasi.ktm.index');
    Route::post('/validasi-ktm/{user}/setuju', [LaboranValidasiKtmController::class, 'approve'])->name('validasi.ktm.approve');
    Route::post('/validasi-ktm/{user}/tolak', [LaboranValidasiKtmController::class, 'reject'])->name('validasi.ktm.reject');
    Route::get('/validasi-ktm/{user}/preview', [LaboranValidasiKtmController::class, 'previewKtm'])->name('validasi.ktm.preview');

<<<<<<< HEAD
    // PBI-02 (SPK-2): Manajemen Pelanggaran & Penangguhan (Suspend) Pemohon oleh Laboran (Syalfa)
    Route::get('/suspend', [LaboranSuspendController::class, 'index'])->name('suspend.index');
    Route::post('/suspend/{user}', [LaboranSuspendController::class, 'suspend'])->name('suspend.store');
    Route::match(['post', 'patch'], '/suspend/{user}/unsuspend', [LaboranSuspendController::class, 'unsuspend'])->name('suspend.unsuspend');
=======
    // PBI-06: Kelola SOP Penyiapan Sampel
    Route::get('/sop', [LaboranSopController::class, 'index'])->name('sop.index');
    Route::get('/sop/{mesin}/edit', [LaboranSopController::class, 'edit'])->name('sop.edit');
    Route::put('/sop/{mesin}', [LaboranSopController::class, 'update'])->name('sop.update');
    Route::delete('/sop/{mesin}', [LaboranSopController::class, 'destroy'])->name('sop.destroy');
>>>>>>> origin/main
});

// =========================================================================
// 4. RUTE ADMINISTRATOR (PBI-03, PBI-04, PBI-16, & Manajemen Pengguna)
// =========================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // PBI-16: Dashboard Analitik Penggunaan Laboratorium
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pengguna Civitas Akademika
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/pengguna', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/pengguna/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // PBI-03: Master Data Fakultas & Program Studi
    Route::get('/master-fakultas-prodi', [AdminFakultasProdiController::class, 'index'])->name('master.fakultas-prodi.index');
    Route::post('/fakultas', [AdminFakultasProdiController::class, 'storeFakultas'])->name('fakultas.store');
    Route::put('/fakultas/{fakultas}', [AdminFakultasProdiController::class, 'updateFakultas'])->name('fakultas.update');
    Route::delete('/fakultas/{fakultas}', [AdminFakultasProdiController::class, 'destroyFakultas'])->name('fakultas.destroy');

    Route::post('/prodi', [AdminFakultasProdiController::class, 'storeProdi'])->name('prodi.store');
    Route::put('/prodi/{prodi}', [AdminFakultasProdiController::class, 'updateProdi'])->name('prodi.update');
    Route::delete('/prodi/{prodi}', [AdminFakultasProdiController::class, 'destroyProdi'])->name('prodi.destroy');

    // PBI-04: Katalog Mesin & Status Operasional
    Route::resource('mesin', AdminMesinLabController::class)->except(['show']);
    Route::patch('/mesin/{mesin}/toggle', [AdminMesinLabController::class, 'toggleStatus'])->name('mesin.toggle');
});
