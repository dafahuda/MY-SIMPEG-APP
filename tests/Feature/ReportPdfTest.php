<?php

namespace Tests\Feature;

use App\Models\InstansiLembaga;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private UnitKerja $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->unit = UnitKerja::factory()->create(['nama_unit' => 'Sekretariat Daerah Test']);

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
    }

    private function buatPegawai(array $attr = []): Pegawai
    {
        $u = User::factory()->create(['role' => 'pegawai']);
        return Pegawai::factory()->create(array_merge([
            'user_id' => $u->id,
            'unit_kerja_id' => $this->unit->id,
        ], $attr));
    }

    public function test_unduh_pdf_nominatif(): void
    {
        $this->buatPegawai();

        $res = $this->actingAs($this->admin)
            ->get(route('report.pdf', 'nominatif') . '?unit_kerja_id=' . $this->unit->id);

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
        $this->assertStringContainsString('attachment;', $res->headers->get('Content-Disposition'));
    }

    public function test_unduh_pdf_duk(): void
    {
        $this->buatPegawai();

        $res = $this->actingAs($this->admin)
            ->get(route('report.pdf', 'duk') . '?unit_kerja_id=' . $this->unit->id);

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_unduh_pdf_keadaan_pegawai(): void
    {
        $res = $this->actingAs($this->admin)
            ->get(route('report.pdf', 'keadaan_pegawai') . '?unit_kerja_id=' . $this->unit->id);

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_unduh_pdf_bezetting(): void
    {
        $res = $this->actingAs($this->admin)
            ->get(route('report.pdf', 'bezetting') . '?unit_kerja_id=' . $this->unit->id);

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_unduh_pdf_pensiun(): void
    {
        // pegawai lahir 1968 -> pensiun 2026 (tahun ini)
        $this->buatPegawai(['tgl_lahir' => '1968-05-01']);

        $res = $this->actingAs($this->admin)
            ->get(route('report.pdf', 'pensiun') . '?periode=tahun_ini');

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
        $this->assertStringContainsString('pensiun', $res->headers->get('Content-Disposition'));
    }

    public function test_jenis_tidak_dikenal_404(): void
    {
        $this->actingAs($this->admin)
            ->get('/report/pdf/jenis_ngawur')
            ->assertNotFound();
    }

    public function test_pegawai_ditolak(): void
    {
        $pegawai = User::factory()->create(['role' => 'pegawai']);

        $this->actingAs($pegawai)
            ->get(route('report.pdf', 'nominatif'))
            ->assertForbidden();
    }

    public function test_butuh_login(): void
    {
        $this->get(route('report.pdf', 'nominatif'))
            ->assertRedirect('/login');
    }
}
