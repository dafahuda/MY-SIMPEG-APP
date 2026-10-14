<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstansiLembaga>
 */
class InstansiLembagaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //,
            'nama_instansi_lembaga' => 'Contoh Nama Instansi Lembaga',
            'kabupaten_kota' => 'Kabupaten',
            'nama_kota_kabupaten' => 'Contoh Nama Kota Kabupaten',
            'alamat' => 'Contoh Alamat',
            'no_telp' => 'Contoh No Telp',
            'email' => 'Contoh Email',
            'kepala_dinas' => 'Contoh Kepala Dinas',
            'nip' => 'Contoh Nip',
            'gambar_logo' => 'Contoh Gambar Logo',
        ];
    }
}
