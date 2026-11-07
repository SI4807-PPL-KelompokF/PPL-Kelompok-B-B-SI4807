<?php

namespace App\Http\Controllers;

use App\Models\TiketPengujian;
use App\Services\LandingContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    /** @var LandingContentService */
    private $contentService;

    public function __construct(LandingContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Tampilkan halaman beranda utama Telyuji.
     */
    public function index()
    {
        return view('welcome', [
            'heroSlides' => $this->contentService->heroSlides(),
            'flowSteps' => $this->contentService->flowSteps(),
            'catalogInstruments' => $this->contentService->catalogInstruments(),
            'faqs' => $this->contentService->faqs(),
            'ribbonInstruments' => $this->contentService->ribbonInstruments(),
        ]);
    }

    /**
     * Endpoint pelacakan status sampel publik berdasarkan nomor resi / tiket.
     * Mengembalikan ringkasan progres tanpa mengekspos path penyimpanan privat.
     */
    public function track(string $nomorTiket): JsonResponse
    {
        $cleanCode = strtoupper(trim($nomorTiket));

        try {
            if (class_exists(TiketPengujian::class) && Schema::hasTable('tiket_pengujian')) {
                $tiket = TiketPengujian::with(['dokumenHasil', 'auditTrails'])
                    ->whereRaw('UPPER(nomor_tiket) = ?', [$cleanCode])
                    ->first();

                if ($tiket) {
                    return response()->json($tiket->toPublicTrackingPayload());
                }
            }
        } catch (\Throwable $e) {
            // Fallback aman apabila koneksi DB belum siap
        }

        return response()->json([
            'found' => false,
            'id' => $cleanCode,
        ], 404);
    }
}
