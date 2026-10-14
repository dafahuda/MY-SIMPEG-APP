<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pangkat>
 */
class PangkatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'master_pangkat_id' => \App\Models\MasterPangkat::factory(),
            'master_golongan_id' => \App\Models\MasterGolongan::factory(),
            'jenis_pangkat' => 'Contoh Jenis Pangkat',
            'tmt_pangkat_mulai' => 'Contoh Tmt Pangkat Mulai',
            'tmt_pangkat_selesai' => 'Contoh Tmt Pangkat Selesai',
            'no_sk' => '3273012501900004',
            'tgl_sk' => '2026-01-01',
            'pejabat_pengesah_sk' => 'Contoh Pejabat Pengesah Sk',
        ];
    }
}
