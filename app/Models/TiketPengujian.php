<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketPengujian extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'Menunggu Verifikasi';
    public const STATUS_DALAM_ANTREAN = 'Dalam Antrean';
    public const STATUS_SEDANG_DIUJI = 'Sedang Diuji';
    public const STATUS_SELESAI = 'Selesai';
    public const STATUS_DITOLAK = 'Ditolak';

    private const STAGE_MAP = [
        self::STATUS_MENUNGGU => 2,
        self::STATUS_DALAM_ANTREAN => 3,
        self::STATUS_SEDANG_DIUJI => 4,
        self::STATUS_SELESAI => 5,
        self::STATUS_DITOLAK => 0,
    ];

    protected $table = 'tiket_pengujian';

    protected $fillable = [
        'nomor_tiket',
        'user_id',
        'nama_pemohon',
        'nim_nip',
        'fakultas',
        'prodi',
        'mesin_lab_id',
        'nama_sampel',
        'deskripsi_sampel',
        'tujuan_pengujian',
        'jenis_sampel',
        'tautan_file_cloud',
        'instrumen',
        'laboratorium',
        'status',
        'tanggal_uji',
        'barcode_token',
        'barcode_path',
        'submitted_at',
        'received_at',
        'testing_at',
        'completed_at',
        'rejected_at',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_uji' => 'date',
        'submitted_at' => 'datetime',
        'received_at' => 'datetime',
        'testing_at' => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Relasi ke akun pemohon (apabila terhubung).
     */
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke mesin lab yang dipilih.
     */
    public function mesinLab()
    {
        return $this->belongsTo(MesinLab::class, 'mesin_lab_id');
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

    public function isDitolak(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    public function stageNumber(): int
    {
        return self::STAGE_MAP[$this->status] ?? 1;
    }

    /**
     * Generate nomor tiket unik: TLY-YYYYMMDD-XXX
     */
    public static function generateNomorTiket(): string
    {
        $prefix = 'TLY-' . now()->format('Ymd') . '-';
        $lastToday = static::where('nomor_tiket', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_tiket');

        if ($lastToday) {
            $lastNum = (int) substr($lastToday, -3);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad((string) $nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate barcode token unik.
     */
    public static function generateBarcodeToken(): string
    {
        return strtoupper(bin2hex(random_bytes(16)));
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
     * Data timeline untuk tampilan tracking PBI-09.
     */
    public function getTimelineData(): array
    {
        $stages = [
            [
                'key' => 'submitted',
                'label' => 'Terdaftar',
                'desc' => 'Pengajuan diterima sistem',
                'timestamp' => $this->submitted_at,
                'active' => true,
            ],
            [
                'key' => 'resi',
                'label' => 'Resi Terbit',
                'desc' => 'Resi E-Ticket PDF ber-barcode terbit',
                'timestamp' => $this->submitted_at,
                'active' => $this->barcode_token !== null,
            ],
            [
                'key' => 'antrean',
                'label' => 'Dalam Antrean',
                'desc' => 'Sampel diterima laboran',
                'timestamp' => $this->received_at,
                'active' => in_array($this->status, [
                    self::STATUS_DALAM_ANTREAN,
                    self::STATUS_SEDANG_DIUJI,
                    self::STATUS_SELESAI,
                ]),
            ],
            [
                'key' => 'diuji',
                'label' => 'Sedang Diuji',
                'desc' => 'Proses pengujian di instrumen lab',
                'timestamp' => $this->testing_at,
                'active' => in_array($this->status, [
                    self::STATUS_SEDANG_DIUJI,
                    self::STATUS_SELESAI,
                ]),
            ],
            [
                'key' => 'selesai',
                'label' => 'Selesai & Unduh',
                'desc' => 'LHU resmi terbit, siap unduh',
                'timestamp' => $this->completed_at,
                'active' => $this->status === self::STATUS_SELESAI,
            ],
        ];

        return [
            'stages' => $stages,
            'current_stage' => $this->stageNumber(),
            'is_rejected' => $this->isDitolak(),
            'rejection_reason' => $this->alasan_penolakan,
            'rejected_at' => $this->rejected_at,
        ];
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
