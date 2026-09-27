<?php

namespace Tests\Feature;

use App\Models\InstansiLembaga;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiodataPdfTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $pegawaiUser;
    private Pegawai $pegawai;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create(['role' => 'superadmin']);

        $unit = \App\Models\UnitKerja::factory()->create(['nama_unit' => 'Sekretariat Daerah Test']);

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

        $this->pegawaiUser = User::factory()->create(['role' => 'pegawai']);
        $this->pegawai = Pegawai::factory()->create([
            'user_id' => $this->pegawaiUser->id,
            'unit_kerja_id' => $unit->id,
        ]);
    }

    public function test_superadmin_bisa_unduh_biodata_pegawai_lain(): void
    {
        $res = $this->actingAs($this->superadmin)
            ->get(route('pegawai.biodata.pdf', $this->pegawai->id));

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
        $this->assertStringContainsString('biodata-', $res->headers->get('Content-Disposition'));
    }

    public function test_pegawai_bisa_unduh_biodata_sendiri(): void
    {
        $res = $this->actingAs($this->pegawaiUser)
            ->get(route('profile.pegawai.pdf'));

        $res->assertOk();
        $this->assertSame('application/pdf', $res->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_pegawai_ditolak_unduh_biodata_orang_lain(): void
    {
        // pegawai lain
        $u2 = User::factory()->create(['role' => 'pegawai']);
        $p2 = Pegawai::factory()->create(['user_id' => $u2->id]);

        $res = $this->actingAs($this->pegawaiUser)
            ->get(route('pegawai.biodata.pdf', $p2->id));

        $res->assertForbidden();
    }

    public function test_butuh_login(): void
    {
        $this->get(route('pegawai.biodata.pdf', $this->pegawai->id))
            ->assertRedirect('/login');
    }

    public function test_pegawai_tanpa_data_pegawai_diredirect(): void
    {
        // admin punya role admin tapi route unduh_pdf milik pegawai login sendiri
        // buat user pegawai TANPA baris tb_pegawai
        $userTanpaPegawai = User::factory()->create(['role' => 'pegawai']);

        $res = $this->actingAs($userTanpaPegawai)
            ->get(route('profile.pegawai.pdf'));

        $res->assertRedirect('/profile_saya');
    }

    public function test_halaman_print_biodata_tetap_jalan(): void
    {
        // regresi: route print lama tidak rusak setelah refactor
        $res = $this->actingAs($this->pegawaiUser)
            ->get(route('profile.pegawai.print'));

        $res->assertOk();
    }
}
