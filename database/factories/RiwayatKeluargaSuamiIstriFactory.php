<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiwayatKeluargaSuamiIstri>
 */
class RiwayatKeluargaSuamiIstriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_ktp_nik' => '3273012501880003',
            'nama' => 'Contoh Nama',
            'tgl_lahir' => '2026-01-01',
            'tempat_lahir' => 'Contoh Tempat Lahir',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Contoh Pekerjaan',
            'status_hubungan' => 'Suami'
        ];
    }
}
