<?php

namespace Tests\Feature;

use App\Models\Diklat;
use App\Models\Hukuman;
use App\Models\IzinKawin;
use App\Models\LatihanJabatan;
use App\Models\Mutasi;
use App\Models\Pegawai;
use App\Models\Penghargaan;
use App\Models\PenugasanLuarNegeri;
use App\Models\RencanaDiklat;
use App\Models\Seminar;
use App\Models\Tunjangan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KepegawaianRelatedScopeTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('indexPages')]
    public function test_pegawai_index_only_shows_own_related_records(string $url, string $visibleText, string $hiddenText, callable $createRecord): void
    {
        [$pegawai, $otherPegawai] = $this->seedPegawaiPair();

        $createRecord($pegawai, $visibleText);
        $createRecord($otherPegawai, $hiddenText);

        $this->actingAs($pegawai->user)
            ->get($url)
            ->assertOk()
            ->assertSee($visibleText)
            ->assertDontSee($hiddenText);
    }

    public static function indexPages(): array
    {
        return [
            'hukuman' => ['/kepegawaian/hukuman', 'Hukuman Sendiri', 'Hukuman Orang Lain', self::hukuman(...)],
            'mutasi' => ['/kepegawaian/mutasi', 'Mutasi Sendiri', 'Mutasi Orang Lain', self::mutasi(...)],
            'seminar' => ['/kepegawaian/seminar', 'Seminar Sendiri', 'Seminar Orang Lain', self::seminar(...)],
            'penghargaan' => ['/kepegawaian/penghargaan', 'Penghargaan Sendiri', 'Penghargaan Orang Lain', self::penghargaan(...)],
            'tunjangan' => ['/kepegawaian/tunjangan', 'Tunjangan Sendiri', 'Tunjangan Orang Lain', self::tunjangan(...)],
            'izin_kawin' => ['/kepegawaian/izin_kawin', 'Izin Sendiri', 'Izin Orang Lain', self::izinKawin(...)],
            'latihan_jabatan' => ['/kepegawaian/latihan_jabatan', 'Latihan Sendiri', 'Latihan Orang Lain', self::latihanJabatan(...)],
            'penugasan_ln' => ['/kepegawaian/penugasan_ln', 'Penugasan Sendiri', 'Penugasan Orang Lain', self::penugasanLuarNegeri(...)],
            'rencana_diklat' => ['/kepegawaian/rencana_diklat', 'Rencana Sendiri', 'Rencana Orang Lain', self::rencanaDiklat(...)],
            'diklat' => ['/kepegawaian/diklat', 'Diklat Sendiri', 'Diklat Orang Lain', self::diklat(...)],
        ];
    }

    private function seedPegawaiPair(): array
    {
        $unitKerja = UnitKerja::factory()->create();

        $pegawai = Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id])->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Sendiri',
        ]);

        $otherPegawai = Pegawai::factory()->create([
            'user_id' => User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id])->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Orang Lain',
        ]);

        return [$pegawai, $otherPegawai];
    }

    private static function hukuman(Pegawai $pegawai, string $text): void
    {
        Hukuman::create([
            'pegawai_id' => $pegawai->id,
            'pelanggaran_yg_dilakukan' => $text,
            'tingkat_hukuman' => 'Ringan',
            'jenis_hukuman' => 'Teguran Lisan',
            'isi_teguran' => $text,
            'pejabat_pengesahan_sk_hukuman' => 'Pejabat',
            'no_sk' => 'SK-' . $text,
            'file_sk_hukuman' => '/storage/document/hukuman.pdf',
            'tgl_pengesahan_sk' => '2026-01-01',
            'tmt_hukuman_mulai' => '2026-01-02',
            'tmt_hukuman_pemulihan' => '2026-02-02',
            'no_pemulihan_hukuman' => 'PH-' . $text,
            'pejabat_pemulihan_hukuman' => 'Pejabat',
            'tgl_pemulihan_hukuman' => '2026-02-03',
        ]);
    }

    private static function mutasi(Pegawai $pegawai, string $text): void
    {
        Mutasi::create([
            'pegawai_id' => $pegawai->id,
            'jenis_mutasi' => 'Masuk',
            'instansi_tujuan' => $text,
            'no_sk_mutasi' => 'SK-' . $text,
            'tgl_sk_mutasi' => '2026-01-01',
            'file_sk_mutasi' => '/storage/document/mutasi.pdf',
        ]);
    }

    private static function seminar(Pegawai $pegawai, string $text): void
    {
        Seminar::create([
            'pegawai_id' => $pegawai->id,
            'nama_seminar' => $text,
            'tingkat_kegiatan' => 'Nasional',
            'tempat_seminar' => 'Bandung',
            'tgl_seminar' => '2026-01-01',
            'penyelenggara' => $text,
            'jumlah_jam' => '8',
            'no_piagam' => 'PIAGAM-' . $text,
            'tgl_piagam' => '2026-01-02',
            'file_piagam' => '/storage/document/piagam.pdf',
        ]);
    }

    private static function penghargaan(Pegawai $pegawai, string $text): void
    {
        Penghargaan::create([
            'pegawai_id' => $pegawai->id,
            'nama_penghargaan' => $text,
            'instansi_pemberi' => 'Instansi',
            'tingkat_kegiatan' => 'Nasional',
            'tempat_penghargaan' => 'Bandung',
            'tgl_penghargaan' => '2026-01-01',
            'file_sertifikat_penghargaan' => '/storage/document/penghargaan.pdf',
            'tahun' => '2026',
            'no_sertifikat' => 'SERT-' . $text,
        ]);
    }

    private static function tunjangan(Pegawai $pegawai, string $text): void
    {
        Tunjangan::create([
            'pegawai_id' => $pegawai->id,
            'no_tunjangan' => 'TUNJ-' . $text,
            'jenis_tunjangan_anak' => $text,
            'tgl_tunjangan' => '2026-01-01',
            'terhitung_mulai' => '2026-01-01',
            'akta_perkawinan_dari' => 'Disdukcapil',
            'no_akta_perkawinan' => 'AK-' . $text,
            'tgl_akta_perkawinan' => '2026-01-01',
            'akta_kelahiran_dari' => 'Disdukcapil',
            'no_akta_kelahiran' => 'LAH-' . $text,
            'tgl_akta_kelahiran' => '2026-01-01',
            'tebusan' => 'Tembusan',
        ]);
    }

    private static function izinKawin(Pegawai $pegawai, string $text): void
    {
        IzinKawin::create([
            'pegawai_id' => $pegawai->id,
            'no_surat_izin_perkawinan' => 'IZIN-' . $text,
            'tgl_izin_surat_perkawinan' => '2026-01-01',
            'kebangsaan_pegawai' => 'Indonesia',
            'nama_wali_bapak_pegawai' => 'Bapak Pegawai',
            'pekerjaan_wali_bapak_pegawai' => 'PNS',
            'alamat_wali_bapak' => 'Bandung',
            'nama_wali_ibu_pegawai' => 'Ibu Pegawai',
            'pekerjaan_wali_ibu_pegawai' => 'PNS',
            'alamat_wali_ibu_pegawai' => 'Bandung',
            'nama_calon_suami_istri' => $text,
            'tempat_lahir_calon_suami_istri' => 'Bandung',
            'tgl_lahir_calon_suami_istri' => '1990-01-01',
            'pekerjaan_calon_suami_istri' => 'PNS',
            'nip_nik_calon_suami_istri' => '3276010101900001',
            'pangkat_golongan_calon_suami_istri' => 'III/a',
            'jabatan_calon_suami_istri' => 'Staf',
            'instansi_calon_suami_istri' => 'Instansi',
            'kebangsaan_calon_suami_istri' => 'Indonesia',
            'agama_calon_suami_istri' => 'Islam',
            'alamat_calon_suami_istri' => 'Bandung',
            'nama_wali_bapak_calon_suami_istri' => 'Bapak Calon',
            'pekerjaan_wali_bapak_calon_suami_istri' => 'PNS',
            'alamat_wali_bapak_calon_suami_istri' => 'Bandung',
            'nama_wali_ibu_calon_suami_istri' => 'Ibu Calon',
            'pekerjaan_wali_ibu_calon_suami_istri' => 'PNS',
            'alamat_wali_ibu_calon_suami_istri' => 'Bandung',
            'tempat_perkawinan' => $text,
            'tgl_perkawinan' => '2026-01-01',
            'tgl_ditetapkan_perkawinan' => '2026-01-02',
        ]);
    }

    private static function latihanJabatan(Pegawai $pegawai, string $text): void
    {
        LatihanJabatan::create([
            'pegawai_id' => $pegawai->id,
            'tempat_latihan' => $text,
            'waktu_latihan' => '2026-01-01',
            'nama_pelatih' => $text,
            'tahun_latihan' => '2026',
            'jumlah_jam' => '8',
            'nomor_sertifikat' => 'SERT-' . $text,
            'tgl_sertifikat' => '2026-01-02',
            'file_sertifikat' => '/storage/document/latihan.pdf',
        ]);
    }

    private static function penugasanLuarNegeri(Pegawai $pegawai, string $text): void
    {
        PenugasanLuarNegeri::create([
            'pegawai_id' => $pegawai->id,
            'alasan_penugasan' => $text,
            'negara_tujuan' => $text,
            'tahun' => '2026',
            'durasi_hari' => '5',
            'no_st' => 'ST-' . $text,
            'st' => '/storage/document/st.pdf',
        ]);
    }

    private static function rencanaDiklat(Pegawai $pegawai, string $text): void
    {
        RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => $text,
            'target_kompetensi' => $text,
            'kategori_diklat' => 'Teknis',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Kebutuhan',
            'catatan' => null,
            'status' => 'planned',
        ]);
    }

    private static function diklat(Pegawai $pegawai, string $text): void
    {
        Diklat::create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => null,
            'nama_diklat' => $text,
            'jumlah_jam' => '40',
            'penyelenggara' => $text,
            'tempat' => 'Bandung',
            'angkatan' => '1',
            'tahun' => '2026',
            'no_sttpp' => 'STTPP-' . $text,
            'tgl_sttpp' => '2026-01-02',
            'file_sertifikat_diklat' => '/storage/document/diklat.pdf',
        ]);
    }
}
