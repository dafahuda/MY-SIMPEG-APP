<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penghargaan>
 */
class PenghargaanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_penghargaan' => 'Satyalancana Karya Satya',
            'instansi_pemberi' => 'Contoh Instansi Pemberi',
            'tingkat_kegiatan' => 'Nasional',
            'tempat_penghargaan' => 'Contoh Tempat Penghargaan',
            'tgl_penghargaan' => '2026-01-01',
            'tahun' => 2026,
            'no_sertifikat' => '3273012501900004'
        ];
    }
}
