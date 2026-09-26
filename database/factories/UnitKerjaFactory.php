<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnitKerja>
 */
class UnitKerjaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $units = [
            'Sekretariat Daerah', 'Dinas Pendidikan', 'Dinas Kesehatan',
            'Dinas Sosial', 'Badan Kepegawaian Daerah', 'Dinas Pekerjaan Umum',
            'Badan Pengelolaan Keuangan', 'Dinas Komunikasi dan Informatika',
            'Badan Perencanaan Pembangunan', 'Dinas Kependudukan dan Pencatatan Sipil',
        ];

        return [
            'nama_unit' => $units[($counter - 1) % count($units)] . ' ' . $this->faker->numberBetween(1, 99),
            'alamat' => $this->faker->address(),
        ];
    }
}
