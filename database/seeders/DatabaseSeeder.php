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
        // Migrasikan akun lama admin@telyuji.ac.id jika masih ada secara mulus tanpa membuat duplikasi
        $oldAdmin = User::where('email', 'admin@telyuji.ac.id')->first();
        if ($oldAdmin) {
            $existingNew = User::where('email', 'admin@student.telkomuniversity.ac.id')->first();
            if (!$existingNew) {
                $oldAdmin->update([
                    'email'    => 'admin@student.telkomuniversity.ac.id',
                    'name'     => 'Kepala Laboratorium',
                    'password' => Hash::make('Telyuji#2026'),
                    'role'     => User::ROLE_ADMIN,
                ]);
            }
        }

        // Admin / Kepala Lab (kebijakan domain @student.telkomuniversity.ac.id)
        User::updateOrCreate(
            ['email' => 'admin@student.telkomuniversity.ac.id'],
            [
                'name'     => 'Kepala Laboratorium',
                'password' => Hash::make('Telyuji#2026'),
                'role'     => User::ROLE_ADMIN,
            ]
        );

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
