<?php

namespace Database\Seeders;

use App\Http\Controllers\LaboranHasilUjiController;
use App\Models\User;
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
                'role' => 'laboran',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pemohon@student.telkomuniversity.ac.id'],
            [
                'name' => 'Nadia Putri Rahmawati',
                'password' => Hash::make('Telyuji#2026'),
                'role' => 'pemohon',
            ]
        );

        app(LaboranHasilUjiController::class)->ensureInitialDataSeeded();
    }
}
