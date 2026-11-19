<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi01ProfilDanValidasiKtmDuskTest extends DuskTestCase
{
    /**
     * File gambar sementara untuk pengujian unggah berkas KTM.
     */
    private static $sampleKtmPath;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Buat file PNG valid sementara untuk pengujian attach file
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ktm_test_' . time() . '.png';
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor(200, 120);
            $bg = imagecolorallocate($im, 128, 0, 0);
            imagefill($im, 0, 0, $bg);
            imagepng($im, $tempPath);
            imagedestroy($im);
        } else {
            file_put_contents($tempPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
        }
        self::$sampleKtmPath = $tempPath;
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$sampleKtmPath && file_exists(self::$sampleKtmPath)) {
            @unlink(self::$sampleKtmPath);
        }
        parent::tearDownAfterClass();
    }

    /**
     * Skenario 1 (PBI-01 Sisi Pemohon):
     * Pemohon login, membuka form profil akademik, mengisi nama, NIM, memilih
     * Fakultas & Prodi secara dinamis, mengunggah foto KTM/ID, dan menyimpan perubahan.
     */
    public function testPemohonCanUpdateAcademicProfileAndUploadKtm()
    {
        $pemohon = User::updateOrCreate(
            ['email' => 'syalfa.pemohon@telkomuniversity.ac.id'],
            [
                'name' => 'Syalfa Oktaviana',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
                'status_validasi_ktm' => 'Belum Tervalidasi',
            ]
        );

        $this->browse(function (Browser $browser) use ($pemohon) {
            // 1. Kunjungi halaman profil pemohon
            $browser->loginAs($pemohon)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas')
                ->assertPresent('@input-nama-lengkap')
                ->assertPresent('@input-nomor-induk')
                ->assertPresent('@input-email-institusi')
                ->assertPresent('@select-fakultas')
                ->assertPresent('@select-prodi')
                ->assertPresent('@btn-submit-profil')
                ->assertPresent('@pill-status-validasi')
                ->assertSee('Portal Mahasiswa');

            // 2. Isi data profil akademik
            $browser->type('@input-nama-lengkap', 'Syalfa Oktaviana Wijaya')
                ->type('@input-nomor-induk', '102022400228')
                ->select('@select-fakultas', 'Fakultas Rekayasa Industri (FRI)')
                ->pause(300) // Beri jeda sejenak untuk event onchange memuat opsi prodi via JS
                ->select('@select-prodi', 'S1 Sistem Informasi');

            // 3. Lampirkan berkas KTM/ID
            if (self::$sampleKtmPath && file_exists(self::$sampleKtmPath)) {
                $browser->attach('@foto-identitas-input', self::$sampleKtmPath);
            }

            // 4. Klik submit form profil
            $browser->script("document.querySelector('[dusk=\"btn-submit-profil\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-submit-profil\"]').click();");

            // 5. Verifikasi respon sukses dan status profil
            $browser->waitForText('Profil akademik berhasil diperbarui.')
                ->assertSee('Syalfa Oktaviana Wijaya')
                ->assertSee('102022400228')
                ->assertSee('Belum Tervalidasi');
        });
    }

    /**
     * Skenario 2 (PBI-01 Sisi Laboran):
     * Petugas Laboran login, membuka panel validasi KTM, memeriksa antrean pemohon
     * yang membutuhkan verifikasi, melihat berkas, dan menyetujui (approve) validasi KTM.
     */
    public function testLaboranCanInspectAndApprovePemohonKtm()
    {
        $pemohon = User::where('email', 'syalfa.pemohon@telkomuniversity.ac.id')->first();
        if (!$pemohon) {
            $pemohon = User::create([
                'name' => 'Syalfa Oktaviana Wijaya',
                'nama_lengkap' => 'Syalfa Oktaviana Wijaya',
                'email' => 'syalfa.pemohon@telkomuniversity.ac.id',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
                'nomor_induk' => '102022400228',
                'fakultas_id' => 'Fakultas Rekayasa Industri (FRI)',
                'prodi_id' => 'S1 Sistem Informasi',
                'foto_identitas' => 'identitas/dummy_ktm.png',
                'status_validasi_ktm' => 'Belum Tervalidasi',
            ]);
        }

        // Pastikan foto_identitas terisi agar tombol aksi Laboran muncul
        if (empty($pemohon->foto_identitas)) {
            $pemohon->update([
                'foto_identitas' => 'identitas/dummy_ktm.png',
                'status_validasi_ktm' => 'Belum Tervalidasi',
            ]);
        }

        $laboran = User::updateOrCreate(
            ['email' => 'laboran@telkomuniversity.ac.id'],
            [
                'name' => 'Raka Pratama, S.T.',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_LABORAN,
            ]
        );

        $this->browse(function (Browser $browser) use ($laboran, $pemohon) {
            // 1. Laboran membuka panel Validasi KTM
            $browser->loginAs($laboran)
                ->visit('/laboran/validasi-ktm')
                ->waitForText('Validasi Identitas & KTM')
                ->assertPresent('@table-validasi-ktm')
                ->assertPresent('@tab-semua')
                ->assertPresent('@tab-menunggu')
                ->assertPresent('@tab-tervalidasi')
                ->assertPresent('@tab-ditolak')
                ->assertSee('Daftar Pemohon & Berkas Identitas')
                ->waitFor("@row-pemohon-{$pemohon->id}");

            // 2. Verifikasi data pemohon terlihat di tabel
            $browser->assertSeeIn("@row-pemohon-{$pemohon->id}", 'Syalfa Oktaviana Wijaya')
                ->assertSeeIn("@row-pemohon-{$pemohon->id}", '102022400228')
                ->assertPresent("@btn-setujui-{$pemohon->id}")
                ->assertPresent("@btn-tolak-{$pemohon->id}");

            // 3. Laboran mengklik tombol Setujui (Approve)
            $browser->script("window.confirm = function() { return true; };");
            $browser->script("document.querySelector('[dusk=\"btn-setujui-{$pemohon->id}\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-setujui-{$pemohon->id}\"]').click();");

            // 4. Verifikasi status pemohon berubah menjadi Tervalidasi
            $browser->waitForText('berhasil divalidasi')
                ->assertSeeIn("@status-badge-{$pemohon->id}", 'Tervalidasi');
        });
    }

    /**
     * Skenario 3 (Verifikasi End-to-End Status Pemohon):
     * Pemohon membuka kembali halaman profilnya dan melihat bahwa identitas
     * serta statusnya sudah berstatus "Tervalidasi" dengan badge hijau.
     */
    public function testPemohonStatusReflectsTervalidasiAfterLaboranApproval()
    {
        $pemohon = User::where('email', 'syalfa.pemohon@telkomuniversity.ac.id')->first();
        if ($pemohon) {
            $pemohon->update(['status_validasi_ktm' => 'Tervalidasi']);
        }

        $this->browse(function (Browser $browser) use ($pemohon) {
            $browser->loginAs($pemohon)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas')
                ->assertPresent('@pill-status-validasi')
                ->assertSeeIn('@pill-status-validasi', 'Tervalidasi')
                ->assertSee('Identitas Anda telah diverifikasi oleh Laboran.');
        });
    }

    /**
     * Skenario 4 (PBI-01 Sisi Laboran Penolakan Berkas):
     * Laboran dapat menolak berkas KTM yang tidak valid dan pemohon melihat status "Ditolak".
     */
    public function testLaboranCanRejectInvalidKtm()
    {
        $pemohonDitolak = User::updateOrCreate(
            ['email' => 'pemohon.buram@telkomuniversity.ac.id'],
            [
                'name' => 'Budi Santoso',
                'nama_lengkap' => 'Budi Santoso',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
                'nomor_induk' => '102022400999',
                'fakultas_id' => 'Fakultas Informatika (FIF)',
                'prodi_id' => 'S1 Informatika',
                'foto_identitas' => 'identitas/buram.jpg',
                'status_validasi_ktm' => 'Belum Tervalidasi',
            ]
        );

        $laboran = User::where('email', 'laboran@telkomuniversity.ac.id')->first();

        $this->browse(function (Browser $browser) use ($laboran, $pemohonDitolak) {
            // 1. Laboran menolak berkas pemohon
            $browser->loginAs($laboran)
                ->visit('/laboran/validasi-ktm')
                ->waitFor("@row-pemohon-{$pemohonDitolak->id}")
                ->script("window.confirm = function() { return true; };");

            $browser->script("document.querySelector('[dusk=\"btn-tolak-{$pemohonDitolak->id}\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-tolak-{$pemohonDitolak->id}\"]').click();");

            $browser->waitForText('telah ditolak')
                ->assertSeeIn("@status-badge-{$pemohonDitolak->id}", 'Ditolak');

            // 2. Pemohon mengecek halaman profil dan melihat status Ditolak
            $browser->loginAs($pemohonDitolak)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas')
                ->assertPresent('@pill-status-validasi')
                ->assertSeeIn('@pill-status-validasi', 'Ditolak')
                ->assertSee('Validasi KTM ditolak. Silakan unggah ulang foto KTM yang valid.');
        });
    }
}
