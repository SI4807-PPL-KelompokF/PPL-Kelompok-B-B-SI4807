<?php

namespace Tests\Browser;

use App\Models\MesinLab;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi07Pbi08Pbi09PengajuanDuskTest extends DuskTestCase
{
    /**
     * Pengujian Dusk PBI-07, PBI-08, PBI-09 (Giri Pulung Nugroho):
     * 1. Formulir dinamis pengajuan uji lab (PBI-07 / UC-07).
     * 2. UI Visual Timeline Tracking 5 tahapan (PBI-09 / UC-09).
     * 3. Komponen Resi E-Ticket Barcode resmi Code128 & cetak (PBI-08 / UC-08).
     */
    public function testPemohonCanAccessPengajuanFormAndInspectTrackingAndResi()
    {
        $pemohon = User::updateOrCreate(
            ['email' => 'pemohon.dusk@telkomuniversity.ac.id'],
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

        $tiket = TiketPengujian::updateOrCreate(
            ['nomor_tiket' => 'TU-LAB-2026-7788'],
            [
                'user_id' => $pemohon->id,
                'barcode_token' => 'BAR-7788-DUSK',
                'nama_pemohon' => 'Giri Pulung Nugroho',
                'nim_nip' => '102022400284',
                'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
                'prodi' => 'S1 Sistem Informasi',
                'mesin_lab_id' => $mesin->id,
                'jenis_sampel' => 'fisik',
                'nama_sampel' => 'Spesimen Komposit Polimer A1',
                'deskripsi_sampel' => 'Spesimen polimer transparan untuk uji tarik.',
                'tujuan_pengujian' => 'Analisis modulus elastisitas riset TA SI-4807.',
                'tanggal_uji' => date('Y-m-d'),
                'status' => 'Dalam Antrean',
                'stage' => 3,
            ]
        );

        $this->browse(function (Browser $browser) use ($pemohon, $tiket) {
            // 1. Verifikasi Komponen Formulir Pengajuan Dinamis (PBI-07 / UC-07)
            $browser->loginAs($pemohon)
                ->visit('/pengajuan/buat')
                ->waitForText('Formulir Pengajuan Pengujian Sampel', 15)
                ->assertPresent('@input-nama-pemohon')
                ->assertPresent('@input-nim-nip')
                ->assertPresent('@select-fakultas')
                ->assertPresent('@select-prodi')
                ->assertPresent('@select-mesin-lab')
                ->assertPresent('@radio-sampel-fisik')
                ->assertPresent('@radio-sampel-digital')
                ->assertPresent('@input-nama-sampel')
                ->assertPresent('@input-tanggal-uji')
                ->assertPresent('@input-deskripsi-sampel')
                ->assertPresent('@input-tujuan-pengujian')
                ->assertPresent('@btn-submit-pengajuan');

            // 2. Verifikasi Visual Timeline Tracking 5 Tahap (PBI-09 / UC-09)
            $browser->visit('/pengajuan/' . $tiket->nomor_tiket)
                ->waitForText('Visual Timeline Tracking', 15)
                ->assertPresent('@section-timeline-tracking')
                ->assertPresent('@timeline-step-submitted')
                ->assertPresent('@timeline-step-resi')
                ->assertSee('Terdaftar')
                ->assertSee('Resi Terbit')
                ->assertSee('Antrean Lab')
                ->assertSee('Spesimen Komposit Polimer A1');

            // 3. Verifikasi Komponen Resi E-Ticket Barcode Code128 (PBI-08 / UC-08)
            $browser->assertPresent('@btn-unduh-resi')
                ->assertPresent('@btn-cetak-resi')
                ->assertPresent('@barcode-svg-container');

            // 4. Verifikasi Tampilan Cetak Fisik A4 Printer Friendly
            $browser->visit('/pengajuan/' . $tiket->nomor_tiket . '/resi/cetak')
                ->waitForText('RESI E-TICKET PENGUJIAN LABORATORIUM', 15)
                ->assertSee('TU-LAB-2026-7788')
                ->assertSee('Giri Pulung Nugroho');
        });
    }
}
