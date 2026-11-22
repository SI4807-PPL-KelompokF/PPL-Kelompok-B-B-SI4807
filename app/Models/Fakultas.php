<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Fakultas extends Model
{
    use HasFactory;

    protected $table = 'fakultas';

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
    ];

    /**
     * Relasi ke Program Studi (Prodi) yang bernaung di bawah fakultas ini.
     */
    public function prodi()
    {
        return $this->hasMany(Prodi::class, 'fakultas_id');
    }

    /**
     * Relasi ke Pengguna/Pemohon yang terdaftar di fakultas ini.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'fakultas_id');
    }

    /**
     * Dataset referensi standar Fakultas & Program Studi Telkom University.
     */
    public static function defaultDataset(): array
    {
        return [
            [
                'kode' => 'FIF',
                'nama' => 'Fakultas Informatika (FIF)',
                'prodis' => [
                    ['kode' => 'IF',  'nama' => 'S1 Informatika'],
                    ['kode' => 'SI',  'nama' => 'S1 Sistem Informasi'],
                    ['kode' => 'IT',  'nama' => 'S1 Teknologi Informasi'],
                    ['kode' => 'SE',  'nama' => 'S1 Rekayasa Perangkat Lunak'],
                    ['kode' => 'DS',  'nama' => 'S1 Sains Data'],
                    ['kode' => 'MIF', 'nama' => 'S2 Informatika'],
                ],
            ],
            [
                'kode' => 'FTE',
                'nama' => 'Fakultas Teknik Elektro (FTE)',
                'prodis' => [
                    ['kode' => 'TT',  'nama' => 'S1 Teknik Telekomunikasi'],
                    ['kode' => 'EL',  'nama' => 'S1 Teknik Elektro'],
                    ['kode' => 'BM',  'nama' => 'S1 Teknik Biomedis'],
                    ['kode' => 'CE',  'nama' => 'S1 Teknik Komputer'],
                    ['kode' => 'MEL', 'nama' => 'S2 Teknik Elektro'],
                ],
            ],
            [
                'kode' => 'FRI',
                'nama' => 'Fakultas Rekayasa Industri (FRI)',
                'prodis' => [
                    ['kode' => 'TI',  'nama' => 'S1 Teknik Industri'],
                    ['kode' => 'SIS', 'nama' => 'S1 Sistem Informasi'],
                    ['kode' => 'TL',  'nama' => 'S1 Teknik Logistik'],
                    ['kode' => 'MTI', 'nama' => 'S2 Teknik Industri'],
                ],
            ],
            [
                'kode' => 'FEB',
                'nama' => 'Fakultas Ekonomi dan Bisnis (FEB)',
                'prodis' => [
                    ['kode' => 'MN',  'nama' => 'S1 Manajemen'],
                    ['kode' => 'AK',  'nama' => 'S1 Akuntansi'],
                    ['kode' => 'MM',  'nama' => 'S2 Manajemen'],
                ],
            ],
            [
                'kode' => 'FKS',
                'nama' => 'Fakultas Komunikasi dan Ilmu Sosial (FKS)',
                'prodis' => [
                    ['kode' => 'IK',  'nama' => 'S1 Ilmu Komunikasi'],
                    ['kode' => 'PR',  'nama' => 'S1 Hubungan Masyarakat'],
                    ['kode' => 'AB',  'nama' => 'S1 Administrasi Bisnis'],
                ],
            ],
            [
                'kode' => 'FIK',
                'nama' => 'Fakultas Industri Kreatif (FIK)',
                'prodis' => [
                    ['kode' => 'DKV', 'nama' => 'S1 Desain Komunikasi Visual'],
                    ['kode' => 'DI',  'nama' => 'S1 Desain Interior'],
                    ['kode' => 'DP',  'nama' => 'S1 Desain Produk'],
                    ['kode' => 'SR',  'nama' => 'S1 Seni Rupa'],
                ],
            ],
            [
                'kode' => 'FIT',
                'nama' => 'Fakultas Ilmu Terapan (FIT)',
                'prodis' => [
                    ['kode' => 'DTT', 'nama' => 'D3 Teknik Telekomunikasi'],
                    ['kode' => 'DSE', 'nama' => 'D3 Rekayasa Perangkat Lunak'],
                    ['kode' => 'DSI', 'nama' => 'D3 Sistem Informasi'],
                ],
            ],
        ];
    }

    /**
     * Seed master data awal Fakultas & Prodi Telkom University bila kosong.
     */
    public static function seedDefaultData(): void
    {
        if (!Schema::hasTable('fakultas') || !Schema::hasTable('prodi') || self::count() > 0) {
            return;
        }

        foreach (self::defaultDataset() as $f) {
            $fakultas = self::create([
                'kode_fakultas' => $f['kode'],
                'nama_fakultas' => $f['nama'],
            ]);

            foreach ($f['prodis'] as $p) {
                $fakultas->prodi()->create([
                    'kode_prodi' => $p['kode'],
                    'nama_prodi' => $p['nama'],
                ]);
            }
        }
    }

    /**
     * Sumber data tunggal peta Fakultas -> daftar Program Studi (dipakai oleh PBI-01, PBI-03, & PBI-07).
     * Otomatis melakukan seed awal apabila tabel master masih kosong.
     */
    public static function getFakultasProdiMap(bool $includeStaffFallback = false): array
    {
        try {
            if (Schema::hasTable('fakultas') && Schema::hasTable('prodi')) {
                self::seedDefaultData();

                $fakultasList = self::with(['prodi' => function ($q) {
                    $q->orderBy('id', 'asc');
                }])->orderBy('id', 'asc')->get();

                if ($fakultasList->isNotEmpty()) {
                    $map = [];
                    foreach ($fakultasList as $fak) {
                        $map[$fak->nama_fakultas] = $fak->prodi->pluck('nama_prodi')->values()->all();
                    }

                    return $map;
                }
            }
        } catch (\Throwable $e) {
            // Fallback aman bila tabel belum dimigrasi
        }

        $fallback = [];
        foreach (self::defaultDataset() as $f) {
            $fallback[$f['nama']] = array_column($f['prodis'], 'nama');
        }

        if ($includeStaffFallback) {
            $fallback['Staf / Dosen'] = [
                'Dosen Tetap',
                'Dosen Tidak Tetap',
                'Tenaga Kependidikan',
            ];
        }

        return $fallback;
    }
}
