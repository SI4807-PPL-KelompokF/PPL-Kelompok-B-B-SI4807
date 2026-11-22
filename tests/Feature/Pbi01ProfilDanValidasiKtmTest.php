<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Pbi01ProfilDanValidasiKtmTest extends TestCase
{
    use RefreshDatabase;

    private function createPemohon(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Syalfa Oktaviana',
            'nama_lengkap' => 'Syalfa Oktaviana',
            'email' => 'syalfa@student.telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ], $overrides));
    }

    private function createLaboran(): User
    {
        return User::factory()->create([
            'name' => 'Raka Pratama, S.T.',
            'email' => 'laboran.ktm@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);
    }

    public function test_pemohon_can_view_profil_page_and_fetch_prodi_by_fakultas()
    {
        Fakultas::seedDefaultData();
        $fakultas = Fakultas::with('prodi')->firstOrFail();
        $pemohon = $this->createPemohon();

        $this->actingAs($pemohon)
            ->get(route('profil.show'))
            ->assertStatus(200)
            ->assertSee('Profil Akademik &amp; Validasi Identitas', false)
            ->assertSee('dusk="input-nama-lengkap"', false)
            ->assertSee('dusk="input-nomor-induk"', false)
            ->assertSee('dusk="select-fakultas"', false)
            ->assertSee('dusk="select-prodi"', false)
            ->assertSee('dusk="input-foto-identitas"', false)
            ->assertSee('dusk="btn-submit-profil"', false)
            ->assertSee('dusk="pill-status-validasi"', false);

        $this->actingAs($pemohon)
            ->getJson(route('profil.prodi', ['fakultas_id' => $fakultas->nama_fakultas]))
            ->assertStatus(200)
            ->assertJsonStructure(['prodis']);
    }

    public function test_pemohon_can_update_profile_and_upload_ktm_resets_validation_status()
    {
        Storage::fake('public');
        Fakultas::seedDefaultData();

        $prodi = Prodi::with('fakultas')->firstOrFail();
        $fakultas = $prodi->fakultas;
        $pemohon = $this->createPemohon([
            'status_validasi_ktm' => 'Ditolak',
        ]);

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        $ktmFile = UploadedFile::fake()->createWithContent('ktm_syalfa.png', $pngBytes);

        $response = $this->actingAs($pemohon)
            ->put(route('profil.update'), [
                'nama_lengkap' => 'Pemohon Akademik Uji',
                'nomor_induk' => '102022400228',
                'fakultas_id' => $fakultas->nama_fakultas,
                'prodi_id' => $prodi->nama_prodi,
                'foto_identitas' => $ktmFile,
            ]);

        $response->assertRedirect(route('profil.show'))
            ->assertSessionHas('status', 'Profil akademik berhasil diperbarui.');

        $pemohon->refresh();
        $this->assertEquals('Pemohon Akademik Uji', $pemohon->nama_lengkap);
        $this->assertEquals('102022400228', $pemohon->nomor_induk);
        $this->assertEquals($fakultas->nama_fakultas, $pemohon->fakultas_id);
        $this->assertEquals($prodi->nama_prodi, $pemohon->prodi_id);
        $this->assertEquals('Belum Tervalidasi', $pemohon->status_validasi_ktm);
        $this->assertNotNull($pemohon->foto_identitas);
        Storage::disk('public')->assertExists($pemohon->foto_identitas);

        $this->actingAs($pemohon)
            ->get(route('profil.identitas.preview'))
            ->assertStatus(200);
    }

    public function test_profile_update_rejects_mismatched_prodi_and_invalid_ktm_file()
    {
        Storage::fake('public');
        Fakultas::seedDefaultData();

        $fakultas = Fakultas::firstOrFail();
        $pemohon = $this->createPemohon();

        // Prodi tidak sesuai fakultas
        $this->actingAs($pemohon)
            ->from(route('profil.show'))
            ->put(route('profil.update'), [
                'nama_lengkap' => 'Pemohon Uji',
                'nomor_induk' => '102022400300',
                'fakultas_id' => $fakultas->nama_fakultas,
                'prodi_id' => 'Prodi Tidak Ada Di Fakultas Ini',
            ])
            ->assertRedirect(route('profil.show'))
            ->assertSessionHasErrors(['prodi_id']);

        // Berkas KTM bukan JPG/PNG/PDF
        $invalidFile = UploadedFile::fake()->createWithContent('script.exe', 'MZfakebinarypayload');
        $this->actingAs($pemohon)
            ->from(route('profil.show'))
            ->put(route('profil.update'), [
                'nama_lengkap' => 'Pemohon Uji',
                'nomor_induk' => '102022400300',
                'fakultas_id' => $fakultas->nama_fakultas,
                'prodi_id' => $fakultas->prodi()->first()->nama_prodi,
                'foto_identitas' => $invalidFile,
            ])
            ->assertRedirect(route('profil.show'))
            ->assertSessionHasErrors(['foto_identitas']);
    }

    public function test_laboran_can_approve_and_reject_pemohon_ktm_while_pemohon_is_forbidden()
    {
        Fakultas::seedDefaultData();
        $prodi = Prodi::with('fakultas')->firstOrFail();

        $pemohon = $this->createPemohon([
            'nomor_induk' => '102022400228',
            'fakultas_id' => $prodi->fakultas->nama_fakultas,
            'prodi_id' => $prodi->nama_prodi,
            'foto_identitas' => 'identitas/ktm_valid.png',
            'status_validasi_ktm' => 'Belum Tervalidasi',
        ]);
        $laboran = $this->createLaboran();

        // Pemohon dilarang mengakses halaman validasi KTM Laboran
        $this->actingAs($pemohon)
            ->get(route('laboran.validasi.ktm.index'))
            ->assertStatus(403);

        // Laboran dapat melihat daftar pemohon
        $this->actingAs($laboran)
            ->get(route('laboran.validasi.ktm.index'))
            ->assertStatus(200)
            ->assertSee('Validasi Identitas &amp; KTM', false)
            ->assertSee('dusk="row-pemohon-' . $pemohon->id . '"', false)
            ->assertSee('dusk="btn-setujui-' . $pemohon->id . '"', false)
            ->assertSee('dusk="btn-tolak-' . $pemohon->id . '"', false);

        // Laboran menyetujui KTM
        $this->actingAs($laboran)
            ->from(route('laboran.validasi.ktm.index'))
            ->post(route('laboran.validasi.ktm.approve', $pemohon->id))
            ->assertRedirect(route('laboran.validasi.ktm.index'))
            ->assertSessionHas('status');

        $this->assertEquals('Tervalidasi', $pemohon->fresh()->status_validasi_ktm);

        $this->actingAs($pemohon->fresh())
            ->get(route('profil.show'))
            ->assertStatus(200)
            ->assertSee('Tervalidasi')
            ->assertSee('Identitas Anda telah diverifikasi oleh Laboran.');

        // Laboran menolak KTM
        $this->actingAs($laboran)
            ->from(route('laboran.validasi.ktm.index'))
            ->post(route('laboran.validasi.ktm.reject', $pemohon->id))
            ->assertRedirect(route('laboran.validasi.ktm.index'))
            ->assertSessionHas('status');

        $this->assertEquals('Ditolak', $pemohon->fresh()->status_validasi_ktm);

        $this->actingAs($pemohon->fresh())
            ->get(route('profil.show'))
            ->assertStatus(200)
            ->assertSee('Ditolak')
            ->assertSee('Validasi KTM ditolak. Silakan unggah ulang foto KTM yang valid.');
    }
}
