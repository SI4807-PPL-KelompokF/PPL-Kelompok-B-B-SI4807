<?php

namespace Tests\Feature;

use App\Models\MesinLab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pbi06LaboranSopTest extends TestCase
{
    use RefreshDatabase;

    public function test_laboran_can_manage_sanitized_sop_and_reflects_on_pengajuan_form()
    {
        $laboran = User::factory()->create([
            'email' => 'laboran.sop@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);
        $pemohon = User::factory()->create([
            'email' => 'pemohon.sop@student.telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);
        $admin = User::factory()->create([
            'email' => 'admin.sop@telkomuniversity.ac.id',
            'role' => User::ROLE_ADMIN,
        ]);

        // Role lain ditolak 403
        $this->actingAs($pemohon)->get(route('laboran.sop.index'))->assertStatus(403);
        $this->actingAs($admin)->get(route('laboran.sop.index'))->assertStatus(403);

        // Laboran membuka daftar SOP (otomatis seed katalog mesin bila kosong)
        $this->actingAs($laboran)
            ->get(route('laboran.sop.index'))
            ->assertStatus(200)
            ->assertSee('SOP Penyiapan Sampel')
            ->assertSee('dusk="table-sop"', false)
            ->assertDontSee('<style>', false);

        $mesin = MesinLab::firstOrFail();

        $this->actingAs($laboran)
            ->get(route('laboran.sop.edit', $mesin))
            ->assertStatus(200)
            ->assertSee($mesin->nama_mesin)
            ->assertDontSee('<style>', false);

        // Simpan SOP HTML disertai payload XSS berbahaya untuk menguji sanitasi
        $dirtyHtml = '<p onclick="alert(1)"><strong>SOP Khusus UV-Vis</strong></p>'
            . '<ol><li style="color:red">Saring larutan 0.45 mikron.</li></ol>'
            . '<script>alert("xss")</script>';

        $this->actingAs($laboran)
            ->put(route('laboran.sop.update', $mesin), [
                'sop_penyiapan' => $dirtyHtml,
            ])
            ->assertRedirect(route('laboran.sop.index'))
            ->assertSessionHas('status');

        $savedSop = $mesin->fresh()->sop_penyiapan;
        $this->assertStringContainsString('<p><strong>SOP Khusus UV-Vis</strong></p>', $savedSop);
        $this->assertStringContainsString('<ol><li>Saring larutan 0.45 mikron.</li></ol>', $savedSop);
        $this->assertStringNotContainsString('<script', $savedSop);
        $this->assertStringNotContainsString('onclick', $savedSop);
        $this->assertStringNotContainsString('style=', $savedSop);

        // Terhubung pada formulir pengajuan Pemohon (PBI-07)
        $this->actingAs($pemohon)
            ->get(route('pengajuan.create'))
            ->assertStatus(200)
            ->assertSee('SOP Khusus UV-Vis');

        // Hapus SOP mengosongkan teks tanpa menghapus mesin
        $this->actingAs($laboran)
            ->delete(route('laboran.sop.destroy', $mesin))
            ->assertRedirect(route('laboran.sop.index'))
            ->assertSessionHas('status');

        $this->assertNull($mesin->fresh()->sop_penyiapan);
        $this->assertDatabaseHas('mesin_lab', ['id' => $mesin->id]);
    }
}
