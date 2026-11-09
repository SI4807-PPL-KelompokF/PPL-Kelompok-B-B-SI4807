<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\DokumenHasil;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DokumenHasilUjiService
{
    public const MIME_MAP = [
        'pdf' => 'application/pdf',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls' => 'application/vnd.ms-excel',
    ];

    /**
     * Bersihkan nama berkas asli dari karakter kontrol atau upaya path traversal.
     */
    public function sanitizeFileName(string $originalName, string $extension): string
    {
        $base = basename(str_replace('\\', '/', trim($originalName)));
        $clean = preg_replace('/[\x00-\x1F\x7F"\\\\\/;<>|:*?]+/', '_', $base) ?: ('LHU.' . $extension);
        $clean = trim($clean, '. ');

        if ($clean === '' || strtolower((string) pathinfo($clean, PATHINFO_EXTENSION)) !== $extension) {
            $stem = pathinfo($clean, PATHINFO_FILENAME) ?: 'Dokumen_Hasil_Uji';
            $clean = $stem . '.' . $extension;
        }

        if (strlen($clean) > 180) {
            $stem = substr((string) pathinfo($clean, PATHINFO_FILENAME), 0, 165);
            $clean = $stem . '.' . $extension;
        }

        return $clean;
    }

    /**
     * Simpan unggahan perdana atau revisi dokumen hasil uji secara atomik:
     * 1) Validasi prasyarat status tiket ("Sedang Diuji" atau "Selesai")
     * 2) Enkripsi berkas (AES-256-CBC) ke Private Storage + hitung SHA-256
     * 3) Transaksi DB (1:1 DokumenHasil + status tiket Selesai + AuditTrail)
     * 4) Hapus berkas lama hanya setelah commit DB berhasil; rollback file baru jika DB gagal
     */
    public function storeOrRevise(
        TiketPengujian $tiket,
        UploadedFile $file,
        ?User $actor,
        ?string $catatan,
        ?string $ipAddress = null
    ): array {
        if (!$tiket->canUploadHasil()) {
            throw ValidationException::withMessages([
                'berkas_hasil' => 'Dokumen hasil uji hanya dapat diunggah untuk tiket berstatus Sedang Diuji atau Selesai.',
            ]);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $originalName = $this->sanitizeFileName($file->getClientOriginalName(), $extension);
        $mimeType = self::MIME_MAP[$extension] ?? ($file->getClientMimeType() ?: 'application/octet-stream');
        $sizeBytes = (int) $file->getSize();
        $realPath = $file->getRealPath();
        $rawContents = $realPath && is_file($realPath) ? (string) file_get_contents($realPath) : '';
        $checksum = hash('sha256', $rawContents);

        $encryptedPayload = Crypt::encrypt($rawContents);
        $privatePath = 'private/dokumen_hasil/' . $tiket->nomor_tiket . '/' . Str::uuid() . '.' . $extension . '.enc';
        self::putPrivateFile($privatePath, $encryptedPayload);

        $oldPrivatePath = null;
        $result = null;

        try {
            $result = DB::transaction(function () use (
                $tiket,
                $actor,
                $catatan,
                $ipAddress,
                $extension,
                $originalName,
                $mimeType,
                $sizeBytes,
                $checksum,
                $privatePath,
                &$oldPrivatePath
            ) {
                /** @var TiketPengujian $lockedTiket */
                $lockedTiket = TiketPengujian::with('dokumenHasil')
                    ->whereKey($tiket->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$lockedTiket->canUploadHasil()) {
                    throw ValidationException::withMessages([
                        'berkas_hasil' => 'Status tiket berubah dan tidak memenuhi syarat unggah hasil uji.',
                    ]);
                }

                $existingDoc = $lockedTiket->dokumenHasil;
                $isRevision = $existingDoc !== null;
                $oldPrivatePath = $isRevision ? $existingDoc->path_berkas : null;
                $oldFileName = $isRevision ? $existingDoc->nama_berkas_asli : null;
                $newVersion = $isRevision ? ((int) $existingDoc->versi + 1) : 1;
                $trimmedNote = $catatan !== null && trim($catatan) !== '' ? trim($catatan) : null;

                $attributes = [
                    'uploaded_by' => $actor ? $actor->id : null,
                    'nama_berkas_asli' => $originalName,
                    'path_berkas' => $privatePath,
                    'ekstensi' => strtoupper($extension),
                    'mime_type' => $mimeType,
                    'ukuran_bytes' => $sizeBytes,
                    'versi' => $newVersion,
                    'catatan_laboran' => $trimmedNote,
                    'enkripsi_algoritma' => DokumenHasil::ENKRIPSI_DEFAULT,
                    'sha256_checksum' => $checksum,
                ];

                if ($isRevision) {
                    $existingDoc->update($attributes);
                    $dokumen = $existingDoc->fresh();
                } else {
                    $dokumen = DokumenHasil::create(array_merge([
                        'tiket_pengujian_id' => $lockedTiket->id,
                    ], $attributes));
                }

                $lockedTiket->update([
                    'status' => TiketPengujian::STATUS_SELESAI,
                ]);

                $actorName = $actor ? $actor->name : 'Laboran';
                $actionCode = $isRevision ? AuditTrail::AKSI_REVISI : AuditTrail::AKSI_UNGGAH;
                $defaultNote = $isRevision
                    ? "Revisi dokumen hasil uji v{$newVersion}: menggantikan {$oldFileName} dengan {$originalName}."
                    : "Unggah perdana dokumen hasil uji {$originalName}; status tiket otomatis berubah menjadi Selesai.";

                $audit = AuditTrail::create([
                    'tiket_pengujian_id' => $lockedTiket->id,
                    'user_id' => $actor ? $actor->id : null,
                    'nama_aktor' => $actorName,
                    'aksi' => $actionCode,
                    'berkas_lama' => $oldFileName,
                    'berkas_baru' => $originalName,
                    'ukuran_bytes' => $sizeBytes,
                    'versi' => $newVersion,
                    'keterangan' => $trimmedNote ?? $defaultNote,
                    'ip_address' => $ipAddress,
                ]);

                return [
                    'tiket' => $lockedTiket,
                    'dokumen' => $dokumen,
                    'audit' => $audit,
                    'is_revision' => $isRevision,
                    'actor_name' => $actorName,
                    'file_name' => $originalName,
                    'file_size' => $sizeBytes,
                    'version' => $newVersion,
                ];
            });
        } catch (\Throwable $e) {
            self::deletePrivateFile($privatePath);
            throw $e;
        }

        // Hapus berkas terenkripsi lama hanya setelah transaksi DB sukses
        if ($oldPrivatePath && $oldPrivatePath !== $privatePath && self::existsPrivateFile($oldPrivatePath)) {
            self::deletePrivateFile($oldPrivatePath);
        }

        return $result;
    }

    /**
     * Dekripsi berkas hasil uji dari Private Storage dan verifikasi integritas SHA-256.
     */
    public function decryptDocument(TiketPengujian $tiket): array
    {
        $doc = $tiket->dokumenHasil;
        abort_if(!$doc, 404, 'Dokumen hasil uji belum tersedia.');

        if (self::existsPrivateFile($doc->path_berkas)) {
            $encrypted = self::getPrivateFile($doc->path_berkas);

            try {
                $decrypted = (string) Crypt::decrypt($encrypted);
            } catch (DecryptException $e) {
                abort(500, 'Gagal mendekripsi dokumen hasil uji dari penyimpanan privat.');
            }

            if ($doc->sha256_checksum && !hash_equals($doc->sha256_checksum, hash('sha256', $decrypted))) {
                abort(500, 'Verifikasi integritas berkas hasil uji (SHA-256) tidak cocok.');
            }
        } else {
            // Fallback untuk data awal hasil seeder apabila berkas fisik belum dibuat
            $decrypted = "%PDF-1.4\n% Dokumen Hasil Uji Terenkripsi Telyuji - {$tiket->nomor_tiket} ({$doc->nama_berkas_asli})";
        }

        $safeDownloadName = str_replace(['"', "\r", "\n", '\\', '/'], '_', $doc->nama_berkas_asli);

        return [
            'contents' => $decrypted,
            'mime_type' => $doc->mime_type ?: 'application/pdf',
            'file_name' => $safeDownloadName,
        ];
    }

    /**
     * Bangun HTTP response unduhan berkas hasil uji terdekripsi dengan header keamanan penuh.
     */
    public function downloadResponse(TiketPengujian $tiket): Response
    {
        $payload = $this->decryptDocument($tiket);

        return response($payload['contents'], 200, [
            'Content-Type' => $payload['mime_type'],
            'Content-Disposition' => 'attachment; filename="' . $payload['file_name'] . '"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Format daftar antrean tiket untuk antarmuka kerja Laboran (dengan fallback jika DB belum siap).
     */
    public function buildTicketsPayload(?Collection $tiketList): array
    {
        if ($tiketList && $tiketList->isNotEmpty()) {
            return $tiketList->map(function (TiketPengujian $tiket) {
                return $tiket->toQueuePayload();
            })->values()->all();
        }

        return [
            [
                'id' => 'TLY-20261015-089',
                'name' => 'Rizky Aditya Pratama',
                'prodi' => 'S1 Teknik Elektro · FTE',
                'machine' => 'Spektrofotometer UV-Vis',
                'status' => 'testing',
                'upload_url' => route('laboran.pengujian.upload', 'TLY-20261015-089'),
                'download_url' => route('laboran.pengujian.download', 'TLY-20261015-089'),
                'file' => null,
            ],
            [
                'id' => 'TLY-20261015-092',
                'name' => 'Dr. Maya Lestari, M.T.',
                'prodi' => 'Dosen · Fak. Rekayasa Industri',
                'machine' => 'Universal Testing Machine',
                'status' => 'testing',
                'upload_url' => route('laboran.pengujian.upload', 'TLY-20261015-092'),
                'download_url' => route('laboran.pengujian.download', 'TLY-20261015-092'),
                'file' => null,
            ],
            [
                'id' => 'TLY-20261014-071',
                'name' => 'Nadia Putri Anjani',
                'prodi' => 'S2 Informatika · FIF',
                'machine' => 'Analyzer Spektrum RF',
                'status' => 'testing',
                'upload_url' => route('laboran.pengujian.upload', 'TLY-20261014-071'),
                'download_url' => route('laboran.pengujian.download', 'TLY-20261014-071'),
                'file' => null,
            ],
            [
                'id' => 'TLY-20261016-101',
                'name' => 'Salsabila Rahma',
                'prodi' => 'S1 Teknik Telekomunikasi · FTE',
                'machine' => 'Lab Antena & Propagasi',
                'status' => 'testing',
                'upload_url' => route('laboran.pengujian.upload', 'TLY-20261016-101'),
                'download_url' => route('laboran.pengujian.download', 'TLY-20261016-101'),
                'file' => null,
            ],
            [
                'id' => 'TLY-20261013-054',
                'name' => 'Fajar Maulana',
                'prodi' => 'S1 Teknik Fisika · FTE',
                'machine' => 'XRD Diffractometer',
                'status' => 'done',
                'upload_url' => route('laboran.pengujian.upload', 'TLY-20261013-054'),
                'download_url' => route('laboran.pengujian.download', 'TLY-20261013-054'),
                'file' => [
                    'name' => 'LHU-TLY-20261013-054.pdf',
                    'size' => (int) round(1.8 * 1048576),
                    'at' => '14 Okt 2026, 09:12',
                    'by' => 'Budi Santoso',
                    'v' => 1,
                ],
            ],
            [
                'id' => 'TLY-20261012-038',
                'name' => 'Prof. Hendra Gunawan',
                'prodi' => 'Dosen · Fak. Ilmu Terapan',
                'machine' => 'Spektrometer FTIR',
                'status' => 'done',
                'upload_url' => route('laboran.pengujian.upload', 'TLY-20261012-038'),
                'download_url' => route('laboran.pengujian.download', 'TLY-20261012-038'),
                'file' => [
                    'name' => 'LHU-TLY-20261012-038.xlsx',
                    'size' => (int) round(0.9 * 1048576),
                    'at' => '13 Okt 2026, 15:30',
                    'by' => 'Budi Santoso',
                    'v' => 2,
                ],
            ],
        ];
    }

    /**
     * Format daftar riwayat Audit Trail untuk antarmuka kerja Laboran.
     */
    public function buildAuditPayload(?Collection $auditTrails): array
    {
        if ($auditTrails && $auditTrails->isNotEmpty()) {
            return $auditTrails->map(function (AuditTrail $audit) {
                return $audit->toTimelinePayload();
            })->values()->all();
        }

        return [
            [
                'id' => 'AUD-2026-0412',
                'type' => 'upload',
                'at' => '14 Okt 2026, 09:12',
                'ticket' => 'TLY-20261013-054',
                'detail' => 'LHU-TLY-20261013-054.pdf',
                'actor' => 'Budi Santoso',
            ],
            [
                'id' => 'AUD-2026-0405',
                'type' => 'revisi',
                'at' => '13 Okt 2026, 15:30',
                'ticket' => 'TLY-20261012-038',
                'detail' => 'LHU-TLY-20261012-038.xlsx (v2)',
                'actor' => 'Budi Santoso',
            ],
            [
                'id' => 'AUD-2026-0391',
                'type' => 'upload',
                'at' => '12 Okt 2026, 11:05',
                'ticket' => 'TLY-20261012-038',
                'detail' => 'LHU-TLY-20261012-038.xlsx (v1)',
                'actor' => 'Sari Wulandari',
            ],
        ];
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
     * Inisialisasi data sampel antrean laboratorium apabila tabel masih kosong.
     */
    public function ensureInitialDataSeeded(): void
    {
        if (!Schema::hasTable('tiket_pengujian') || TiketPengujian::count() > 0) {
            return;
        }

        $pemohonDefault = Schema::hasTable('users')
            ? User::whereIn('email', ['pemohon@telkomuniversity.ac.id', 'pemohon@student.telkomuniversity.ac.id'])->first()
            : null;

        TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261015-089',
            'nama_pemohon' => 'Rizky Aditya Pratama',
            'nim_nip' => '1103220089',
            'fakultas' => 'S1 Teknik Elektro · FTE',
            'nama_sampel' => 'Larutan Nanopartikel TiO2',
            'instrumen' => 'Spektrofotometer UV-Vis',
            'laboratorium' => 'Lab Material Maju',
            'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
            'tanggal_uji' => '2026-10-15',
        ]);

        TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261015-092',
            'nama_pemohon' => 'Dr. Maya Lestari, M.T.',
            'nim_nip' => '19850610-DOS',
            'fakultas' => 'Dosen · Fak. Rekayasa Industri',
            'nama_sampel' => 'Komposit Serat Karbon Epoksi',
            'instrumen' => 'Universal Testing Machine',
            'laboratorium' => 'Lab Uji Mekanik',
            'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
            'tanggal_uji' => '2026-10-15',
        ]);

        TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261014-071',
            'user_id' => $pemohonDefault ? $pemohonDefault->id : null,
            'nama_pemohon' => 'Nadia Putri Anjani',
            'nim_nip' => '1301220071',
            'fakultas' => 'S2 Informatika · FIF',
            'nama_sampel' => 'Modul Transceiver IoT LoRa 915 MHz',
            'instrumen' => 'Analyzer Spektrum RF',
            'laboratorium' => 'Lab Telekomunikasi Terpadu',
            'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
            'tanggal_uji' => '2026-10-14',
        ]);

        TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261016-101',
            'nama_pemohon' => 'Salsabila Rahma',
            'nim_nip' => '1101220101',
            'fakultas' => 'S1 Teknik Telekomunikasi · FTE',
            'nama_sampel' => 'Prototipe Antena Microstrip Array',
            'instrumen' => 'Lab Antena & Propagasi',
            'laboratorium' => 'Lab Antena & Propagasi',
            'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
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
            'status' => TiketPengujian::STATUS_SELESAI,
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
            'status' => TiketPengujian::STATUS_SELESAI,
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
            'enkripsi_algoritma' => DokumenHasil::ENKRIPSI_DEFAULT,
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
            'enkripsi_algoritma' => DokumenHasil::ENKRIPSI_DEFAULT,
            'sha256_checksum' => hash('sha256', 'LHU-TLY-20261012-038.xlsx'),
        ]);

        AuditTrail::create([
            'tiket_pengujian_id' => $t6->id,
            'nama_aktor' => 'Sari Wulandari',
            'aksi' => AuditTrail::AKSI_UNGGAH,
            'berkas_lama' => null,
            'berkas_baru' => 'LHU-TLY-20261012-038.xlsx',
            'ukuran_bytes' => (int) round(0.85 * 1048576),
            'versi' => 1,
            'keterangan' => 'Unggah perdana dokumen hasil uji Spektrometer FTIR.',
        ]);

        AuditTrail::create([
            'tiket_pengujian_id' => $t6->id,
            'nama_aktor' => 'Budi Santoso',
            'aksi' => AuditTrail::AKSI_REVISI,
            'berkas_lama' => 'LHU-TLY-20261012-038.xlsx',
            'berkas_baru' => 'LHU-TLY-20261012-038.xlsx',
            'ukuran_bytes' => (int) round(0.9 * 1048576),
            'versi' => 2,
            'keterangan' => 'Revisi dokumen hasil uji v2.',
        ]);

        AuditTrail::create([
            'tiket_pengujian_id' => $t5->id,
            'nama_aktor' => 'Budi Santoso',
            'aksi' => AuditTrail::AKSI_UNGGAH,
            'berkas_lama' => null,
            'berkas_baru' => 'LHU-TLY-20261013-054.pdf',
            'ukuran_bytes' => (int) round(1.8 * 1048576),
            'versi' => 1,
            'keterangan' => 'Unggah perdana LHU XRD Diffractometer.',
        ]);
    }
}
