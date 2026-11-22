<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pbi03MasterFakultasProdiTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Administrator Telyuji',
            'email' => 'admin.pbi03@telkomuniversity.ac.id',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_admin_can_view_master_fakultas_prodi_and_non_admin_is_forbidden()
    {
        $admin = $this->createAdmin();
        $pemohon = User::factory()->create([
            'email' => 'pemohon.pbi03@student.telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);
        $laboran = User::factory()->create([
            'email' => 'laboran.pbi03@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);

        $this->actingAs($pemohon)
            ->get(route('admin.master.fakultas-prodi.index'))
            ->assertStatus(403);

        $this->actingAs($laboran)
            ->get(route('admin.master.fakultas-prodi.index'))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->get(route('admin.master.fakultas-prodi.index'))
            ->assertStatus(200)
            ->assertSee('Kelola Master Data Fakultas &amp; Program Studi', false)
            ->assertSee('dusk="table-fakultas"', false)
            ->assertSee('dusk="table-prodi"', false)
            ->assertSee('Fakultas Informatika (FIF)')
            ->assertSee('tu-fak-fte', false)
            ->assertSee('tu-fak-fri', false)
            ->assertSee('tu-fak-fif', false)
            ->assertSee('tu-fak-feb', false)
            ->assertSee('tu-fak-fks', false)
            ->assertSee('tu-fak-fik', false)
            ->assertSee('tu-fak-fit', false)
            ->assertDontSee('Portal Admin')
            ->assertDontSee('Referensi Akademik')
            ->assertDontSee('<style>', false)
            ->assertDontSee('href="#"', false);

        $this->assertGreaterThan(0, Fakultas::count());
        $this->assertGreaterThan(0, Prodi::count());
    }

    public function test_admin_can_crud_fakultas_and_respects_referential_integrity()
    {
        $admin = $this->createAdmin();
        Fakultas::seedDefaultData();

        // Create Fakultas
        $this->actingAs($admin)
            ->post(route('admin.fakultas.store'), [
                'kode_fakultas' => 'ftu-test',
                'nama_fakultas' => 'Fakultas Teknik Terapan Uji',
            ])
            ->assertRedirect(route('admin.master.fakultas-prodi.index'))
            ->assertSessionHas('status', 'Fakultas Fakultas Teknik Terapan Uji (FTU-TEST) berhasil ditambahkan.');

        $fakultas = Fakultas::where('kode_fakultas', 'FTU-TEST')->firstOrFail();

        // Update Fakultas
        $this->actingAs($admin)
            ->put(route('admin.fakultas.update', $fakultas->id), [
                'kode_fakultas' => 'FTU-TEST',
                'nama_fakultas' => 'Fakultas Teknik Terapan Edit',
            ])
            ->assertRedirect(route('admin.master.fakultas-prodi.index'))
            ->assertSessionHas('status', 'Data Fakultas Fakultas Teknik Terapan Edit berhasil diperbarui.');

        // Tidak boleh menghapus Fakultas yang masih menaungi Prodi
        $prodi = $fakultas->prodi()->create([
            'kode_prodi' => 'TMP',
            'nama_prodi' => 'S1 Sementara',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.master.fakultas-prodi.index'))
            ->delete(route('admin.fakultas.destroy', $fakultas->id))
            ->assertSessionHasErrors(['fakultas']);

        $this->assertDatabaseHas('fakultas', ['id' => $fakultas->id]);

        // Hapus Prodi terlebih dahulu, baru hapus Fakultas
        $prodi->delete();

        $this->actingAs($admin)
            ->delete(route('admin.fakultas.destroy', $fakultas->id))
            ->assertRedirect(route('admin.master.fakultas-prodi.index'))
            ->assertSessionHas('status', 'Fakultas Fakultas Teknik Terapan Edit berhasil dihapus dari master data.');

        $this->assertDatabaseMissing('fakultas', ['id' => $fakultas->id]);
    }

    public function test_admin_can_crud_program_studi_and_delete_prodi_without_kode_prodi()
    {
        $admin = $this->createAdmin();
        Fakultas::seedDefaultData();
        $fakultasInduk = Fakultas::firstOrFail();

        // Create Prodi tanpa kode_prodi (nullable) untuk memastikan destroyProdi tidak salah cocokkan NULL
        $this->actingAs($admin)
            ->post(route('admin.prodi.store'), [
                'fakultas_id' => $fakultasInduk->id,
                'kode_prodi' => null,
                'nama_prodi' => 'S1 Desain Eksperimental Uji',
            ])
            ->assertRedirect(route('admin.master.fakultas-prodi.index'))
            ->assertSessionHas('status', 'Program Studi S1 Desain Eksperimental Uji berhasil ditambahkan.');

        $prodi = Prodi::where('nama_prodi', 'S1 Desain Eksperimental Uji')->firstOrFail();

        // Update Prodi
        $this->actingAs($admin)
            ->put(route('admin.prodi.update', $prodi->id), [
                'fakultas_id' => $fakultasInduk->id,
                'kode_prodi' => null,
                'nama_prodi' => 'S1 Desain Eksperimental Edit',
            ])
            ->assertRedirect(route('admin.master.fakultas-prodi.index'))
            ->assertSessionHas('status', 'Program Studi S1 Desain Eksperimental Edit berhasil diperbarui.');

        // Delete Prodi (meskipun admin sendiri punya prodi_id = null, harus tetap bisa dihapus)
        $this->actingAs($admin)
            ->delete(route('admin.prodi.destroy', $prodi->id))
            ->assertRedirect(route('admin.master.fakultas-prodi.index'))
            ->assertSessionHas('status', 'Program Studi S1 Desain Eksperimental Edit berhasil dihapus dari master data.');

        $this->assertDatabaseMissing('prodi', ['id' => $prodi->id]);
    }

    public function test_search_filter_and_api_prodi_by_fakultas_endpoint_work()
    {
        $admin = $this->createAdmin();
        Fakultas::seedDefaultData();
        $fakultas = Fakultas::where('kode_fakultas', 'FRI')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.master.fakultas-prodi.index', [
                'search_fakultas' => 'FRI',
                'filter_fakultas_id' => $fakultas->id,
            ]))
            ->assertStatus(200)
            ->assertSee('Fakultas Rekayasa Industri (FRI)')
            ->assertSee('S1 Teknik Industri');

        $pemohon = User::factory()->create([
            'email' => 'pemohon.api@student.telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);

        $this->actingAs($pemohon)
            ->getJson(route('api.fakultas.prodi', $fakultas->id))
            ->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'fakultas_id' => $fakultas->id,
                'nama_fakultas' => $fakultas->nama_fakultas,
            ]);
    }
}
