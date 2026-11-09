<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\DokumenHasilUjiService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Migrasi otomatis akun lama berdomain @student.telkomuniversity.ac.id ke @telkomuniversity.ac.id
        if (
            User::where('email', 'laboran@student.telkomuniversity.ac.id')->exists()
            && !User::where('email', 'laboran@telkomuniversity.ac.id')->exists()
        ) {
            User::where('email', 'laboran@student.telkomuniversity.ac.id')
                ->update(['email' => 'laboran@telkomuniversity.ac.id']);
        }

        if (
            User::where('email', 'pemohon@student.telkomuniversity.ac.id')->exists()
            && !User::where('email', 'pemohon@telkomuniversity.ac.id')->exists()
        ) {
            User::where('email', 'pemohon@student.telkomuniversity.ac.id')
                ->update(['email' => 'pemohon@telkomuniversity.ac.id']);
        }

        User::updateOrCreate(
            ['email' => 'admin@telkomuniversity.ac.id'],
            [
                'name' => 'Administrator Telyuji',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'laboran@telkomuniversity.ac.id'],
            [
                'name' => 'Raka Pratama, S.T.',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_LABORAN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pemohon@telkomuniversity.ac.id'],
            [
                'name' => 'Nadia Putri Rahmawati',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
            ]
        );

        app(DokumenHasilUjiService::class)->ensureInitialDataSeeded();
    }
}
