<?php

namespace Tests\Browser;

use App\Models\AuditTrail;
use App\Models\TiketPengujian;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LaboranAntreanTest extends DuskTestCase
{
    /**
     * Pengujian Dusk PBI-10 (UC-10):
     * Laboran memvalidasi penerimaan sampel fisik,
     * memproses sampel menggunakan mesin, dan
     * memastikan setiap perubahan tercatat di Audit Trail.
     */
    public function testLaboranCanValidateAndUpdateQueueStatus(): void
    {
        // 1. Membuat atau mengambil akun Laboran untuk pengujian
        $laboran = User::firstOrCreate(
            [
                'email' => 'laboran@telkomuniversity.ac.id',
            ],
            [
                'name' => 'Laboran Testing',
                'password' => bcrypt('password'),
                'role' => User::ROLE_LABORAN,
            ]
        );

        // 2. Membuat atau memperbarui data tiket pengujian
        $tiket = TiketPengujian::updateOrCreate(
            ['nomor_tiket' => 'TLY-TEST-PBI10-0001'],
            [
                'nama_pemohon' => 'Pengguna Testing',
                'nim_nip' => '102022400131',
                'fakultas' => 'Fakultas Informatika',
                'prodi' => 'Sistem Informasi',
                'nama_sampel' => 'Sampel Pengujian Otomatis',
                'deskripsi_sampel' => 'Data untuk pengujian Dusk PBI-10',
                'tujuan_pengujian' => 'Pengujian validasi antrean',
                'jenis_sampel' => 'fisik',
                'instrumen' => 'Mesin Uji Otomatis',
                'laboratorium' => 'Laboratorium Pengujian',
                'status' => TiketPengujian::STATUS_MENUNGGU,
                'tanggal_uji' => now()->toDateString(),
                'submitted_at' => now(),
            ]
        );

        try {
            $this->browse(function (Browser $browser) use ($laboran, $tiket) {

                // 3. Login sebagai Laboran dan membuka halaman antrean
                $browser->loginAs($laboran)
                    ->visit('/laboran/antrean')
                    ->waitForText('Antrean Pengujian', 15)
                    ->assertPathIs('/laboran/antrean')
                    ->assertSee($tiket->nomor_tiket)
                    ->assertSee('Menunggu Verifikasi');

                // 4. Menguji penerimaan sampel fisik
                $urlTerima = route(
                    'laboran.antrean.terima-fisik',
                    $tiket->nomor_tiket
                );

                $browser->script("
                    document.querySelector(
                        'form[action=\"{$urlTerima}\"] button[type=\"submit\"]'
                    ).click();
                ");

                $browser->waitForText('Sampel berhasil diterima', 10)
                    ->assertSee('Dalam Antrean');

                // Memastikan status tiket berubah
                $tiket->refresh();

                $this->assertSame(
                    TiketPengujian::STATUS_DALAM_ANTREAN,
                    $tiket->status,
                    'FAIL: Status tiket tidak berubah menjadi Dalam Antrean.'
                );

                // 5. Menguji proses pengujian menggunakan mesin
                $urlProses = route(
                    'laboran.antrean.proses-mesin',
                    $tiket->nomor_tiket
                );

                $browser->script("
                    document.querySelector(
                        'form[action=\"{$urlProses}\"] button[type=\"submit\"]'
                    ).click();
                ");

                $browser->waitForText('Sedang Diuji', 10)
                    ->assertSee('Sedang Diuji');

                // Memastikan status tiket berubah
                $tiket->refresh();

                $this->assertSame(
                    TiketPengujian::STATUS_SEDANG_DIUJI,
                    $tiket->status,
                    'FAIL: Status tiket tidak berubah menjadi Sedang Diuji.'
                );
            });

            // 6. Memastikan Audit Trail penerimaan sampel tercatat
            $this->assertDatabaseHas('audit_trails', [
                'tiket_pengujian_id' => $tiket->id,
                'aksi' => 'TERIMA_FISIK',
                'berkas_lama' => TiketPengujian::STATUS_MENUNGGU,
                'berkas_baru' => TiketPengujian::STATUS_DALAM_ANTREAN,
            ]);

            // 7. Memastikan Audit Trail proses mesin tercatat
            $this->assertDatabaseHas('audit_trails', [
                'tiket_pengujian_id' => $tiket->id,
                'aksi' => 'PROSES_MESIN',
                'berkas_lama' => TiketPengujian::STATUS_DALAM_ANTREAN,
                'berkas_baru' => TiketPengujian::STATUS_SEDANG_DIUJI,
            ]);

        } finally {

            // 8. Membersihkan data khusus pengujian
            AuditTrail::where(
                'tiket_pengujian_id',
                $tiket->id
            )->delete();

            $tiket->delete();
        }
    }
}
