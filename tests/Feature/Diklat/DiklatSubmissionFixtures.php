<?php

namespace Tests\Feature\Diklat;

use App\Models\Pegawai;
use App\Models\PengajuanDiklat;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait DiklatSubmissionFixtures
{
    private function createSubmissionScopeFixture(): array
    {
        $unitInScope = $this->createSubmissionUnit('Unit Pengajuan In Scope');
        $unitOutOfScope = $this->createSubmissionUnit('Unit Pengajuan Out Scope');

        $admin = $this->createSubmissionUser('admin-pengajuan-scope', 'admin', $unitInScope);
        $superadmin = $this->createSubmissionUser('superadmin-pengajuan-scope', 'superadmin');
        $pegawaiInScope = $this->createSubmissionPegawai('pegawai-pengajuan-in-scope', $unitInScope);
        $pegawaiOutOfScope = $this->createSubmissionPegawai('pegawai-pengajuan-out-scope', $unitOutOfScope);

        $rencanaInScope = $this->createAssignedSubmissionRencana($pegawaiInScope, 'Rencana Pengajuan In Scope');
        $rencanaOutOfScope = $this->createAssignedSubmissionRencana($pegawaiOutOfScope, 'Rencana Pengajuan Out Scope');

        return [
            'unitInScope' => $unitInScope,
            'unitOutOfScope' => $unitOutOfScope,
            'admin' => $admin,
            'superadmin' => $superadmin,
            'pegawaiInScope' => $pegawaiInScope,
            'pegawaiOutOfScope' => $pegawaiOutOfScope,
            'rencanaInScope' => $rencanaInScope,
            'rencanaOutOfScope' => $rencanaOutOfScope,
            'submissionInScope' => $this->createSubmission($rencanaInScope, $pegawaiInScope, PengajuanDiklat::STATUS_PENDING),
            'submissionOutOfScope' => $this->createSubmission($rencanaOutOfScope, $pegawaiOutOfScope, PengajuanDiklat::STATUS_PENDING),
        ];
    }

    private function createSubmissionUnit(string $name): UnitKerja
    {
        return UnitKerja::create([
            'nama_unit' => $name,
            'alamat' => 'Jl. ' . $name,
        ]);
    }

    private function createSubmissionUser(string $username, string $role, ?UnitKerja $unitKerja = null): User
    {
        return User::create([
            'username' => $username,
            'name' => 'User ' . $username,
            'email' => $username . '@example.test',
            'role' => $role,
            'unit_kerja_id' => $role === 'superadmin' ? null : $unitKerja?->id,
            'password' => bcrypt('password'),
        ]);
    }

    private function createSubmissionPegawai(string $username, UnitKerja $unitKerja): Pegawai
    {
        $user = $this->createSubmissionUser($username, 'pegawai', $unitKerja);

        return Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto.jpg',
            'nip' => (string) random_int(100000000000000000, 999999999999999999),
            'nik' => (string) random_int(1000000000000000, 9999999999999999),
            'nama' => 'Pegawai ' . $username,
            'gelar' => 'S.T.',
            'gelar_depan' => null,
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1990-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Nikah',
            'alamat' => 'Jl. Pegawai ' . $username,
            'no_hp' => '081234567890',
            'email' => $username . '@example.test',
            'email_gov' => $username . '@mail.go.id',
            'no_npwp' => '00.000.000.0-000.000',
            'no_bpjs' => '0000000000000001',
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-' . $username,
            'no_sk_cpns' => 'SKCPNS-' . $username,
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-' . $username,
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'nilai_tpp' => 0,
        ]);
    }

    private function createAssignedSubmissionRencana(Pegawai $pegawai, string $name, string $year = '2028'): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => $year,
            'nama_diklat_rencana' => $name,
            'target_kompetensi' => 'Kompetensi pengajuan',
            'kategori_diklat' => 'Teknis',
            'prioritas' => 'Tinggi',
            'target_jam' => 32,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengajuan bukti diklat',
            'catatan' => null,
            'status' => 'planned',
        ]);
    }

    private function createSubmission(RencanaDiklat $rencanaDiklat, Pegawai $pegawai, string $status): PengajuanDiklat
    {
        return PengajuanDiklat::create([
            'rencana_diklat_id' => $rencanaDiklat->id,
            'pegawai_id' => $pegawai->id,
            'status' => $status,
            'file_bukti' => 'bukti/' . $rencanaDiklat->id . '-' . $pegawai->id . '.pdf',
            'nomor_sertifikat' => 'CERT-' . $rencanaDiklat->id,
            'tanggal_sertifikat' => $rencanaDiklat->tahun_rencana . '-03-01',
            'jumlah_jam_realisasi' => 32,
            'catatan_pegawai' => 'Bukti diklat sudah diunggah.',
            'submitted_at' => now(),
        ]);
    }

    private function fakeSubmissionEvidenceUpload(string $filename = 'bukti-diklat.pdf'): UploadedFile
    {
        Storage::fake('public');

        return UploadedFile::fake()->create($filename, 128, 'application/pdf');
    }
}
