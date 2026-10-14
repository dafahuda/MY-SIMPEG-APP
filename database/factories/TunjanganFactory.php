<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tunjangan>
 */
class TunjanganFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_tunjangan' => 'TRB/001/2026',
            'jenis_tunjangan_anak' => 'Anak Pertama',
            'tgl_tunjangan' => '2026-01-01',
            'terhitung_mulai' => 'Contoh Terhitung Mulai',
            'akta_perkawinan_dari' => 'Contoh Akta Perkawinan Dari',
            'no_akta_perkawinan' => '3273012501900004',
            'tgl_akta_perkawinan' => '2026-01-01',
            'akta_kelahiran_dari' => 'Contoh Akta Kelahiran Dari',
            'no_akta_kelahiran' => '3273012501900004',
            'tgl_akta_kelahiran' => '2026-01-01',
            'tebusan' => 'Contoh Tebusan',
        ];
    }
}
