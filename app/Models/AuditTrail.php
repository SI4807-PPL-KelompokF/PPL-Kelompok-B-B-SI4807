<?php

namespace App\Models;

use App\Models\Concerns\FormatsByteSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use FormatsByteSize, HasFactory;

    public const AKSI_PENGAJUAN = 'PENGAJUAN_SAMPEL_BARU';
    public const AKSI_TERIMA_FISIK = 'TERIMA_FISIK';
    public const AKSI_VERIFIKASI_DIGITAL = 'VERIFIKASI_DIGITAL';
    public const AKSI_PROSES_MESIN = 'PROSES_MESIN';
    public const AKSI_UNGGAH = 'UNGGAH_HASIL_UJI';
    public const AKSI_REVISI = 'REVISI_HASIL_UJI';

    public const LHU_ACTIONS = [
        self::AKSI_UNGGAH,
        self::AKSI_REVISI,
    ];

    protected $table = 'audit_trails';

    protected $fillable = [
        'tiket_pengujian_id',
        'user_id',
        'nama_aktor',
        'aksi',
        'berkas_lama',
        'berkas_baru',
        'ukuran_bytes',
        'versi',
        'keterangan',
        'ip_address',
    ];

    protected $casts = [
        'ukuran_bytes' => 'integer',
        'versi' => 'integer',
    ];

    public function tiketPengujian()
    {
        return $this->belongsTo(TiketPengujian::class, 'tiket_pengujian_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isRevision(): bool
    {
        return $this->aksi === self::AKSI_REVISI;
    }

    public function actionLabel(): string
    {
        $labels = [
            self::AKSI_PENGAJUAN => 'Pengajuan Sampel Baru',
            self::AKSI_TERIMA_FISIK => 'Penerimaan Sampel Fisik',
            self::AKSI_VERIFIKASI_DIGITAL => 'Verifikasi Berkas Digital',
            self::AKSI_PROSES_MESIN => 'Proses Pengujian Mesin',
            self::AKSI_UNGGAH => 'Unggah Hasil Uji (LHU)',
            self::AKSI_REVISI => 'Revisi Hasil Uji (LHU)',
        ];

        return $labels[$this->aksi] ?? ucwords(strtolower(str_replace('_', ' ', (string) $this->aksi)));
    }

    public function getKodeAuditAttribute(): string
    {
        return 'AUD-2026-' . str_pad((string) (400 + (int) $this->id), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Format data audit trail untuk antarmuka tabel riwayat aktivitas LHU (PBI-13).
     */
    public function toTimelinePayload(): array
    {
        $isRevision = $this->isRevision();

        return [
            'id' => $this->kode_audit,
            'type' => $isRevision ? 'revisi' : 'upload',
            'label' => $isRevision ? 'Revisi' : 'Unggah',
            'at' => $this->created_at ? $this->created_at->format('d M Y, H:i') : 'Baru saja',
            'ticket' => $this->tiketPengujian ? $this->tiketPengujian->nomor_tiket : '-',
            'detail' => $this->berkas_baru . ($this->versi > 1 || $isRevision ? ' (v' . $this->versi . ')' : ''),
            'actor' => $this->nama_aktor,
        ];
    }
}
