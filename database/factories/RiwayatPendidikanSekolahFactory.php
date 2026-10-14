<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiwayatPendidikanSekolah>
 */
class RiwayatPendidikanSekolahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenjang_pendidikan' => 'S1',
            'nama_sekolah_universitas' => 'Contoh Nama Sekolah Universitas',
            'lokasi' => 'Contoh Lokasi',
            'jurusan' => 'Contoh Jurusan',
            'no_ijazah' => '3273012501900004',
            'tgl_ijazah' => '2026-01-01',
            'nama_kepsek_rektor' => 'Contoh Nama Kepsek Rektor'
        ];
    }
}
