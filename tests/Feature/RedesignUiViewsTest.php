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
            ->assertSee('general-sans', false)
            ->assertSee('css/app.css?id=', false)
            ->assertSee('images/logo-telyuji.png', false)
            ->assertSee('id="themeToggleBtn"', false)
            ->assertSee('dusk="theme-toggle"', false)
            ->assertSee('id="mobileMenuBtn"', false)
            ->assertSee('id="mobileMenu"', false)
            ->assertSee('images/buildings/btp.jpg', false)
            ->assertSee('images/buildings/lab-kedokteran.jpg', false)
            ->assertSee('images/buildings/lab-fabrikasi.jpg', false)
            ->assertSee('images/buildings/lab-praktikum.jpg', false)
            ->assertSee('Fakultas &amp; Pusat Riset Kampus', false)
            ->assertSee('tu-ruler', false)
            ->assertSee('Alur Standar Pengujian Sampel')
            ->assertSee('tu-numeral', false)
            ->assertSee('Katalog Laboratorium', false)
            ->assertSee('tu-card-img', false)
            ->assertSee('tu-card-btn', false)
            ->assertSee('Ajukan Pengujian')
            ->assertSee('tu-chip tu-chip--cream', false)
            ->assertSee('Lab High Performance Computing')
            ->assertSee('Pertanyaan Umum (FAQ)')
            ->assertDontSee('Layanan Terakreditasi')
            ->assertDontSee('pad(idx + 1)', false)
            ->assertDontSee('Tahap 1 / 5')
            ->assertDontSee('<style>', false);
    }

    public function test_auth_pages_render_redesigned_auth_shell_and_password_toggle()
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200)
            ->assertSee('Masuk ke Telyuji')
            ->assertSee('placeholder="nama@telkomuniversity.ac.id"', false)
            ->assertDontSee('id="email-hint"', false)
            ->assertDontSee('Minimal 8 karakter')
            ->assertSee('data-toggle-password="password"', false)
            ->assertDontSee('tu-logo-mark inline-grid', false)
            ->assertDontSee('<style>', false);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSee('Pendaftaran Akun')
            ->assertSee('placeholder="nama@telkomuniversity.ac.id"', false)
            ->assertDontSee('id="email-hint"', false)
            ->assertSee('Minimal 8 karakter')
            ->assertSee('Kombinasi huruf kecil &amp; huruf besar', false)
            ->assertSee('Mengandung simbol')
            ->assertSee('id="password-match-hint"', false)
            ->assertSee('data-password-match="password"', false)
            ->assertSee('data-toggle-password="password-confirm"', false)
            ->assertDontSee('<style>', false);

        $this->get('/password/reset')
            ->assertStatus(200)
            ->assertSee('Atur Ulang Kata Sandi')
            ->assertSee('placeholder="nama@telkomuniversity.ac.id"', false)
            ->assertDontSee('id="email-hint"', false)
            ->assertDontSee('<style>', false);
    }

    public function test_auth_validation_enforces_telkom_university_email_and_strong_password()
    {
        $this->from('/login')->post('/login', [
            'email' => 'user@gmail.com',
            'password' => 'secret123',
        ])->assertRedirect('/login')
          ->assertSessionHasErrors(['email']);

        $this->from('/register')->post('/register', [
            'name' => 'Mahasiswa Uji',
            'email' => 'mahasiswa@gmail.com',
            'password' => 'tanpasimbol123A',
            'password_confirmation' => 'tanpasimbol123A',
        ])->assertRedirect('/register')
          ->assertSessionHasErrors(['email', 'password']);

        // Baik @telkomuniversity.ac.id (dosen/laboran) maupun @student.telkomuniversity.ac.id (mahasiswa) lolos validasi regex email
        $this->from('/password/reset/sample-token')->post('/password/reset', [
            'token' => 'sample-token',
            'email' => 'dosen@telkomuniversity.ac.id',
            'password' => 'tanpasimbol123A',
            'password_confirmation' => 'tanpasimbol123A',
        ])->assertRedirect('/password/reset/sample-token')
          ->assertSessionDoesntHaveErrors(['email'])
          ->assertSessionHasErrors(['password']);

        $this->from('/password/reset/sample-token')->post('/password/reset', [
            'token' => 'sample-token',
            'email' => 'mahasiswa@student.telkomuniversity.ac.id',
            'password' => 'tanpasimbol123A',
            'password_confirmation' => 'tanpasimbol123A',
        ])->assertRedirect('/password/reset/sample-token')
          ->assertSessionDoesntHaveErrors(['email'])
          ->assertSessionHasErrors(['password']);

        $this->from('/password/reset')->post('/password/email', [
            'email' => 'mahasiswa@gmail.com',
        ])->assertRedirect('/password/reset')
          ->assertSessionHasErrors(['email']);
    }

    public function test_dashboard_home_renders_redesigned_metrics_and_empty_state()
    {
        $user = User::factory()->make([
            'name' => 'Dosen Uji',
            'email' => 'dosen@telkomuniversity.ac.id',
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertStatus(200)
            ->assertSee('Dashboard Layanan Pengujian')
            ->assertSee('Dosen Uji')
            ->assertSee('Belum ada pengujian sampel aktif')
            ->assertSee('#katalog-lab', false)
            ->assertSee('#alur', false)
            ->assertDontSee('href="#"', false)
            ->assertDontSee('<style>', false);
    }

    public function test_remaining_auth_views_render_cleanly()
    {
        $this->get('/password/reset/sample-token?email=test%40telkomuniversity.ac.id')
            ->assertStatus(200)
            ->assertSee('Perbarui Kata Sandi')
            ->assertSee('placeholder="nama@telkomuniversity.ac.id"', false)
            ->assertDontSee('id="email-hint"', false)
            ->assertDontSee('<style>', false);

        $user = User::factory()->make([
            'name' => 'Dosen Uji',
            'email' => 'dosen@telkomuniversity.ac.id',
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

    public function test_custom_error_pages_render_lab_diagnostic_ui_without_inline_style_tags()
    {
        $this->get('/halaman-tidak-terdaftar-404')
            ->assertStatus(404)
            ->assertSee('dusk="error-page-404"', false)
            ->assertSee('tu-numeral', false)
            ->assertSee('404')
            ->assertSee('Halaman Tidak Ditemukan')
            ->assertDontSee('Lab System', false)
            ->assertDontSee('<style>', false);

        foreach (['403', '419', '429', '500', '503'] as $code) {
            $html = view('errors.' . $code)->render();
            $this->assertStringContainsString('dusk="error-page-' . $code . '"', $html);
            $this->assertStringContainsString('tu-numeral', $html);
            $this->assertStringContainsString($code, $html);
            $this->assertStringNotContainsString('Lab System', $html);
            $this->assertStringNotContainsString('<style>', $html);
        }
    }
}
