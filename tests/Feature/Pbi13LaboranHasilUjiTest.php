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
            'role' => 'laboran',
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
        $this->assertEquals('Sedang Diuji', $tiket->status);
        $this->assertNull($tiket->dokumenHasil);

        $pdfFile = UploadedFile::fake()->createWithContent(
            'LHU-TLY-20261015-089.pdf',
            '%PDF-1.4 Fake Encrypted Lab Result Content Telyuji'
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
        $this->assertEquals('Selesai', $tiket->status);

        $dokumen = DokumenHasil::where('tiket_pengujian_id', $tiket->id)->first();
        $this->assertNotNull($dokumen);
        $this->assertEquals(1, $dokumen->versi);
        $this->assertEquals('LHU-TLY-20261015-089.pdf', $dokumen->nama_berkas_asli);
        $this->assertEquals('AES-256-CBC', $dokumen->enkripsi_algoritma);

        // Pastikan berkas tersimpan terenkripsi di Private Storage
        $this->assertTrue(LaboranHasilUjiController::existsPrivateFile($dokumen->path_berkas));
        $rawEncrypted = LaboranHasilUjiController::getPrivateFile($dokumen->path_berkas);
        $this->assertNotEquals('%PDF-1.4 Fake Encrypted Lab Result Content Telyuji', $rawEncrypted);
        $this->assertEquals(
            '%PDF-1.4 Fake Encrypted Lab Result Content Telyuji',
            Crypt::decrypt($rawEncrypted)
        );

        // Pastikan tercatat di Audit Trail
        $audit = AuditTrail::where('tiket_pengujian_id', $tiket->id)
            ->where('aksi', 'UNGGAH_HASIL_UJI')
            ->first();
        $this->assertNotNull($audit);
        $this->assertEquals('LHU-TLY-20261015-089.pdf', $audit->berkas_baru);
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
        $this->assertEquals('Sedang Diuji', $tiket->status);
        $this->assertNull($tiket->dokumenHasil);
    }

    public function test_revision_flow_replaces_existing_document_1_to_1_and_logs_revision_audit_trail()
    {
        $laboran = $this->createLaboran();

        $this->actingAs($laboran)->get(route('laboran.pengujian.index'));
        $tiket = TiketPengujian::where('nomor_tiket', 'TLY-20261013-054')->firstOrFail();

        $this->assertEquals('Selesai', $tiket->status);
        $oldDoc = $tiket->dokumenHasil;
        $this->assertNotNull($oldDoc);
        $this->assertEquals(1, $oldDoc->versi);
        $oldFileName = $oldDoc->nama_berkas_asli;
        $oldPath = $oldDoc->path_berkas;

        // Buat berkas lama palsu di storage untuk memastikan penghapusan berjalan
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
            ->where('aksi', 'REVISI_HASIL_UJI')
            ->latest('id')
            ->first();

        $this->assertNotNull($revisionAudit);
        $this->assertEquals($oldFileName, $revisionAudit->berkas_lama);
        $this->assertEquals('LHU-TLY-20261013-054-v2.xlsx', $revisionAudit->berkas_baru);
    }
}
