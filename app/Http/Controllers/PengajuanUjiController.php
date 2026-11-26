<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengajuanUjiRequest;
use App\Models\AuditTrail;
use App\Models\Fakultas;
use App\Models\MesinLab;
use App\Models\TiketPengujian;
use App\Services\BarcodeGeneratorService;
use App\Services\ResiPdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengajuanUjiController extends Controller
{
    /** @var ResiPdfService */
    private $resiPdfService;

    /** @var BarcodeGeneratorService */
    private $barcodeService;

    public function __construct(ResiPdfService $resiPdfService, BarcodeGeneratorService $barcodeService)
    {
        $this->middleware('auth');
        $this->resiPdfService = $resiPdfService;
        $this->barcodeService = $barcodeService;
    }

    /**
     * Tampilkan formulir dinamis pengajuan uji lab (PBI-07 / UC-07).
     */
    public function create()
    {
        $user = Auth::user();

        // Pemeriksaan sanksi akun suspended (UC-03 / UC-07 Alur Alternatif)
        if (isset($user->is_suspended) && $user->is_suspended) {
            return redirect()->route('home')->withErrors([
                'suspended' => 'Akun Anda saat ini ditangguhkan (suspended) oleh Laboran: ' . ($user->alasan_suspend ?: 'Pelanggaran SOP Laboratorium.') . ' Silakan hubungi koordinator laboratorium kampus.',
            ]);
        }

        MesinLab::seedDefaultData();

        $mesinList = MesinLab::orderBy('laboratorium')->orderBy('nama_mesin')->get();
        $fakultasProdiMap = Fakultas::getFakultasProdiMap();

        return view('pengajuan.create', [
            'mesinList' => $mesinList,
            'fakultasProdiMap' => $fakultasProdiMap,
            'user' => $user,
        ]);
    }

    /**
     * Simpan pengajuan uji baru, buat Resi PDF ber-barcode otomatis, dan catat ke antrean (PBI-07 & PBI-08).
     */
    public function store(PengajuanUjiRequest $request)
    {
        $user = Auth::user();
        $mesin = MesinLab::findOrFail($request->input('mesin_lab_id'));

        $nomorTiket = TiketPengujian::generateNomorTiket();
        $barcodeToken = TiketPengujian::generateBarcodeToken();

        $tiket = DB::transaction(function () use ($request, $user, $mesin, $nomorTiket, $barcodeToken) {
            $t = TiketPengujian::create([
                'nomor_tiket' => $nomorTiket,
                'user_id' => $user->id,
                'nama_pemohon' => $request->input('nama_pemohon'),
                'nim_nip' => $request->input('nim_nip'),
                'fakultas' => $request->input('fakultas'),
                'prodi' => $request->input('prodi'),
                'mesin_lab_id' => $mesin->id,
                'nama_sampel' => $request->input('nama_sampel'),
                'deskripsi_sampel' => $request->input('deskripsi_sampel'),
                'tujuan_pengujian' => $request->input('tujuan_pengujian'),
                'jenis_sampel' => $request->input('jenis_sampel'),
                'tautan_file_cloud' => $request->input('tautan_file_cloud'),
                'instrumen' => $mesin->nama_mesin,
                'laboratorium' => $mesin->laboratorium,
                'status' => TiketPengujian::STATUS_MENUNGGU,
                'tanggal_uji' => $request->input('tanggal_uji'),
                'barcode_token' => $barcodeToken,
                'submitted_at' => now(),
            ]);

            // Catat log Audit Trail awal
            AuditTrail::create([
                'tiket_pengujian_id' => $t->id,
                'user_id' => $user->id,
                'nama_aktor' => $user->displayName(),
                'aksi' => AuditTrail::AKSI_PENGAJUAN,
                'berkas_lama' => null,
                'berkas_baru' => "Resi-{$nomorTiket}.pdf",
                'ukuran_bytes' => 1024,
                'versi' => 1,
                'keterangan' => "Pengajuan sampel '{$t->nama_sampel}' terdaftar pada instrumen {$mesin->nama_mesin}. Resi E-Ticket diterbitkan.",
                'ip_address' => $request->ip(),
            ]);

            return $t;
        });

        // Simpan file fisik Resi PDF ke private storage
        try {
            $pdfContent = $this->resiPdfService->generate($tiket);
            $fileName = "resi_{$tiket->nomor_tiket}.pdf";
            $storagePath = "pengajuan_resi/{$fileName}";
            Storage::disk('local')->put($storagePath, $pdfContent);
            $tiket->update(['barcode_path' => $storagePath]);
        } catch (\Throwable $e) {
            // PDF dapat di-generate ulang on the fly bila ada kendala disk
        }

        $flashMsg = $tiket->jenis_sampel === 'digital'
            ? "Pengajuan sampel digital berhasil disimpan dengan tiket {$tiket->nomor_tiket}. Resi E-Ticket telah terbit."
            : "Pengajuan pengujian sampel berhasil disimpan dengan nomor tiket {$tiket->nomor_tiket}. Silakan unduh dan cetak Resi Barcode berikut untuk ditempelkan pada sampel fisik Anda.";

        return redirect()
            ->route('pengajuan.show', $tiket->nomor_tiket)
            ->with('status', $flashMsg)
            ->with('baru_dibuat', true);
    }

    /**
     * Tampilkan detail pelacakan Visual Timeline Tracking (PBI-09 / UC-09).
     */
    public function show(string $nomorTiket)
    {
        $tiket = TiketPengujian::with(['mesinLab', 'dokumenHasil', 'auditTrails'])
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $user = Auth::user();

        if (!$user->isLaboran() && !$user->isAdmin() && (int) $tiket->user_id !== (int) $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pengajuan ini.');
        }

        $timeline = $tiket->getTimelineData();
        $barcodeSvg = $this->barcodeService->getSvg($tiket->nomor_tiket, 280, 56, true);

        return view('pengajuan.show', [
            'tiket' => $tiket,
            'timeline' => $timeline,
            'barcodeSvg' => $barcodeSvg,
        ]);
    }

    /**
     * Unduh berkas Resi PDF resmi ber-barcode (PBI-08 / UC-08).
     */
    public function downloadResi(string $nomorTiket)
    {
        $tiket = TiketPengujian::where('nomor_tiket', $nomorTiket)->firstOrFail();
        $user = Auth::user();

        if (!$user->isLaboran() && !$user->isAdmin() && (int) $tiket->user_id !== (int) $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $pdfBinary = $this->resiPdfService->generate($tiket);
        $fileName = "Resi-Telyuji-{$tiket->nomor_tiket}.pdf";

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Pratinjau langsung Resi PDF di peramban (inline view).
     */
    public function previewResi(string $nomorTiket)
    {
        $tiket = TiketPengujian::where('nomor_tiket', $nomorTiket)->firstOrFail();
        $user = Auth::user();

        if (!$user->isLaboran() && !$user->isAdmin() && (int) $tiket->user_id !== (int) $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $pdfBinary = $this->resiPdfService->generate($tiket);

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Resi-Telyuji-' . $tiket->nomor_tiket . '.pdf"',
        ]);
    }

    /**
     * Cetak Resi via halaman ramah printer (window.print).
     */
    public function printResi(string $nomorTiket)
    {
        $tiket = TiketPengujian::where('nomor_tiket', $nomorTiket)->firstOrFail();
        $user = Auth::user();

        if (!$user->isLaboran() && !$user->isAdmin() && (int) $tiket->user_id !== (int) $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $barcodeSvg = $this->barcodeService->getSvg($tiket->nomor_tiket, 320, 68, true);

        return view('pengajuan.print-resi', [
            'tiket' => $tiket,
            'barcodeSvg' => $barcodeSvg,
        ]);
    }
}
