<?php

namespace Tests\Feature;

use App\Models\DokumenHasil;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class Pbi14UnduhHasilUjiTest extends TestCase
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

    private function createUser(string $email, string $role, string $name = 'Pengguna Telyuji'): User
    {
        return User::factory()->create([
            'name' => $name,
            'nama_lengkap' => $name,
            'email' => $email,
            'role' => $role,
        ]);
    }

    private function createTiket(User $owner, string $nomorTiket, string $status): TiketPengujian
    {
        return TiketPengujian::create([
            'nomor_tiket' => $nomorTiket,
            'user_id' => $owner->id,
            'nama_pemohon' => $owner->displayName(),
            'nim_nip' => '102022400114',
            'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
            'prodi' => 'S1 Sistem Informasi',
            'nama_sampel' => 'Sampel Polimer ' . $nomorTiket,
            'instrumen' => 'Spektrofotometer UV-Vis',
            'laboratorium' => 'Lab Material Maju',
            'jenis_sampel' => 'fisik',
            'status' => $status,
            'tanggal_uji' => now()->toDateString(),
            'barcode_token' => 'BAR-' . $nomorTiket,
            'submitted_at' => now()->subHours(3),
        ]);
    }

    public function test_download_button_is_disabled_and_endpoint_returns_403_when_status_is_not_selesai(): void
    {
        $owner = $this->createUser('pemohon.pbi14.a@telkomuniversity.ac.id', User::ROLE_PEMOHON, 'Pemohon Uji');

        $nonCompletedStatuses = [
            TiketPengujian::STATUS_MENUNGGU => 'TLY-PBI14-001',
            TiketPengujian::STATUS_DALAM_ANTREAN => 'TLY-PBI14-002',
            TiketPengujian::STATUS_SEDANG_DIUJI => 'TLY-PBI14-003',
            TiketPengujian::STATUS_DITOLAK => 'TLY-PBI14-004',
        ];

        foreach ($nonCompletedStatuses as $status => $nomorTiket) {
            $tiket = $this->createTiket($owner, $nomorTiket, $status);

            // Meskipun secara anomali ada baris dokumen_hasil, selama status != Selesai tetap harus nonaktif & ditolak 403
            DokumenHasil::create([
                'tiket_pengujian_id' => $tiket->id,
                'nama_berkas_asli' => "LHU-{$nomorTiket}.pdf",
                'path_berkas' => "private/dokumen_hasil/{$nomorTiket}/dummy.pdf.enc",
                'ekstensi' => 'PDF',
                'mime_type' => 'application/pdf',
                'ukuran_bytes' => 2048,
                'versi' => 1,
                'enkripsi_algoritma' => DokumenHasil::ENKRIPSI_DEFAULT,
                'sha256_checksum' => hash('sha256', $nomorTiket),
            ]);

            $this->actingAs($owner)
                ->get(route('home'))
                ->assertStatus(200)
                ->assertSee('dusk="download-lhu-disabled-' . $nomorTiket . '"', false)
                ->assertDontSee('dusk="download-lhu-' . $nomorTiket . '"', false);

            $this->actingAs($owner)
                ->get(route('pengajuan.show', $nomorTiket))
                ->assertStatus(200)
                ->assertSee('dusk="btn-unduh-lhu-disabled"', false)
                ->assertSee('dusk="card-btn-unduh-lhu-disabled"', false)
                ->assertDontSee('dusk="btn-unduh-lhu"', false)
                ->assertDontSee('dusk="card-btn-unduh-lhu"', false)
                ->assertDontSee('<style>', false);

            $this->actingAs($owner)
                ->get(route('pemohon.pengujian.download', $nomorTiket))
                ->assertStatus(403);
        }
    }

    public function test_owner_sees_active_download_button_and_can_download_decrypted_lhu_when_selesai(): void
    {
        $owner = $this->createUser('pemohon.pbi14.owner@telkomuniversity.ac.id', User::ROLE_PEMOHON, 'Pemilik Riset');
        $laboran = $this->createUser('laboran.pbi14@telkomuniversity.ac.id', User::ROLE_LABORAN, 'Laboran Penguji');

        $tiket = $this->createTiket($owner, 'TLY-PBI14-010', TiketPengujian::STATUS_SEDANG_DIUJI);

        $pdfPayload = "%PDF-1.4\nLaporan Hasil Uji Rahasia PBI-14 TLY-PBI14-010";
        $pdfUpload = UploadedFile::fake()->createWithContent('LHU-PBI14-010.pdf', $pdfPayload);

        $this->actingAs($laboran)
            ->postJson(route('laboran.pengujian.upload', $tiket->nomor_tiket), [
                'berkas_hasil' => $pdfUpload,
                'catatan_laboran' => 'Grafik spektrum terverifikasi.',
            ])
            ->assertStatus(200);

        $this->actingAs($owner)
            ->get(route('home'))
            ->assertStatus(200)
            ->assertSee('dusk="download-lhu-TLY-PBI14-010"', false)
            ->assertDontSee('dusk="download-lhu-disabled-TLY-PBI14-010"', false);

        $this->actingAs($owner)
            ->get(route('pengajuan.show', $tiket->nomor_tiket))
            ->assertStatus(200)
            ->assertSee('dusk="btn-unduh-lhu"', false)
            ->assertSee('dusk="card-btn-unduh-lhu"', false)
            ->assertSee('LHU-PBI14-010.pdf')
            ->assertSee('Grafik spektrum terverifikasi.')
            ->assertDontSee('dusk="btn-unduh-lhu-disabled"', false)
            ->assertDontSee('dusk="card-btn-unduh-lhu-disabled"', false)
            ->assertDontSee('<style>', false);

        $download = $this->actingAs($owner)
            ->get(route('pemohon.pengujian.download', $tiket->nomor_tiket));

        $download->assertStatus(200)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString('no-store', (string) $download->headers->get('Cache-Control'));
        $this->assertEquals($pdfPayload, $download->getContent());
    }

    public function test_non_owner_users_receive_http_403_when_accessing_lhu_download(): void
    {
        $owner = $this->createUser('owner.pbi14.sec@telkomuniversity.ac.id', User::ROLE_PEMOHON, 'Pemilik Asli');
        $otherPemohon = $this->createUser('other.pbi14.sec@telkomuniversity.ac.id', User::ROLE_PEMOHON, 'Pemohon Lain');
        $admin = $this->createUser('admin.pbi14.sec@telkomuniversity.ac.id', User::ROLE_ADMIN, 'Admin Lab');
        $laboran = $this->createUser('laboran.pbi14.sec@telkomuniversity.ac.id', User::ROLE_LABORAN, 'Laboran Lab');

        $tiketMilikOwner = $this->createTiket($owner, 'TLY-PBI14-020', TiketPengujian::STATUS_SEDANG_DIUJI);
        $pdfUpload = UploadedFile::fake()->createWithContent('LHU-Rahasia.pdf', "%PDF-1.4\nSecret Research Data");

        $this->actingAs($laboran)
            ->postJson(route('laboran.pengujian.upload', $tiketMilikOwner->nomor_tiket), [
                'berkas_hasil' => $pdfUpload,
            ])
            ->assertStatus(200);

        // Reset sesi autentikasi untuk memverifikasi tamu belum login diarahkan ke halaman login
        $this->app['auth']->forgetGuards();

        $this->get(route('pemohon.pengujian.download', $tiketMilikOwner->nomor_tiket))
            ->assertRedirect(route('login'));

        // Pemohon lain, Admin, dan Laboran tidak boleh mengunduh via portal pemilik (user_id != pemilik -> 403)
        foreach ([$otherPemohon, $admin, $laboran] as $nonOwner) {
            $this->actingAs($nonOwner)
                ->get(route('pemohon.pengujian.download', $tiketMilikOwner->nomor_tiket))
                ->assertStatus(403);
        }

        // Tiket tanpa user_id pemilik juga wajib ditolak (403) pada portal pemohon
        $orphanTiket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-PBI14-021',
            'user_id' => null,
            'nama_pemohon' => 'Anonim',
            'nim_nip' => '102022400999',
            'fakultas' => 'Fakultas Informatika (FIF)',
            'nama_sampel' => 'Dataset Anonim',
            'instrumen' => 'Cluster HPC',
            'laboratorium' => 'Lab Komputasi',
            'jenis_sampel' => 'digital',
            'status' => TiketPengujian::STATUS_SELESAI,
            'tanggal_uji' => now()->toDateString(),
        ]);

        $this->actingAs($owner)
            ->get(route('pemohon.pengujian.download', $orphanTiket->nomor_tiket))
            ->assertStatus(403);

        // Laboran tetap dapat mengunduh lewat endpoint operasional Laboran
        $this->actingAs($laboran)
            ->get(route('laboran.pengujian.download', $tiketMilikOwner->nomor_tiket))
            ->assertStatus(200);
    }
}
