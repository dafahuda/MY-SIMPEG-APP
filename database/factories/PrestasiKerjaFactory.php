<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PrestasiKerja>
 */
class PrestasiKerjaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'periode_nilai_dari' => '2026-01-01',
            'periode_nilai_sampai' => '2026-06-30',
            'tgl_pejabat_penilai' => '2026-07-01',
            'isi_tanggapan' => 'Pertahankan',
            'tgl_diterima_atasan' => '2026-07-02',
            'tahun_periode' => 2026,
            'nama_pejabat_nilai' => 'Contoh Nama Pejabat Nilai',
            'nama_atasan_pejabat_penilai' => 'Contoh Nama Atasan Pejabat Penilai',
            'tgl_keberatan_pegawai' => '2026-01-01',
            'isi_keberatan' => 'Contoh Isi Keberatan',
            'tgl_keputusan_atasan_pejabat_penilai' => '2026-01-01',
            'isi_keputusan' => 'Contoh Isi Keputusan',
            'rekomendasi' => 'Contoh Rekomendasi',
            'tgl_diterima_pegawai' => '2026-01-01',
        ];
    }
}
