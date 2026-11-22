<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Administrator Telyuji',
            'email' => 'admin@telkomuniversity.ac.id',
            'password' => Hash::make('Telyuji#2026'),
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_admin_can_view_user_management_page_without_inline_style_tags()
    {
        $admin = $this->createAdmin();
        User::factory()->create([
            'name' => 'Raka Pratama, S.T.',
            'email' => 'laboran@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200)
            ->assertSee('Manajemen Pengguna')
            ->assertSee('dusk="users-table"', false)
            ->assertSee('dusk="user-drawer-panel"', false)
            ->assertSee('dusk="btn-add-user"', false)
            ->assertSee('dusk="nav-admin-users"', false)
            ->assertDontSee('dusk="nav-laboran-pbi13"', false)
            ->assertDontSee('dusk="nav-pemohon-dashboard"', false)
            ->assertSee('laboran@telkomuniversity.ac.id')
            ->assertDontSee('<style>', false);
    }

    public function test_each_role_is_redirected_and_rendered_according_to_its_own_role()
    {
        $admin = $this->createAdmin();
        $laboran = User::factory()->create([
            'name' => 'Raka Pratama, S.T.',
            'email' => 'laboran@telkomuniversity.ac.id',
            'password' => Hash::make('Telyuji#2026'),
            'role' => User::ROLE_LABORAN,
        ]);
        $pemohon = User::factory()->create([
            'name' => 'Nadia Putri Rahmawati',
            'email' => 'pemohon@telkomuniversity.ac.id',
            'password' => Hash::make('Telyuji#2026'),
            'role' => User::ROLE_PEMOHON,
        ]);

        // 1. Login sebagai Admin diarahkan langsung ke /admin/pengguna
        $this->post('/login', [
            'email' => 'admin@telkomuniversity.ac.id',
            'password' => 'Telyuji#2026',
        ])->assertRedirect(route('admin.users.index'));
        $this->post('/logout');

        // 2. Login sebagai Laboran diarahkan langsung ke /laboran/pengujian
        $this->post('/login', [
            'email' => 'laboran@telkomuniversity.ac.id',
            'password' => 'Telyuji#2026',
        ])->assertRedirect(route('laboran.pengujian.index'));
        $this->post('/logout');

        // 3. Login sebagai Pemohon diarahkan ke /home
        $this->post('/login', [
            'email' => 'pemohon@telkomuniversity.ac.id',
            'password' => 'Telyuji#2026',
        ])->assertRedirect(route('home'));

        // 4. Tampilan Pemohon di /home hanya menampilkan menu Dashboard (tanpa menu Admin/Laboran)
        $this->actingAs($pemohon)
            ->get('/home')
            ->assertStatus(200)
            ->assertSee('Dashboard Layanan Pengujian')
            ->assertSee('dusk="nav-pemohon-dashboard"', false)
            ->assertDontSee('dusk="nav-admin-users"', false)
            ->assertDontSee('dusk="nav-laboran-pbi13"', false);

        // 5. Admin mengakses /home menampilkan halaman Manajemen Pengguna
        $this->actingAs($admin)
            ->get('/home')
            ->assertStatus(200)
            ->assertSee('Manajemen Pengguna')
            ->assertSee('dusk="users-table"', false);

        // 6. Admin tidak dapat mengakses workspace operasional Laboran (pemisahan tugas ketat)
        $this->actingAs($admin)
            ->get(route('laboran.pengujian.index'))
            ->assertStatus(403);
    }

    public function test_non_admin_roles_are_forbidden_from_accessing_user_management()
    {
        $laboran = User::factory()->create([
            'email' => 'laboran@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);
        $pemohon = User::factory()->create([
            'email' => 'pemohon@student.telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);

        $this->actingAs($laboran)
            ->get(route('admin.users.index'))
            ->assertStatus(403);

        $this->actingAs($pemohon)
            ->get(route('admin.users.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_create_user_with_telkom_email_and_rejects_external_domain()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pengguna Luar',
                'email' => 'luar@gmail.com',
                'role' => User::ROLE_PEMOHON,
                'password' => 'Telyuji#2026',
                'password_confirmation' => 'Telyuji#2026',
            ])
            ->assertSessionHasErrors(['email']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Dr. Budi Raharjo',
                'email' => 'budi.raharjo@telkomuniversity.ac.id',
                'role' => User::ROLE_PEMOHON,
                'password' => 'Telyuji#2026',
                'password_confirmation' => 'Telyuji#2026',
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'Akun pengguna berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'email' => 'budi.raharjo@telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);
    }

    public function test_admin_can_update_and_delete_other_users_but_cannot_delete_or_demote_self()
    {
        $admin = $this->createAdmin();
        $target = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@student.telkomuniversity.ac.id',
            'role' => User::ROLE_PEMOHON,
        ]);

        // Update peran & nama tanpa mengubah password
        $this->actingAs($admin)
            ->put(route('admin.users.update', $target->id), [
                'name' => 'Siti Aminah, S.Si.',
                'email' => 'siti@telkomuniversity.ac.id',
                'role' => User::ROLE_LABORAN,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Siti Aminah, S.Si.',
            'email' => 'siti@telkomuniversity.ac.id',
            'role' => User::ROLE_LABORAN,
        ]);

        // Admin tidak dapat menurunkan perannya sendiri
        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin->id), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => User::ROLE_PEMOHON,
            ])
            ->assertSessionHasErrors(['role']);

        // Admin tidak dapat menghapus akunnya sendiri
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin->id))
            ->assertSessionHasErrors(['delete']);

        // Admin dapat menghapus akun pengguna lain
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $target->id))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }
}
