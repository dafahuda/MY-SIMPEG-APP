<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hukuman>
 */
class HukumanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pelanggaran_yg_dilakukan' => 'Terlambat hadir',
            'tingkat_hukuman' => 'Ringan',
            'jenis_hukuman' => 'Teguran Lisan',
            'isi_teguran' => 'Contoh Isi Teguran',
            'pejabat_pengesahan_sk_hukuman' => 'Contoh Pejabat Pengesahan Sk Hukuman',
            'no_sk' => '3273012501900004',
            'tgl_pengesahan_sk' => '2026-01-01',
            'tmt_hukuman_mulai' => 'Contoh Tmt Hukuman Mulai',
            'tmt_hukuman_pemulihan' => 'Contoh Tmt Hukuman Pemulihan',
            'pejabat_pemulihan_hukuman' => 'Contoh Pejabat Pemulihan Hukuman',
            'no_pemulihan_hukuman' => '3273012501900004',
            'tgl_pemulihan_hukuman' => '2026-01-01',
        ];
    }
}
