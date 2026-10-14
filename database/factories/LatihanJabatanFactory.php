<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LatihanJabatan>
 */
class LatihanJabatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tempat_latihan' => 'Jakarta',
            'waktu_latihan' => '2026-01-01',
            'nama_pelatih' => 'Contoh Nama Pelatih',
            'tahun_latihan' => 2026,
            'jumlah_jam' => 8,
            'nomor_sertifikat' => 'Contoh Nomor Sertifikat',
            'tgl_sertifikat' => '2026-01-01',
            'file_sertifikat' => 'Contoh File Sertifikat',
        ];
    }
}
