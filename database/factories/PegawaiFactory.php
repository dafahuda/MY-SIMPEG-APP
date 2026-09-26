<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pegawai>
 */
class PegawaiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'unit_kerja_id' => \App\Models\UnitKerja::factory(),
            'foto' => '-',
            'nip' => (string) $this->faker->unique()->numerify('##################'),
            'nama' => $this->faker->name(),
            'gelar' => $this->faker->randomElement(['S.T.', 'S.E.', 'S.Kom.', 'M.M.', 'S.Sos.']),
            'tmpt_lahir' => $this->faker->city(),
            'tgl_lahir' => $this->faker->date('Y-m-d', '-25 years'),
            'jenis_kelamin' => $this->faker->randomElement(['laki-laki', 'perempuan']),
            'agama' => $this->faker->randomElement(['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Kong Hu Cu']),
            'golongan_darah' => $this->faker->randomElement(['A', 'AB', 'B', 'O', 'Tidak Tahu']),
            'status_pernikahan' => $this->faker->randomElement(['Nikah', 'Belum Nikah', 'Cerai Mati', 'Cerai Hidup']),
            'nik' => (string) $this->faker->unique()->numerify('################'),
            'alamat' => $this->faker->address(),
            'no_hp' => '08' . $this->faker->numerify('##########'),
            'email' => $this->faker->safeEmail(),
            'email_gov' => $this->faker->safeEmail(),
            'no_npwp' => (string) $this->faker->numerify('##############'),
            'no_bpjs' => (string) $this->faker->numerify('############'),
            'status_kepegawaian' => $this->faker->randomElement(['PNS', 'PPPK', 'CPNS']),
            'karpeg' => (string) $this->faker->numerify('##########'),
            'no_sk_cpns' => null,
            'tmt_cpns' => null,
            'no_sk_pns' => null,
            'tmt_pns' => $this->faker->date('Y-m-d', '-10 years'),
            'gol_awal' => $this->faker->randomElement(['I/a', 'II/a', 'III/a']),
            'nilai_tpp' => 0,
        ];
    }
}
