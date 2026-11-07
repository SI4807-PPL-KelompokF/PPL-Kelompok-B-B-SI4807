<?php

namespace App\Models;

use App\Models\Concerns\FormatsByteSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenHasil extends Model
{
    use FormatsByteSize, HasFactory;

    public const ENKRIPSI_DEFAULT = 'AES-256-CBC';

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

    protected $casts = [
        'ukuran_bytes' => 'integer',
        'versi' => 'integer',
    ];

    public function tiketPengujian()
    {
        return $this->belongsTo(TiketPengujian::class, 'tiket_pengujian_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
