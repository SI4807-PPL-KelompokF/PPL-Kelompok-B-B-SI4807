<?php

namespace Tests\Feature;

use App\Http\Controllers\LaboranHasilUjiController;
use App\Models\AuditTrail;
use App\Models\DokumenHasil;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class Pbi13LaboranHasilUjiTest extends TestCase
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

    private function createLaboran(): User
    {
        return User::factory()->create([
            'name' => 'Raka Pratama, S.T.',
            'email' => 'laboran@student.telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);
    }

    private function createPemohon(string $email = 'pemohon1@student.telkomuniversity.ac.id', string $name = 'Mahasiswa Pemohon'): User
    {
        return User::factory()->create([
            'name' => $name,
            'email' => $email,
            'role' => User::ROLE_PEMOHON,
        ]);
    }

    public function test_laboran_workspace_renders_queue_drawer_and_dusk_hooks_without_style_tags_or_preview_bar()
    {
        $laboran = $this->createLaboran();

        $response = $this->actingAs($laboran)
            ->get(route('laboran.pengujian.index'));

        $response->assertStatus(200)
            ->assertSee('Unggah &amp; Revisi Hasil Uji', false)
            ->assertSee('dusk="queue-table"', false)
            ->assertSee('dusk="upload-workspace-panel"', false)
            ->assertSee('dusk="dropzone-normal"', false)
            ->assertSee('dusk="dropzone-valid-state"', false)
            ->assertSee('dusk="dropzone-error-state"', false)
            ->assertSee('dusk="submit-upload-button"', false)
            ->assertSee('dusk="audit-trail-table"', false)
            ->assertSee('TLY-20261015-089')
            ->assertSee('Sedang Diuji')
            ->assertDontSee('Pratinjau tampilan')
            ->assertDontSee('Otomatis tercatat setiap unggah', false)
            ->assertDontSee('<style>', false);
    }

    public function test_selecting_completed_ticket_displays_revision_mode_banner_and_active_document_card()
    {
        $laboran = $this->createLaboran();

        $response = $this->actingAs($laboran)
            ->get(route('laboran.pengujian.index', ['tiket' => 'TLY-20261013-054']));

        $response->assertStatus(200)
            ->assertSee('dusk="revision-mode-banner"', false)
            ->assertSee('dusk="existing-file-name"', false)
            ->assertSee('dusk="download-existing-file"', false)
            ->assertSee('Mode revisi')
            ->assertSee('LHU-TLY-20261013-054.pdf')
            ->assertSee('Ganti file &amp; catat revisi', false)
            ->assertDontSee('<style>', false);
    }

    public function test_laboran_can_upload_valid_pdf_and_status_becomes_selesai_with_encrypted_private_storage_and_audit_trail()
    {
        $laboran = $this->createLaboran();

        // Inisialisasi data sampel antrean awal
        $this->actingAs($laboran)->get(route('laboran.pengujian.index'));

        $tiket = TiketPengujian::where('nomor_tiket', 'TLY-20261015-089')->firstOrFail();
        $this->assertEquals(TiketPengujian::STATUS_SEDANG_DIUJI, $tiket->status);
        $this->assertNull($tiket->dokumenHasil);

        $rawContent = '%PDF-1.4 Fake Encrypted Lab Result Content Telyuji';
        $pdfFile = UploadedFile::fake()->createWithContent(
            'LHU-TLY-20261015-089.pdf',
            $rawContent
        );

        $response = $this->actingAs($laboran)->post(
            route('laboran.pengujian.upload', $tiket->nomor_tiket),
            [
                'berkas_hasil' => $pdfFile,
                'catatan_laboran' => 'Pengujian spektrofotometer UV-Vis selesai.',
            ]
        );

        $response->assertRedirect(route('laboran.pengujian.index', [
            'tiket' => 'TLY-20261015-089',
        ]));
        $response->assertSessionHas('status');

        $tiket->refresh();
        $this->assertEquals(TiketPengujian::STATUS_SELESAI, $tiket->status);

        $dokumen = DokumenHasil::where('tiket_pengujian_id', $tiket->id)->first();
        $this->assertNotNull($dokumen);
        $this->assertEquals(1, $dokumen->versi);
        $this->assertEquals('LHU-TLY-20261015-089.pdf', $dokumen->nama_berkas_asli);
        $this->assertEquals(DokumenHasil::ENKRIPSI_DEFAULT, $dokumen->enkripsi_algoritma);
        $this->assertEquals(hash('sha256', $rawContent), $dokumen->sha256_checksum);

        // Pastikan berkas tersimpan terenkripsi di Private Storage
        $this->assertTrue(LaboranHasilUjiController::existsPrivateFile($dokumen->path_berkas));
        $rawEncrypted = LaboranHasilUjiController::getPrivateFile($dokumen->path_berkas);
        $this->assertNotEquals($rawContent, $rawEncrypted);
        $this->assertEquals($rawContent, Crypt::decrypt($rawEncrypted));

        // Pastikan tercatat di Audit Trail beserta IP address
        $audit = AuditTrail::where('tiket_pengujian_id', $tiket->id)
            ->where('aksi', AuditTrail::AKSI_UNGGAH)
            ->first();
        $this->assertNotNull($audit);
        $this->assertEquals('LHU-TLY-20261015-089.pdf', $audit->berkas_baru);
        $this->assertNotNull($audit->ip_address);
    }

    public function test_upload_rejects_file_larger_than_5mb_or_invalid_format()
    {
        $laboran = $this->createLaboran();

        $this->actingAs($laboran)->get(route('laboran.pengujian.index'));
        $tiket = TiketPengujian::where('nomor_tiket', 'TLY-20261015-089')->firstOrFail();

        // 1. Uji berkas > 5 MB (6000 KB)
        $oversizedPdf = UploadedFile::fake()->create('LHU_Oversized.pdf', 6000, 'application/pdf');

        $responseOversized = $this->actingAs($laboran)
            ->from(route('laboran.pengujian.index', ['tiket' => $tiket->nomor_tiket]))
            ->post(route('laboran.pengujian.upload', $tiket->nomor_tiket), [
                'berkas_hasil' => $oversizedPdf,
            ]);

        $responseOversized->assertRedirect(route('laboran.pengujian.index', ['tiket' => $tiket->nomor_tiket]));
        $responseOversized->assertSessionHasErrors(['berkas_hasil']);

        // 2. Uji format tidak didukung (.jpg)
        $invalidFormatFile = UploadedFile::fake()->create('Foto_Sampel.jpg', 500, 'image/jpeg');

        $responseFormat = $this->actingAs($laboran)
            ->from(route('laboran.pengujian.index', ['tiket' => $tiket->nomor_tiket]))
            ->post(route('laboran.pengujian.upload', $tiket->nomor_tiket), [
                'berkas_hasil' => $invalidFormatFile,
            ]);

        $responseFormat->assertRedirect(route('laboran.pengujian.index', ['tiket' => $tiket->nomor_tiket]));
        $responseFormat->assertSessionHasErrors(['berkas_hasil']);

        // Pastikan status tiket tidak berubah dan tidak ada dokumen hasil tersimpan
        $tiket->refresh();
        $this->assertEquals(TiketPengujian::STATUS_SEDANG_DIUJI, $tiket->status);
        $this->assertNull($tiket->dokumenHasil);
    }

    public function test_revision_flow_replaces_existing_document_1_to_1_and_logs_revision_audit_trail()
    {
        $laboran = $this->createLaboran();

        $this->actingAs($laboran)->get(route('laboran.pengujian.index'));
        $tiket = TiketPengujian::where('nomor_tiket', 'TLY-20261013-054')->firstOrFail();

        $this->assertEquals(TiketPengujian::STATUS_SELESAI, $tiket->status);
        $oldDoc = $tiket->dokumenHasil;
        $this->assertNotNull($oldDoc);
        $this->assertEquals(1, $oldDoc->versi);
        $oldFileName = $oldDoc->nama_berkas_asli;
        $oldPath = $oldDoc->path_berkas;

        // Buat berkas lama palsu di storage untuk memastikan penghapusan berjalan pasca-commit
        LaboranHasilUjiController::putPrivateFile($oldPath, Crypt::encrypt('Old PDF Content'));

        $revisedExcel = UploadedFile::fake()->create(
            'LHU-TLY-20261013-054-v2.xlsx',
            820,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $response = $this->actingAs($laboran)->post(
            route('laboran.pengujian.upload', $tiket->nomor_tiket),
            [
                'berkas_hasil' => $revisedExcel,
                'catatan_laboran' => 'Koreksi nilai difraksi puncak utama sampel.',
            ]
        );

        $response->assertRedirect(route('laboran.pengujian.index', [
            'tiket' => 'TLY-20261013-054',
        ]));

        // Relasi 1:1 pada tabel dokumen_hasil (tetap 1 baris untuk tiket ini, versi naik menjadi 2)
        $this->assertEquals(1, DokumenHasil::where('tiket_pengujian_id', $tiket->id)->count());

        $updatedDoc = DokumenHasil::where('tiket_pengujian_id', $tiket->id)->firstOrFail();
        $this->assertEquals(2, $updatedDoc->versi);
        $this->assertEquals('LHU-TLY-20261013-054-v2.xlsx', $updatedDoc->nama_berkas_asli);
        $this->assertEquals('XLSX', $updatedDoc->ekstensi);

        // Berkas terenkripsi lama dihapus dan berkas baru tersimpan
        $this->assertFalse(LaboranHasilUjiController::existsPrivateFile($oldPath));
        $this->assertTrue(LaboranHasilUjiController::existsPrivateFile($updatedDoc->path_berkas));

        // Audit trail mencatat REVISI_HASIL_UJI beserta nama berkas lama dan baru
        $revisionAudit = AuditTrail::where('tiket_pengujian_id', $tiket->id)
            ->where('aksi', AuditTrail::AKSI_REVISI)
            ->latest('id')
            ->first();

        $this->assertNotNull($revisionAudit);
        $this->assertEquals($oldFileName, $revisionAudit->berkas_lama);
        $this->assertEquals('LHU-TLY-20261013-054-v2.xlsx', $revisionAudit->berkas_baru);
    }

    public function test_pemohon_role_is_forbidden_from_accessing_laboran_workspace_or_uploading_results()
    {
        $laboran = $this->createLaboran();
        $this->actingAs($laboran)->get(route('laboran.pengujian.index'));

        $pemohon = $this->createPemohon();

        $this->actingAs($pemohon)
            ->get(route('laboran.pengujian.index'))
            ->assertStatus(403);

        $pdfFile = UploadedFile::fake()->createWithContent('LHU-Unauthorized.pdf', '%PDF-1.4 Unauthorized');

        $this->actingAs($pemohon)
            ->postJson(route('laboran.pengujian.upload', 'TLY-20261015-089'), [
                'berkas_hasil' => $pdfFile,
            ])
            ->assertStatus(403);
    }

    public function test_upload_rejects_ticket_still_in_menunggu_verifikasi_status_and_spoofed_magic_bytes()
    {
        $laboran = $this->createLaboran();

        $pendingTicket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261020-201',
            'nama_pemohon' => 'Dimas Anggara',
            'nim_nip' => '1301220201',
            'fakultas' => 'S1 Informatika · FIF',
            'nama_sampel' => 'Sensor Gas MQ-135',
            'instrumen' => 'Digital Oscilloscope 1 GHz',
            'laboratorium' => 'Lab Embedded & IoT',
            'status' => TiketPengujian::STATUS_MENUNGGU,
            'tanggal_uji' => '2026-10-20',
        ]);

        $validPdf = UploadedFile::fake()->createWithContent('LHU-Pending.pdf', '%PDF-1.4 Valid PDF Header');

        $this->actingAs($laboran)
            ->postJson(route('laboran.pengujian.upload', $pendingTicket->nomor_tiket), [
                'berkas_hasil' => $validPdf,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['berkas_hasil']);

        $this->assertNull($pendingTicket->fresh()->dokumenHasil);

        // Uji berkas spoofing (ekstensi .pdf tetapi berisi skrip PHP/HTML) pada tiket Sedang Diuji
        $testingTicket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261020-202',
            'nama_pemohon' => 'Alya Zahra',
            'nim_nip' => '1103220202',
            'fakultas' => 'S1 Teknik Elektro · FTE',
            'nama_sampel' => 'Elektroda Grafena',
            'instrumen' => 'Spektrofotometer UV-Vis',
            'laboratorium' => 'Lab Material Maju',
            'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
            'tanggal_uji' => '2026-10-20',
        ]);

        $spoofedPdf = UploadedFile::fake()->createWithContent('Spoofed.pdf', '<?php echo "not a pdf"; ?>');

        $this->actingAs($laboran)
            ->postJson(route('laboran.pengujian.upload', $testingTicket->nomor_tiket), [
                'berkas_hasil' => $spoofedPdf,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['berkas_hasil']);

        $this->assertEquals(TiketPengujian::STATUS_SEDANG_DIUJI, $testingTicket->fresh()->status);
    }

    public function test_laboran_and_ticket_owner_can_download_decrypted_lhu_while_other_pemohon_is_forbidden()
    {
        $laboran = $this->createLaboran();
        $owner = $this->createPemohon('owner@student.telkomuniversity.ac.id', 'Pemilik Sampel');
        $other = $this->createPemohon('other@student.telkomuniversity.ac.id', 'Pemohon Lain');

        $tiket = TiketPengujian::create([
            'nomor_tiket' => 'TLY-20261021-305',
            'user_id' => $owner->id,
            'nama_pemohon' => $owner->name,
            'nim_nip' => '1102220305',
            'fakultas' => 'S1 Teknik Fisika · FTE',
            'nama_sampel' => 'Lapisan Tipis ZnO',
            'instrumen' => 'XRD Diffractometer',
            'laboratorium' => 'Lab Karakterisasi Material',
            'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
            'tanggal_uji' => '2026-10-21',
        ]);

        $pdfPayload = "%PDF-1.4\nOfficial Telyuji Encrypted Report TLY-20261021-305";
        $pdfUpload = UploadedFile::fake()->createWithContent('LHU;TLY-20261021-305.pdf', $pdfPayload);

        // Unggah via endpoint JSON (seperti fetch() pada drawer UI)
        $jsonResp = $this->actingAs($laboran)
            ->postJson(route('laboran.pengujian.upload', $tiket->nomor_tiket), [
                'berkas_hasil' => $pdfUpload,
                'catatan_laboran' => 'Hasil karakterisasi XRD terverifikasi.',
            ]);

        $jsonResp->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'mode' => 'upload',
                'ticket' => 'TLY-20261021-305',
                'file_name' => 'LHU_TLY-20261021-305.pdf',
                'version' => 1,
            ]);

        // 1. Laboran dapat mengunduh & mendekripsi berkas
        $laboranDownload = $this->actingAs($laboran)
            ->get(route('laboran.pengujian.download', $tiket->nomor_tiket));

        $laboranDownload->assertStatus(200)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertEquals($pdfPayload, $laboranDownload->getContent());

        // 2. Pemohon pemilik tiket melihat tiketnya di /home dan dapat mengunduh LHU
        $this->actingAs($owner)
            ->get(route('home'))
            ->assertStatus(200)
            ->assertSee('TLY-20261021-305')
            ->assertSee('Unduh LHU')
            ->assertDontSee('<style>', false);

        $ownerDownload = $this->actingAs($owner)
            ->get(route('pemohon.pengujian.download', $tiket->nomor_tiket));

        $ownerDownload->assertStatus(200);
        $this->assertEquals($pdfPayload, $ownerDownload->getContent());

        // 3. Pemohon lain ditolak (403 Forbidden)
        $this->actingAs($other)
            ->get(route('pemohon.pengujian.download', $tiket->nomor_tiket))
            ->assertStatus(403);
    }

    public function test_public_sample_tracking_endpoint_returns_sanitized_ticket_progress()
    {
        $laboran = $this->createLaboran();
        $this->actingAs($laboran)->get(route('laboran.pengujian.index'));

        $this->getJson(route('public.track', 'TLY-20261015-089'))
            ->assertStatus(200)
            ->assertJson([
                'found' => true,
                'id' => 'TLY-20261015-089',
                'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
                'stage' => 4,
            ])
            ->assertJsonMissing(['path_berkas']);

        $this->getJson(route('public.track', 'TLY-TIDAK-ADA-999'))
            ->assertStatus(404)
            ->assertJson([
                'found' => false,
            ]);
    }
}
