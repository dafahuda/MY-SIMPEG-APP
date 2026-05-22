<?php

namespace Tests\Feature\Security\Support;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;

trait RoleMatrixFixtures
{
    private static int $roleMatrixId = 100000;

    protected function createUnitKerja(?string $namaUnit = null): UnitKerja
    {
        return new UnitKerja([
            'id' => self::$roleMatrixId++,
            'nama_unit' => $namaUnit ?? fake()->unique()->words(3, true),
            'alamat' => 'Jl. Unit Matrix',
        ]);
    }

    protected function createRoleUser(string $role, string $username, string $email, ?int $unitKerjaId = null): User
    {
        return User::factory()->make([
            'id' => self::$roleMatrixId++,
            'username' => $username,
            'name' => ucfirst($role) . ' Matrix',
            'email' => $email,
            'email_verified_at' => now(),
            'role' => $role,
            'unit_kerja_id' => $unitKerjaId,
            'password' => bcrypt('password'),
        ]);
    }

    protected function createPegawaiUser(string $username, string $email, string $nama, ?int $unitKerjaId = null): array
    {
        $unitKerjaId ??= $this->createUnitKerja()->id;

        $user = $this->createRoleUser('pegawai', $username, $email, $unitKerjaId);

        $pegawai = new Pegawai([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerjaId,
            'foto' => 'foto.jpg',
            'nip' => fake()->unique()->numerify('##################'),
            'nik' => fake()->unique()->numerify('################'),
            'nama' => $nama,
            'gelar' => 'S.T.',
            'gelar_depan' => null,
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Belum Nikah',
            'alamat' => 'Jl. Matrix No. 1',
            'no_hp' => '081234567890',
            'email' => $email,
            'email_gov' => $email,
            'no_npwp' => '00.000.000.0-000.000',
            'no_bpjs' => '0000000000000001',
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-MATRIX',
            'no_sk_cpns' => 'SKCPNS-MATRIX',
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-MATRIX',
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'nilai_tpp' => 0,
        ]);
        $pegawai->id = self::$roleMatrixId++;

        return [$user, $pegawai];
    }
}
