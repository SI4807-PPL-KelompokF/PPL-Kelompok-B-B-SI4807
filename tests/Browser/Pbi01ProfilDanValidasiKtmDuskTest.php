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

    private function getOrCreateUserByRole(string $role, array $defaults = []): User
    {
        $user = User::where('role', $role)->first();

        if ($user) {
            if (!empty($defaults)) {
                $user->update($defaults);
            }
            return $user->fresh();
        }

        return User::create(array_merge([
            'name' => ucfirst($role) . ' Uji Telyuji',
            'nama_lengkap' => ucfirst($role) . ' Uji Telyuji',
            'email' => $role . '.dusk@telyuji.ac.id',
            'password' => Hash::make('Telyuji#2026'),
            'role' => $role,
        ], $defaults));
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
     * Skenario 1 (PBI-01 Sisi Pemohon)
     */
    public function testPemohonCanUpdateAcademicProfileAndUploadKtm()
    {
        [$fakultas, $prodi] = $this->getActiveFakultasAndProdi();

        // Pastikan profil pemohon dalam kondisi awal belum terisi KTM
        $pemohon = $this->getOrCreateUserByRole(User::ROLE_PEMOHON, [
            'foto_identitas' => null,
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ]);

        $namaUji = 'Pemohon Akademik Uji';
        $nimUji = '102022400228';

        $this->browse(function (Browser $browser) use ($pemohon, $fakultas, $prodi, $namaUji, $nimUji) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($pemohon)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas', 10)
                ->assertPresent('@input-nama-lengkap')
                ->assertPresent('@input-nomor-induk')
                ->assertPresent('@input-email-institusi')
                ->assertPresent('@select-fakultas')
                ->assertPresent('@select-prodi')
                ->assertPresent('@btn-submit-profil')
                ->assertPresent('@pill-status-validasi');

            $browser->clear('@input-nama-lengkap')
                ->type('@input-nama-lengkap', $namaUji)
                ->clear('@input-nomor-induk')
                ->type('@input-nomor-induk', $nimUji);

            if ($fakultas) {
                $browser->select('@select-fakultas', (string) $fakultas->id)
                    ->pause(400);
            }

            if ($prodi) {
                $browser->select('@select-prodi', (string) $prodi->id);
            }

            // Cek apakah atribut dusk="@foto-identitas-input" atau input[name="foto_identitas"] tersedia
            if (self::$sampleKtmPath && file_exists(self::$sampleKtmPath)) {
                $hasDuskAttr = $browser->script("return !!document.querySelector('[dusk=\"foto-identitas-input\"]');")[0] ?? false;
                if ($hasDuskAttr) {
                    $browser->attach('@foto-identitas-input', self::$sampleKtmPath);
                } else {
                    $browser->attach('input[name="foto_identitas"]', self::$sampleKtmPath);
                }
            }

            $browser->script("document.querySelector('[dusk=\"btn-submit-profil\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-submit-profil\"]').click();");

            $browser->waitForText('Profil akademik berhasil diperbarui.', 10)
                ->assertSee($namaUji)
                ->assertSee($nimUji)
                ->assertSee('Belum Tervalidasi');
        });
    }

    /**
     * Skenario 2 (PBI-01 Sisi Laboran - Approve)
     */
    public function testLaboranCanInspectAndApprovePemohonKtm()
    {
        [$fakultas, $prodi] = $this->getActiveFakultasAndProdi();

        $pemohon = $this->getOrCreateUserByRole(User::ROLE_PEMOHON, [
            'nama_lengkap' => 'Pemohon Akademik Uji',
            'nomor_induk' => '102022400228',
            'fakultas_id' => $fakultas ? $fakultas->id : null,
            'prodi_id' => $prodi ? $prodi->id : null,
            'foto_identitas' => 'identitas/dummy_ktm.png',
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ]);

        $laboran = $this->getOrCreateUserByRole(User::ROLE_LABORAN);

        $this->browse(function (Browser $browser) use ($laboran, $pemohon) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($laboran)
                ->visit('/laboran/validasi-ktm')
                ->waitForText('Validasi Identitas & KTM', 10)
                ->assertPresent('@table-validasi-ktm')
                ->assertPresent('@tab-semua')
                ->assertPresent('@tab-menunggu')
                ->assertPresent('@tab-tervalidasi')
                ->assertPresent('@tab-ditolak')
                ->waitFor("@row-pemohon-{$pemohon->id}", 10);

            $browser->assertSeeIn("@row-pemohon-{$pemohon->id}", $pemohon->nama_lengkap ?? $pemohon->name)
                ->assertPresent("@btn-setujui-{$pemohon->id}")
                ->assertPresent("@btn-tolak-{$pemohon->id}");

            $browser->script("window.confirm = function() { return true; };");
            $browser->script("document.querySelector('[dusk=\"btn-setujui-{$pemohon->id}\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-setujui-{$pemohon->id}\"]').click();");

            $browser->waitForText('berhasil divalidasi', 10)
                ->assertSeeIn("@status-badge-{$pemohon->id}", 'Tervalidasi');
        });
    }

    /**
     * Skenario 3 (Verifikasi End-to-End Status Pemohon)
     */
    public function testPemohonStatusReflectsTervalidasiAfterLaboranApproval()
    {
        $pemohon = $this->getOrCreateUserByRole(User::ROLE_PEMOHON, [
            'status_validasi_ktm' => 'Tervalidasi',
        ]);

        $this->browse(function (Browser $browser) use ($pemohon) {
            // Bersihkan cookie & sesi browser sebelumnya agar tidak terjadi session lock di server CI
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($pemohon)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas', 10)
                ->assertPresent('@pill-status-validasi')
                ->assertSeeIn('@pill-status-validasi', 'Tervalidasi')
                ->assertSee('Identitas Anda telah diverifikasi oleh Laboran.');
        });
    }

    /**
     * Skenario 4 (PBI-01 Sisi Laboran - Penolakan Berkas)
     */
    public function testLaboranCanRejectInvalidKtm()
    {
        [$fakultas, $prodi] = $this->getActiveFakultasAndProdi();

        $pemohonDitolak = User::where('role', User::ROLE_PEMOHON)->skip(1)->first()
            ?? $this->getOrCreateUserByRole(User::ROLE_PEMOHON);

        $pemohonDitolak->update([
            'nama_lengkap' => $pemohonDitolak->nama_lengkap ?: $pemohonDitolak->name,
            'nomor_induk' => $pemohonDitolak->nomor_induk ?: '102022400999',
            'fakultas_id' => $fakultas ? $fakultas->id : $pemohonDitolak->fakultas_id,
            'prodi_id' => $prodi ? $prodi->id : $pemohonDitolak->prodi_id,
            'foto_identitas' => 'identitas/buram.jpg',
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ]);

        $laboran = $this->getOrCreateUserByRole(User::ROLE_LABORAN);

        $this->browse(function (Browser $browser) use ($laboran, $pemohonDitolak) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($laboran)
                ->visit('/laboran/validasi-ktm')
                ->waitFor("@row-pemohon-{$pemohonDitolak->id}", 10)
                ->script("window.confirm = function() { return true; };");

            $browser->script("document.querySelector('[dusk=\"btn-tolak-{$pemohonDitolak->id}\"]').scrollIntoView({block: 'center', behavior: 'instant'});");
            $browser->script("document.querySelector('[dusk=\"btn-tolak-{$pemohonDitolak->id}\"]').click();");

            $browser->waitForText('telah ditolak', 10)
                ->waitFor("@status-badge-{$pemohonDitolak->id}", 10)
                ->assertSeeIn("@status-badge-{$pemohonDitolak->id}", 'Ditolak');

            // Reset sesi sebelum beralih login ke pemohon
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($pemohonDitolak)
                ->visit('/profil')
                ->waitForText('Profil Akademik & Validasi Identitas', 10)
                ->assertPresent('@pill-status-validasi')
                ->assertSeeIn('@pill-status-validasi', 'Ditolak')
                ->assertSee('Validasi KTM ditolak. Silakan unggah ulang foto KTM yang valid.');
        });
    }
}