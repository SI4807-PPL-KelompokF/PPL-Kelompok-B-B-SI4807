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
            ->assertSee('placeholder="nama@student.telkomuniversity.ac.id"', false)
            ->assertDontSee('id="email-hint"', false)
            ->assertDontSee('Minimal 8 karakter')
            ->assertSee('data-toggle-password="password"', false)
            ->assertDontSee('tu-logo-mark inline-grid', false)
            ->assertDontSee('<style>', false);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSee('Pendaftaran Akun')
            ->assertSee('placeholder="nama@student.telkomuniversity.ac.id"', false)
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
            ->assertSee('placeholder="nama@student.telkomuniversity.ac.id"', false)
            ->assertDontSee('id="email-hint"', false)
            ->assertDontSee('<style>', false);
    }

    public function test_auth_validation_enforces_student_email_and_strong_password()
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

        $this->from('/password/reset')->post('/password/email', [
            'email' => 'mahasiswa@gmail.com',
        ])->assertRedirect('/password/reset')
          ->assertSessionHasErrors(['email']);
    }

    public function test_dashboard_home_renders_redesigned_metrics_and_empty_state()
    {
        $user = User::factory()->make([
            'name' => 'Mahasiswa Uji',
            'email' => 'mahasiswa@student.telkomuniversity.ac.id',
        ]);

        $this->actingAs($user)
            ->get('/home')
            ->assertStatus(200)
            ->assertSee('Dashboard Layanan Pengujian')
            ->assertSee('Mahasiswa Uji')
            ->assertSee('Belum ada pengujian sampel aktif')
            ->assertSee('#katalog-lab', false)
            ->assertSee('#alur', false)
            ->assertDontSee('href="#"', false)
            ->assertDontSee('<style>', false);
    }

    public function test_remaining_auth_views_render_cleanly()
    {
        $this->get('/password/reset/sample-token?email=test%40student.telkomuniversity.ac.id')
            ->assertStatus(200)
            ->assertSee('Perbarui Kata Sandi')
            ->assertSee('placeholder="nama@student.telkomuniversity.ac.id"', false)
            ->assertDontSee('id="email-hint"', false)
            ->assertDontSee('<style>', false);

        $user = User::factory()->make([
            'name' => 'Mahasiswa Uji',
            'email' => 'mahasiswa@student.telkomuniversity.ac.id',
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
