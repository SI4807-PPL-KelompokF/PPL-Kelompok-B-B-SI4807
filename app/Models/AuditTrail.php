<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

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
    ];

    public function tiketPengujian()
    {
        return $this->belongsTo(TiketPengujian::class, 'tiket_pengujian_id');
    }

    public function getUkuranFormattedAttribute(): string
    {
        $bytes = (int) $this->ukuran_bytes;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        }
        return max(1, (int) round($bytes / 1024)) . ' KB';
    }
}
