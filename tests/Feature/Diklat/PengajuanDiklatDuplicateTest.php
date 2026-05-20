<?php

namespace Tests\Feature\Diklat;

use App\Models\PengajuanDiklat;
use App\Models\RencanaDiklat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PengajuanDiklatDuplicateTest extends TestCase
{
    use DiklatGapReportFixtures;
    use RefreshDatabase;

    public function test_active_duplicate_submission_for_same_rencana_and_pegawai_is_rejected(): void
    {
        $fixtures = $this->seedGapReportFixtures();
        $pegawai = $fixtures['pegawaiA'];
        $rencana = $this->createUniqueRencana($pegawai->id, '2028', 'Pengajuan Duplicate Diklat');

        PengajuanDiklat::create([
            'rencana_diklat_id' => $rencana->id,
            'pegawai_id' => $pegawai->id,
            'status' => PengajuanDiklat::STATUS_PENDING,
            'file_bukti' => 'bukti/pertama.pdf',
        ]);

        $this->expectException(ValidationException::class);

        PengajuanDiklat::create([
            'rencana_diklat_id' => $rencana->id,
            'pegawai_id' => $pegawai->id,
            'status' => PengajuanDiklat::STATUS_REVISION_REQUESTED,
            'file_bukti' => 'bukti/kedua.pdf',
        ]);
    }

    public function test_cancelled_submission_does_not_block_new_active_submission(): void
    {
        $fixtures = $this->seedGapReportFixtures();
        $pegawai = $fixtures['pegawaiA'];
        $rencana = $this->createUniqueRencana($pegawai->id, '2028', 'Pengajuan Cancelled Diklat');

        PengajuanDiklat::create([
            'rencana_diklat_id' => $rencana->id,
            'pegawai_id' => $pegawai->id,
            'status' => PengajuanDiklat::STATUS_CANCELLED,
            'file_bukti' => 'bukti/dibatalkan.pdf',
        ]);

        $pengajuan = PengajuanDiklat::create([
            'rencana_diklat_id' => $rencana->id,
            'pegawai_id' => $pegawai->id,
            'status' => PengajuanDiklat::STATUS_PENDING,
            'file_bukti' => 'bukti/aktif.pdf',
        ]);

        $this->assertTrue($pengajuan->isPending());
        $this->assertDatabaseCount('tb_pengajuan_diklat', 2);
    }

    public function test_submission_must_belong_to_the_same_pegawai_as_rencana(): void
    {
        $fixtures = $this->seedGapReportFixtures();
        $rencana = $this->createUniqueRencana($fixtures['pegawaiA']->id, '2028', 'Pengajuan Wrong Pegawai Diklat');

        $this->expectException(ValidationException::class);

        PengajuanDiklat::create([
            'rencana_diklat_id' => $rencana->id,
            'pegawai_id' => $fixtures['pegawaiB']->id,
            'status' => PengajuanDiklat::STATUS_PENDING,
            'file_bukti' => 'bukti/salah-pegawai.pdf',
        ]);
    }

    private function createUniqueRencana(int $pegawaiId, string $tahun, string $nama): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawaiId,
            'tahun_rencana' => $tahun,
            'nama_diklat_rencana' => $nama,
            'target_kompetensi' => 'Kompetensi teknis',
            'kategori_diklat' => 'Teknis',
            'prioritas' => 'Tinggi',
            'target_jam' => 32,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan kompetensi',
            'catatan' => null,
            'status' => 'planned',
        ]);
    }
}
