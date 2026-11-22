<?php

namespace Tests\Browser;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi01ProfilDanValidasiKtmDuskTest extends DuskTestCase
{
    private static $sampleKtmPath;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

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

    private function getOrCreateDuskUser(string $email, string $role, array $attributes = []): User
    {
        if (!empty($attributes['nomor_induk'])) {
            User::where('nomor_induk', $attributes['nomor_induk'])
                ->where('email', '!=', $email)
                ->update(['nomor_induk' => null]);
        }

        return User::updateOrCreate(
            ['email' => $email],
            array_merge([
                'name' => ucfirst($role) . ' Uji Telyuji',
                'nama_lengkap' => ucfirst($role) . ' Uji Telyuji',
                'password' => Hash::make('Telyuji#2026'),
                'role' => $role,
            ], $attributes)
        );
    }

    private function getActiveFakultasAndProdi(): array
    {
        if (class_exists(Fakultas::class) && method_exists(Fakultas::class, 'seedDefaultData') && Fakultas::count() === 0) {
            Fakultas::seedDefaultData();
        }

        $prodi = Prodi::with('fakultas')->first();
        $fakultas = $prodi ? $prodi->fakultas : Fakultas::first();

        return [$fakultas, $prodi];
    }

    /**
     * Skenario 1 (PBI-01 Sisi Pemohon):
     * Pemohon melengkapi data profil akademik & mengunggah foto KTM.
     */
    public function testPemohonCanUpdateAcademicProfileAndUploadKtm()
    {
        [$fakultas, $prodi] = $this->getActiveFakultasAndProdi();

        $namaUji = 'Pemohon Akademik Uji';
        $nimUji = '102022400228';

        // Pastikan nomor_induk uji tidak bentrok dengan akun lain
        User::where('nomor_induk', $nimUji)
            ->where('email', '!=', 'pemohon.pbi01.dusk@telkomuniversity.ac.id')
            ->update(['nomor_induk' => null]);

        // Pastikan profil pemohon dalam kondisi awal belum terisi KTM
        $pemohon = $this->getOrCreateDuskUser('pemohon.pbi01.dusk@telkomuniversity.ac.id', User::ROLE_PEMOHON, [
            'name' => 'Pemohon Uji Telyuji',
            'nama_lengkap' => 'Pemohon Uji Telyuji',
            'nomor_induk' => null,
            'fakultas_id' => null,
            'prodi_id' => null,
            'foto_identitas' => null,
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ]);

        $this->browse(function (Browser $browser) use ($pemohon, $fakultas, $prodi, $namaUji, $nimUji) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($pemohon)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas', 15)
                ->assertPresent('@input-nama-lengkap')
                ->assertPresent('@input-nomor-induk')
                ->assertPresent('@input-email-institusi')
                ->assertPresent('@select-fakultas')
                ->assertPresent('@select-prodi')
                ->assertPresent('@input-foto-identitas')
                ->assertPresent('@btn-submit-profil')
                ->assertPresent('@pill-status-validasi');

            $browser->clear('@input-nama-lengkap')
                ->type('@input-nama-lengkap', $namaUji)
                ->clear('@input-nomor-induk')
                ->type('@input-nomor-induk', $nimUji);

            if ($fakultas) {
                $browser->select('@select-fakultas', $fakultas->nama_fakultas)
                    ->waitUntil("document.querySelectorAll('#prodi_id option').length > 1", 10);
            }

            if ($prodi) {
                $browser->select('@select-prodi', $prodi->nama_prodi);
            }

            if (self::$sampleKtmPath && file_exists(self::$sampleKtmPath)) {
                $browser->attach('@input-foto-identitas', self::$sampleKtmPath);
            }

            $browser->script("document.querySelector('[dusk=\"btn-submit-profil\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-submit-profil\"]').click();");

            $browser->waitForText('Profil akademik berhasil diperbarui.', 15)
                ->assertInputValue('@input-nama-lengkap', $namaUji)
                ->assertInputValue('@input-nomor-induk', $nimUji)
                ->assertPresent('@berkas-preview')
                ->assertSeeIn('@pill-status-validasi', 'Belum Tervalidasi');
        });
    }

    /**
     * Skenario 2 (PBI-01 Sisi Laboran - Approve):
     * Laboran melihat daftar berkas identitas pemohon dan menyetujui KTM.
     */
    public function testLaboranCanInspectAndApprovePemohonKtm()
    {
        [$fakultas, $prodi] = $this->getActiveFakultasAndProdi();

        $pemohon = $this->getOrCreateDuskUser('pemohon.pbi01.dusk@telkomuniversity.ac.id', User::ROLE_PEMOHON, [
            'name' => 'Pemohon Akademik Uji',
            'nama_lengkap' => 'Pemohon Akademik Uji',
            'nomor_induk' => '102022400228',
            'fakultas_id' => $fakultas ? $fakultas->nama_fakultas : null,
            'prodi_id' => $prodi ? $prodi->nama_prodi : null,
            'foto_identitas' => 'identitas/dummy_ktm.png',
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ]);

        $laboran = $this->getOrCreateDuskUser('laboran.pbi01.dusk@telkomuniversity.ac.id', User::ROLE_LABORAN, [
            'name' => 'Laboran Validasi Uji',
            'nama_lengkap' => 'Laboran Validasi Uji',
        ]);

        $this->browse(function (Browser $browser) use ($laboran, $pemohon) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($laboran)
                ->visit('/laboran/validasi-ktm?q=' . urlencode($pemohon->email))
                ->waitForText('Validasi Identitas & KTM', 15)
                ->assertPresent('@table-validasi-ktm')
                ->assertPresent('@tab-semua')
                ->assertPresent('@tab-menunggu')
                ->assertPresent('@tab-tervalidasi')
                ->assertPresent('@tab-ditolak')
                ->waitFor("@row-pemohon-{$pemohon->id}", 15);

            $browser->assertSeeIn("@row-pemohon-{$pemohon->id}", $pemohon->nama_lengkap ?? $pemohon->name)
                ->assertPresent("@btn-setujui-{$pemohon->id}")
                ->assertPresent("@btn-tolak-{$pemohon->id}");

            $browser->script("window.confirm = function() { return true; };");
            $browser->script("document.querySelector('[dusk=\"btn-setujui-{$pemohon->id}\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-setujui-{$pemohon->id}\"]').click();");

            $browser->waitForText('berhasil divalidasi', 15)
                ->waitFor("@status-badge-{$pemohon->id}", 10)
                ->assertSeeIn("@status-badge-{$pemohon->id}", 'Tervalidasi');
        });
    }

    /**
     * Skenario 3 (Verifikasi End-to-End Status Pemohon):
     * Halaman profil pemohon menampilkan status Tervalidasi setelah disetujui Laboran.
     */
    public function testPemohonStatusReflectsTervalidasiAfterLaboranApproval()
    {
        $pemohon = $this->getOrCreateDuskUser('pemohon.pbi01.dusk@telkomuniversity.ac.id', User::ROLE_PEMOHON, [
            'status_validasi_ktm' => 'Tervalidasi',
        ]);

        $this->browse(function (Browser $browser) use ($pemohon) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($pemohon)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas', 15)
                ->assertPresent('@pill-status-validasi')
                ->assertSeeIn('@pill-status-validasi', 'Tervalidasi')
                ->assertSee('Identitas Anda telah diverifikasi oleh Laboran.');
        });
    }

    /**
     * Skenario 4 (PBI-01 Sisi Laboran - Penolakan Berkas):
     * Laboran menolak berkas KTM tidak valid dan status pemohon berubah menjadi Ditolak.
     */
    public function testLaboranCanRejectInvalidKtm()
    {
        [$fakultas, $prodi] = $this->getActiveFakultasAndProdi();

        $pemohonDitolak = $this->getOrCreateDuskUser('pemohon.pbi01.reject.dusk@telkomuniversity.ac.id', User::ROLE_PEMOHON, [
            'name' => 'Pemohon Tolak Uji',
            'nama_lengkap' => 'Pemohon Tolak Uji',
            'nomor_induk' => '102022400999',
            'fakultas_id' => $fakultas ? $fakultas->nama_fakultas : null,
            'prodi_id' => $prodi ? $prodi->nama_prodi : null,
            'foto_identitas' => 'identitas/buram.jpg',
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ]);

        $laboran = $this->getOrCreateDuskUser('laboran.pbi01.dusk@telkomuniversity.ac.id', User::ROLE_LABORAN, [
            'name' => 'Laboran Validasi Uji',
            'nama_lengkap' => 'Laboran Validasi Uji',
        ]);

        $this->browse(function (Browser $browser) use ($laboran, $pemohonDitolak) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($laboran)
                ->visit('/laboran/validasi-ktm?q=' . urlencode($pemohonDitolak->email))
                ->waitFor("@row-pemohon-{$pemohonDitolak->id}", 15)
                ->script("window.confirm = function() { return true; };");

            $browser->script("document.querySelector('[dusk=\"btn-tolak-{$pemohonDitolak->id}\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-tolak-{$pemohonDitolak->id}\"]').click();");

            $browser->waitForText('telah ditolak', 15)
                ->waitFor("@status-badge-{$pemohonDitolak->id}", 10)
                ->assertSeeIn("@status-badge-{$pemohonDitolak->id}", 'Ditolak');

            // Reset sesi sebelum beralih login ke pemohon
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($pemohonDitolak)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas', 15)
                ->assertPresent('@pill-status-validasi')
                ->assertSeeIn('@pill-status-validasi', 'Ditolak')
                ->assertSee('Validasi KTM ditolak. Silakan unggah ulang foto KTM yang valid.');
        });
    }
}
