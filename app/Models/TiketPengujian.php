<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketPengujian extends Model
{
    use HasFactory;

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
}
