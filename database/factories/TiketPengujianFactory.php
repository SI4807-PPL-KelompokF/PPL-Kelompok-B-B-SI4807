<?php

namespace Database\Factories;

use App\Models\TiketPengujian;
use Illuminate\Database\Eloquent\Factories\Factory;

class TiketPengujianFactory extends Factory
{
    protected $model = TiketPengujian::class;

    /**
     * Daftar instrumen mesin laboratorium yang realistis.
     */
    private static array $instrumenList = [
        'XRF (X-Ray Fluorescence)',
        'SEM (Scanning Electron Microscope)',
        'FTIR Spectroscopy',
        'UV-Vis Spectrophotometer',
        'AAS (Atomic Absorption Spectroscopy)',
        'GC-MS (Gas Chromatography-Mass Spectrometry)',
        'HPLC (High-Performance Liquid Chromatography)',
        'TGA (Thermogravimetric Analysis)',
    ];

    /**
     * Daftar fakultas Telkom University.
     */
    private static array $fakultasList = [
        'Fakultas Teknik Elektro',
        'Fakultas Informatika',
        'Fakultas Rekayasa Industri',
        'Fakultas Ekonomi dan Bisnis',
        'Fakultas Komunikasi dan Desain',
        'Fakultas Ilmu Terapan',
    ];

    public function definition(): array
    {
        $instrumen = $this->faker->randomElement(self::$instrumenList);
        $status    = $this->faker->randomElement([
            TiketPengujian::STATUS_MENUNGGU,
            TiketPengujian::STATUS_SEDANG_DIUJI,
            TiketPengujian::STATUS_SELESAI,
        ]);

        return [
            'nomor_tiket'  => 'TLY-TEST-' . strtoupper($this->faker->unique()->bothify('######')),
            'user_id'      => null,
            'nama_pemohon' => $this->faker->name(),
            'nim_nip'      => $this->faker->numerify('##############'),
            'fakultas'     => $this->faker->randomElement(self::$fakultasList),
            'nama_sampel'  => $this->faker->words(3, true),
            'instrumen'    => $instrumen,
            'laboratorium' => 'Lab ' . $this->faker->word(),
            'status'       => $status,
            'tanggal_uji'  => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function menunggu(): static
    {
        return $this->state(['status' => TiketPengujian::STATUS_MENUNGGU]);
    }

    public function sedangDiuji(): static
    {
        return $this->state(['status' => TiketPengujian::STATUS_SEDANG_DIUJI]);
    }

    public function selesai(): static
    {
        return $this->state(['status' => TiketPengujian::STATUS_SELESAI]);
    }

    public function forFakultas(string $fakultas): static
    {
        return $this->state(['fakultas' => $fakultas]);
    }

    public function forInstrumen(string $instrumen): static
    {
        return $this->state(['instrumen' => $instrumen]);
    }
}
