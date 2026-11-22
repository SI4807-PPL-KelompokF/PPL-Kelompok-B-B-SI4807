<?php

namespace Tests\Feature;

use App\Models\MesinLab;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pbi04MesinLabTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role, string $email): User
    {
        return User::factory()->create([
            'email' => $email,
            'role' => $role,
        ]);
    }

    public function test_admin_can_view_machine_catalog_without_inline_style_and_non_admin_is_forbidden()
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, 'admin.mesin@telkomuniversity.ac.id');
        $laboran = $this->createUserWithRole(User::ROLE_LABORAN, 'laboran.mesin@telkomuniversity.ac.id');
        $pemohon = $this->createUserWithRole(User::ROLE_PEMOHON, 'pemohon.mesin@student.telkomuniversity.ac.id');

        $this->actingAs($laboran)->get(route('admin.mesin.index'))->assertStatus(403);
        $this->actingAs($pemohon)->get(route('admin.mesin.index'))->assertStatus(403);

        $this->actingAs($admin)
            ->get(route('admin.mesin.index'))
            ->assertStatus(200)
            ->assertSee('Katalog Mesin Laboratorium')
            ->assertSee('dusk="table-mesin"', false)
            ->assertSee('Spektrofotometer UV-Vis Terintegrasi')
            ->assertDontSee('<style>', false);

        $this->assertGreaterThanOrEqual(5, MesinLab::count());
    }

    public function test_admin_can_crud_and_toggle_machine_status_using_status_mesin()
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, 'admin.crud@telkomuniversity.ac.id');

        // Create
        $this->actingAs($admin)
            ->post(route('admin.mesin.store'), [
                'nama_mesin' => 'Atomic Absorption Spectrometer (AAS)',
                'kategori_layanan' => 'Material & Kimia Terapan',
                'laboratorium' => 'Lab Analisis Unsur Terpadu',
                'status_mesin' => MesinLab::STATUS_AKTIF,
                'kuota_harian' => 4,
                'estimasi_sla_hari' => 3,
            ])
            ->assertRedirect(route('admin.mesin.index'))
            ->assertSessionHas('status', 'Mesin laboratorium berhasil ditambahkan.');

        $mesin = MesinLab::where('nama_mesin', 'Atomic Absorption Spectrometer (AAS)')->firstOrFail();
        $this->assertTrue($mesin->isAktif());
        $this->assertEquals(4, $mesin->kuota_harian);
        $this->assertEquals(3, $mesin->estimasi_sla_hari);

        // Toggle Aktif -> Maintenance -> Aktif
        $this->actingAs($admin)
            ->from(route('admin.mesin.index'))
            ->patch(route('admin.mesin.toggle', $mesin))
            ->assertRedirect(route('admin.mesin.index'))
            ->assertSessionHas('status');

        $this->assertEquals(MesinLab::STATUS_MAINTENANCE, $mesin->fresh()->status_mesin);

        $this->actingAs($admin)
            ->from(route('admin.mesin.index'))
            ->patch(route('admin.mesin.toggle', $mesin))
            ->assertRedirect(route('admin.mesin.index'));

        $this->assertEquals(MesinLab::STATUS_AKTIF, $mesin->fresh()->status_mesin);

        // Update
        $this->actingAs($admin)
            ->put(route('admin.mesin.update', $mesin), [
                'nama_mesin' => 'AAS Presisi Generasi 2',
                'kategori_layanan' => 'Material & Kimia Terapan',
                'laboratorium' => 'Lab Analisis Unsur Terpadu',
                'status_mesin' => MesinLab::STATUS_AKTIF,
                'kuota_harian' => 6,
                'estimasi_sla_hari' => 2,
            ])
            ->assertRedirect(route('admin.mesin.index'))
            ->assertSessionHas('status', 'Data mesin laboratorium berhasil diperbarui.');

        $this->assertEquals('AAS Presisi Generasi 2', $mesin->fresh()->nama_mesin);
        $this->assertEquals(6, $mesin->fresh()->kuota_harian);

        // Delete mesin yang belum terpakai
        $this->actingAs($admin)
            ->delete(route('admin.mesin.destroy', $mesin))
            ->assertRedirect(route('admin.mesin.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('mesin_lab', ['id' => $mesin->id]);
    }

    public function test_machine_referenced_by_tickets_cannot_be_deleted_and_quota_tracks_by_id()
    {
        $admin = $this->createUserWithRole(User::ROLE_ADMIN, 'admin.guard@telkomuniversity.ac.id');

        $mesin = MesinLab::create([
            'nama_mesin' => 'XRD Diffractometer Uji',
            'kategori_layanan' => 'Material & Kimia Terapan',
            'laboratorium' => 'Lab Karakterisasi Material',
            'status_mesin' => MesinLab::STATUS_AKTIF,
            'kuota_harian' => 3,
            'estimasi_sla_hari' => 2,
        ]);

        TiketPengujian::create([
            'nomor_tiket' => 'TLY-MESIN-001',
            'nama_pemohon' => 'Peneliti Uji',
            'nim_nip' => '102022400111',
            'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
            'mesin_lab_id' => $mesin->id,
            'nama_sampel' => 'Serbuk Oksida',
            'instrumen' => $mesin->nama_mesin,
            'laboratorium' => $mesin->laboratorium,
            'status' => TiketPengujian::STATUS_MENUNGGU,
            'tanggal_uji' => now()->toDateString(),
        ]);

        // Meski nama mesin diubah, sisa kuota harian tetap menghitung tiket lewat mesin_lab_id
        $mesin->update(['nama_mesin' => 'XRD Diffractometer Baru']);
        $this->assertEquals(2, $mesin->fresh()->sisaKuotaHarian(now()->toDateString()));

        // Tidak boleh dihapus karena masih direferensikan tiket
        $this->actingAs($admin)
            ->from(route('admin.mesin.index'))
            ->delete(route('admin.mesin.destroy', $mesin))
            ->assertRedirect(route('admin.mesin.index'))
            ->assertSessionHasErrors(['mesin']);

        $this->assertDatabaseHas('mesin_lab', ['id' => $mesin->id]);
    }
}
