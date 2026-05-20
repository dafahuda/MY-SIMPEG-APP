<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use App\Services\DiklatGapAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatGapFormulaTest extends TestCase
{
    use RefreshDatabase;

    public function test_gap_metrics_are_calculated_from_the_service_only(): void
    {
        $pegawai = $this->createPegawai();

        $rencanaPlannedLinked = $this->createRencana($pegawai, 'planned', '2026', 40, 'Rencana 1');
        $rencanaRealized = $this->createRencana($pegawai, 'realized', '2026', 30, 'Rencana 2');
        $rencanaNotRealized = $this->createRencana($pegawai, 'planned', '2026', 24, 'Rencana 3');
        $this->createRencana($pegawai, 'draft', '2026', 100, 'Rencana Draft');

        $plannedLinkedDiklat = $this->createDiklat($pegawai, $rencanaPlannedLinked, '2027', 35, 'Diklat 1');
        $realizedLinkedDiklat = $this->createDiklat($pegawai, $rencanaRealized, '2027', 28, 'Diklat 2');
        $this->createDiklat($pegawai, null, '2026', 18, 'Diklat Lepas');

        $service = new DiklatGapAnalyticsService();
        $summary = $service->summary(
            RencanaDiklat::with('diklat')->get(),
            Diklat::with('rencanaDiklat')->get()
        );

        $this->assertSame(3, $summary['planned_count']);
        $this->assertSame(1, $summary['realized_linked_count']);
        $this->assertSame(2, $summary['not_realized_count']);
        $this->assertSame(1, $summary['out_of_plan_count']);
        $this->assertSame(1, $summary['cross_year_realized_count']);
        $this->assertSame(94, $summary['planned_hours']);
        $this->assertSame(28, $summary['realized_linked_hours']);
        $this->assertSame(66, $summary['hour_gap']);

        $this->assertSame(3, $service->plannedCount(RencanaDiklat::with('diklat')->get()));
        $this->assertSame(1, $service->realizedLinkedCount(RencanaDiklat::with('diklat')->get()));
        $this->assertSame(2, $service->notRealizedCount(RencanaDiklat::with('diklat')->get()));
        $this->assertSame(1, $service->outOfPlanCount(Diklat::with('rencanaDiklat')->get()));
        $this->assertSame(1, $service->crossYearRealizedCount(RencanaDiklat::with('diklat')->get()));
        $this->assertSame(94, $service->plannedHours(RencanaDiklat::with('diklat')->get()));
        $this->assertSame(28, $service->realizedLinkedHours(RencanaDiklat::with('diklat')->get()));
        $this->assertSame(66, $service->hourGap(RencanaDiklat::with('diklat')->get()));

        $this->assertSame('planned', $rencanaPlannedLinked->fresh()->status);
        $this->assertNotSame('realized', $rencanaPlannedLinked->fresh()->status);
        $this->assertEquals(35, $plannedLinkedDiklat->fresh()->jumlah_jam);
        $this->assertEquals(28, $realizedLinkedDiklat->fresh()->jumlah_jam);
        $this->assertSame(1, $summary['realized_linked_count'], 'linked planned row must not count as realized');
    }

    private function createPegawai(): Pegawai
    {
        $unitKerja = UnitKerja::create([
            'nama_unit' => 'Unit Gap',
            'alamat' => 'Jl. Gap No. 1',
        ]);

        $user = User::create([
            'username' => 'pegawai-gap',
            'name' => 'Pegawai Gap',
            'email' => 'pegawai.gap@example.test',
            'role' => 'pegawai',
            'password' => bcrypt('password'),
        ]);

        return Pegawai::create([
            'user_id' => $user->id,
            'unit_kerja_id' => $unitKerja->id,
            'foto' => 'foto.jpg',
            'nip' => '198801012020011001',
            'nik' => '3276010101880001',
            'nama' => 'Pegawai Gap',
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
            'email' => 'pegawai.gap@example.test',
            'email_gov' => 'pegawai.gap@gov.test',
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

    private function createRencana(Pegawai $pegawai, string $status, string $tahun, int $targetJam, string $nama): RencanaDiklat
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

    private function createDiklat(Pegawai $pegawai, ?RencanaDiklat $rencana, string $tahun, int $jumlahJam, string $nama): Diklat
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
