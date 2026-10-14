<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RiwayatPendidikanBahasa>
 */
class RiwayatPendidikanBahasaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenis_bahasa' => 'Inggris',
            'bahasa' => 'Contoh Bahasa',
            'kemampuan_bicara' => 'Aktif'
        ];
    }
}
