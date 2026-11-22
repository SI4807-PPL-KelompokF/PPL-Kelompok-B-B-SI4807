<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MesinLab extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'Aktif';
    public const STATUS_MAINTENANCE = 'Maintenance';

    public const DEFAULT_KATEGORI_LAYANAN = [
        'Material & Kimia Terapan',
        'Telekomunikasi, RF & IoT',
        'Bioteknologi & Lingkungan',
        'Simulasi & Komputasi Riset',
    ];

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
        return $this->status_mesin === self::STATUS_AKTIF;
    }

    /**
     * Hitung sisa kuota harian mesin pada tanggal tertentu.
     * Mencocokkan berdasarkan relasi mesin_lab_id maupun nama instrumen agar akurat meski nama mesin diubah.
     */
    public function sisaKuotaHarian(?string $tanggal = null): int
    {
        $tanggal = $tanggal ?: now()->toDateString();

        $terpakai = TiketPengujian::where(function ($q) {
            $q->where('mesin_lab_id', $this->id)
                ->orWhere('instrumen', $this->nama_mesin);
        })
            ->whereDate('tanggal_uji', $tanggal)
            ->whereNotIn('status', [TiketPengujian::STATUS_DITOLAK])
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

    /**
     * Seed data katalog awal mesin laboratorium secara idempoten (sumber tunggal untuk Seeder, PBI-04, PBI-06, & PBI-07).
     */
    public static function seedDefaultData(): void
    {
        if (!Schema::hasTable('mesin_lab') || self::count() > 0) {
            return;
        }

        $dataset = [
            [
                'nama_mesin' => 'Spektrofotometer UV-Vis Terintegrasi',
                'kategori_layanan' => 'Material & Kimia Terapan',
                'laboratorium' => 'Lab Kimia Terpadu Telkom University',
                'status_mesin' => self::STATUS_AKTIF,
                'kuota_harian' => 6,
                'estimasi_sla_hari' => 2,
                'sop_penyiapan' => "1. Sampel cairan harus disaring menggunakan syringe filter 0.45 um sebelum diserahkan.\n2. Wadah vial kaca borosilikat transparan volume minimal 5 mL bertutup ulir kedap udara.\n3. Beri label kode sampel tahan air pada dinding vial (bukan pada tutup).\n4. Lampirkan lembar data pengujian atau perkiraan panjang gelombang absorban yang diharapkan.\n5. Sisa spesimen wajib diambil maksimal 7 (tujuh) hari kerja setelah pengujian selesai.",
            ],
            [
                'nama_mesin' => 'Chamber Anechoic & Spektrum RF Analyzer',
                'kategori_layanan' => 'Telekomunikasi, RF & IoT',
                'laboratorium' => 'Lab Telekomunikasi Terpadu Kampus',
                'status_mesin' => self::STATUS_AKTIF,
                'kuota_harian' => 4,
                'estimasi_sla_hari' => 3,
                'sop_penyiapan' => "1. Prototipe antena atau modul IoT telah dilengkapi konektor SMA/U.FL standar.\n2. Sertakan power supply / baterai cadangan dan spesifikasi frekuensi operasional (900 MHz - 6 GHz).\n3. Sampel bebas dari bahan magnetik yang tidak terlindung.\n4. Pengujian wajib didampingi pemohon pada sesi kalibrasi polarisasi bila diperlukan.",
            ],
            [
                'nama_mesin' => 'Microplate Reader & Sentrifugasi Berpendingin',
                'kategori_layanan' => 'Bioteknologi & Lingkungan',
                'laboratorium' => 'Lab Bioteknologi & Rekayasa Hayati',
                'status_mesin' => self::STATUS_AKTIF,
                'kuota_harian' => 5,
                'estimasi_sla_hari' => 3,
                'sop_penyiapan' => "1. Sampel biologis/enzimatik disimpan dalam ice box / thermo-cool bag saat penyerahan.\n2. Tabung mikro sentrifus 1.5 mL bebas kontaminasi dan diberi label nomor tiket.\n3. Jangan membawa spesimen infeksius pathogen level 3 tanpa izin khusus K3 Lab.",
            ],
            [
                'nama_mesin' => 'High-Performance Cluster GPU Simulasi CAE/FEA',
                'kategori_layanan' => 'Simulasi & Komputasi Riset',
                'laboratorium' => 'Lab Riset Komputasi Lanjut Kampus',
                'status_mesin' => self::STATUS_AKTIF,
                'kuota_harian' => 8,
                'estimasi_sla_hari' => 1,
                'sop_penyiapan' => "1. Siapkan dataset atau berkas mesh 3D / skrip simulasi pada Google Drive atau OneDrive kampus.\n2. Pastikan akses link terbuka untuk dibaca oleh laboran (laboran@telkomuniversity.ac.id).\n3. Sertakan file petunjuk instruksi parameter eksekusi (.txt / .md).\n4. Resi berfungsi sebagai E-Ticket digital tanpa perlu mencetak kertas.",
            ],
            [
                'nama_mesin' => 'Universal Testing Machine (UTM Mekanik 50 kN)',
                'kategori_layanan' => 'Material & Kimia Terapan',
                'laboratorium' => 'Lab Rekayasa Material & Mekanik',
                'status_mesin' => self::STATUS_MAINTENANCE,
                'kuota_harian' => 3,
                'estimasi_sla_hari' => 4,
                'sop_penyiapan' => "1. Dimensi spesimen uji tarik/tekan wajib memenuhi standar ASTM E8M atau JIS Z 2201.\n2. Permukaan spesimen rata dan bersih dari oli atau karat berlebih.\n3. Mesin saat ini sedang dalam jadwal kalibrasi sensor load-cell rutin.",
            ],
        ];

        foreach ($dataset as $row) {
            self::create($row);
        }
    }
}
