<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketPengujian extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'Menunggu Verifikasi';
    public const STATUS_SEDANG_DIUJI = 'Sedang Diuji';
    public const STATUS_SELESAI = 'Selesai';

    private const STAGE_MAP = [
        self::STATUS_MENUNGGU => 2,
        self::STATUS_SEDANG_DIUJI => 4,
        self::STATUS_SELESAI => 5,
    ];

    protected $table = 'tiket_pengujian';

    protected $fillable = [
        'nomor_tiket',
        'user_id',
        'nama_pemohon',
        'nim_nip',
        'fakultas',
        'nama_sampel',
        'instrumen',
        'laboratorium',
        'status',
        'tanggal_uji',
    ];

    protected $casts = [
        'tanggal_uji' => 'date',
    ];

    /**
     * Relasi ke akun pemohon (apabila terhubung).
     */
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi 1:1 ke dokumen hasil uji aktif.
     */
    public function dokumenHasil()
    {
        return $this->hasOne(DokumenHasil::class, 'tiket_pengujian_id');
    }

    /**
     * Riwayat audit trail tiket pengujian.
     */
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'tiket_pengujian_id')->latest();
    }

    /**
     * Sesuai prasyarat UC-13, unggah/revisi hanya diizinkan saat tiket berstatus
     * "Sedang Diuji" (unggah perdana) atau "Selesai" (mode revisi).
     */
    public function canUploadHasil(): bool
    {
        return in_array($this->status, [self::STATUS_SEDANG_DIUJI, self::STATUS_SELESAI], true);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function stageNumber(): int
    {
        return self::STAGE_MAP[$this->status] ?? 2;
    }

    /**
     * Periksa apakah pengguna berhak mengunduh dokumen LHU tiket ini.
     */
    public function canBeDownloadedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->isLaboran()
            || ($this->user_id !== null && (int) $this->user_id === (int) $user->id);
    }

    /**
     * Format ringkasan progres publik untuk fitur lacak cepat pada halaman beranda.
     */
    public function toPublicTrackingPayload(): array
    {
        $latestAudit = $this->auditTrails->first();
        $picLabel = $latestAudit ? e($latestAudit->nama_aktor) : 'Tim Laboran Telyuji';
        $labLabel = e($this->laboratorium);
        $dateLabel = $this->tanggal_uji ? $this->tanggal_uji->format('d M Y') : '-';

        $footer = $this->isCompleted()
            ? "Laboran PIC: <strong>{$picLabel}</strong> ({$labLabel}) &bull; LHU Resmi Tersedia ({$dateLabel})"
            : "Lokasi: <strong>{$labLabel}</strong> &bull; Tanggal Uji: {$dateLabel}";

        return [
            'found' => true,
            'id' => $this->nomor_tiket,
            'note' => e($this->instrumen . ' · ' . $this->nama_sampel),
            'status' => $this->status,
            'stage' => $this->stageNumber(),
            'footer' => $footer,
        ];
    }

    /**
     * Format data tiket untuk antrean interaktif pada panel kerja Laboran.
     */
    public function toQueuePayload(): array
    {
        $doc = $this->dokumenHasil;
        $latestAudit = $this->auditTrails ? $this->auditTrails->sortByDesc('id')->first() : null;

        return [
            'id' => $this->nomor_tiket,
            'name' => $this->nama_pemohon,
            'prodi' => $this->fakultas,
            'machine' => $this->instrumen,
            'status' => $this->isCompleted() ? 'done' : 'testing',
            'upload_url' => route('laboran.pengujian.upload', $this->nomor_tiket),
            'download_url' => route('laboran.pengujian.download', $this->nomor_tiket),
            'file' => $doc ? [
                'name' => $doc->nama_berkas_asli,
                'size' => (int) $doc->ukuran_bytes,
                'at' => $doc->updated_at ? $doc->updated_at->format('d M Y, H:i') : '14 Okt 2026, 09:12',
                'by' => $latestAudit ? $latestAudit->nama_aktor : 'Budi Santoso',
                'v' => (int) $doc->versi,
            ] : null,
        ];
    }
}
