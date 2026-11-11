<?php

namespace Database\Seeders;

use App\Models\MesinLab;
use App\Models\User;
use App\Services\DokumenHasilUjiService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Migrasi otomatis akun lama berdomain @student.telkomuniversity.ac.id ke @telkomuniversity.ac.id
        if (
            User::where('email', 'laboran@student.telkomuniversity.ac.id')->exists()
            && !User::where('email', 'laboran@telkomuniversity.ac.id')->exists()
        ) {
            User::where('email', 'laboran@student.telkomuniversity.ac.id')
                ->update(['email' => 'laboran@telkomuniversity.ac.id']);
        }

        if (
            User::where('email', 'pemohon@student.telkomuniversity.ac.id')->exists()
            && !User::where('email', 'pemohon@telkomuniversity.ac.id')->exists()
        ) {
            User::where('email', 'pemohon@student.telkomuniversity.ac.id')
                ->update(['email' => 'pemohon@telkomuniversity.ac.id']);
        }

        User::updateOrCreate(
            ['email' => 'admin@telkomuniversity.ac.id'],
            [
                'name' => 'Administrator Telyuji',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'laboran@telkomuniversity.ac.id'],
            [
                'name' => 'Raka Pratama, S.T.',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_LABORAN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pemohon@telkomuniversity.ac.id'],
            [
                'name' => 'Nadia Putri Rahmawati',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
            ]
        );

        app(DokumenHasilUjiService::class)->ensureInitialDataSeeded();

        // Seed Katalog Mesin Lab untuk PBI-07 & PBI-08
        if (MesinLab::count() === 0) {
            $mesinData = [
                [
                    'nama_mesin' => 'Spektrofotometer UV-Vis Terintegrasi',
                    'kategori_layanan' => 'Material & Kimia Terapan',
                    'laboratorium' => 'Lab Kimia Terpadu Telkom University',
                    'status_mesin' => 'Aktif',
                    'kuota_harian' => 6,
                    'estimasi_sla_hari' => 2,
                    'sop_penyiapan' => "1. Sampel cairan harus disaring menggunakan syringe filter 0.45 um sebelum diserahkan.\n2. Wadah vial kaca borosilikat transparan volume minimal 5 mL bertutup ulir kedap udara.\n3. Beri label kode sampel tahan air pada dinding vial (bukan pada tutup).\n4. Lampirkan lembar data pengujian atau perkiraan panjang gelombang absorban yang diharapkan.\n5. Sisa spesimen wajib diambil maksimal 7 (tujuh) hari kerja setelah pengujian selesai.",
                ],
                [
                    'nama_mesin' => 'Chamber Anechoic & Spektrum RF Analyzer',
                    'kategori_layanan' => 'Telekomunikasi, RF & IoT',
                    'laboratorium' => 'Lab Telekomunikasi Terpadu Kampus',
                    'status_mesin' => 'Aktif',
                    'kuota_harian' => 4,
                    'estimasi_sla_hari' => 3,
                    'sop_penyiapan' => "1. Prototipe antena atau modul IoT telah dilengkapi konektor SMA/U.FL standar.\n2. Sertakan power supply / baterai cadangan dan spesifikasi frekuensi operasional (900 MHz - 6 GHz).\n3. Sampel bebas dari bahan magnetik yang tidak terlindung.\n4. Pengujian wajib didampingi pemohon pada sesi kalibrasi polarisasi bila diperlukan.",
                ],
                [
                    'nama_mesin' => 'Microplate Reader & Sentrifugasi Berpendingin',
                    'kategori_layanan' => 'Bioteknologi & Lingkungan',
                    'laboratorium' => 'Lab Bioteknologi & Rekayasa Hayati',
                    'status_mesin' => 'Aktif',
                    'kuota_harian' => 5,
                    'estimasi_sla_hari' => 3,
                    'sop_penyiapan' => "1. Sampel biologis/enzimatik disimpan dalam ice box / thermo-cool bag saat penyerahan.\n2. Tabung mikro sentrifus 1.5 mL bebas kontaminasi dan diberi label nomor tiket.\n3. Jangan membawa spesimen infeksius pathogen level 3 tanpa izin khusus K3 Lab.",
                ],
                [
                    'nama_mesin' => 'High-Performance Cluster GPU Simulasi CAE/FEA',
                    'kategori_layanan' => 'Simulasi & Komputasi Riset',
                    'laboratorium' => 'Lab Riset Komputasi Lanjut Kampus',
                    'status_mesin' => 'Aktif',
                    'kuota_harian' => 8,
                    'estimasi_sla_hari' => 1,
                    'sop_penyiapan' => "1. Siapkan dataset atau berkas mesh 3D / skrip simulasi pada Google Drive atau OneDrive kampus.\n2. Pastikan akses link terbuka untuk dibaca oleh laboran (laboran@telkomuniversity.ac.id).\n3. Sertakan file petunjuk instruksi parameter eksekusi (.txt / .md).\n4. Resi berfungsi sebagai E-Ticket digital tanpa perlu mencetak kertas.",
                ],
                [
                    'nama_mesin' => 'Universal Testing Machine (UTM Mekanik 50 kN)',
                    'kategori_layanan' => 'Material & Kimia Terapan',
                    'laboratorium' => 'Lab Rekayasa Material & Mekanik',
                    'status_mesin' => 'Maintenance',
                    'kuota_harian' => 3,
                    'estimasi_sla_hari' => 4,
                    'sop_penyiapan' => "1. Dimensi spesimen uji tarik/tekan wajib memenuhi standar ASTM E8M atau JIS Z 2201.\n2. Permukaan spesimen rata dan bersih dari oli atau karat berlebih.\n3. Mesin saat ini sedang dalam jadwal kalibrasi sensor load-cell rutin.",
                ],
            ];

            foreach ($mesinData as $m) {
                MesinLab::create($m);
            }
        }
    }
}
