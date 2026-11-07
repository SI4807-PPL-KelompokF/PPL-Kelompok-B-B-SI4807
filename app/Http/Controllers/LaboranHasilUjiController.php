<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadHasilUjiRequest;
use App\Models\AuditTrail;
use App\Models\TiketPengujian;
use App\Services\DokumenHasilUjiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class LaboranHasilUjiController extends Controller
{
    /** @var DokumenHasilUjiService */
    private $hasilUjiService;

    public function __construct(DokumenHasilUjiService $hasilUjiService)
    {
        $this->middleware(['auth', 'laboran']);
        $this->hasilUjiService = $hasilUjiService;
    }

    /**
     * Tampilkan antarmuka PBI-13 (UC-13: Unggah & Revisi Hasil Uji oleh Laboran).
     */
    public function index(Request $request)
    {
        $tiketList = collect();
        $auditTrails = collect();
        $selectedTiket = null;
        $metrics = [
            'sedang_diuji' => 4,
            'selesai' => 2,
            'total_revisi' => 1,
        ];

        try {
            $this->hasilUjiService->ensureInitialDataSeeded();

            if (Schema::hasTable('tiket_pengujian')) {
                $tiketList = TiketPengujian::with(['dokumenHasil', 'auditTrails'])
                    ->whereIn('status', [
                        TiketPengujian::STATUS_SEDANG_DIUJI,
                        TiketPengujian::STATUS_SELESAI,
                    ])
                    ->orderByRaw("CASE WHEN status = 'Sedang Diuji' THEN 0 ELSE 1 END")
                    ->orderBy('id', 'asc')
                    ->get();

                $selectedNomor = $request->query('tiket');
                $selectedTiket = $selectedNomor
                    ? $tiketList->firstWhere('nomor_tiket', $selectedNomor)
                    : null;

                $auditTrails = AuditTrail::with('tiketPengujian')
                    ->latest('id')
                    ->take(15)
                    ->get();

                $metrics = [
                    'sedang_diuji' => $tiketList->where('status', TiketPengujian::STATUS_SEDANG_DIUJI)->count(),
                    'selesai' => $tiketList->where('status', TiketPengujian::STATUS_SELESAI)->count(),
                    'total_revisi' => AuditTrail::where('aksi', AuditTrail::AKSI_REVISI)->count(),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback apabila database lokal belum dimigrasi
        }

        $ticketsPayload = $this->hasilUjiService->buildTicketsPayload($tiketList);
        $auditPayload = $this->hasilUjiService->buildAuditPayload($auditTrails);
        $initialSelected = $selectedTiket ? $selectedTiket->nomor_tiket : null;
        $initialDoc = $selectedTiket ? $selectedTiket->dokumenHasil : null;
        $initialIsRev = $initialDoc !== null;
        $testingCount = collect($ticketsPayload)->where('status', 'testing')->count();
        $doneCount = collect($ticketsPayload)->where('status', 'done')->count();
        $revCount = collect($auditPayload)->where('type', 'revisi')->count();

        return view('laboran.hasil-uji', compact(
            'tiketList',
            'selectedTiket',
            'auditTrails',
            'metrics',
            'ticketsPayload',
            'auditPayload',
            'initialSelected',
            'initialDoc',
            'initialIsRev',
            'testingCount',
            'doneCount',
            'revCount'
        ));
    }

    /**
     * Proses unggah perdana atau revisi dokumen hasil uji (PDF / Excel maks. 5 MB)
     * ke Private Storage terenkripsi (AES-256-CBC) secara transaksional dan catat ke Audit Trail.
     */
    public function upload(UploadHasilUjiRequest $request, string $nomorTiket)
    {
        $this->hasilUjiService->ensureInitialDataSeeded();

        $tiket = TiketPengujian::with('dokumenHasil')
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        try {
            $outcome = $this->hasilUjiService->storeOrRevise(
                $tiket,
                $request->file('berkas_hasil'),
                Auth::user(),
                $request->input('catatan_laboran'),
                $request->ip()
            );
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => collect($e->errors())->flatten()->first() ?: 'Validasi gagal.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()
                ->route('laboran.pengujian.index', ['tiket' => $tiket->nomor_tiket])
                ->withErrors($e->errors())
                ->withInput();
        }

        $isRevision = (bool) $outcome['is_revision'];
        $originalName = (string) $outcome['file_name'];
        $newVersion = (int) $outcome['version'];
        /** @var AuditTrail $audit */
        $audit = $outcome['audit'];

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'mode' => $isRevision ? 'revisi' : 'upload',
                'ticket' => $tiket->nomor_tiket,
                'file_name' => $originalName,
                'file_size' => (int) $outcome['file_size'],
                'version' => $newVersion,
                'actor' => (string) $outcome['actor_name'],
                'audit_id' => $audit->kode_audit,
                'download_url' => route('laboran.pengujian.download', $tiket->nomor_tiket),
            ]);
        }

        $flashMessage = $isRevision
            ? "Revisi dokumen hasil uji untuk tiket {$tiket->nomor_tiket} ({$originalName} · v{$newVersion}) berhasil disimpan."
            : "Dokumen hasil uji {$originalName} berhasil diunggah. Status tiket {$tiket->nomor_tiket} otomatis berubah menjadi Selesai.";

        return redirect()
            ->route('laboran.pengujian.index', ['tiket' => $tiket->nomor_tiket])
            ->with('status', $flashMessage)
            ->with('last_action_mode', $isRevision ? 'revisi' : 'unggah');
    }

    /**
     * Unduh & dekripsi dokumen hasil uji dari Private Storage (khusus Laboran).
     */
    public function download(string $nomorTiket)
    {
        $tiket = TiketPengujian::with('dokumenHasil')
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        return $this->hasilUjiService->downloadResponse($tiket);
    }

    public function ensureInitialDataSeeded(): void
    {
        $this->hasilUjiService->ensureInitialDataSeeded();
    }

    public static function privateStorageFullPath(string $relativePath): string
    {
        return DokumenHasilUjiService::privateStorageFullPath($relativePath);
    }

    public static function putPrivateFile(string $relativePath, string $contents): void
    {
        DokumenHasilUjiService::putPrivateFile($relativePath, $contents);
    }

    public static function existsPrivateFile(string $relativePath): bool
    {
        return DokumenHasilUjiService::existsPrivateFile($relativePath);
    }

    public static function getPrivateFile(string $relativePath): string
    {
        return DokumenHasilUjiService::getPrivateFile($relativePath);
    }

    public static function deletePrivateFile(string $relativePath): void
    {
        DokumenHasilUjiService::deletePrivateFile($relativePath);
    }
}
