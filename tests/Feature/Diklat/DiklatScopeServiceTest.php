<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use App\Services\DiklatScopeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatScopeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_scope_limits_access_to_same_unit_records(): void
    {
        [$unitA, $pegawaiA, $rencanaA, $diklatA] = $this->seedScopedRecords('A');
        [, $pegawaiB, $rencanaB, $diklatB] = $this->seedScopedRecords('B');

        $admin = $this->createUser('admin-a', 'admin.a@example.test', 'Admin A', 'admin', $unitA->id);
        $service = new DiklatScopeService();

        $this->assertSame([$pegawaiA->id], $service->scopePegawaiQuery(Pegawai::query(), $admin)->pluck('id')->all());
        $this->assertSame([$rencanaA->id], $service->scopeRencanaDiklatQuery(RencanaDiklat::query(), $admin)->pluck('id')->all());
        $this->assertSame([$diklatA->id], $service->scopeDiklatQuery(Diklat::query(), $admin)->pluck('id')->all());
        $this->assertNotContains($pegawaiB->id, $service->scopePegawaiQuery(Pegawai::query(), $admin)->pluck('id')->all());
        $this->assertNotContains($rencanaB->id, $service->scopeRencanaDiklatQuery(RencanaDiklat::query(), $admin)->pluck('id')->all());
        $this->assertNotContains($diklatB->id, $service->scopeDiklatQuery(Diklat::query(), $admin)->pluck('id')->all());
    }

    public function test_pegawai_scope_limits_to_own_records_only(): void
    {
        [$unitA, $pegawaiA, $rencanaA, $diklatA] = $this->seedScopedRecords('A');
        [, , $rencanaB, $diklatB] = $this->seedScopedRecords('B');

        $service = new DiklatScopeService();
        $user = $pegawaiA->user;

        $this->assertSame([$pegawaiA->id], $service->scopePegawaiQuery(Pegawai::query(), $user)->pluck('id')->all());
        $this->assertSame([$rencanaA->id], $service->scopeRencanaDiklatQuery(RencanaDiklat::query(), $user)->pluck('id')->all());
        $this->assertSame([$diklatA->id], $service->scopeDiklatQuery(Diklat::query(), $user)->pluck('id')->all());
        $this->assertNotContains($rencanaB->id, $service->scopeRencanaDiklatQuery(RencanaDiklat::query(), $user)->pluck('id')->all());
        $this->assertNotContains($diklatB->id, $service->scopeDiklatQuery(Diklat::query(), $user)->pluck('id')->all());
    }

    public function test_superadmin_scope_keeps_all_records_visible(): void
    {
        $this->seedScopedRecords('A');
        $this->seedScopedRecords('B');

        $superadmin = $this->createUser('superadmin', 'superadmin@example.test', 'Super Admin', 'superadmin');
        $service = new DiklatScopeService();

        $this->assertCount(2, $service->scopePegawaiQuery(Pegawai::query(), $superadmin)->get());
        $this->assertCount(2, $service->scopeRencanaDiklatQuery(RencanaDiklat::query(), $superadmin)->get());
        $this->assertCount(2, $service->scopeDiklatQuery(Diklat::query(), $superadmin)->get());
    }

    private function seedScopedRecords(string $suffix): array
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit ' . $suffix,
            'alamat' => 'Jl. ' . $suffix,
        ]);

        $pegawai = $this->createPegawai(
            'pegawai-' . strtolower($suffix),
            'pegawai.' . strtolower($suffix) . '@example.test',
            'Pegawai ' . $suffix,
            $unitKerja
        );

        $rencana = RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => 'Rencana ' . $suffix,
            'target_kompetensi' => 'Kompetensi ' . $suffix,
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan ' . $suffix,
            'catatan' => null,
            'status' => 'planned',
        ]);

        $diklat = Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Diklat ' . $suffix,
            'jumlah_jam' => 40,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-' . $suffix,
            'tgl_sttpp' => '2026-05-09',
            'file_sertifikat_diklat' => null,
        ]);

        return [$unitKerja, $pegawai, $rencana, $diklat];
    }

    private function createUser(string $username, string $email, string $name, string $role, ?int $unitKerjaId = null): User
    {
        return User::create([
            'username' => $username,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'unit_kerja_id' => $unitKerjaId,
            'password' => bcrypt('password'),
        ]);
    }

    private function createPegawai(string $username, string $email, string $nama, UnitKerja $unitKerja): Pegawai
    {
        $user = $this->createUser($username, $email, $nama, 'pegawai', $unitKerja->id);

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
}
