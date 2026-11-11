<?php

namespace Tests\Feature;

use App\Models\AuditTrail;
use App\Models\MesinLab;
use App\Models\TiketPengujian;
use App\Models\User;
use App\Services\BarcodeGeneratorService;
use App\Services\ResiPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pbi07Pbi08Pbi09PengajuanTest extends TestCase
{
    use RefreshDatabase;

    private $pemohon;
    private $laboran;
    private $mesinAktif;
    private $mesinMaint;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = User::factory()->create([
            'name' => 'Giri Pulung Nugroho',
            'email' => 'giri@telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);

        $this->laboran = User::factory()->create([
            'name' => 'Laboran Teknik Terpadu',
            'email' => 'laboran.teknik@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);

        $this->mesinAktif = MesinLab::create([
            'nama_mesin' => 'Spektrofotometer UV-Vis Terintegrasi',
            'kategori_layanan' => 'Material & Kimia Terapan',
            'laboratorium' => 'Lab Kimia Terpadu Telkom University',
            'status_mesin' => 'Aktif',
            'kuota_harian' => 5,
            'estimasi_sla_hari' => 2,
            'sop_penyiapan' => 'Gunakan vial kaca 5 mL bertutup kedap udara.',
        ]);

        $this->mesinMaint = MesinLab::create([
            'nama_mesin' => 'Universal Testing Machine 50 kN',
            'kategori_layanan' => 'Material & Kimia Terapan',
            'laboratorium' => 'Lab Rekayasa Material & Mekanik',
            'status_mesin' => 'Maintenance',
            'kuota_harian' => 2,
            'estimasi_sla_hari' => 3,
            'sop_penyiapan' => 'Spesimen memenuhi standar ASTM E8M.',
        ]);
    }

    /**
     * PBI-07: Pemohon dapat membuka formulir dinamis pengajuan uji lab.
     */
    public function test_pemohon_can_access_dynamic_pengajuan_form(): void
    {
        $response = $this->actingAs($this->pemohon)->get(route('pengajuan.create'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Pengajuan Pengujian Sampel');
        $response->assertSee($this->mesinAktif->nama_mesin);
        $response->assertSee('Identitas Akademik Pemohon');
        $response->assertSee('Spesimen Fisik');
        $response->assertSee('Berkas / Dataset Digital');
    }

    /**
     * PBI-07 & PBI-08: Pemohon berhasil mengajukan sampel fisik dan sistem otomatis men-generate Resi PDF ber-barcode.
     */
    public function test_pemohon_can_submit_physical_specimen_and_generate_barcode_ticket(): void
    {
        $payload = [
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
            'prodi' => 'S1 Sistem Informasi',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Larutan Ekstrak Daun Sirih 100 ppm',
            'deskripsi_sampel' => 'Larutan berwarna hijau muda jernih volume 10 mL dalam vial kaca.',
            'tujuan_pengujian' => 'Uji absorbansi flavonoid pada rentang 200 - 800 nm untuk tugas akhir.',
            'jenis_sampel' => 'fisik',
            'tanggal_uji' => now()->addDays(1)->toDateString(),
        ];

        $response = $this->actingAs($this->pemohon)->post(route('pengajuan.store'), $payload);

        $tiket = TiketPengujian::where('user_id', $this->pemohon->id)->latest('id')->first();

        $this->assertNotNull($tiket);
        $this->assertStringStartsWith('TLY-', $tiket->nomor_tiket);
        $this->assertEquals(TiketPengujian::STATUS_MENUNGGU, $tiket->status);
        $this->assertEquals('fisik', $tiket->jenis_sampel);
        $this->assertNotNull($tiket->barcode_token);
        $this->assertNotNull($tiket->submitted_at);

        // Verifikasi tercatat di Audit Trail
        $audit = AuditTrail::where('tiket_pengujian_id', $tiket->id)->first();
        $this->assertNotNull($audit);
        $this->assertEquals('PENGAJUAN_SAMPEL_BARU', $audit->aksi);

        $response->assertRedirect(route('pengajuan.show', $tiket->nomor_tiket));
        $response->assertSessionHas('status');
    }

    /**
     * PBI-07: Pemohon dapat mengajukan sampel digital dengan tautan cloud.
     */
    public function test_pemohon_can_submit_digital_specimen_with_cloud_link(): void
    {
        $payload = [
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Informatika (FIF)',
            'prodi' => 'S1 Informatika',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Dataset Simulasi Mesh Turbin Angin 3D',
            'deskripsi_sampel' => 'Arsip zip berukuran 250 MB berisi skrip OpenFOAM dan geometry STL.',
            'tujuan_pengujian' => 'Simulasi aerodinamika CFD menggunakan cluster GPU.',
            'jenis_sampel' => 'digital',
            'tautan_file_cloud' => 'https://drive.google.com/drive/folders/telyuji-test-cloud',
            'tanggal_uji' => now()->addDays(2)->toDateString(),
        ];

        $response = $this->actingAs($this->pemohon)->post(route('pengajuan.store'), $payload);

        $tiket = TiketPengujian::where('user_id', $this->pemohon->id)->latest('id')->first();

        $this->assertNotNull($tiket);
        $this->assertEquals('digital', $tiket->jenis_sampel);
        $this->assertEquals('https://drive.google.com/drive/folders/telyuji-test-cloud', $tiket->tautan_file_cloud);
        $response->assertRedirect(route('pengajuan.show', $tiket->nomor_tiket));
    }

    /**
     * PBI-07: Sampel digital mewajibkan isian tautan file cloud valid.
     */
    public function test_digital_specimen_requires_cloud_url(): void
    {
        $payload = [
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Informatika (FIF)',
            'prodi' => 'S1 Informatika',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Dataset Riset Komputasi',
            'deskripsi_sampel' => 'Deskripsi sampel digital.',
            'tujuan_pengujian' => 'Tujuan simulasi komputasi.',
            'jenis_sampel' => 'digital',
            'tautan_file_cloud' => null, // Kosong
            'tanggal_uji' => now()->addDays(1)->toDateString(),
        ];

        $response = $this->actingAs($this->pemohon)->post(route('pengajuan.store'), $payload);

        $response->assertSessionHasErrors(['tautan_file_cloud']);
    }

    /**
     * PBI-07 (Alur Alternatif UC-07): Sistem menolak pengajuan jika mesin berstatus Maintenance.
     */
    public function test_cannot_submit_when_machine_is_under_maintenance(): void
    {
        $payload = [
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
            'prodi' => 'S1 Teknik Industri',
            'mesin_lab_id' => $this->mesinMaint->id, // Mesin Maintenance
            'nama_sampel' => 'Batang Baja Uji Tarik',
            'deskripsi_sampel' => 'Baja karbon silinder standar ASTM.',
            'tujuan_pengujian' => 'Uji kekuatan luluh material.',
            'jenis_sampel' => 'fisik',
            'tanggal_uji' => now()->addDays(1)->toDateString(),
        ];

        $response = $this->actingAs($this->pemohon)->post(route('pengajuan.store'), $payload);

        $response->assertSessionHasErrors(['mesin_lab_id']);
    }

    /**
     * PBI-08 (UC-08): Endpoint unduh Resi PDF menghasilkan file PDF biner yang valid.
     */
    public function test_resi_pdf_download_endpoint_returns_valid_pdf_stream(): void
    {
        $tiket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261111-001',
            'user_id' => $this->pemohon->id,
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Rekayasa Industri',
            'prodi' => 'S1 Sistem Informasi',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Ekstrak Cairan Organik',
            'deskripsi_sampel' => 'Vial kaca transparan 10 mL.',
            'tujuan_pengujian' => 'Uji absorbansi optik.',
            'jenis_sampel' => 'fisik',
            'instrumen' => $this->mesinAktif->nama_mesin,
            'laboratorium' => $this->mesinAktif->laboratorium,
            'status' => TiketPengujian::STATUS_MENUNGGU,
            'tanggal_uji' => now()->toDateString(),
            'barcode_token' => 'TOKEN123456789',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->pemohon)->get(route('pengajuan.resi.download', $tiket->nomor_tiket));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $response->getContent());
    }

    /**
     * PBI-08: Halaman cetak dan pratinjau resi berfungsi normal.
     */
    public function test_resi_print_and_preview_endpoints_work(): void
    {
        $tiket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261111-002',
            'user_id' => $this->pemohon->id,
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Rekayasa Industri',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Spesimen Uji',
            'instrumen' => $this->mesinAktif->nama_mesin,
            'laboratorium' => $this->mesinAktif->laboratorium,
            'status' => TiketPengujian::STATUS_MENUNGGU,
            'tanggal_uji' => now()->toDateString(),
            'submitted_at' => now(),
        ]);

        $printResp = $this->actingAs($this->pemohon)->get(route('pengajuan.resi.print', $tiket->nomor_tiket));
        $printResp->assertStatus(200);
        $printResp->assertSee('RESI E-TICKET &amp; BUKTI PENDAFTARAN SPESIMEN', false);

        $previewResp = $this->actingAs($this->pemohon)->get(route('pengajuan.resi.preview', $tiket->nomor_tiket));
        $previewResp->assertStatus(200);
        $previewResp->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * PBI-09: Tampilan Visual Timeline Tracking menampilkan seluruh tahapan linimasa secara komprehensif.
     */
    public function test_visual_timeline_tracking_view_renders_all_stages(): void
    {
        $tiket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261111-003',
            'user_id' => $this->pemohon->id,
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Rekayasa Industri',
            'prodi' => 'S1 Sistem Informasi',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Nanopartikel TiO2',
            'instrumen' => $this->mesinAktif->nama_mesin,
            'laboratorium' => $this->mesinAktif->laboratorium,
            'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
            'tanggal_uji' => now()->toDateString(),
            'submitted_at' => now()->subHours(5),
            'received_at' => now()->subHours(3),
            'testing_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($this->pemohon)->get(route('pengajuan.show', $tiket->nomor_tiket));

        $response->assertStatus(200);
        $response->assertSee('Visual Timeline Tracking');
        $response->assertSee('Terdaftar');
        $response->assertSee('Resi Terbit');
        $response->assertSee('Dalam Antrean');
        $response->assertSee('Sedang Diuji');
        $response->assertSee('Selesai &amp; Unduh', false);
        $response->assertSee($tiket->nomor_tiket);
        $response->assertSee('Unduh Resi PDF');
        $response->assertSee('Cetak Barcode');
    }

    /**
     * PBI-09: Linimasa menampilkan banner penolakan yang jelas saat sampel ditolak (PBI-12 / SC-12).
     */
    public function test_visual_timeline_displays_rejection_banner_when_status_is_ditolak(): void
    {
        $tiket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261111-004',
            'user_id' => $this->pemohon->id,
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Rekayasa Industri',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Cairan Terkontaminasi',
            'instrumen' => $this->mesinAktif->nama_mesin,
            'laboratorium' => $this->mesinAktif->laboratorium,
            'status' => TiketPengujian::STATUS_DITOLAK,
            'alasan_penolakan' => 'Wadah vial retak dan sampel bocor tidak steril.',
            'tanggal_uji' => now()->toDateString(),
            'rejected_at' => now(),
        ]);

        $response = $this->actingAs($this->pemohon)->get(route('pengajuan.show', $tiket->nomor_tiket));

        $response->assertStatus(200);
        $response->assertSee('Pengajuan Spesimen Ditolak oleh Laboran');
        $response->assertSee('Wadah vial retak dan sampel bocor tidak steril.');
    }

    /**
     * Pengguna lain yang tidak berwenang tidak dapat mengakses tiket milik orang lain.
     */
    public function test_unauthorized_user_cannot_access_or_download_other_users_ticket(): void
    {
        $anotherUser = User::factory()->create([
            'email' => 'stranger@telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);

        $tiket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261111-005',
            'user_id' => $this->pemohon->id,
            'nama_pemohon' => 'Giri Pulung Nugroho',
            'nim_nip' => '102022400284',
            'fakultas' => 'Fakultas Rekayasa Industri',
            'mesin_lab_id' => $this->mesinAktif->id,
            'nama_sampel' => 'Rahasia Riset',
            'instrumen' => $this->mesinAktif->nama_mesin,
            'laboratorium' => $this->mesinAktif->laboratorium,
            'status' => TiketPengujian::STATUS_MENUNGGU,
            'tanggal_uji' => now()->toDateString(),
        ]);

        // Pengguna lain ditolak (403 Forbidden)
        $respShow = $this->actingAs($anotherUser)->get(route('pengajuan.show', $tiket->nomor_tiket));
        $respShow->assertStatus(403);

        $respDownload = $this->actingAs($anotherUser)->get(route('pengajuan.resi.download', $tiket->nomor_tiket));
        $respDownload->assertStatus(403);

        // Laboran memiliki hak akses operasional (200 OK)
        $respLaboran = $this->actingAs($this->laboran)->get(route('pengajuan.show', $tiket->nomor_tiket));
        $respLaboran->assertStatus(200);
    }
}
