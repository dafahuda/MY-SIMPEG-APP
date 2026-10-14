<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seminar>
 */
class SeminarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_seminar' => 'Seminar Nasional',
            'tingkat_kegiatan' => 'Nasional',
            'tempat_seminar' => 'Contoh Tempat Seminar',
            'tgl_seminar' => '2026-01-01',
            'penyelenggara' => 'Contoh Penyelenggara',
            'jumlah_jam' => 8,
            'no_piagam' => '3273012501900004',
            'tgl_piagam' => '2026-01-01',
            'file_piagam' => 'Contoh File Piagam',
        ];
    }
}
