<?php

namespace Tests\Browser;

use App\Models\DokumenHasil;
use App\Models\TiketPengujian;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Pbi14UnduhHasilUjiDuskTest extends DuskTestCase
{
    public function testPemohonAuthorizedDownloadStateAndAccessControl()
    {
        $owner = User::updateOrCreate(
            ['email' => 'pemohon.pbi14.owner@telkomuniversity.ac.id'],
            [
                'name' => 'Pemohon Pemilik LHU',
                'nama_lengkap' => 'Pemohon Pemilik LHU',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
            ]
        );

        $other = User::updateOrCreate(
            ['email' => 'pemohon.pbi14.other@telkomuniversity.ac.id'],
            [
                'name' => 'Pemohon Bukan Pemilik',
                'nama_lengkap' => 'Pemohon Bukan Pemilik',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
            ]
        );

        $tiketProses = TiketPengujian::updateOrCreate(
            ['nomor_tiket' => 'TLY-DUSK14-001'],
            [
                'user_id' => $owner->id,
                'nama_pemohon' => $owner->displayName(),
                'nim_nip' => '102022400141',
                'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
                'prodi' => 'S1 Sistem Informasi',
                'nama_sampel' => 'Sampel Dalam Proses',
                'instrumen' => 'Spektrofotometer UV-Vis',
                'laboratorium' => 'Lab Material Maju',
                'jenis_sampel' => 'fisik',
                'status' => TiketPengujian::STATUS_SEDANG_DIUJI,
                'tanggal_uji' => date('Y-m-d'),
                'barcode_token' => 'DUSK14-PROC',
                'submitted_at' => now()->subHours(4),
                'received_at' => now()->subHours(2),
                'testing_at' => now()->subHour(),
            ]
        );

        $tiketSelesai = TiketPengujian::updateOrCreate(
            ['nomor_tiket' => 'TLY-DUSK14-002'],
            [
                'user_id' => $owner->id,
                'nama_pemohon' => $owner->displayName(),
                'nim_nip' => '102022400142',
                'fakultas' => 'Fakultas Rekayasa Industri (FRI)',
                'prodi' => 'S1 Sistem Informasi',
                'nama_sampel' => 'Sampel Hasil Selesai',
                'instrumen' => 'XRD Diffractometer',
                'laboratorium' => 'Lab Karakterisasi Material',
                'jenis_sampel' => 'fisik',
                'status' => TiketPengujian::STATUS_SELESAI,
                'tanggal_uji' => date('Y-m-d'),
                'barcode_token' => 'DUSK14-DONE',
                'submitted_at' => now()->subHours(6),
                'received_at' => now()->subHours(4),
                'testing_at' => now()->subHours(3),
                'completed_at' => now()->subHour(),
            ]
        );

        DokumenHasil::updateOrCreate(
            ['tiket_pengujian_id' => $tiketSelesai->id],
            [
                'uploaded_by' => $owner->id,
                'nama_berkas_asli' => 'LHU-TLY-DUSK14-002.pdf',
                'path_berkas' => 'private/dokumen_hasil/TLY-DUSK14-002/dusk-v1.pdf.enc',
                'ekstensi' => 'PDF',
                'mime_type' => 'application/pdf',
                'ukuran_bytes' => 4096,
                'versi' => 1,
                'catatan_laboran' => 'Pengujian selesai tanpa anomali.',
                'enkripsi_algoritma' => DokumenHasil::ENKRIPSI_DEFAULT,
                'sha256_checksum' => hash('sha256', 'LHU-TLY-DUSK14-002.pdf'),
            ]
        );

        try {
            $this->browse(function (Browser $browser) use ($owner, $other, $tiketProses, $tiketSelesai) {
                $browser->driver->manage()->deleteAllCookies();

                // 1. Pada dashboard pemohon: tiket belum Selesai berstatus disabled, tiket Selesai aktif
                $browser->loginAs($owner)
                    ->visit('/home')
                    ->waitForText('Daftar Pengujian Sampel Terkini', 15)
                    ->assertPresent("@download-lhu-disabled-{$tiketProses->nomor_tiket}")
                    ->assertDisabled("@download-lhu-disabled-{$tiketProses->nomor_tiket}")
                    ->assertMissing("@download-lhu-{$tiketProses->nomor_tiket}")
                    ->assertPresent("@download-lhu-{$tiketSelesai->nomor_tiket}");

                // 2. Detail tiket belum Selesai -> tombol unduh LHU di header & kartu kanan disabled
                $browser->visit('/pengajuan/' . $tiketProses->nomor_tiket)
                    ->waitForText('Visual Timeline Tracking', 15)
                    ->assertPresent('@btn-unduh-lhu-disabled')
                    ->assertDisabled('@btn-unduh-lhu-disabled')
                    ->assertPresent('@card-btn-unduh-lhu-disabled')
                    ->assertDisabled('@card-btn-unduh-lhu-disabled')
                    ->assertMissing('@btn-unduh-lhu')
                    ->assertMissing('@card-btn-unduh-lhu');

                // 3. Detail tiket Selesai -> tombol unduh LHU aktif & berkas LHU tampil
                $browser->visit('/pengajuan/' . $tiketSelesai->nomor_tiket)
                    ->waitForText('LHU-TLY-DUSK14-002.pdf', 15)
                    ->assertPresent('@btn-unduh-lhu')
                    ->assertPresent('@card-btn-unduh-lhu')
                    ->assertMissing('@btn-unduh-lhu-disabled')
                    ->assertMissing('@card-btn-unduh-lhu-disabled')
                    ->assertScript("document.querySelector('[dusk=\"card-btn-unduh-lhu\"]').getAttribute('href').endsWith('/pengajuan/{$tiketSelesai->nomor_tiket}/unduh-lhu')");

                // 4. Pengguna lain tidak dapat mengakses URL unduh LHU (HTTP 403 Forbidden)
                $browser->driver->manage()->deleteAllCookies();
                $browser->loginAs($other)
                    ->visit('/pengajuan/' . $tiketSelesai->nomor_tiket . '/unduh-lhu')
                    ->waitForText('Akses Ditolak', 15)
                    ->assertSee('403')
                    ->assertSee('Akses Ditolak');
            });
        } finally {
            DokumenHasil::where('tiket_pengujian_id', $tiketSelesai->id)->delete();
            TiketPengujian::whereIn('nomor_tiket', ['TLY-DUSK14-001', 'TLY-DUSK14-002'])->delete();
        }
    }
}
