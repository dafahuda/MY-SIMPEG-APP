<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KGB>
 */
class KGBFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tmt = $this->faker->dateTimeBetween('-2 years', '+1 year');

        return [
            'pegawai_id' => \App\Models\Pegawai::factory(),
            'no_kgb' => 'KGB-' . $this->faker->unique()->numberBetween(1000, 9999),
            'tgl_kgb' => (clone $tmt)->modify('-30 days')->format('Y-m-d'),
            'pejabat' => 'Kepala ' . $this->faker->company(),
            'no_sk_terakhir' => 'SK-' . $this->faker->numberBetween(100, 999),
            'tgl_sk_terakhir' => (clone $tmt)->modify('-2 years')->format('Y-m-d'),
            'tgl_berlaku_gaji' => $tmt->format('Y-m-d'),
            'masa_kerja_lama_tahun' => (string) $this->faker->numberBetween(1, 30),
            'masa_kerja_lama_bulan' => '0',
            'gaji_baru' => (string) ($this->faker->numberBetween(2, 5) * 1000000),
            'gaji_baru_terbilang' => ' Lima Ratus Ribu Rupiah',
            'masa_kerja_baru_tahun' => (string) $this->faker->numberBetween(2, 31),
            'masa_kerja_baru_bulan' => '0',
            'tmt_kgb' => $tmt->format('Y-m-d'),
            'tembusan' => null,
            'periode' => (string) $tmt->format('Y'),
        ];
    }
}
