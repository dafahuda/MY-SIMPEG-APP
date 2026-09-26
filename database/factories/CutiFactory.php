<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cuti>
 */
class CutiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = $this->faker->dateTimeBetween('-1 month', '+2 months');
        $selesai = (clone $mulai)->modify('+' . $this->faker->numberBetween(1, 10) . ' days');

        return [
            'pegawai_id' => \App\Models\Pegawai::factory(),
            'jenis_cuti' => $this->faker->randomElement([
                'Tahunan', 'Besar', 'Sakit', 'Menikah', 'Bersalin',
                'Meninggalkan Pekerjaan', 'Karena Alasan Penting', 'Diluar Tanggungan Negara',
            ]),
            'no_surat_cuti' => 'SC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'tgl_surat_cuti' => $mulai->format('Y-m-d'),
            'pelaksanaan_cuti_mulai' => $mulai->format('Y-m-d'),
            'pelaksanaan_cuti_selesai' => $selesai->format('Y-m-d'),
            'durasi_cuti' => (string) $this->faker->numberBetween(1, 10),
            'ketentuan_a' => '-',
            'ketentuan_b' => '-',
            'ketentuan_c' => '-',
            'tebusan' => '-',
            'status' => 'pending',
        ];
    }
}
