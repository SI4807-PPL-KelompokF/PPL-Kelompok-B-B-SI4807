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
        User::updateOrCreate(
            ['email' => 'laboran@student.telkomuniversity.ac.id'],
            [
                'name' => 'Raka Pratama, S.T.',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_LABORAN,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pemohon@student.telkomuniversity.ac.id'],
            [
                'name' => 'Nadia Putri Rahmawati',
                'password' => Hash::make('Telyuji#2026'),
                'role' => User::ROLE_PEMOHON,
            ]
        );

        app(DokumenHasilUjiService::class)->ensureInitialDataSeeded();
    }
}
