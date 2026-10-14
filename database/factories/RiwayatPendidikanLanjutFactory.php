<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiwayatPendidikanLanjut>
 */
class RiwayatPendidikanLanjutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenjang_pendidikan' => 'S2',
            'nama_sekolah_universitas' => 'Contoh Nama Sekolah Universitas',
            'jurusan' => 'Contoh Jurusan',
            'thn_mulai' => 'Contoh Thn Mulai',
            'thn_selesai' => 'Contoh Thn Selesai',
            'status' => 'Tugas Belajar'
        ];
    }
}
