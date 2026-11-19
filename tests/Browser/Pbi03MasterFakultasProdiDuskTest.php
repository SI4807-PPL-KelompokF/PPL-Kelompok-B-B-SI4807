<?php

namespace Tests\Browser;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-03 (UC-04): Kelola Master Data Fakultas dan Program Studi
 * Role: Administrator (Syalfa Oktaviana Wijaya) — Branch: Syalfa
 *
 * Menguji fungsionalitas CRUD, pencarian, dan filter master data institusi.
 */
class Pbi03MasterFakultasProdiDuskTest extends DuskTestCase
{
    /**
     * Pastikan master data awal dan user admin siap sebelum pengujian.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists(Fakultas::class)) {
            Fakultas::seedDefaultData();
        }
    }

    /**
     * Skenario 1 (PBI-03 Tampilan & Elemen Dashboard Admin):
     * Admin login, membuka halaman Master Fakultas & Prodi, memverifikasi
     * breadcrumb, counter pill total, tabel data, serta tombol aksi master data.
     */
    public function testAdminCanViewMasterFakultasProdiDashboardAndElements()
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin.syalfa@telyuji.ac.id'],
            [
                'name' => 'Administrator Telyuji',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_ADMIN,
            ]
        );

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

    /**
     * Skenario 2 (PBI-03 Operasi CRUD Master Data Fakultas):
     * Admin dapat menambah fakultas baru, memperbarui (edit) datanya,
     * dan menghapus fakultas tersebut dengan aman.
     */
    public function testAdminCanCreateUpdateAndDeleteFakultas()
    {
        $admin = User::where('email', 'admin.syalfa@telyuji.ac.id')->first();

        $kodeFakultasBaru = 'FTU-TEST';
        $namaFakultasBaru = 'Fakultas Teknik Terapan Uji';
        $namaFakultasEdit = 'Fakultas Teknik Terapan Edit';

        $this->browse(function (Browser $browser) use ($admin, $kodeFakultasBaru, $namaFakultasBaru, $namaFakultasEdit) {
            // 1. Buka halaman master
            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Master Data Fakultas');

            // 2. Klik tombol tambah fakultas & buka modal
            $browser->click('@btn-tambah-fakultas')
                ->waitFor('@modal-tambah-fakultas')
                ->type('@input-tambah-kode-fakultas', $kodeFakultasBaru)
                ->type('@input-tambah-nama-fakultas', $namaFakultasBaru)
                ->click('@btn-submit-tambah-fakultas');

            // 3. Verifikasi alert sukses dan data tampil di tabel
            $browser->waitForText('Fakultas berhasil ditambahkan.')
                ->assertSee($kodeFakultasBaru)
                ->assertSee($namaFakultasBaru);

            // 4. Ambil ID fakultas yang baru dibuat untuk pengujian edit & hapus
            $fakultas = Fakultas::where('kode_fakultas', $kodeFakultasBaru)->first();
            $this->assertNotNull($fakultas, 'Fakultas baru harus tersimpan di database.');

            // 5. Klik edit pada fakultas yang baru dibuat
            $browser->click("@btn-edit-fakultas-{$fakultas->id}")
                ->waitFor('@modal-edit-fakultas')
                ->clear('@input-edit-nama-fakultas')
                ->type('@input-edit-nama-fakultas', $namaFakultasEdit)
                ->click('@btn-submit-edit-fakultas');

            // 6. Verifikasi alert sukses dan nama fakultas terupdate
            $browser->waitForText('Fakultas berhasil diperbarui.')
                ->assertSee($namaFakultasEdit);

            // 7. Hapus fakultas uji
            $browser->script("window.confirm = function() { return true; };");
            $browser->script("document.querySelector('[dusk=\"btn-hapus-fakultas-{$fakultas->id}\"]').click();");

            // 8. Verifikasi penghapusan berhasil
            $browser->waitForText('Fakultas berhasil dihapus.')
                ->assertDontSee($kodeFakultasBaru);
        });
    }

    /**
     * Skenario 3 (PBI-03 Operasi CRUD Master Data Program Studi):
     * Admin dapat menambah program studi baru pada fakultas tertentu,
     * memperbarui datanya, dan menghapus prodi tersebut.
     */
    public function testAdminCanCreateUpdateAndDeleteProgramStudi()
    {
        $admin = User::where('email', 'admin.syalfa@telyuji.ac.id')->first();
        $fakultasInduk = Fakultas::first();

        $kodeProdiBaru = 'DSN-TEST';
        $namaProdiBaru = 'S1 Desain Eksperimental Uji';
        $namaProdiEdit = 'S1 Desain Eksperimental Edit';

        $this->browse(function (Browser $browser) use ($admin, $fakultasInduk, $kodeProdiBaru, $namaProdiBaru, $namaProdiEdit) {
            // 1. Kunjungi halaman master data
            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Master Data Program Studi');

            // 2. Klik tambah prodi & isi formulir modal
            $browser->click('@btn-tambah-prodi')
                ->waitFor('@modal-tambah-prodi')
                ->select('@select-tambah-prodi-fakultas', (string)$fakultasInduk->id)
                ->type('@input-tambah-kode-prodi', $kodeProdiBaru)
                ->type('@input-tambah-nama-prodi', $namaProdiBaru)
                ->click('@btn-submit-tambah-prodi');

            // 3. Verifikasi respon sukses
            $browser->waitForText('Program Studi berhasil ditambahkan.')
                ->assertSee($kodeProdiBaru)
                ->assertSee($namaProdiBaru);

            // 4. Dapatkan ID prodi dari database
            $prodi = Prodi::where('kode_prodi', $kodeProdiBaru)->first();
            $this->assertNotNull($prodi, 'Program studi baru harus tersimpan di database.');

            // 5. Ubah data prodi via modal edit
            $browser->click("@btn-edit-prodi-{$prodi->id}")
                ->waitFor('@modal-edit-prodi')
                ->clear('@input-edit-nama-prodi')
                ->type('@input-edit-nama-prodi', $namaProdiEdit)
                ->click('@btn-submit-edit-prodi');

            // 6. Verifikasi perubahan prodi
            $browser->waitForText('Program Studi berhasil diperbarui.')
                ->assertSee($namaProdiEdit);

            // 7. Hapus prodi uji
            $browser->script("window.confirm = function() { return true; };");
            $browser->script("document.querySelector('[dusk=\"btn-hapus-prodi-{$prodi->id}\"]').click();");

            // 8. Verifikasi prodi berhasil dihapus
            $browser->waitForText('Program Studi berhasil dihapus.')
                ->assertDontSee($kodeProdiBaru);
        });
    }

    /**
     * Skenario 4 (PBI-03 Filter & Pencarian Master Data):
     * Admin dapat mencari data fakultas berdasarkan kata kunci pencarian,
     * serta memfilter daftar program studi berdasarkan fakultas induk yang dipilih.
     */
    public function testAdminCanSearchAndFilterFakultasAndProdi()
    {
        $admin = User::where('email', 'admin.syalfa@telyuji.ac.id')->first();
        $fri = Fakultas::where('kode_fakultas', 'FRI')->first() ?: Fakultas::first();

        $this->browse(function (Browser $browser) use ($admin, $fri) {
            // 1. Buka halaman master
            $browser->loginAs($admin)
                ->visit('/admin/master-fakultas-prodi')
                ->waitForText('Kelola Master Data Fakultas & Program Studi');

            // 2. Lakukan pencarian nama/kode fakultas
            $browser->type('@input-search-fakultas', 'Informatika')
                ->keys('@input-search-fakultas', '{enter}')
                ->waitForLocation('/admin/master-fakultas-prodi')
                ->assertSee('Informatika');

            // 3. Filter program studi berdasarkan fakultas induk
            if ($fri) {
                $browser->select('@select-filter-fakultas', (string)$fri->id)
                    ->waitForLocation('/admin/master-fakultas-prodi')
                    ->assertSee($fri->nama_fakultas);
            }
        });
    }
}
