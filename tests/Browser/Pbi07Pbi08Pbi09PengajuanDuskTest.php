<?php

namespace Tests\Browser;

use App\Models\MesinLab;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi07Pbi08Pbi09PengajuanDuskTest extends DuskTestCase
{
    /**
     * Pengujian Dusk PBI-07, PBI-08, PBI-09 (Giri Pulung Nugroho):
     * Pemohon mengisi formulir dinamis pengajuan uji lab, sistem menerbitkan resi PDF ber-barcode,
     * dan menampilkan Visual Timeline Tracking secara interaktif.
     */
    public function testPemohonCanSubmitPengajuanAndTrackVisualTimeline()
    {
        $pemohon = User::updateOrCreate(
            ['email' => 'pemohon@telkomuniversity.ac.id'],
            [
                'name' => 'Giri Pulung Nugroho',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
            ]
        );

        $mesin = MesinLab::firstOrCreate(
            ['nama_mesin' => 'Spektrofotometer UV-Vis Terintegrasi'],
            [
                'kategori_layanan' => 'Material & Kimia Terapan',
                'laboratorium' => 'Lab Kimia Terpadu Telkom University',
                'status_mesin' => 'Aktif',
                'kuota_harian' => 6,
                'estimasi_sla_hari' => 2,
                'sop_penyiapan' => 'Vial kaca 5 mL kedap udara.',
            ]
        );

        $this->browse(function (Browser $browser) use ($pemohon, $mesin) {
            // 1. Pemohon membuka form dinamis pengajuan (PBI-07 / UC-07)
            $browser->loginAs($pemohon)
                ->visit('/pengajuan/buat')
                ->waitForText('Formulir Pengajuan Pengujian Sampel')
                ->assertPresent('@input-nama-pemohon')
                ->assertPresent('@input-nim-nip')
                ->assertPresent('@select-fakultas')
                ->assertPresent('@select-prodi')
                ->assertPresent('@select-mesin-lab')
                ->assertPresent('@radio-sampel-fisik')
                ->assertPresent('@radio-sampel-digital')
                ->assertPresent('@input-nama-sampel')
                ->assertPresent('@input-deskripsi-sampel')
                ->assertPresent('@input-tujuan-pengujian')
                ->assertPresent('@btn-submit-pengajuan');

            // 2. Isi formulir
            $browser->select('@select-fakultas', 'Fakultas Rekayasa Industri (FRI)')
                ->select('@select-mesin-lab', (string) $mesin->id)
                ->type('@input-nama-sampel', 'Spesimen Uji Tarik Polimer')
                ->type('@input-deskripsi-sampel', 'Spesimen polimer transparan berbentuk dog-bone.')
                ->type('@input-tujuan-pengujian', 'Analisis modulus elastisitas untuk Tugas Akhir SI-4807.')
                ->scrollIntoView('@btn-submit-pengajuan')
                ->click('@btn-submit-pengajuan');

            // 3. Verifikasi dialihkan ke halaman Visual Timeline Tracking (PBI-09 / UC-09)
            $browser->waitForText('Visual Timeline Tracking')
                ->assertPresent('@section-timeline-tracking')
                ->assertPresent('@timeline-step-submitted')
                ->assertPresent('@timeline-step-resi')
                ->assertSee('Terdaftar')
                ->assertSee('Resi Terbit')
                ->assertSee('Spesimen Uji Tarik Polimer');

            // 4. Verifikasi komponen Resi Barcode (PBI-08 / UC-08)
            $browser->assertPresent('@btn-unduh-resi')
                ->assertPresent('@btn-cetak-resi')
                ->assertPresent('@barcode-svg-container');
        });
    }
}
