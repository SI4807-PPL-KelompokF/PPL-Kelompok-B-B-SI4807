<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenHasil extends Model
{
    use HasFactory;

    protected $table = 'dokumen_hasil';

    protected $fillable = [
        'tiket_pengujian_id',
        'uploaded_by',
        'nama_berkas_asli',
        'path_berkas',
        'ekstensi',
        'mime_type',
        'ukuran_bytes',
        'versi',
        'catatan_laboran',
        'enkripsi_algoritma',
        'sha256_checksum',
    ];

    public function tiketPengujian()
    {
        return $this->belongsTo(TiketPengujian::class, 'tiket_pengujian_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
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
