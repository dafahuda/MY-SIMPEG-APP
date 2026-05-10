<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;

trait DiklatGapReportFixtures
{
    private function seedGapReportFixtures(): array
    {
        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit A Gap',
            'alamat' => 'Jl. Unit A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit B Gap',
            'alamat' => 'Jl. Unit B',
        ]);

        $admin = User::create([
            'username' => 'admin-gap-unit',
            'name' => 'Admin Gap Unit',
            'email' => 'admin.gap.unit@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $unitA->id,
            'password' => bcrypt('password'),
        ]);

        $superadmin = User::create([
            'username' => 'superadmin-gap-unit',
            'name' => 'Superadmin Gap Unit',
            'email' => 'superadmin.gap.unit@example.test',
            'role' => 'superadmin',
            'password' => bcrypt('password'),
        ]);

        $pegawaiA = $this->createPegawai('pegawai-gap-a', 'pegawai.gap.a@example.test', 'Pegawai Gap A', $unitA);
        $pegawaiB = $this->createPegawai('pegawai-gap-b', 'pegawai.gap.b@example.test', 'Pegawai Gap B', $unitB);

        $rencanaAPlanned = $this->createRencana($pegawaiA, '2026', 'Rencana A Planned', 'planned', 40);
        $rencanaARealized = $this->createRencana($pegawaiA, '2026', 'Rencana A Realized', 'realized', 30);
        $this->createDiklat($pegawaiA, $rencanaARealized, '2027', 'Diklat A Realized', 28);
        $this->createDiklat($pegawaiA, null, '2027', 'Diklat A Out of Plan', 16);

        $rencanaBRealized = $this->createRencana($pegawaiB, '2026', 'Rencana B Realized', 'realized', 50);
        $this->createDiklat($pegawaiB, $rencanaBRealized, '2027', 'Diklat B Realized', 40);
        $this->createDiklat($pegawaiB, null, '2027', 'Diklat B Out of Plan', 20);

        return [
            'unitA' => $unitA,
            'unitB' => $unitB,
            'admin' => $admin,
            'superadmin' => $superadmin,
            'pegawaiA' => $pegawaiA,
            'pegawaiB' => $pegawaiB,
            'rencanaAPlanned' => $rencanaAPlanned,
            'rencanaARealized' => $rencanaARealized,
            'rencanaBRealized' => $rencanaBRealized,
        ];
    }

    private function createPegawai(string $username, string $email, string $nama, UnitKerja $unitKerja): Pegawai
    {
        $user = User::create([
            'username' => $username,
            'name' => $nama,
            'email' => $email,
            'role' => 'pegawai',
            'unit_kerja_id' => $unitKerja->id,
            'password' => bcrypt('password'),
        ]);

        return Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto.jpg',
            'nip' => '198801012020011001',
            'nik' => '3276010101880001',
            'nama' => $nama,
            'gelar' => 'S.T.',
            'gelar_depan' => 'Ir.',
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1988-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Nikah',
            'alamat' => 'Jl. Contoh No. 1',
            'no_hp' => '081234567890',
            'email' => $email,
            'email_gov' => $email,
            'no_npwp' => '00.000.000.0-000.000',
            'no_bpjs' => '0000000000000001',
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-001',
            'no_sk_cpns' => 'SKCPNS-001',
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-001',
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'nilai_tpp' => 0,
        ]);
    }

    private function createRencana(Pegawai $pegawai, string $tahun, string $nama, string $status, int $targetJam): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => $tahun,
            'nama_diklat_rencana' => $nama,
            'target_kompetensi' => 'Kompetensi',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => $targetJam,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan',
            'catatan' => null,
            'status' => $status,
        ]);
    }

    private function createDiklat(Pegawai $pegawai, ?RencanaDiklat $rencana, string $tahun, string $nama, int $jumlahJam): Diklat
    {
        return Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana?->id,
            'nama_diklat' => $nama,
            'jumlah_jam' => $jumlahJam,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => $tahun,
            'no_sttpp' => 'STTPP-' . $nama,
            'tgl_sttpp' => $tahun . '-05-09',
            'file_sertifikat_diklat' => null,
        ]);
    }
}
