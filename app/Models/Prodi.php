<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'prodi';

    protected $fillable = [
        'fakultas_id',
        'kode_prodi',
        'nama_prodi',
    ];

    /**
     * Relasi ke Fakultas induk.
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    /**
     * Relasi ke Pengguna/Pemohon yang terdaftar di program studi ini.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'prodi_id');
    }
}
