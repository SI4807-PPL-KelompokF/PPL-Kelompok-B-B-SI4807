<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengajuanUjiRequest;
use App\Models\AuditTrail;
use App\Models\MesinLab;
use App\Models\TiketPengujian;
use App\Services\BarcodeGeneratorService;
use App\Services\ResiPdfService;
use Illuminate\Http\Request;
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

        $this->ensureCatalogSeeded();

        $mesinList = MesinLab::orderBy('laboratorium')->orderBy('nama_mesin')->get();

        $fakultasProdiMap = $this->getFakultasProdiMap();

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
                'nama_aktor' => $user->name,
                'aksi' => 'PENGAJUAN_SAMPEL_BARU',
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

        // Otorisasi: pemilik tiket, laboran, atau admin
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

    /**
     * Pastikan master data katalog mesin terisi bila tabel masih kosong.
     */
    private function ensureCatalogSeeded(): void
    {
        if (MesinLab::count() > 0) {
            return;
        }

        $data = [
            [
                'nama_mesin' => 'Spektrofotometer UV-Vis Terintegrasi',
                'kategori_layanan' => 'Material & Kimia Terapan',
                'laboratorium' => 'Lab Kimia Terpadu Telkom University',
                'status_mesin' => 'Aktif',
                'kuota_harian' => 6,
                'estimasi_sla_hari' => 2,
                'sop_penyiapan' => "1. Sampel cairan harus disaring menggunakan syringe filter 0.45 um sebelum diserahkan.\n2. Wadah vial kaca borosilikat transparan volume minimal 5 mL bertutup ulir kedap udara.\n3. Beri label kode sampel tahan air pada dinding vial (bukan pada tutup).\n4. Lampirkan lembar data pengujian atau perkiraan panjang gelombang absorban yang diharapkan.\n5. Sisa spesimen wajib diambil maksimal 7 (tujuh) hari kerja setelah pengujian selesai.",
            ],
            [
                'nama_mesin' => 'Chamber Anechoic & Spektrum RF Analyzer',
                'kategori_layanan' => 'Telekomunikasi, RF & IoT',
                'laboratorium' => 'Lab Telekomunikasi Terpadu Kampus',
                'status_mesin' => 'Aktif',
                'kuota_harian' => 4,
                'estimasi_sla_hari' => 3,
                'sop_penyiapan' => "1. Prototipe antena atau modul IoT telah dilengkapi konektor SMA/U.FL standar.\n2. Sertakan power supply / baterai cadangan dan spesifikasi frekuensi operasional (900 MHz - 6 GHz).\n3. Sampel bebas dari bahan magnetik yang tidak terlindung.\n4. Pengujian wajib didampingi pemohon pada sesi kalibrasi polarisasi bila diperlukan.",
            ],
            [
                'nama_mesin' => 'Microplate Reader & Sentrifugasi Berpendingin',
                'kategori_layanan' => 'Bioteknologi & Lingkungan',
                'laboratorium' => 'Lab Bioteknologi & Rekayasa Hayati',
                'status_mesin' => 'Aktif',
                'kuota_harian' => 5,
                'estimasi_sla_hari' => 3,
                'sop_penyiapan' => "1. Sampel biologis/enzimatik disimpan dalam ice box / thermo-cool bag saat penyerahan.\n2. Tabung mikro sentrifus 1.5 mL bebas kontaminasi dan diberi label nomor tiket.\n3. Jangan membawa spesimen infeksius pathogen level 3 tanpa izin khusus K3 Lab.",
            ],
            [
                'nama_mesin' => 'High-Performance Cluster GPU Simulasi CAE/FEA',
                'kategori_layanan' => 'Simulasi & Komputasi Riset',
                'laboratorium' => 'Lab Riset Komputasi Lanjut Kampus',
                'status_mesin' => 'Aktif',
                'kuota_harian' => 8,
                'estimasi_sla_hari' => 1,
                'sop_penyiapan' => "1. Siapkan dataset atau berkas mesh 3D / skrip simulasi pada Google Drive atau OneDrive kampus.\n2. Pastikan akses link terbuka untuk dibaca oleh laboran (laboran@telkomuniversity.ac.id).\n3. Sertakan file petunjuk instruksi parameter eksekusi (.txt / .md).\n4. Resi berfungsi sebagai E-Ticket digital tanpa perlu mencetak kertas.",
            ],
            [
                'nama_mesin' => 'Universal Testing Machine (UTM Mekanik 50 kN)',
                'kategori_layanan' => 'Material & Kimia Terapan',
                'laboratorium' => 'Lab Rekayasa Material & Mekanik',
                'status_mesin' => 'Maintenance',
                'kuota_harian' => 3,
                'estimasi_sla_hari' => 4,
                'sop_penyiapan' => "1. Dimensi spesimen uji tarik/tekan wajib memenuhi standar ASTM E8M atau JIS Z 2201.\n2. Permukaan spesimen rata dan bersih dari oli atau karat berlebih.\n3. Mesin saat ini sedang dalam jadwal kalibrasi sensor load-cell rutin.",
            ],
        ];

        foreach ($data as $row) {
            MesinLab::create($row);
        }
    }

    /**
     * Master data Fakultas dan Program Studi Telkom University.
     */
    private function getFakultasProdiMap(): array
    {
        return [
            'Fakultas Informatika (FIF)' => [
                'S1 Informatika',
                'S1 Sistem Informasi',
                'S1 Teknologi Informasi',
                'S1 Rekayasa Perangkat Lunak',
                'S1 Sains Data',
                'S2 Informatika',
            ],
            'Fakultas Teknik Elektro (FTE)' => [
                'S1 Teknik Telekomunikasi',
                'S1 Teknik Elektro',
                'S1 Teknik Biomedis',
                'S1 Teknik Komputer',
                'S2 Teknik Elektro',
            ],
            'Fakultas Rekayasa Industri (FRI)' => [
                'S1 Teknik Industri',
                'S1 Sistem Informasi',
                'S1 Teknik Logistik',
                'S2 Teknik Industri',
            ],
            'Fakultas Ekonomi dan Bisnis (FEB)' => [
                'S1 Manajemen',
                'S1 Akuntansi',
                'S2 Manajemen',
            ],
            'Fakultas Komunikasi dan Ilmu Sosial (FKS)' => [
                'S1 Ilmu Komunikasi',
                'S1 Hubungan Masyarakat',
                'S1 Administrasi Bisnis',
            ],
            'Fakultas Industri Kreatif (FIK)' => [
                'S1 Desain Komunikasi Visual',
                'S1 Desain Interior',
                'S1 Desain Produk',
                'S1 Seni Rupa',
            ],
        ];
    }
}
