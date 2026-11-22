<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesinLab extends Model
{
    use HasFactory;

    protected $table = 'mesin_lab';

    protected $fillable = [
        'nama_mesin',
        'kategori_layanan',
        'laboratorium',
        'status_mesin',
        'kuota_harian',
        'estimasi_sla_hari',
        'sop_penyiapan',
    ];

    protected $casts = [
        'kuota_harian' => 'integer',
        'estimasi_sla_hari' => 'integer',
    ];

    /**
     * Periksa apakah mesin sedang aktif (bukan maintenance).
     */
    public function isAktif(): bool
    {
        return $this->status_mesin === 'Aktif';
    }

    /**
     * Hitung sisa kuota harian mesin pada tanggal tertentu.
     */
    public function sisaKuotaHarian(?string $tanggal = null): int
    {
        $tanggal = $tanggal ?: now()->toDateString();

        $terpakai = TiketPengujian::where('instrumen', $this->nama_mesin)
            ->whereDate('tanggal_uji', $tanggal)
            ->whereNotIn('status', ['Ditolak'])
            ->count();

        return max(0, $this->kuota_harian - $terpakai);
    }

    /**
     * Relasi ke tiket pengujian yang menggunakan mesin ini.
     */
    public function tiketPengujian()
    {
        return $this->hasMany(TiketPengujian::class, 'mesin_lab_id');
    }
}

