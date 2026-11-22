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
     * Peta warna identitas resmi 7 Fakultas Telkom University (Single Source of Truth).
     */
    public const IDENTITY_COLORS = [
        'FTE' => ['code' => 'FTE', 'hex' => '#1E3A8A', 'chip_class' => 'tu-fak-fte'], // Biru Tua
        'FRI' => ['code' => 'FRI', 'hex' => '#166534', 'chip_class' => 'tu-fak-fri'], // Hijau Tua
        'FIF' => ['code' => 'FIF', 'hex' => '#D4A017', 'chip_class' => 'tu-fak-fif'], // Emas / Kuning Emas
        'FEB' => ['code' => 'FEB', 'hex' => '#38BDF8', 'chip_class' => 'tu-fak-feb'], // Biru Muda
        'FKS' => ['code' => 'FKS', 'hex' => '#7C3AED', 'chip_class' => 'tu-fak-fks'], // Ungu
        'FIK' => ['code' => 'FIK', 'hex' => '#F97316', 'chip_class' => 'tu-fak-fik'], // Jingga / Oranye
        'FIT' => ['code' => 'FIT', 'hex' => '#84CC16', 'chip_class' => 'tu-fak-fit'], // Hijau Muda
    ];

    public const DEFAULT_IDENTITY_COLOR = [
        'code' => '',
        'hex' => '#64748B',
        'chip_class' => 'tu-fak-default',
    ];

    /**
     * Resolusi warna identitas resmi berdasarkan kode atau nama fakultas.
     *
     * @return array{code:string,hex:string,chip_class:string}
     */
    public static function resolveIdentity(?string $kodeAtauNama): array
    {
        $raw = trim((string) ($kodeAtauNama ?? ''));
        if ($raw === '') {
            return self::DEFAULT_IDENTITY_COLOR;
        }

        // Ambil kode setelah pemisah '·' jika berformat "Prodi · FTE"
        if (mb_strpos($raw, '·') !== false) {
            $parts = explode('·', $raw);
            $raw = trim((string) end($parts));
        }

        // Tangkap kode singkat dalam tanda kurung seperti "Fakultas Rekayasa Industri (FRI)"
        if (preg_match('/\(([A-Za-z]{2,5})\)\s*$/', $raw, $m)) {
            $code = strtoupper($m[1]);
            if ($code === 'FKB') {
                $code = 'FKS';
            }
            if (isset(self::IDENTITY_COLORS[$code])) {
                return self::IDENTITY_COLORS[$code];
            }
        }

        $upper = strtoupper(trim((string) preg_replace('/\s*\([A-Za-z]{2,5}\)\s*$/', '', $raw)));
        if ($upper === 'FKB') {
            $upper = 'FKS';
        }
        if (isset(self::IDENTITY_COLORS[$upper])) {
            return self::IDENTITY_COLORS[$upper];
        }

        $nameToCode = [
            'FAKULTAS TEKNIK ELEKTRO' => 'FTE',
            'FAK. TEKNIK ELEKTRO' => 'FTE',
            'TEKNIK ELEKTRO' => 'FTE',
            'FAKULTAS REKAYASA INDUSTRI' => 'FRI',
            'FAK. REKAYASA INDUSTRI' => 'FRI',
            'REKAYASA INDUSTRI' => 'FRI',
            'FAKULTAS INFORMATIKA' => 'FIF',
            'FAK. INFORMATIKA' => 'FIF',
            'INFORMATIKA' => 'FIF',
            'FAKULTAS EKONOMI DAN BISNIS' => 'FEB',
            'FAK. EKONOMI DAN BISNIS' => 'FEB',
            'EKONOMI DAN BISNIS' => 'FEB',
            'FAKULTAS KOMUNIKASI DAN ILMU SOSIAL' => 'FKS',
            'KOMUNIKASI DAN ILMU SOSIAL' => 'FKS',
            'FAKULTAS INDUSTRI KREATIF' => 'FIK',
            'INDUSTRI KREATIF' => 'FIK',
            'FAKULTAS ILMU TERAPAN' => 'FIT',
            'FAK. ILMU TERAPAN' => 'FIT',
            'ILMU TERAPAN' => 'FIT',
        ];

        if (isset($nameToCode[$upper])) {
            return self::IDENTITY_COLORS[$nameToCode[$upper]];
        }

        return self::DEFAULT_IDENTITY_COLOR;
    }

    public function identityHex(): string
    {
        return self::resolveIdentity($this->kode_fakultas ?: $this->nama_fakultas)['hex'];
    }

    public function identityChipClass(): string
    {
        return self::resolveIdentity($this->kode_fakultas ?: $this->nama_fakultas)['chip_class'];
    }

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
