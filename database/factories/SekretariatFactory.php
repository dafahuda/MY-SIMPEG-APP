<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sekretariat>
 */
class SekretariatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_sekretariat' => 'Sekretariat Utama',
            'kabupaten_kota' => 'Kabupaten',
            'nama_kabupaten_kota' => 'Contoh Nama Kabupaten Kota',
            'alamat' => 'Contoh Alamat',
            'no_telp' => '3273012501900004',
            'email' => 'Contoh Email',
            'sekretaris' => 'Contoh Sekretaris',
            'nip' => '3273012501900004',
            'gambar_logo' => 'Contoh Gambar Logo',
        ];
    }
}
