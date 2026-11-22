<?php

namespace Tests\Browser;

use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi16DashboardDuskTest extends DuskTestCase
{
    private static function getOrCreateUser(string $email, string $name, string $role): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('Telyuji#2026'),
                'role' => $role,
            ]
        );
    }

    /**
     * Skenario A: Pengujian Hak Akses
     * 1. Pengguna belum login diarahkan ke halaman login saat mengakses /admin/dashboard.
     * 2. Pemohon diblokir (403 Forbidden) saat mengakses /admin/dashboard.
     * 3. Laboran diblokir (403 Forbidden) saat mengakses /admin/dashboard.
     * 4. Admin yang login dapat mengakses /admin/dashboard dan melihat judul serta menu.
     */
    public function testAccessControlDashboardAdmin()
    {
        $pemohon = self::getOrCreateUser('pemohon.dusk@student.telkomuniversity.ac.id', 'Pemohon Dusk Test', User::ROLE_PEMOHON);
        $laboran = self::getOrCreateUser('laboran.dusk@student.telkomuniversity.ac.id', 'Laboran Dusk Test', User::ROLE_LABORAN);
        $admin   = self::getOrCreateUser('admin.dusk@student.telkomuniversity.ac.id', 'Admin Dusk Test', User::ROLE_ADMIN);

        $this->browse(function (Browser $browser) use ($pemohon, $laboran, $admin) {
            // 1. Guest diarahkan ke halaman login
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/admin/dashboard')
                ->waitForText('Masuk ke Telyuji')
                ->assertSee('Masuk ke Telyuji');

            // 1b. Validasi login: email dengan domain selain @telkomuniversity.ac.id ditolak
            $browser->visit('/login')
                ->waitForText('Masuk ke Telyuji')
                ->type('#email', 'admin@telyuji.ac.id')
                ->type('#password', 'Telyuji#2026')
                ->press('button[type="submit"]')
                ->waitForText('Alamat email wajib menggunakan domain @telkomuniversity.ac.id.')
                ->assertSee('Alamat email wajib menggunakan domain @telkomuniversity.ac.id.');

            // 1c. Validasi registrasi: domain wajib @telkomuniversity.ac.id
            $browser->visit('/register')
                ->waitForText('Pendaftaran Akun')
                ->type('#name', 'Akun Tes Registrasi')
                ->type('#email', 'pemohon@telyuji.ac.id')
                ->type('#password', 'Telyuji#2026')
                ->type('#password-confirm', 'Telyuji#2026')
                ->press('button[type="submit"]')
                ->waitForText('Alamat email wajib menggunakan domain @telkomuniversity.ac.id.')
                ->assertSee('Alamat email wajib menggunakan domain @telkomuniversity.ac.id.');

            // 2. Pemohon diblokir dengan 403 saat mengakses dashboard admin
            $browser->loginAs($pemohon)
                ->visit('/admin/dashboard')
                ->assertSee('403');

            // 3. Laboran diblokir dengan 403 saat mengakses dashboard admin
            $browser->loginAs($laboran)
                ->visit('/admin/dashboard')
                ->assertSee('403');

            // 4. Admin login menggunakan form login asli dan diarahkan ke /admin/dashboard
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                ->waitForText('Masuk ke Telyuji')
                ->type('#email', $admin->email)
                ->type('#password', 'Telyuji#2026')
                ->press('button[type="submit"]')
                ->waitForText('Dashboard Laboratorium')
                ->assertScript("window.location.pathname.endsWith('/admin/dashboard')")
                ->assertSee('Dashboard Laboratorium')
                ->assertPresent('@nav-admin-dashboard')
                ->assertPresent('@metric-total')
                ->assertPresent('@metric-menunggu')
                ->assertPresent('@metric-sedang-diuji')
                ->assertPresent('@metric-selesai');
        });
    }

    /**
     * Skenario B & C: Pengujian Ringkasan Metrik dan Visualisasi Grafik
     * Memverifikasi data aktual dihitung dengan tepat, tidak ada duplikasi,
     * dan elemen visual (kartu metrik, canvas chart, tabel instrumen, legenda fakultas) muncul.
     */
    public function testDashboardDisplaysAccurateMetricsAndCharts()
    {
        $admin = self::getOrCreateUser('admin.dusk@student.telkomuniversity.ac.id', 'Admin Dusk Test', User::ROLE_ADMIN);

        // Siapkan data pengujian terisolasi dengan prefix TLY-DUSK-
        TiketPengujian::where('nomor_tiket', 'LIKE', 'TLY-DUSK-%')->delete();

        $tickets = [
            // Menunggu Verifikasi
            [
                'nomor_tiket' => 'TLY-DUSK-001',
                'nama_pemohon' => 'Dusk User 1',
                'nim_nip' => '1101200001',
                'fakultas' => 'Fakultas Teknik Elektro',
                'nama_sampel' => 'Sampel Semikonduktor',
                'instrumen' => 'SEM (Scanning Electron Microscope)',
                'laboratorium' => 'Lab Fisika Lanjutan',
                'status' => TiketPengujian::STATUS_MENUNGGU,
                'tanggal_uji' => now()->toDateString(),
            ],
            [
                'nomor_tiket' => 'TLY-DUSK-002',
                'nama_pemohon' => 'Dusk User 2',
                'nim_nip' => '1101200002',
                'fakultas' => 'Fakultas Teknik Elektro',
                'nama_sampel' => 'Sampel Komposit Polimer',
                'instrumen' => 'FTIR Spectroscopy',
                'laboratorium' => 'Lab Kimia Terapan',
                'status' => TiketPengujian::STATUS_MENUNGGU,
                'tanggal_uji' => now()->toDateString(),
            ],
            // Sedang Diuji
            [
                'nomor_tiket' => 'TLY-DUSK-003',
                'nama_pemohon' => 'Dusk User 3',
                'nim_nip' => '1101200003',
                'fakultas' => 'Fakultas Informatika',
                'nama_sampel' => 'Model Akselerasi AI',
                'instrumen' => 'SEM (Scanning Electron Microscope)',
                'laboratorium' => 'Lab Komputasi Kinerja Tinggi',
                'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
                'tanggal_uji' => now()->toDateString(),
            ],
            // Selesai
            [
                'nomor_tiket' => 'TLY-DUSK-004',
                'nama_pemohon' => 'Dusk User 4',
                'nim_nip' => '1101200004',
                'fakultas' => 'Fakultas Rekayasa Industri',
                'nama_sampel' => 'Paduan Logam Aluminium',
                'instrumen' => 'XRF (X-Ray Fluorescence)',
                'laboratorium' => 'Lab Metalurgi',
                'status' => TiketPengujian::STATUS_SELESAI,
                'tanggal_uji' => now()->toDateString(),
            ],
        ];

        foreach ($tickets as $t) {
            TiketPengujian::create($t);
        }

        // Ambil data aktual keseluruhan dari DB untuk assertion yang dinamis dan presisi
        $totalDb = TiketPengujian::count();
        $menungguDb = TiketPengujian::where('status', TiketPengujian::STATUS_MENUNGGU)->count();
        $sedangDiujiDb = TiketPengujian::where('status', TiketPengujian::STATUS_SEDANG_DIUJI)->count();
        $selesaiDb = TiketPengujian::where('status', TiketPengujian::STATUS_SELESAI)->count();

        
        $this->browse(function (Browser $browser) use ($admin, $totalDb, $menungguDb, $sedangDiujiDb, $selesaiDb) {
            $browser->loginAs($admin)
                ->visit('/admin/dashboard')
                ->waitForText('Dashboard Laboratorium')

                // Verifikasi nilai metrik sesuai database
                ->assertAttribute('@val-total', 'data-count', (string) $totalDb)
                ->assertAttribute('@val-menunggu', 'data-count', (string) $menungguDb)
                ->assertAttribute('@val-sedang-diuji', 'data-count', (string) $sedangDiujiDb)
                ->assertAttribute('@val-selesai', 'data-count', (string) $selesaiDb)

                // Verifikasi elemen grafik
                ->assertPresent('@chart-instrumen-section')
                ->assertPresent('@canvas-instrumen')
                ->assertPresent('@chart-fakultas-section')
                ->assertPresent('@canvas-fakultas')
                ->assertPresent('@table-instrumen-section')
                ->assertPresent('@fakultas-legend-table');

            // Tunggu sampai kedua grafik benar-benar dibuat
            // Verifikasi Chart.js berhasil dimuat
            $browser->assertScript('typeof window.Chart === "function"');

            // Verifikasi kedua instance grafik berhasil dibuat
            $browser->assertScript(
                '!!window.telyujiDashboardCharts.instrumen'
            );

            $browser->assertScript(
                '!!window.telyujiDashboardCharts.fakultas'
            );

            // Verifikasi dataset grafik tidak kosong
            $browser->assertScript(
                'window.telyujiDashboardCharts.instrumen.data.datasets[0].data.length > 0'
            );

            $browser->assertScript(
                'window.telyujiDashboardCharts.fakultas.data.datasets[0].data.length > 0'
            );

            // Verifikasi canvas benar-benar memiliki ukuran render
            $browser->assertScript(
                'window.telyujiDashboardCharts.instrumen.canvas.width > 0 &&
                window.telyujiDashboardCharts.instrumen.canvas.height > 0'
            );

            $browser->assertScript(
                'window.telyujiDashboardCharts.fakultas.canvas.width > 0 &&
                window.telyujiDashboardCharts.fakultas.canvas.height > 0'
            );

            // Verifikasi informasi grafik ditampilkan
            $browser->assertScript("window.telyujiDashboardCharts.instrumen.data.labels.includes('SEM (Scanning Electron Microscope)')");
                $browser->assertScript("window.telyujiDashboardCharts.fakultas.data.labels.includes('Fakultas Teknik Elektro')");
        });

        // Bersihkan tiket uji
        TiketPengujian::where('nomor_tiket', 'LIKE', 'TLY-DUSK-%')->delete();
    }

    /**
     * Skenario D: Pengujian Kondisi Khusus / Empty State
     * Memverifikasi saat tabel kosong atau tidak ada tiket, dashboard tetap aman,
     * tidak crash / menghasilkan error, dan menampilkan empty state yang informatif.
     */
    public function testDashboardEmptyStateWhenNoTickets()
    {
        $admin = self::getOrCreateUser('admin.dusk@student.telkomuniversity.ac.id', 'Admin Dusk Test', User::ROLE_ADMIN);

        // Simpan sementara atribut mentah tiket yang ada agar tipe tanggal Y-m-d MySQL tidak berubah
        $backupTickets = TiketPengujian::all()->map(function ($item) {
            return $item->getAttributes();
        })->toArray();
        TiketPengujian::query()->delete();

        try {
            $this->browse(function (Browser $browser) use ($admin) {
                $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->waitForText('Dashboard Laboratorium')
                    ->assertAttribute('@val-total', 'data-count', '0')
                    ->assertAttribute('@val-menunggu', 'data-count', '0')
                    ->assertAttribute('@val-sedang-diuji', 'data-count', '0')
                    ->assertAttribute('@val-selesai', 'data-count', '0')
                    ->assertPresent('@empty-instrumen')
                    ->assertSee('Belum ada data penggunaan mesin')
                    ->assertPresent('@empty-fakultas')
                    ->assertSee('Belum ada data fakultas');
            });
        } finally {
            // Kembalikan data semula secara presisi menggunakan DB insert agar ID dan relasi tetap utuh
            TiketPengujian::query()->delete();
            foreach ($backupTickets as $data) {
                \Illuminate\Support\Facades\DB::table('tiket_pengujian')->insert($data);
            }
        }
    }
}