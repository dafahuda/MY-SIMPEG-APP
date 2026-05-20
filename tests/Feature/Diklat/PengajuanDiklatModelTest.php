<?php

namespace Tests\Feature\Diklat;

use App\Models\Diklat;
use App\Models\PengajuanDiklat;
use App\Models\RencanaDiklat;
use App\Models\User;
use App\Support\DiklatGlossary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanDiklatModelTest extends TestCase
{
    use DiklatGapReportFixtures;
    use RefreshDatabase;

    public function test_pengajuan_diklat_persists_with_relationships_and_status_labels(): void
    {
        $fixtures = $this->seedGapReportFixtures();
        $pegawai = $fixtures['pegawaiA'];
        $rencana = RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => '2028',
            'nama_diklat_rencana' => 'Pengajuan Model Diklat',
            'target_kompetensi' => 'Kompetensi teknis',
            'kategori_diklat' => 'Teknis',
            'prioritas' => 'Tinggi',
            'target_jam' => 32,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan pengembangan kompetensi',
            'catatan' => null,
            'status' => 'planned',
        ]);
        $diklat = Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $rencana->id,
            'nama_diklat' => 'Pengajuan Model Diklat',
            'jumlah_jam' => 32,
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Bandung',
            'angkatan' => 'I',
            'tahun' => '2028',
            'no_sttpp' => 'CERT-PENGAJUAN-001',
            'tgl_sttpp' => '2028-03-01',
            'file_sertifikat_diklat' => 'sertifikat/pengajuan-model.pdf',
        ]);
        $verifier = User::create([
            'username' => 'verifier-pengajuan-model',
            'name' => 'Verifier Pengajuan Model',
            'email' => 'verifier.pengajuan.model@example.test',
            'role' => 'admin',
            'unit_kerja_id' => $pegawai->unit_kerja_id,
            'password' => bcrypt('password'),
        ]);

        $pengajuan = PengajuanDiklat::create([
            'rencana_diklat_id' => $rencana->id,
            'pegawai_id' => $pegawai->id,
            'diklat_id' => $diklat->id,
            'status' => PengajuanDiklat::STATUS_APPROVED,
            'file_bukti' => 'bukti/pengajuan-model.pdf',
            'nomor_sertifikat' => 'CERT-PENGAJUAN-001',
            'tanggal_sertifikat' => '2028-03-01',
            'jumlah_jam_realisasi' => 32,
            'catatan_pegawai' => 'Sudah selesai.',
            'catatan_verifikator' => 'Disetujui.',
            'verified_by' => $verifier->id,
            'verified_at' => '2028-03-02 10:00:00',
            'submitted_at' => '2028-03-01 09:00:00',
            'revision_count' => 1,
        ]);

        $this->assertTrue($pengajuan->isApproved());
        $this->assertFalse($pengajuan->isPending());
        $this->assertSame('Terverifikasi', $pengajuan->statusLabel());
        $this->assertSame('Menunggu Verifikasi', DiklatGlossary::submissionStatusLabel(PengajuanDiklat::STATUS_PENDING));
        $this->assertSame('Perlu Revisi', DiklatGlossary::submissionStatusLabel(PengajuanDiklat::STATUS_REVISION_REQUESTED));
        $this->assertSame('Dibatalkan', DiklatGlossary::submissionStatusLabel(PengajuanDiklat::STATUS_CANCELLED));
        $this->assertTrue($pegawai->pengajuanDiklat()->whereKey($pengajuan->id)->exists());
        $this->assertTrue($rencana->pengajuanDiklat()->whereKey($pengajuan->id)->exists());
        $this->assertTrue($pengajuan->pegawai->is($pegawai));
        $this->assertTrue($pengajuan->rencanaDiklat->is($rencana));
        $this->assertTrue($pengajuan->diklat->is($diklat));
        $this->assertTrue($pengajuan->verifier->is($verifier));
    }
}
