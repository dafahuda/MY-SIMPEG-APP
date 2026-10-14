<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mutasi>
 */
class MutasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis_mutasi' => 'Masuk',
            'instansi_tujuan' => 'Contoh Instansi Tujuan',
            'no_sk_mutasi' => '3273012501900004',
            'tgl_sk_mutasi' => '2026-01-01'
        ];
    }
}
