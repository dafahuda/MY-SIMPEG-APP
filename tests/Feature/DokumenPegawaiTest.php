<?php

namespace Tests\Feature;

use App\Models\DokumenPegawai;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenPegawaiTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $pegawaiUser;
    private Pegawai $pegawai;
    private Pegawai $pegawaiLain;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $unit = UnitKerja::factory()->create();
        $this->pegawaiUser = User::factory()->create(['role' => 'pegawai']);
        $this->pegawai = Pegawai::factory()->create([
            'user_id' => $this->pegawaiUser->id,
            'unit_kerja_id' => $unit->id,
        ]);

        $userLain = User::factory()->create(['role' => 'pegawai']);
        $this->pegawaiLain = Pegawai::factory()->create([
            'user_id' => $userLain->id,
            'unit_kerja_id' => $unit->id,
        ]);
    }

    public function test_superadmin_bisa_melihat_halaman_dokumen(): void
    {
        $res = $this->actingAs($this->superadmin)
            ->get(route('dokumen-pegawai.index', $this->pegawai->id));

        $res->assertOk()
            ->assertSee('Dokumen Digital')
            ->assertSee($this->pegawai->nama)
            ->assertSee('Belum ada dokumen');
    }

    public function test_upload_dokumen_berhasil(): void
    {
        $file = UploadedFile::fake()->create('sk-pns.pdf', 100, 'application/pdf');

        $res = $this->actingAs($this->superadmin)
            ->post(route('dokumen-pegawai.store', $this->pegawai->id), [
                'jenis_dokumen' => 'sk_pns',
                'nama_dokumen' => 'SK PNS Tahun 2015',
                'file' => $file,
                'keterangan' => 'Dokumen asli hasil scan',
            ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('tb_dokumen_pegawai', [
            'pegawai_id' => $this->pegawai->id,
            'jenis_dokumen' => 'sk_pns',
            'nama_dokumen' => 'SK PNS Tahun 2015',
            'uploaded_by' => $this->superadmin->id,
        ]);
        Storage::disk('private')->assertExists(
            DokumenPegawai::first()->file_path
        );
    }

    public function test_upload_file_tidak_valid_ditolak(): void
    {
        $file = UploadedFile::fake()->create('virus.exe', 10);

        $res = $this->actingAs($this->superadmin)
            ->post(route('dokumen-pegawai.store', $this->pegawai->id), [
                'jenis_dokumen' => 'lainnya',
                'nama_dokumen' => 'File jahat',
                'file' => $file,
            ]);

        $res->assertSessionHasErrors('file');
        $this->assertDatabaseCount('tb_dokumen_pegawai', 0);
    }

    public function test_upload_dokumen_terlalu_besar_ditolak(): void
    {
        $file = UploadedFile::fake()->create('besar.pdf', 6 * 1024, 'application/pdf'); // 6 MB

        $res = $this->actingAs($this->superadmin)
            ->post(route('dokumen-pegawai.store', $this->pegawai->id), [
                'jenis_dokumen' => 'ijazah',
                'nama_dokumen' => 'Ijazah besar',
                'file' => $file,
            ]);

        $res->assertSessionHasErrors('file');
        $this->assertDatabaseCount('tb_dokumen_pegawai', 0);
    }

    public function test_pegawai_bisa_upload_dokumen_sendiri(): void
    {
        $file = UploadedFile::fake()->create('ktp.jpg', 50, 'image/jpeg');

        $res = $this->actingAs($this->pegawaiUser)
            ->post(route('dokumen-pegawai.store', $this->pegawai->id), [
                'jenis_dokumen' => 'ktp',
                'nama_dokumen' => 'KTP saya',
                'file' => $file,
            ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('tb_dokumen_pegawai', [
            'pegawai_id' => $this->pegawai->id,
            'uploaded_by' => $this->pegawaiUser->id,
        ]);
    }

    public function test_pegawai_tidak_bisa_upload_untuk_pegawai_lain(): void
    {
        $file = UploadedFile::fake()->create('ktp.jpg', 50, 'image/jpeg');

        $res = $this->actingAs($this->pegawaiUser)
            ->post(route('dokumen-pegawai.store', $this->pegawaiLain->id), [
                'jenis_dokumen' => 'ktp',
                'nama_dokumen' => 'KTP orang lain',
                'file' => $file,
            ]);

        $res->assertForbidden();
        $this->assertDatabaseCount('tb_dokumen_pegawai', 0);
    }

    public function test_lihat_dan_unduh_dokumen(): void
    {
        $dok = $this->buatDokumen($this->pegawai->id);

        $this->actingAs($this->superadmin)
            ->get(route('dokumen-pegawai.show', $dok->id))
            ->assertOk();

        $this->actingAs($this->superadmin)
            ->get(route('dokumen-pegawai.download', $dok->id))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=' . $dok->file_name);
    }

    public function test_pegawai_lain_tidak_bisa_melihat_dokumen(): void
    {
        $dok = $this->buatDokumen($this->pegawaiLain->id);

        $res = $this->actingAs($this->pegawaiUser)
            ->get(route('dokumen-pegawai.show', $dok->id));

        $res->assertForbidden();
    }

    public function test_admin_boleh_hapus_tapi_pegawai_tidak(): void
    {
        $dok = $this->buatDokumen($this->pegawai->id);

        // Pegawai (pemilik) tidak boleh hapus
        $this->actingAs($this->pegawaiUser)
            ->delete(route('dokumen-pegawai.destroy', $dok->id))
            ->assertForbidden();
        $this->assertDatabaseCount('tb_dokumen_pegawai', 1);

        // Superadmin boleh hapus — file ikut terhapus dari disk
        $path = $dok->file_path;
        $this->actingAs($this->superadmin)
            ->delete(route('dokumen-pegawai.destroy', $dok->id))
            ->assertRedirect();
        $this->assertDatabaseCount('tb_dokumen_pegawai', 0);
        Storage::disk('private')->assertMissing($path);
    }

    public function test_admin_unit_lain_tidak_bisa_akses(): void
    {
        $unitLain = UnitKerja::factory()->create();
        $adminLain = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitLain->id]);

        $res = $this->actingAs($adminLain)
            ->get(route('dokumen-pegawai.index', $this->pegawai->id));

        $res->assertForbidden();
    }

    public function test_route_butuh_login(): void
    {
        $this->get(route('dokumen-pegawai.index', $this->pegawai->id))
            ->assertRedirect('/login');
    }

    private function buatDokumen(int $pegawaiId): DokumenPegawai
    {
        $path = "dokumen-pegawai/{$pegawaiId}/test.pdf";
        Storage::disk('private')->put($path, 'dummy content');

        return DokumenPegawai::create([
            'pegawai_id' => $pegawaiId,
            'jenis_dokumen' => 'sk_pns',
            'nama_dokumen' => 'SK PNS',
            'file_path' => $path,
            'file_name' => 'sk.pdf',
            'mime_type' => 'application/pdf',
            'size' => 102400,
            'uploaded_by' => $this->superadmin->id,
        ]);
    }
}
