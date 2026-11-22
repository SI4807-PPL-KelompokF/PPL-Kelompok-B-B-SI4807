<?php

namespace Tests\Feature;

use App\Http\Controllers\DashboardController;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PBI-16 Feature Test: Dashboard Analitik Penggunaan Laboratorium.
 *
 * Menguji logika agregasi statistik, hak akses role, dan penanganan
 * kondisi data kosong / relasi tidak lengkap.
 *
 * Menggunakan RefreshDatabase agar tidak mencemari database produksi.
 */
class Pbi16DashboardTest extends TestCase
{
    use RefreshDatabase;

    // ══════════════════════════════════════════════════════════════════════
    // A. PENGUJIAN HAK AKSES
    // ══════════════════════════════════════════════════════════════════════

    /** @test */
    public function admin_yang_sudah_login_dapat_mengakses_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function pemohon_tidak_dapat_mengakses_dashboard_admin(): void
    {
        $pemohon = User::factory()->create(['role' => User::ROLE_PEMOHON]);

        $response = $this->actingAs($pemohon)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    /** @test */
    public function laboran_tidak_dapat_mengakses_dashboard_admin(): void
    {
        $laboran = User::factory()->create(['role' => User::ROLE_LABORAN]);

        $response = $this->actingAs($laboran)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    /** @test */
    public function pengguna_yang_belum_login_diarahkan_ke_halaman_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function login_dengan_domain_selain_student_telkomuniversity_ditolak(): void
    {
        $response = $this->post(route('login'), [
            'email'    => 'admin@telyuji.ac.id',
            'password' => 'Telyuji#2026',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function admin_dapat_login_dengan_domain_student_telkomuniversity_dan_mengakses_dashboard(): void
    {
        $password = 'Telyuji#2026';
        $admin = User::factory()->create([
            'email'    => 'admin@student.telkomuniversity.ac.id',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role'     => User::ROLE_ADMIN,
        ]);

        $loginResponse = $this->post(route('login'), [
            'email'    => 'admin@student.telkomuniversity.ac.id',
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($admin);

        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertOk();
    }

    /** @test */
    public function laboran_dapat_login_dengan_domain_student_telkomuniversity(): void
    {
        $password = 'Telyuji#2026';
        $laboran = User::factory()->create([
            'email'    => 'laboran@student.telkomuniversity.ac.id',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role'     => User::ROLE_LABORAN,
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'laboran@student.telkomuniversity.ac.id',
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($laboran);
    }

    /** @test */
    public function pemohon_dapat_login_dengan_domain_student_telkomuniversity(): void
    {
        $password = 'Telyuji#2026';
        $pemohon = User::factory()->create([
            'email'    => 'pemohon@student.telkomuniversity.ac.id',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role'     => User::ROLE_PEMOHON,
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'pemohon@student.telkomuniversity.ac.id',
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($pemohon);
    }

    /** @test */
    public function registrasi_dengan_domain_selain_student_telkomuniversity_ditolak(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Calon Pemohon',
            'email'                 => 'calon@telyuji.ac.id',
            'password'              => 'Telyuji#2026',
            'password_confirmation' => 'Telyuji#2026',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    // ══════════════════════════════════════════════════════════════════════
    // B. PENGUJIAN RINGKASAN METRIK
    // ══════════════════════════════════════════════════════════════════════

    /** @test */
    public function dashboard_menampilkan_total_pengajuan_yang_sesuai_database(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', function (array $metrics) {
            return $metrics['total'] === 5;
        });
    }

    /** @test */
    public function dashboard_menampilkan_jumlah_antrean_aktif_yang_benar(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(3)->menunggu()->create();
        TiketPengujian::factory()->count(2)->sedangDiuji()->create();
        TiketPengujian::factory()->count(1)->selesai()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('metrics', function (array $m) {
            return $m['menunggu']     === 3
                && $m['sedang_diuji'] === 2
                && $m['selesai']      === 1
                && $m['total']        === 6;
        });
    }

    /** @test */
    public function penghitungan_metrik_tidak_ada_duplikasi(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(4)->menunggu()->create();
        TiketPengujian::factory()->count(3)->sedangDiuji()->create();
        TiketPengujian::factory()->count(2)->selesai()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('metrics', function (array $m) {
            // Jumlah per status harus tepat sama dengan total (tidak ada overlap)
            $sumBagian = $m['menunggu'] + $m['sedang_diuji'] + $m['selesai'];
            return $sumBagian === $m['total'];
        });
    }

    // ══════════════════════════════════════════════════════════════════════
    // C. PENGUJIAN GRAFIK — AKURASI DATA AGREGASI
    // ══════════════════════════════════════════════════════════════════════

    /** @test */
    public function grafik_instrumen_mengelompokkan_pengajuan_per_mesin_dengan_benar(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(3)->forInstrumen('XRF (X-Ray Fluorescence)')->create();
        TiketPengujian::factory()->count(5)->forInstrumen('SEM (Scanning Electron Microscope)')->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('usageByInstrumen', function ($collection) {
            $xrf = $collection->firstWhere('instrumen', 'XRF (X-Ray Fluorescence)');
            $sem = $collection->firstWhere('instrumen', 'SEM (Scanning Electron Microscope)');

            return $xrf && (int) $xrf->jumlah_pengajuan === 3
                && $sem && (int) $sem->jumlah_pengajuan === 5;
        });
    }

    /** @test */
    public function grafik_instrumen_diurutkan_descending(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(2)->forInstrumen('XRF (X-Ray Fluorescence)')->create();
        TiketPengujian::factory()->count(7)->forInstrumen('SEM (Scanning Electron Microscope)')->create();
        TiketPengujian::factory()->count(4)->forInstrumen('FTIR Spectroscopy')->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('usageByInstrumen', function ($collection) {
            $counts = $collection->pluck('jumlah_pengajuan')->map(fn($v) => (int)$v)->toArray();
            $sorted = $counts;
            rsort($sorted);
            return $counts === $sorted;
        });
    }

    /** @test */
    
/** @test */
    public function grafik_fakultas_mengelompokkan_pengajuan_per_fakultas_dengan_benar(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(4)
            ->forFakultas('Fakultas Teknik Elektro')->create();

        TiketPengujian::factory()->count(2)
            ->forFakultas('Fakultas Informatika')->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('usageByFakultas', function ($collection) {
            $fte = $collection->firstWhere(
                'fakultas', 'Fakultas Teknik Elektro'
            );

            $fif = $collection->firstWhere(
                'fakultas', 'Fakultas Informatika'
            );

            return $fte && (int) $fte->jumlah_pengajuan === 4
                && $fif && (int) $fif->jumlah_pengajuan === 2;
        });
    }

    /** @test */
    
/** @test */
    public function grafik_fakultas_menggabungkan_program_studi_dengan_kode_fakultas_yang_sama(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(2)
            ->forFakultas('S1 Teknik Elektro · FTE')->create();

        TiketPengujian::factory()
            ->forFakultas('S1 Teknik Telekomunikasi · FTE')->create();

        TiketPengujian::factory()
            ->forFakultas('S2 Informatika · FIF')->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('usageByFakultas', function ($collection) {
            $fte = $collection->firstWhere(
                'fakultas', 'Fakultas Teknik Elektro'
            );

            $fif = $collection->firstWhere(
                'fakultas', 'Fakultas Informatika'
            );

            return $fte && (int) $fte->jumlah_pengajuan === 3
                && $fif && (int) $fif->jumlah_pengajuan === 1;
        });
    }

    /** @test */
    public function grafik_tidak_menghasilkan_duplikasi_penghitungan(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        TiketPengujian::factory()->count(3)->forFakultas('Fakultas Teknik Elektro')->create();
        TiketPengujian::factory()->count(2)->forFakultas('Fakultas Informatika')->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('usageByFakultas', function ($collection) {
            $totalDariFakultas = $collection->sum('jumlah_pengajuan');
            return (int) $totalDariFakultas === 5; // 3 + 2 = 5, tidak lebih
        });
    }

    /** @test */
    public function tiket_tanpa_informasi_fakultas_ditangani_sebagai_tidak_diketahui(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Kolom fakultas = NOT NULL di migration, jadi tes empty string saja.
        TiketPengujian::factory()->create(['fakultas' => '']);
        TiketPengujian::factory()->create(['fakultas' => '  ']);
        TiketPengujian::factory()->create(['fakultas' => 'Fakultas Teknik Elektro']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('usageByFakultas', function ($collection) {
            // Tiket dengan fakultas kosong harus masuk ke "Tidak Diketahui"
            $tidakDiketahui = $collection->first(function ($item) {
                return str_contains(strtolower($item->fakultas ?? ''), 'tidak diketahui');
            });
            return $tidakDiketahui !== null && (int) $tidakDiketahui->jumlah_pengajuan === 2;
        });
    }

    // ══════════════════════════════════════════════════════════════════════
    // D. PENGUJIAN KONDISI KHUSUS
    // ══════════════════════════════════════════════════════════════════════

    /** @test */
    public function dashboard_tetap_bisa_dibuka_ketika_belum_ada_pengajuan(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('metrics', function (array $m) {
            return $m['total'] === 0
                && $m['menunggu'] === 0
                && $m['sedang_diuji'] === 0
                && $m['selesai'] === 0;
        });
        $response->assertViewHas('usageByInstrumen', fn($c) => $c->isEmpty());
        $response->assertViewHas('usageByFakultas',  fn($c) => $c->isEmpty());
    }

    /** @test */
    public function dashboard_menampilkan_data_meski_hanya_sebagian_mesin_memiliki_pengajuan(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Hanya 1 mesin yang memiliki pengajuan
        TiketPengujian::factory()->count(2)->forInstrumen('XRF (X-Ray Fluorescence)')->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('usageByInstrumen', function ($c) {
            return $c->count() === 1;
        });
    }

    /** @test */
    public function dashboard_tidak_error_ketika_instrumen_null_atau_kosong(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        // Tiket dengan instrumen kosong tidak boleh menyebabkan error
        TiketPengujian::factory()->create(['instrumen' => '']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    // ══════════════════════════════════════════════════════════════════════
    // E. PENGUJIAN CONTROLLER — ISOLASI LOGIKA
    // ══════════════════════════════════════════════════════════════════════

    /** @test */
    public function controller_mengirim_semua_variabel_yang_dibutuhkan_view(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHasAll(['metrics', 'usageByInstrumen', 'usageByFakultas']);
    }

    /** @test */
    public function metrik_total_selalu_sama_dengan_seluruh_tiket_di_database(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $jumlahDibuat = 7;
        TiketPengujian::factory()->count($jumlahDibuat)->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertViewHas('metrics', function (array $m) use ($jumlahDibuat) {
            return $m['total'] === $jumlahDibuat;
        });
    }

    /** @test */
    public function grafik_fakultas_menggunakan_warna_identitas_resmi_dan_bebas_badge_berlebih(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $expectedColors = [
            'Fakultas Teknik Elektro' => '#1E3A8A',
            'Fakultas Rekayasa Industri' => '#166534',
            'Fakultas Informatika' => '#D4A017',
            'Fakultas Ekonomi dan Bisnis' => '#38BDF8',
            'Fakultas Komunikasi dan Ilmu Sosial' => '#7C3AED',
            'Fakultas Industri Kreatif' => '#F97316',
            'Fakultas Ilmu Terapan' => '#84CC16',
        ];

        foreach (array_keys($expectedColors) as $fakultasName) {
            TiketPengujian::factory()->create(['fakultas' => $fakultasName]);
        }

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertDontSee('Dashboard Analitik')
            ->assertDontSee('>Bar Chart<', false)
            ->assertDontSee('>Doughnut<', false)
            ->assertViewHas('usageByFakultas', function ($collection) use ($expectedColors) {
                $byName = $collection->keyBy('fakultas');
                foreach ($expectedColors as $name => $hex) {
                    if (!isset($byName[$name]) || $byName[$name]->warna !== $hex) {
                        return false;
                    }
                }

                return true;
            });
    }
}