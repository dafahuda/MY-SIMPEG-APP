<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jabatan>
 */
class JabatanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'master_jabatan_id' => \App\Models\MasterJabatan::factory(),
            'master_eselon_id' => \App\Models\MasterEselon::factory(),
            'jenis_jabatan' => 'Jabatan Struktural',
            'periode' => '-',
            'tahun_ke' => '1',
            'no_sk' => '3273012501900004',
            'tgl_sk' => '2026-01-01',
            'terbit' => 'Contoh Terbit',
        ];
    }
}
