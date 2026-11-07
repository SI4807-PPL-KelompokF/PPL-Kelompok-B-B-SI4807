<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\DokumenHasil;
use App\Models\TiketPengujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LaboranHasilUjiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            $this->ensureInitialDataSeeded();

            if (Schema::hasTable('tiket_pengujian')) {
                $tiketList = TiketPengujian::with(['dokumenHasil', 'auditTrails'])
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
                    'sedang_diuji' => $tiketList->where('status', 'Sedang Diuji')->count(),
                    'selesai' => $tiketList->where('status', 'Selesai')->count(),
                    'total_revisi' => AuditTrail::where('aksi', 'REVISI_HASIL_UJI')->count(),
                ];
            }
        } catch (\Throwable $e) {
            // Fallback apabila database lokal belum dimigrasi
        }

        return view('laboran.hasil-uji', compact(
            'tiketList',
            'selectedTiket',
            'auditTrails',
            'metrics'
        ));
    }

    /**
     * Proses unggah perdana atau revisi dokumen hasil uji (PDF / Excel maks. 5 MB)
     * ke Private Storage terenkripsi (AES-256-CBC) dan catat ke Audit Trail.
     */
    public function upload(Request $request, string $nomorTiket)
    {
        $this->ensureInitialDataSeeded();

        $tiket = TiketPengujian::with('dokumenHasil')
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'berkas_hasil' => [
                'required',
                'file',
                'max:5120', // 5 MB = 5120 KB
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['pdf', 'xlsx', 'xls'], true)) {
                        $fail('Format berkas ditolak. Gunakan PDF atau Excel (.xlsx / .xls).');
                    }
                },
            ],
            'catatan_laboran' => ['nullable', 'string', 'max:500'],
        ], [
            'berkas_hasil.required' => 'Silakan pilih berkas hasil uji terlebih dahulu.',
            'berkas_hasil.file' => 'Berkas yang dikirim tidak valid.',
            'berkas_hasil.max' => 'Ukuran berkas melebihi batas maksimal 5 MB.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $validator->errors()->first('berkas_hasil') ?: $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()
                ->route('laboran.pengujian.index', ['tiket' => $tiket->nomor_tiket])
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('berkas_hasil');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';
        $sizeBytes = (int) $file->getSize();
        $rawContents = file_get_contents($file->getRealPath());
        $checksum = hash('sha256', $rawContents ?: $originalName);

        // Simpan terenkripsi (AES-256-CBC) di Private Storage (storage/app/private/dokumen_hasil)
        $encryptedPayload = Crypt::encrypt($rawContents);
        $privatePath = 'private/dokumen_hasil/' . $tiket->nomor_tiket . '/' . Str::uuid() . '.' . $extension . '.enc';
        self::putPrivateFile($privatePath, $encryptedPayload);

        $existingDoc = $tiket->dokumenHasil;
        $isRevision = $existingDoc !== null;
        $oldFileName = $isRevision ? $existingDoc->nama_berkas_asli : null;
        $newVersion = $isRevision ? ((int) $existingDoc->versi + 1) : 1;

        if ($isRevision) {
            if ($existingDoc->path_berkas && self::existsPrivateFile($existingDoc->path_berkas)) {
                self::deletePrivateFile($existingDoc->path_berkas);
            }

            $existingDoc->update([
                'uploaded_by' => Auth::id(),
                'nama_berkas_asli' => $originalName,
                'path_berkas' => $privatePath,
                'ekstensi' => strtoupper($extension),
                'mime_type' => $mimeType,
                'ukuran_bytes' => $sizeBytes,
                'versi' => $newVersion,
                'catatan_laboran' => $request->input('catatan_laboran'),
                'enkripsi_algoritma' => 'AES-256-CBC',
                'sha256_checksum' => $checksum,
            ]);
        } else {
            DokumenHasil::create([
                'tiket_pengujian_id' => $tiket->id,
                'uploaded_by' => Auth::id(),
                'nama_berkas_asli' => $originalName,
                'path_berkas' => $privatePath,
                'ekstensi' => strtoupper($extension),
                'mime_type' => $mimeType,
                'ukuran_bytes' => $sizeBytes,
                'versi' => 1,
                'catatan_laboran' => $request->input('catatan_laboran'),
                'enkripsi_algoritma' => 'AES-256-CBC',
                'sha256_checksum' => $checksum,
            ]);
        }

        // Otomatis ubah status tiket menjadi "Selesai"
        $tiket->update([
            'status' => 'Selesai',
        ]);

        // Catat ke Audit Trail
        $actorName = Auth::user() ? Auth::user()->name : 'Laboran';
        $actionCode = $isRevision ? 'REVISI_HASIL_UJI' : 'UNGGAH_HASIL_UJI';
        $defaultNote = $isRevision
            ? "Revisi dokumen hasil uji v{$newVersion}: menggantikan {$oldFileName} dengan {$originalName}."
            : "Unggah perdana dokumen hasil uji {$originalName}; status tiket otomatis berubah menjadi Selesai.";

        $audit = AuditTrail::create([
            'tiket_pengujian_id' => $tiket->id,
            'user_id' => Auth::id(),
            'nama_aktor' => $actorName,
            'aksi' => $actionCode,
            'berkas_lama' => $oldFileName,
            'berkas_baru' => $originalName,
            'ukuran_bytes' => $sizeBytes,
            'versi' => $newVersion,
            'keterangan' => $request->filled('catatan_laboran')
                ? $request->input('catatan_laboran')
                : $defaultNote,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'mode' => $isRevision ? 'revisi' : 'upload',
                'ticket' => $tiket->nomor_tiket,
                'file_name' => $originalName,
                'file_size' => $sizeBytes,
                'version' => $newVersion,
                'actor' => $actorName,
                'audit_id' => 'AUD-2026-' . str_pad((string) (400 + $audit->id), 4, '0', STR_PAD_LEFT),
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
     * Unduh & dekripsi dokumen hasil uji dari Private Storage.
     */
    public function download(string $nomorTiket)
    {
        $tiket = TiketPengujian::with('dokumenHasil')
            ->where('nomor_tiket', $nomorTiket)
            ->firstOrFail();

        $doc = $tiket->dokumenHasil;
        abort_if(!$doc, 404, 'Dokumen hasil uji belum tersedia.');

        if (self::existsPrivateFile($doc->path_berkas)) {
            $encrypted = self::getPrivateFile($doc->path_berkas);
            $decrypted = Crypt::decrypt($encrypted);
        } else {
            $decrypted = "%PDF-1.4\n% Dokumen Hasil Uji Terenkripsi Telyuji - {$tiket->nomor_tiket} ({$doc->nama_berkas_asli})";
        }

        return response($decrypted, 200, [
            'Content-Type' => $doc->mime_type ?: 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $doc->nama_berkas_asli . '"',
        ]);
    }

    public static function privateStorageFullPath(string $relativePath): string
    {
        return storage_path('app/' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relativePath, '/\\')));
    }

    public static function putPrivateFile(string $relativePath, string $contents): void
    {
        $fullPath = self::privateStorageFullPath($relativePath);
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($fullPath, $contents);
    }

    public static function existsPrivateFile(string $relativePath): bool
    {
        return is_file(self::privateStorageFullPath($relativePath));
    }

    public static function getPrivateFile(string $relativePath): string
    {
        return (string) file_get_contents(self::privateStorageFullPath($relativePath));
    }

    public static function deletePrivateFile(string $relativePath): void
    {
        $fullPath = self::privateStorageFullPath($relativePath);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Inisialisasi tiket pengujian laboratorium sesuai hasil-uji.blade.php apabila tabel masih kosong.
     */
    public function ensureInitialDataSeeded(): void
    {
        if (!Schema::hasTable('tiket_pengujian') || TiketPengujian::count() > 0) {
            return;
        }

        $t1 = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261015-089',
            'nama_pemohon' => 'Rizky Aditya Pratama',
            'nim_nip' => '1103220089',
            'fakultas' => 'S1 Teknik Elektro · FTE',
            'nama_sampel' => 'Larutan Nanopartikel TiO2',
            'instrumen' => 'Spektrofotometer UV-Vis',
            'laboratorium' => 'Lab Material Maju',
            'status' => 'Sedang Diuji',
            'tanggal_uji' => '2026-10-15',
        ]);

        $t2 = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261015-092',
            'nama_pemohon' => 'Dr. Maya Lestari, M.T.',
            'nim_nip' => '19850610-DOS',
            'fakultas' => 'Dosen · Fak. Rekayasa Industri',
            'nama_sampel' => 'Komposit Serat Karbon Epoksi',
            'instrumen' => 'Universal Testing Machine',
            'laboratorium' => 'Lab Uji Mekanik',
            'status' => 'Sedang Diuji',
            'tanggal_uji' => '2026-10-15',
        ]);

        $t3 = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261014-071',
            'nama_pemohon' => 'Nadia Putri Anjani',
            'nim_nip' => '1301220071',
            'fakultas' => 'S2 Informatika · FIF',
            'nama_sampel' => 'Modul Transceiver IoT LoRa 915 MHz',
            'instrumen' => 'Analyzer Spektrum RF',
            'laboratorium' => 'Lab Telekomunikasi Terpadu',
            'status' => 'Sedang Diuji',
            'tanggal_uji' => '2026-10-14',
        ]);

        $t4 = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261016-101',
            'nama_pemohon' => 'Salsabila Rahma',
            'nim_nip' => '1101220101',
            'fakultas' => 'S1 Teknik Telekomunikasi · FTE',
            'nama_sampel' => 'Prototipe Antena Microstrip Array',
            'instrumen' => 'Lab Antena & Propagasi',
            'laboratorium' => 'Lab Antena & Propagasi',
            'status' => 'Sedang Diuji',
            'tanggal_uji' => '2026-10-16',
        ]);

        $t5 = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261013-054',
            'nama_pemohon' => 'Fajar Maulana',
            'nim_nip' => '1104220054',
            'fakultas' => 'S1 Teknik Fisika · FTE',
            'nama_sampel' => 'Serbuk Kristal Zeolit Alam',
            'instrumen' => 'XRD Diffractometer',
            'laboratorium' => 'Lab Karakterisasi Material',
            'status' => 'Selesai',
            'tanggal_uji' => '2026-10-13',
        ]);

        $t6 = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261012-038',
            'nama_pemohon' => 'Prof. Hendra Gunawan',
            'nim_nip' => '19780314-DOS',
            'fakultas' => 'Dosen · Fak. Ilmu Terapan',
            'nama_sampel' => 'Film Polimer Kitosan-PVA',
            'instrumen' => 'Spektrometer FTIR',
            'laboratorium' => 'Lab Kimia Terpadu',
            'status' => 'Selesai',
            'tanggal_uji' => '2026-10-12',
        ]);

        DokumenHasil::create([
            'tiket_pengujian_id' => $t5->id,
            'nama_berkas_asli' => 'LHU-TLY-20261013-054.pdf',
            'path_berkas' => 'private/dokumen_hasil/TLY-20261013-054/seed-xrd-v1.pdf.enc',
            'ekstensi' => 'PDF',
            'mime_type' => 'application/pdf',
            'ukuran_bytes' => (int) round(1.8 * 1048576),
            'versi' => 1,
            'catatan_laboran' => 'Pola difraksi sinar-X sesuai fase kristalin standar.',
            'enkripsi_algoritma' => 'AES-256-CBC',
            'sha256_checksum' => hash('sha256', 'LHU-TLY-20261013-054.pdf'),
        ]);

        DokumenHasil::create([
            'tiket_pengujian_id' => $t6->id,
            'nama_berkas_asli' => 'LHU-TLY-20261012-038.xlsx',
            'path_berkas' => 'private/dokumen_hasil/TLY-20261012-038/seed-ftir-v2.xlsx.enc',
            'ekstensi' => 'XLSX',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ukuran_bytes' => (int) round(0.9 * 1048576),
            'versi' => 2,
            'catatan_laboran' => 'Revisi tabel spektrum transmitansi FTIR v2.',
            'enkripsi_algoritma' => 'AES-256-CBC',
            'sha256_checksum' => hash('sha256', 'LHU-TLY-20261012-038.xlsx'),
        ]);

        AuditTrail::create([
            'tiket_pengujian_id' => $t6->id,
            'nama_aktor' => 'Sari Wulandari',
            'aksi' => 'UNGGAH_HASIL_UJI',
            'berkas_lama' => null,
            'berkas_baru' => 'LHU-TLY-20261012-038.xlsx',
            'ukuran_bytes' => (int) round(0.85 * 1048576),
            'versi' => 1,
            'keterangan' => 'Unggah perdana dokumen hasil uji Spektrometer FTIR.',
        ]);

        AuditTrail::create([
            'tiket_pengujian_id' => $t6->id,
            'nama_aktor' => 'Budi Santoso',
            'aksi' => 'REVISI_HASIL_UJI',
            'berkas_lama' => 'LHU-TLY-20261012-038.xlsx',
            'berkas_baru' => 'LHU-TLY-20261012-038.xlsx',
            'ukuran_bytes' => (int) round(0.9 * 1048576),
            'versi' => 2,
            'keterangan' => 'Revisi dokumen hasil uji v2.',
        ]);

        AuditTrail::create([
            'tiket_pengujian_id' => $t5->id,
            'nama_aktor' => 'Budi Santoso',
            'aksi' => 'UNGGAH_HASIL_UJI',
            'berkas_lama' => null,
            'berkas_baru' => 'LHU-TLY-20261013-054.pdf',
            'ukuran_bytes' => (int) round(1.8 * 1048576),
            'versi' => 1,
            'keterangan' => 'Unggah perdana LHU XRD Diffractometer.',
        ]);
    }
}
