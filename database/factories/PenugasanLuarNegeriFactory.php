<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PenugasanLuarNegeri>
 */
class PenugasanLuarNegeriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alasan_penugasan' => 'Diklat singkat',
            'negara_tujuan' => 'Contoh Negara Tujuan',
            'tahun' => 2026,
            'durasi_hari' => 'Contoh Durasi Hari',
            'no_st' => '3273012501900004',
            'st' => 'Contoh St'
        ];
    }
}
