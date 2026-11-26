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
        return User::updateOrCreate(
            ['email' => 'admin.pbi03.dusk@telkomuniversity.ac.id'],
            [
                'name' => 'Administrator Telyuji',
                'nama_lengkap' => 'Administrator Telyuji',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_ADMIN,
            ]
        );
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
                ->waitForText('Kelola Master Data Fakultas & Program Studi', 15)
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

        Fakultas::where('kode_fakultas', $kodeFakultasBaru)
            ->orWhereIn('nama_fakultas', [$namaFakultasBaru, $namaFakultasEdit])
            ->delete();

        try {
            $this->browse(function (Browser $browser) use ($admin, $kodeFakultasBaru, $namaFakultasBaru, $namaFakultasEdit) {
                $browser->driver->manage()->deleteAllCookies();

                $browser->loginAs($admin)
                    ->visit('/admin/master-fakultas-prodi')
                    ->waitForText('Master Data Fakultas', 15);

                $this->jsClick($browser, '@btn-tambah-fakultas');

                $browser->waitFor('@modal-tambah-fakultas', 10)
                    ->pause(250)
                    ->type('@input-tambah-kode-fakultas', $kodeFakultasBaru)
                    ->type('@input-tambah-nama-fakultas', $namaFakultasBaru);

                $this->jsClick($browser, '@btn-submit-tambah-fakultas');

                $browser->waitForText("Fakultas {$namaFakultasBaru} ({$kodeFakultasBaru}) berhasil ditambahkan.", 15);

                $fakultas = Fakultas::where('kode_fakultas', $kodeFakultasBaru)->first();
                $this->assertNotNull($fakultas, 'Fakultas baru harus tersimpan di database.');

                $browser->visit('/admin/master-fakultas-prodi?search_fakultas=' . urlencode($kodeFakultasBaru))
                    ->waitFor("@row-fakultas-{$fakultas->id}", 15)
                    ->assertSeeIn('@table-fakultas', $kodeFakultasBaru)
                    ->assertSeeIn('@table-fakultas', $namaFakultasBaru);

                $this->jsClick($browser, "@btn-edit-fakultas-{$fakultas->id}");

                $browser->waitFor('@modal-edit-fakultas', 10)
                    ->pause(250)
                    ->clear('@input-edit-nama-fakultas')
                    ->type('@input-edit-nama-fakultas', $namaFakultasEdit);

                $this->jsClick($browser, '@btn-submit-edit-fakultas');

                $browser->waitForText("Data Fakultas {$namaFakultasEdit} berhasil diperbarui.", 15);

                $browser->visit('/admin/master-fakultas-prodi?search_fakultas=' . urlencode($kodeFakultasBaru))
                    ->waitFor("@row-fakultas-{$fakultas->id}", 15)
                    ->assertSeeIn('@table-fakultas', $namaFakultasEdit);

                $browser->script("window.confirm = function() { return true; };");
                $this->jsClick($browser, "@btn-hapus-fakultas-{$fakultas->id}");

                $browser->waitForText("Fakultas {$namaFakultasEdit} berhasil dihapus dari master data.", 15)
                    ->assertDontSeeIn('@table-fakultas', $kodeFakultasBaru);
            });
        } finally {
            Fakultas::where('kode_fakultas', $kodeFakultasBaru)
                ->orWhereIn('nama_fakultas', [$namaFakultasBaru, $namaFakultasEdit])
                ->delete();
        }
    }

    public function testAdminCanCreateUpdateAndDeleteProgramStudi()
    {
        $admin = $this->getAdminUser();
        $fakultasInduk = Fakultas::first();

        $kodeProdiBaru = 'DSN-TEST';
        $namaProdiBaru = 'S1 Desain Eksperimental Uji';
        $namaProdiEdit = 'S1 Desain Eksperimental Edit';

        Prodi::where('kode_prodi', $kodeProdiBaru)
            ->orWhereIn('nama_prodi', [$namaProdiBaru, $namaProdiEdit])
            ->delete();

        try {
            $this->browse(function (Browser $browser) use ($admin, $fakultasInduk, $kodeProdiBaru, $namaProdiBaru, $namaProdiEdit) {
                $browser->driver->manage()->deleteAllCookies();

                $browser->loginAs($admin)
                    ->visit('/admin/master-fakultas-prodi')
                    ->waitForText('Master Data Program Studi', 15);

                $this->jsClick($browser, '@btn-tambah-prodi');

                $browser->waitFor('@modal-tambah-prodi', 10)
                    ->pause(250)
                    ->select('@select-tambah-prodi-fakultas', (string) $fakultasInduk->id)
                    ->type('@input-tambah-kode-prodi', $kodeProdiBaru)
                    ->type('@input-tambah-nama-prodi', $namaProdiBaru);

                $this->jsClick($browser, '@btn-submit-tambah-prodi');

                $browser->waitForText("Program Studi {$namaProdiBaru} berhasil ditambahkan.", 15);

                $prodi = Prodi::where('kode_prodi', $kodeProdiBaru)->first();
                $this->assertNotNull($prodi, 'Program studi baru harus tersimpan di database.');

                $browser->visit('/admin/master-fakultas-prodi?search_prodi=' . urlencode($kodeProdiBaru))
                    ->waitFor("@row-prodi-{$prodi->id}", 15)
                    ->assertSeeIn('@table-prodi', $kodeProdiBaru)
                    ->assertSeeIn('@table-prodi', $namaProdiBaru);

                $this->jsClick($browser, "@btn-edit-prodi-{$prodi->id}");

                $browser->waitFor('@modal-edit-prodi', 10)
                    ->pause(250)
                    ->clear('@input-edit-nama-prodi')
                    ->type('@input-edit-nama-prodi', $namaProdiEdit);

                $this->jsClick($browser, '@btn-submit-edit-prodi');

                $browser->waitForText("Program Studi {$namaProdiEdit} berhasil diperbarui.", 15);

                $browser->visit('/admin/master-fakultas-prodi?search_prodi=' . urlencode($kodeProdiBaru))
                    ->waitFor("@row-prodi-{$prodi->id}", 15)
                    ->assertSeeIn('@table-prodi', $namaProdiEdit);

                $browser->script("window.confirm = function() { return true; };");
                $this->jsClick($browser, "@btn-hapus-prodi-{$prodi->id}");

                $browser->waitForText("Program Studi {$namaProdiEdit} berhasil dihapus dari master data.", 15)
                    ->assertDontSeeIn('@table-prodi', $kodeProdiBaru);
            });
        } finally {
            Prodi::where('kode_prodi', $kodeProdiBaru)
                ->orWhereIn('nama_prodi', [$namaProdiBaru, $namaProdiEdit])
                ->delete();
        }
    }

    public function testAdminCanSearchAndFilterFakultasAndProdi()
    {
        $admin = $this->getAdminUser();
        $fakultasSample = Fakultas::first();

        $this->browse(function (Browser $browser) use ($admin, $fakultasSample) {
            $browser->driver->manage()->deleteAllCookies();

            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Kelola Master Data Fakultas & Program Studi', 15);

            if ($fakultasSample) {
                $browser->type('@input-search-fakultas', $fakultasSample->kode_fakultas)
                    ->keys('@input-search-fakultas', '{enter}')
                    ->waitUntil("window.location.search.includes('search_fakultas=')", 10)
                    ->waitForText($fakultasSample->nama_fakultas, 10)
                    ->assertSeeIn('@table-fakultas', $fakultasSample->nama_fakultas);

                $browser->select('@select-filter-fakultas', (string) $fakultasSample->id)
                    ->waitUntil("window.location.search.includes('filter_fakultas_id=" . $fakultasSample->id . "')", 10)
                    ->waitForText($fakultasSample->nama_fakultas, 10)
                    ->assertSeeIn('@table-prodi', $fakultasSample->nama_fakultas);
            }
        });
    }
}
