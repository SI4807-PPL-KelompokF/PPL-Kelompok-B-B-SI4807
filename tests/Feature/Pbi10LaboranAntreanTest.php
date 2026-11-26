<?php

namespace Tests\Feature;

use App\Models\AuditTrail;
use App\Models\Fakultas;
use App\Models\MesinLab;
use App\Models\TiketPengujian;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class Pbi10LaboranAntreanTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        $privateDir = storage_path('app/private/dokumen_hasil');
        if (is_dir($privateDir)) {
            File::deleteDirectory($privateDir);
        }

        parent::tearDown();
    }

    public function test_laboran_antrean_handles_physical_and_digital_tickets_end_to_end_to_lhu_completion()
    {
        $laboran = User::factory()->create([
            'name' => 'Raka Pratama, S.T.',
            'nama_lengkap' => 'Raka Pratama, S.T.',
            'email' => 'laboran.antrean@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);
        $pemohon = User::factory()->create([
            'name' => 'Pemohon Integrasi',
            'nama_lengkap' => 'Pemohon Integrasi',
            'nomor_induk' => '102022400555',
            'email' => 'pemohon.antrean@student.telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);
        $admin = User::factory()->create([
            'email' => 'admin.antrean@telkomuniversity.ac.id',
            'role' => User::ROLE_ADMIN,
        ]);

        // Role selain Laboran ditolak 403
        $this->actingAs($pemohon)->get(route('laboran.antrean.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('laboran.antrean.index'))->assertStatus(403);

        MesinLab::seedDefaultData();
        $mesin = MesinLab::where('status_mesin', MesinLab::STATUS_AKTIF)->firstOrFail();

        // 1. Tiket sampel FISIK
        $tiketFisik = TiketPengujian::create([
            'nomor_tiket' => 'TLY-INT-FISIK-01',
            'user_id' => $pemohon->id,
            'nama_pemohon' => $pemohon->displayName(),
            'nim_nip' => $pemohon->nomor_induk,
            'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
            'prodi' => 'S1 Sistem Informasi',
            'mesin_lab_id' => $mesin->id,
            'nama_sampel' => 'Larutan Uji Fisik',
            'jenis_sampel' => 'fisik',
            'instrumen' => $mesin->nama_mesin,
            'laboratorium' => $mesin->laboratorium,
            'status' => TiketPengujian::STATUS_MENUNGGU,
            'tanggal_uji' => now()->toDateString(),
            'submitted_at' => now(),
        ]);

        // 2. Tiket sampel DIGITAL
        $tiketDigital = TiketPengujian::create([
            'nomor_tiket' => 'TLY-INT-DIGI-02',
            'user_id' => $pemohon->id,
            'nama_pemohon' => $pemohon->displayName(),
            'nim_nip' => $pemohon->nomor_induk,
            'fakultas' => 'Fakultas Informatika (FIF)',
            'prodi' => 'S1 Informatika',
            'mesin_lab_id' => $mesin->id,
            'nama_sampel' => 'Dataset Simulasi CFD',
            'jenis_sampel' => 'digital',
            'tautan_file_cloud' => 'https://drive.google.com/drive/folders/test-telyuji',
            'instrumen' => $mesin->nama_mesin,
            'laboratorium' => $mesin->laboratorium,
            'status' => TiketPengujian::STATUS_MENUNGGU,
            'tanggal_uji' => now()->toDateString(),
            'submitted_at' => now(),
        ]);

        // Antrean tampil bersih tanpa tag <style> maupun atribut style= inline
        $this->actingAs($laboran)
            ->get(route('laboran.antrean.index'))
            ->assertStatus(200)
            ->assertSee('Antrean Pengujian')
            ->assertSee('TLY-INT-FISIK-01')
            ->assertSee('TLY-INT-DIGI-02')
            ->assertSee('Terima Sampel')
            ->assertSee('Verifikasi Berkas Digital')
            ->assertDontSee('<style>', false)
            ->assertDontSee('style="background-color:', false);

        // terimaFisik menolak sampel digital (422)
        $this->actingAs($laboran)
            ->post(route('laboran.antrean.terima-fisik', $tiketDigital->nomor_tiket))
            ->assertStatus(422);

        // verifikasiDigital meloloskan sampel digital -> Dalam Antrean
        $this->actingAs($laboran)
            ->post(route('laboran.antrean.verifikasi-digital', $tiketDigital->nomor_tiket))
            ->assertRedirect(route('laboran.antrean.index'))
            ->assertSessionHas('status');

        $this->assertEquals(TiketPengujian::STATUS_DALAM_ANTREAN, $tiketDigital->fresh()->status);
        $this->assertNotNull($tiketDigital->fresh()->received_at);

        // terimaFisik meloloskan sampel fisik -> Dalam Antrean -> prosesMesin -> Sedang Diuji
        $this->actingAs($laboran)
            ->post(route('laboran.antrean.terima-fisik', $tiketFisik->nomor_tiket))
            ->assertRedirect(route('laboran.antrean.index'));

        $this->assertEquals(TiketPengujian::STATUS_DALAM_ANTREAN, $tiketFisik->fresh()->status);

        $this->actingAs($laboran)
            ->post(route('laboran.antrean.proses-mesin', $tiketFisik->nomor_tiket))
            ->assertRedirect(route('laboran.antrean.index'));

        $tiketFisik->refresh();
        $this->assertEquals(TiketPengujian::STATUS_SEDANG_DIUJI, $tiketFisik->status);
        $this->assertNotNull($tiketFisik->testing_at);

        // Lanjut PBI-13: unggah LHU -> status Selesai & completed_at terisi untuk PBI-09
        $pdf = UploadedFile::fake()->createWithContent('LHU-INT-01.pdf', '%PDF-1.4 Valid Test PDF');
        $this->actingAs($laboran)
            ->post(route('laboran.pengujian.upload', $tiketFisik->nomor_tiket), [
                'berkas_hasil' => $pdf,
            ])
            ->assertRedirect(route('laboran.pengujian.index', ['tiket' => $tiketFisik->nomor_tiket]));

        $tiketFisik->refresh();
        $this->assertEquals(TiketPengujian::STATUS_SELESAI, $tiketFisik->status);
        $this->assertNotNull($tiketFisik->completed_at);

        // Panel LHU PBI-13 hanya menampilkan jejak audit LHU (tidak tercampur TERIMA_FISIK/PROSES_MESIN)
        $lhuPage = $this->actingAs($laboran)->get(route('laboran.pengujian.index'));
        $lhuPage->assertStatus(200)
            ->assertSee('LHU-INT-01.pdf')
            ->assertDontSee('TERIMA_FISIK');

        // Halaman detail tiket PBI-09 menampilkan label audit ramah pengguna
        $this->actingAs($pemohon)
            ->get(route('pengajuan.show', $tiketFisik->nomor_tiket))
            ->assertStatus(200)
            ->assertSee('Unggah Hasil Uji (LHU)')
            ->assertSee('Proses Pengujian Mesin')
            ->assertDontSee('PROSES_MESIN');
    }

    public function test_database_seeder_runs_cleanly_and_is_idempotent()
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@telkomuniversity.ac.id', 'role' => User::ROLE_ADMIN]);
        $this->assertDatabaseHas('users', ['email' => 'laboran@telkomuniversity.ac.id', 'role' => User::ROLE_LABORAN]);
        $this->assertDatabaseHas('users', ['email' => 'pemohon@telkomuniversity.ac.id', 'role' => User::ROLE_PEMOHON]);
        $this->assertEquals(1, User::where('email', 'admin@telkomuniversity.ac.id')->count());
        $this->assertGreaterThan(0, Fakultas::count());
        $this->assertGreaterThanOrEqual(5, MesinLab::count());
        $this->assertGreaterThan(0, AuditTrail::count());
    }
}
