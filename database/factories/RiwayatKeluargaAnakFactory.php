<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiwayatKeluargaAnak>
 */
class RiwayatKeluargaAnakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => '3273012501900001',
            'nama' => 'Contoh Nama',
            'tempat_lahir' => 'Contoh Tempat Lahir',
            'tgl_lahir' => '2026-01-01',
            'jenis_kelamin' => 'laki-laki',
            'pendidikan' => 'SD',
            'pekerjaan' => 'Contoh Pekerjaan',
            'status_hubungan' => 'Anak Kandung',
        ];
    }
}
