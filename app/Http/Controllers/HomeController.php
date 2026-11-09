<?php

namespace App\Http\Controllers;

use App\Models\TiketPengujian;
use App\Services\DokumenHasilUjiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilkan halaman kerja sesuai peran pengguna (Admin / Laboran / Pemohon).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return app(AdminUserController::class)->index();
        }

        if ($user && $user->isLaboran()) {
            return app(LaboranHasilUjiController::class)->index($request);
        }

        $tiketSaya = collect();
        $metrics = [
            'total' => 0,
            'menunggu' => 0,
            'sedang_diuji' => 0,
            'selesai' => 0,
        ];

        try {
            if ($user && $user->id && Schema::hasTable('tiket_pengujian')) {
                $tiketSaya = TiketPengujian::with('dokumenHasil')
                    ->where('user_id', $user->id)
                    ->orderByDesc('tanggal_uji')
                    ->orderByDesc('id')
                    ->get();

                $metrics = [
                    'total' => $tiketSaya->count(),
                    'menunggu' => $tiketSaya->where('status', TiketPengujian::STATUS_MENUNGGU)->count(),
                    'sedang_diuji' => $tiketSaya->where('status', TiketPengujian::STATUS_SEDANG_DIUJI)->count(),
                    'selesai' => $tiketSaya->where('status', TiketPengujian::STATUS_SELESAI)->count(),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback apabila tabel belum dimigrasi
        }

        return view('home', compact('tiketSaya', 'metrics'));
    }

    /**
     * Unduh Laporan Hasil Uji (LHU) terenkripsi untuk tiket milik pemohon yang bersangkutan.
     */
    public function downloadHasil(string $nomorTiket, DokumenHasilUjiService $hasilUjiService)
    {
        $tiket = TiketPengujian::with('dokumenHasil')
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        abort_unless($tiket->canBeDownloadedBy(Auth::user()), 403, 'Anda tidak memiliki izin untuk mengunduh dokumen hasil uji ini.');
        abort_unless($tiket->isCompleted() && $tiket->dokumenHasil !== null, 404, 'Laporan Hasil Uji belum diterbitkan.');

        return $hasilUjiService->downloadResponse($tiket);
    }
}
