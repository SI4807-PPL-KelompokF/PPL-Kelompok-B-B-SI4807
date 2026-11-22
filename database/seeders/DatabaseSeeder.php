<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use App\Models\MesinLab;
use App\Models\User;
use App\Services\DokumenHasilUjiService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database secara idempoten.
     *
     * @return void
     */
    public function run()
    {
        $this->migrateLegacyEmail('admin@telyuji.ac.id', 'admin@telkomuniversity.ac.id');
        $this->migrateLegacyEmail('admin@student.telkomuniversity.ac.id', 'admin@telkomuniversity.ac.id');
        $this->migrateLegacyEmail('laboran@student.telkomuniversity.ac.id', 'laboran@telkomuniversity.ac.id');
        $this->migrateLegacyEmail('pemohon@student.telkomuniversity.ac.id', 'pemohon@telkomuniversity.ac.id');

        User::updateOrCreate(
            ['email' => 'admin@telkomuniversity.ac.id'],
            [
                'name' => 'Administrator Telyuji',
                'nama_lengkap' => 'Administrator Telyuji',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'laboran@telkomuniversity.ac.id'],
            [
                'name' => 'Raka Pratama, S.T.',
                'nama_lengkap' => 'Raka Pratama, S.T.',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_LABORAN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pemohon@telkomuniversity.ac.id'],
            [
                'name' => 'Nadia Putri Rahmawati',
                'nama_lengkap' => 'Nadia Putri Rahmawati',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
            ]
        );

        Fakultas::seedDefaultData();
        MesinLab::seedDefaultData();
        app(DokumenHasilUjiService::class)->ensureInitialDataSeeded();
    }

    /**
     * Migrasikan akun legacy ke email resmi @telkomuniversity.ac.id tanpa menimbulkan duplikasi.
     */
    private function migrateLegacyEmail(string $legacyEmail, string $targetEmail): void
    {
        if (
            User::where('email', $legacyEmail)->exists()
            && !User::where('email', $targetEmail)->exists()
        ) {
            User::where('email', $legacyEmail)->update(['email' => $targetEmail]);
        }
    }
}
