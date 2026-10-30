<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RedesignUiViewsTest extends TestCase
{
    public function test_landing_page_renders_redesigned_ui_without_inline_style_tags()
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Layanan Pengujian')
            ->assertSee('images/logo-telyuji.png', false)
            ->assertSee('images/buildings/btp.jpg', false)
            ->assertSee('images/buildings/lab-kedokteran.jpg', false)
            ->assertSee('images/buildings/lab-fabrikasi.jpg', false)
            ->assertSee('images/buildings/lab-praktikum.jpg', false)
            ->assertSee('Alur Standar Pengujian Sampel')
            ->assertSee('Katalog Laboratorium', false)
            ->assertSee('Pertanyaan Umum (FAQ)')
            ->assertDontSee('<style>', false);
    }

    public function test_auth_pages_render_redesigned_auth_shell_and_password_toggle()
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200)
            ->assertSee('Masuk ke Telyuji')
            ->assertSee('data-toggle-password="password"', false)
            ->assertDontSee('tu-logo-mark inline-grid', false)
            ->assertDontSee('<style>', false);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSee('Pendaftaran Akun')
            ->assertSee('data-toggle-password="password-confirm"', false)
            ->assertDontSee('<style>', false);

        $this->get('/password/reset')
            ->assertStatus(200)
            ->assertSee('Atur Ulang Kata Sandi')
            ->assertDontSee('<style>', false);
    }

    public function test_dashboard_home_renders_redesigned_metrics_and_empty_state()
    {
        $user = User::factory()->make([
            'name' => 'Mahasiswa Uji',
            'email' => 'mahasiswa@telkomuniversity.ac.id',
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertStatus(200)
            ->assertSee('Dashboard Layanan Pengujian')
            ->assertSee('Mahasiswa Uji')
            ->assertSee('Belum ada pengujian sampel aktif')
            ->assertDontSee('<style>', false);
    }

    public function test_remaining_auth_views_render_cleanly()
    {
        $this->get('/password/reset/sample-token?email=test%40telkomuniversity.ac.id')
            ->assertStatus(200)
            ->assertSee('Perbarui Kata Sandi')
            ->assertDontSee('<style>', false);

        $user = User::factory()->make([
            'name' => 'Mahasiswa Uji',
            'email' => 'mahasiswa@telkomuniversity.ac.id',
        ]);

        $this->actingAs($user)
            ->get('/password/confirm')
            ->assertStatus(200)
            ->assertSee('Konfirmasi Kata Sandi')
            ->assertDontSee('<style>', false);

        $verifyHtml = view('auth.verify')->render();
        $this->assertStringContainsString('Verifikasi Email Anda', $verifyHtml);
        $this->assertStringNotContainsString('<style>', $verifyHtml);
    }
}
