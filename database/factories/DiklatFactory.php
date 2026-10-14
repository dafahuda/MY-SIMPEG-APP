<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diklat>
 */
class DiklatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_diklat' => 'Diklat Dasar',
            'jumlah_jam' => 8,
            'penyelenggara' => 'Contoh Penyelenggara',
            'tempat' => 'Contoh Tempat',
            'angkatan' => 'Contoh Angkatan',
            'tahun' => 2026,
            'no_sttpp' => '3273012501900004',
            'tgl_sttpp' => '2026-01-01'
        ];
    }
}
