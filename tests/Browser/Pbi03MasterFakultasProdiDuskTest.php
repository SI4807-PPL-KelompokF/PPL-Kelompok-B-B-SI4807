<?php

namespace Tests\Browser;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi03MasterFakultasProdiDuskTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists(Fakultas::class) && method_exists(Fakultas::class, 'seedDefaultData')) {
            Fakultas::seedDefaultData();
        }
    }

    private function getAdminUser(): User
    {
        return User::where('role', User::ROLE_ADMIN)->first()
            ?? User::create([
                'name' => 'Administrator Telyuji',
                'nama_lengkap' => 'Administrator Telyuji',
                'email' => 'admin.dusk@telyuji.ac.id',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_ADMIN,
            ]);
    }

    /**
     * Helper klik elemen via JS setelah scrollIntoView agar bebas dari ElementClickInterceptedException.
     */
    private function jsClick(Browser $browser, string $duskSelector): void
    {
        $name = ltrim($duskSelector, '@');
        $browser->script("
            const el = document.querySelector('[dusk=\"{$name}\"]');
            if (el) {
                el.scrollIntoView({block: 'center', behavior: 'instant'});
                el.click();
            }
        ");
    }

    public function testAdminCanViewMasterFakultasProdiDashboardAndElements()
    {
        $admin = $this->getAdminUser();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Kelola Master Data Fakultas & Program Studi', 10)
                ->assertSee('Portal Admin')
                ->assertSee('Master Data Fakultas')
                ->assertSee('Master Data Program Studi')
                ->assertPresent('@pill-total-fakultas')
                ->assertPresent('@pill-total-prodi')
                ->assertPresent('@table-fakultas')
                ->assertPresent('@table-prodi')
                ->assertPresent('@btn-tambah-fakultas')
                ->assertPresent('@btn-tambah-prodi');
        });
    }

    public function testAdminCanCreateUpdateAndDeleteFakultas()
    {
        $admin = $this->getAdminUser();

        $kodeFakultasBaru = 'FTU-TEST';
        $namaFakultasBaru = 'Fakultas Teknik Terapan Uji';
        $namaFakultasEdit = 'Fakultas Teknik Terapan Edit';

        Fakultas::where('kode_fakultas', $kodeFakultasBaru)->delete();

        $this->browse(function (Browser $browser) use ($admin, $kodeFakultasBaru, $namaFakultasBaru, $namaFakultasEdit) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Master Data Fakultas', 10);

            $this->jsClick($browser, '@btn-tambah-fakultas');

            $browser->waitFor('@modal-tambah-fakultas', 10)
                ->pause(300)
                ->type('@input-tambah-kode-fakultas', $kodeFakultasBaru)
                ->type('@input-tambah-nama-fakultas', $namaFakultasBaru);

            $this->jsClick($browser, '@btn-submit-tambah-fakultas');

            $browser->waitForText('Fakultas berhasil ditambahkan.', 10)
                ->assertSee($kodeFakultasBaru)
                ->assertSee($namaFakultasBaru);

            $fakultas = Fakultas::where('kode_fakultas', $kodeFakultasBaru)->first();
            $this->assertNotNull($fakultas, 'Fakultas baru harus tersimpan di database.');

            $this->jsClick($browser, "@btn-edit-fakultas-{$fakultas->id}");

            $browser->waitFor('@modal-edit-fakultas', 10)
                ->pause(300)
                ->clear('@input-edit-nama-fakultas')
                ->type('@input-edit-nama-fakultas', $namaFakultasEdit);

            $this->jsClick($browser, '@btn-submit-edit-fakultas');

            $browser->waitForText('Fakultas berhasil diperbarui.', 10)
                ->assertSee($namaFakultasEdit);

            $browser->script("window.confirm = function() { return true; };");
            $this->jsClick($browser, "@btn-hapus-fakultas-{$fakultas->id}");

            $browser->waitForText('Fakultas berhasil dihapus.', 10)
                ->assertDontSee($kodeFakultasBaru);
        });
    }

    public function testAdminCanCreateUpdateAndDeleteProgramStudi()
    {
        $admin = $this->getAdminUser();
        $fakultasInduk = Fakultas::first();

        $kodeProdiBaru = 'DSN-TEST';
        $namaProdiBaru = 'S1 Desain Eksperimental Uji';
        $namaProdiEdit = 'S1 Desain Eksperimental Edit';

        Prodi::where('kode_prodi', $kodeProdiBaru)->delete();

        $this->browse(function (Browser $browser) use ($admin, $fakultasInduk, $kodeProdiBaru, $namaProdiBaru, $namaProdiEdit) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Master Data Program Studi', 10);

            // Klik via JS agar tidak terhalang sticky header / scroll viewport
            $this->jsClick($browser, '@btn-tambah-prodi');

            $browser->waitFor('@modal-tambah-prodi', 10)
                ->pause(300)
                ->select('@select-tambah-prodi-fakultas', (string) $fakultasInduk->id)
                ->type('@input-tambah-kode-prodi', $kodeProdiBaru)
                ->type('@input-tambah-nama-prodi', $namaProdiBaru);

            $this->jsClick($browser, '@btn-submit-tambah-prodi');

            $browser->waitForText('Program Studi berhasil ditambahkan.', 10)
                ->assertSee($kodeProdiBaru)
                ->assertSee($namaProdiBaru);

            $prodi = Prodi::where('kode_prodi', $kodeProdiBaru)->first();
            $this->assertNotNull($prodi, 'Program studi baru harus tersimpan di database.');

            $this->jsClick($browser, "@btn-edit-prodi-{$prodi->id}");

            $browser->waitFor('@modal-edit-prodi', 10)
                ->pause(300)
                ->clear('@input-edit-nama-prodi')
                ->type('@input-edit-nama-prodi', $namaProdiEdit);

            $this->jsClick($browser, '@btn-submit-edit-prodi');

            $browser->waitForText('Program Studi berhasil diperbarui.', 10)
                ->assertSee($namaProdiEdit);

            $browser->script("window.confirm = function() { return true; };");
            $this->jsClick($browser, "@btn-hapus-prodi-{$prodi->id}");

            $browser->waitForText('Program Studi berhasil dihapus.', 10)
                ->assertDontSee($kodeProdiBaru);
        });
    }

    public function testAdminCanSearchAndFilterFakultasAndProdi()
    {
        $admin = $this->getAdminUser();
        $fakultasSample = Fakultas::first();

        $this->browse(function (Browser $browser) use ($admin, $fakultasSample) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Kelola Master Data Fakultas & Program Studi', 10);

            if ($fakultasSample) {
                $browser->type('@input-search-fakultas', $fakultasSample->kode_fakultas)
                    ->keys('@input-search-fakultas', '{enter}')
                    ->waitForLocation('/admin/master-fakultas-prodi')
                    ->assertSee($fakultasSample->nama_fakultas);

                $browser->select('@select-filter-fakultas', (string) $fakultasSample->id)
                    ->waitForLocation('/admin/master-fakultas-prodi')
                    ->assertSee($fakultasSample->nama_fakultas);
            }
        });
    }
}