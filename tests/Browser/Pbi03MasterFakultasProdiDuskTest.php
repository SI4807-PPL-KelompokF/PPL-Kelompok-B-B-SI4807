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

    public function testAdminCanViewMasterFakultasProdiDashboardAndElements()
    {
        $admin = $this->getAdminUser();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Kelola Master Data Fakultas & Program Studi')
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
            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Master Data Fakultas');

            $browser->click('@btn-tambah-fakultas')
                ->waitFor('@modal-tambah-fakultas')
                ->type('@input-tambah-kode-fakultas', $kodeFakultasBaru)
                ->type('@input-tambah-nama-fakultas', $namaFakultasBaru)
                ->click('@btn-submit-tambah-fakultas');

            $browser->waitForText('Fakultas berhasil ditambahkan.')
                ->assertSee($kodeFakultasBaru)
                ->assertSee($namaFakultasBaru);

            $fakultas = Fakultas::where('kode_fakultas', $kodeFakultasBaru)->first();
            $this->assertNotNull($fakultas, 'Fakultas baru harus tersimpan di database.');

            $browser->click("@btn-edit-fakultas-{$fakultas->id}")
                ->waitFor('@modal-edit-fakultas')
                ->clear('@input-edit-nama-fakultas')
                ->type('@input-edit-nama-fakultas', $namaFakultasEdit)
                ->click('@btn-submit-edit-fakultas');

            $browser->waitForText('Fakultas berhasil diperbarui.')
                ->assertSee($namaFakultasEdit);

            $browser->script("window.confirm = function() { return true; };");
            $browser->script("document.querySelector('[dusk=\"btn-hapus-fakultas-{$fakultas->id}\"]').click();");

            $browser->waitForText('Fakultas berhasil dihapus.')
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
            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Master Data Program Studi');

            $browser->click('@btn-tambah-prodi')
                ->waitFor('@modal-tambah-prodi')
                ->select('@select-tambah-prodi-fakultas', (string) $fakultasInduk->id)
                ->type('@input-tambah-kode-prodi', $kodeProdiBaru)
                ->type('@input-tambah-nama-prodi', $namaProdiBaru)
                ->click('@btn-submit-tambah-prodi');

            $browser->waitForText('Program Studi berhasil ditambahkan.')
                ->assertSee($kodeProdiBaru)
                ->assertSee($namaProdiBaru);

            $prodi = Prodi::where('kode_prodi', $kodeProdiBaru)->first();
            $this->assertNotNull($prodi, 'Program studi baru harus tersimpan di database.');

            $browser->click("@btn-edit-prodi-{$prodi->id}")
                ->waitFor('@modal-edit-prodi')
                ->clear('@input-edit-nama-prodi')
                ->type('@input-edit-nama-prodi', $namaProdiEdit)
                ->click('@btn-submit-edit-prodi');

            $browser->waitForText('Program Studi berhasil diperbarui.')
                ->assertSee($namaProdiEdit);

            $browser->script("window.confirm = function() { return true; };");
            $browser->script("document.querySelector('[dusk=\"btn-hapus-prodi-{$prodi->id}\"]').click();");

            $browser->waitForText('Program Studi berhasil dihapus.')
                ->assertDontSee($kodeProdiBaru);
        });
    }

    public function testAdminCanSearchAndFilterFakultasAndProdi()
    {
        $admin = $this->getAdminUser();
        $fakultasSample = Fakultas::first();

        $this->browse(function (Browser $browser) use ($admin, $fakultasSample) {
            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Kelola Master Data Fakultas & Program Studi');

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