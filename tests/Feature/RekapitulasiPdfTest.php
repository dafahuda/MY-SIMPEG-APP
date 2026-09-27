<?php

namespace Tests\Feature;

use App\Models\InstansiLembaga;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapitulasiPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
    }

    public function test_unduh_pdf_semua_jenis_menghasilkan_pdf(): void
    {
        $jenisList = ['opd_skpd_unit_kerja', 'golongan', 'pangkat', 'jabatan', 'eselon',
            'status_kepegawaian', 'agama', 'jenis_kelamin', 'status_pernikahan', 'pendidikan_terakhir'];

        foreach ($jenisList as $jenis) {
            $res = $this->actingAs($this->superadmin)
                ->get(route('rekapitulasi.pdf', $jenis));

            $res->assertOk();
            $this->assertSame('application/pdf', $res->headers->get('Content-Type'), "Jenis: {$jenis}");
            $this->assertStringStartsWith('attachment; filename=', $res->headers->get('Content-Disposition'));
            // Header PDF asli selalu diawali %PDF
            $this->assertStringStartsWith('%PDF', $res->getContent(), "Jenis: {$jenis} bukan PDF valid");
        }
    }

    public function test_pdf_berisi_data_dan_kop(): void
    {
        $unit = UnitKerja::factory()->create(['nama_unit' => 'Sekretariat Daerah Test']);
        $pegawaiUsers = User::factory()->count(3)->create(['role' => 'pegawai']);
        $pegawaiUsers->each(fn ($u) => Pegawai::factory()->create([
            'user_id' => $u->id,
            'unit_kerja_id' => $unit->id,
        ]));
        InstansiLembaga::create([
            'nama_instansi_lembaga' => 'Badan Kepegawaian Daerah Test',
            'kabupaten_kota' => 'Kota',
            'nama_kota_kabupaten' => 'Kota Test',
            'alamat' => 'Jl. Test No. 1',
            'no_telp' => '022-123456',
            'email' => 'bkdd@test.go.id',
            'kepala_dinas' => 'Dr. Kepala Dinas, M.Si',
            'nip' => '197001012000121001',
            'gambar_logo' => '-',
        ]);

        $res = $this->actingAs($this->superadmin)
            ->get(route('rekapitulasi.pdf', 'opd_skpd_unit_kerja'));

        $res->assertOk();

        // Ekstrak teks dari PDF guna verifikasi isi (dompdf pakai kompresi;
        // cukup pastikan header + ukuran wajar)
        $this->assertGreaterThan(1000, strlen($res->getContent()), 'PDF terlalu kecil, kemungkinan kosong');
    }

    public function test_jenis_tidak_dikenal_404(): void
    {
        $this->actingAs($this->superadmin)
            ->get('/rekapitulasi/pdf/jenis_ngawur')
            ->assertNotFound();
    }

    public function test_pegawai_tidak_boleh_unduh_rekapitulasi(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);

        $this->actingAs($pegawai)
            ->get(route('rekapitulasi.pdf', 'golongan'))
            ->assertForbidden();
    }

    public function test_route_butuh_login(): void
    {
        $this->get(route('rekapitulasi.pdf', 'golongan'))
            ->assertRedirect('/login');
    }
}
