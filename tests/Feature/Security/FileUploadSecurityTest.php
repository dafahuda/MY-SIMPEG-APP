<?php

namespace Tests\Feature\Security;

use App\Models\Mutasi;
use App\Models\Pegawai;
use App\Models\Seminar;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_dangerous_php_file_extension_is_rejected_by_controller_validation()
    {
        $file = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', $this->validDiklatData($file, $admin->unit_kerja_id));

        $response->assertSessionHasErrors('file_sertifikat_diklat');
        $response->assertRedirect();
    }

    public function test_executable_mime_type_is_rejected_by_controller_validation()
    {
        $file = UploadedFile::fake()->create('script.exe', 100, 'application/x-msdownload');

        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', $this->validDiklatData($file, $admin->unit_kerja_id));

        $response->assertSessionHasErrors('file_sertifikat_diklat');
        $response->assertRedirect();
    }

    public function test_oversize_file_greater_than_2mb_is_rejected()
    {
        // UploadedFile::fake()->create($name, $kilobytes). 2049 KB > 2 MB max.
        $file = UploadedFile::fake()->create('large.pdf', 2049, 'application/pdf');

        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', $this->validDiklatData($file, $admin->unit_kerja_id));

        $response->assertSessionHasErrors('file_sertifikat_diklat');
        $response->assertRedirect();
    }

    public function test_allowed_pdf_file_passes_validation_and_does_not_store_when_other_fields_invalid()
    {
        $file = UploadedFile::fake()->create('valid.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->createAdminUser())
            ->post('/kepegawaian/diklat/tambah_diklat', [
                'file_sertifikat_diklat' => $file,
                'pegawai_id' => null
            ]);

        $response->assertSessionHasErrors('pegawai_id');
        Storage::disk('public')->assertMissing('document/valid.pdf');
    }

    public function test_allowed_image_jpeg_passes_validation()
    {
        $file = UploadedFile::fake()->image('certificate.jpg', 100, 100)->size(500);

        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', $this->validDiklatData($file, $admin->unit_kerja_id));

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
    }

    public function test_allowed_image_png_passes_validation()
    {
        $file = UploadedFile::fake()->image('certificate.png', 100, 100)->size(500);

        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post('/kepegawaian/diklat/tambah_diklat', $this->validDiklatData($file, $admin->unit_kerja_id));

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
    }

    public function test_pegawai_store_rejects_non_file_foto_field(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = $this->createAdminUser($unitKerja->id);
        $userPegawai = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);

        $response = $this->actingAs($admin)
            ->post('/data_pegawai/tambah_data_pegawai', $this->validPegawaiStoreData($userPegawai, $unitKerja, [
                'foto' => 'images/fake-client-path.png',
            ]));

        $response->assertSessionHasErrors('foto');
        $response->assertRedirect();

        $this->assertDatabaseMissing('tb_pegawai', [
            'nip' => '199001012020011001',
            'foto' => 'images/fake-client-path.png',
        ]);
        Storage::disk('public')->assertMissing('images/fake-client-path.png');
    }

    public function test_pegawai_update_ignores_crafted_gambar_lama_and_deletes_only_db_owned_photo(): void
    {
        Storage::disk('public')->put('images/db-owned-old.png', 'old-photo');
        Storage::disk('public')->put('images/victim.png', 'victim-file');

        $unitKerja = UnitKerja::factory()->create();
        $admin = $this->createAdminUser($unitKerja->id);
        $pegawai = Pegawai::factory()->create([
            'unit_kerja_id' => $unitKerja->id,
            'foto' => '/storage/images/db-owned-old.png',
        ]);

        $response = $this->actingAs($admin)
            ->put('/data_pegawai/ubah_data_pegawai/' . $pegawai->id, $this->validPegawaiUpdateData($pegawai, [
                'gambarLama' => 'images/victim.png',
                'foto' => UploadedFile::fake()->image('new-photo.png', 100, 100)->size(128),
            ]));

        $response->assertRedirect('/data_pegawai/pegawai');
        $response->assertSessionHas('success', 'Berhasil mengubah data!');

        Storage::disk('public')->assertExists('images/victim.png');
        Storage::disk('public')->assertMissing('images/db-owned-old.png');

        $pegawai->refresh();
        $this->assertStringStartsWith('/storage/images/', $pegawai->foto);
        $this->assertMatchesRegularExpression('#^/storage/images/[0-9a-f-]{36}\\.png$#', $pegawai->foto);
        Storage::disk('public')->assertExists(ltrim(str_replace('/storage/', '', $pegawai->foto), '/'));
    }

    public function test_pegawai_update_preserves_user_id_when_payload_omits_or_crafts_user_id(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = $this->createAdminUser($unitKerja->id);
        $originalUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $originalUser->id,
            'unit_kerja_id' => $unitKerja->id,
        ]);

        $this->actingAs($admin)
            ->put('/data_pegawai/ubah_data_pegawai/' . $pegawai->id, $this->validPegawaiUpdateData($pegawai, [
                'user_id' => $otherUser->id,
            ]))
            ->assertRedirect('/data_pegawai/pegawai')
            ->assertSessionHas('success', 'Berhasil mengubah data!');

        $this->assertSame($originalUser->id, $pegawai->refresh()->user_id);

        $payloadWithoutUserId = $this->validPegawaiUpdateData($pegawai);
        unset($payloadWithoutUserId['user_id']);

        $this->actingAs($admin)
            ->put('/data_pegawai/ubah_data_pegawai/' . $pegawai->id, $payloadWithoutUserId)
            ->assertRedirect('/data_pegawai/pegawai')
            ->assertSessionHas('success', 'Berhasil mengubah data!');

        $this->assertSame($originalUser->id, $pegawai->refresh()->user_id);
    }

    public function test_pegawai_destroy_deletes_only_db_owned_photo(): void
    {
        Storage::disk('public')->put('images/db-owned-delete.png', 'old-photo');
        Storage::disk('public')->put('images/unrelated-delete.png', 'unrelated-photo');

        $unitKerja = UnitKerja::factory()->create();
        $admin = $this->createAdminUser($unitKerja->id);
        $pegawai = Pegawai::factory()->create([
            'unit_kerja_id' => $unitKerja->id,
            'foto' => '/storage/images/db-owned-delete.png',
        ]);

        $this->actingAs($admin)
            ->delete('/data_pegawai/delete_data_pegawai/' . $pegawai->id)
            ->assertRedirect('/data_pegawai/pegawai')
            ->assertSessionHas('success', 'Berhasil menghapus data!');

        $this->assertDatabaseMissing('tb_pegawai', ['id' => $pegawai->id]);
        Storage::disk('public')->assertMissing('images/db-owned-delete.png');
        Storage::disk('public')->assertExists('images/unrelated-delete.png');
    }

    public function test_mutasi_update_ignores_crafted_file_lama_and_deletes_only_db_owned_document(): void
    {
        Storage::disk('public')->put('document/db-owned-mutasi.pdf', 'old-mutasi');
        Storage::disk('public')->put('document/victim-mutasi.pdf', 'victim-mutasi');

        $unitKerja = UnitKerja::factory()->create();
        $admin = $this->createAdminUser($unitKerja->id);
        $pegawai = Pegawai::factory()->create(['unit_kerja_id' => $unitKerja->id]);
        $mutasi = Mutasi::create($this->validMutasiData($pegawai, [
            'file_sk_mutasi' => '/storage/document/db-owned-mutasi.pdf',
        ]));

        $response = $this->actingAs($admin)
            ->put('/kepegawaian/mutasi/edit_mutasi/' . $mutasi->id, $this->validMutasiData($pegawai, [
                'fileLama' => 'document/victim-mutasi.pdf',
                'file_sk_mutasi' => UploadedFile::fake()->create('evil.php.pdf', 256, 'application/pdf'),
            ]));

        $response->assertRedirect('/kepegawaian/mutasi');
        $response->assertSessionHas('success', 'Berhasil menambahkan data!');

        Storage::disk('public')->assertExists('document/victim-mutasi.pdf');
        Storage::disk('public')->assertMissing('document/db-owned-mutasi.pdf');

        $mutasi->refresh();
        $this->assertStringStartsWith('/storage/document/', $mutasi->file_sk_mutasi);
        $this->assertMatchesRegularExpression('#^/storage/document/[0-9a-f-]{36}\.pdf$#', $mutasi->file_sk_mutasi);
        $this->assertStringNotContainsString('evil.php.pdf', $mutasi->file_sk_mutasi);
        Storage::disk('public')->assertExists(ltrim(str_replace('/storage/', '', $mutasi->file_sk_mutasi), '/'));
    }

    public function test_seminar_store_uses_helper_generated_document_name_not_original_filename(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = $this->createAdminUser($unitKerja->id);
        $pegawai = Pegawai::factory()->create(['unit_kerja_id' => $unitKerja->id]);

        $response = $this->actingAs($admin)
            ->post('/kepegawaian/seminar/tambah_seminar', $this->validSeminarData($pegawai, [
                'file_piagam' => UploadedFile::fake()->create('unicode evil.php.pdf', 256, 'application/pdf'),
            ]));

        $response->assertRedirect('/kepegawaian/seminar');
        $response->assertSessionHas('success', 'Berhasil menambahkan data!');

        $seminar = Seminar::firstOrFail();
        $this->assertStringStartsWith('/storage/document/', $seminar->file_piagam);
        $this->assertMatchesRegularExpression('#^/storage/document/[0-9a-f-]{36}\.pdf$#', $seminar->file_piagam);
        $this->assertStringNotContainsString('unicode evil.php.pdf', $seminar->file_piagam);
        Storage::disk('public')->assertExists(ltrim(str_replace('/storage/', '', $seminar->file_piagam), '/'));
    }

    private function createAdminUser(?int $unitKerjaId = null)
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'unit_kerja_id' => $unitKerjaId ?? UnitKerja::factory()->create()->id,
        ]);

        return $user;
    }

    private function validDiklatData(UploadedFile $file, ?int $unitKerjaId = null)
    {
        $pegawai = Pegawai::factory()->create([
            'unit_kerja_id' => $unitKerjaId ?? UnitKerja::factory(),
        ]);

        return [
            'pegawai_id' => $pegawai->id,
            'nama_diklat' => 'Test Diklat',
            'jumlah_jam' => '40',
            'penyelenggara' => 'BPSDM',
            'tempat' => 'Jakarta',
            'angkatan' => 'I',
            'tahun' => '2025',
            'no_sttpp' => 'STTPP-001',
            'tgl_sttpp' => '2025-01-01',
            'file_sertifikat_diklat' => $file,
        ];
    }

    private function validPegawaiStoreData(User $userPegawai, UnitKerja $unitKerja, array $overrides = []): array
    {
        return array_merge([
            'user_id' => $userPegawai->id,
            'nip' => '199001012020011001',
            'nama' => 'Pegawai Store Fixture',
            'unit_kerja_id' => $unitKerja->id,
            'gelar' => 'S.Kom',
            'gelar_depan' => 'Ir.',
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1988-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Nikah',
            'nik' => '3273010101900001',
            'alamat' => 'Jl. Aman No. 1',
            'no_hp' => '081234567890',
            'email' => 'pegawai.store@example.test',
            'email_gov' => 'pegawai.store@gov.test',
            'no_npwp' => 'NPWP-STORE-001',
            'no_bpjs' => 'BPJS-STORE-001',
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-STORE',
            'no_sk_cpns' => 'SKCPNS-STORE',
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-STORE',
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'foto' => UploadedFile::fake()->image('pegawai.png', 100, 100)->size(128),
            'nilai_tpp' => 0,
        ], $overrides);
    }

    private function validPegawaiUpdateData(Pegawai $pegawai, array $overrides = []): array
    {
        return array_merge([
            'nip' => $pegawai->nip,
            'nama' => 'Nama Pegawai Update',
            'unit_kerja_id' => $pegawai->unit_kerja_id,
            'gelar' => 'S.Kom',
            'gelar_depan' => 'Ir.',
            'tmpt_lahir' => 'Bandung',
            'tgl_lahir' => '1988-01-01',
            'jenis_kelamin' => 'laki-laki',
            'agama' => 'Islam',
            'golongan_darah' => 'O',
            'status_pernikahan' => 'Nikah',
            'nik' => $pegawai->nik,
            'alamat' => 'Jl. Aman No. 1',
            'no_hp' => '081234567890',
            'email' => 'pegawai.update@example.test',
            'email_gov' => 'pegawai.update@gov.test',
            'no_npwp' => $pegawai->no_npwp,
            'no_bpjs' => $pegawai->no_bpjs,
            'status_kepegawaian' => 'PNS',
            'karpeg' => 'KARPEG-UPDATE',
            'no_sk_cpns' => 'SKCPNS-UPDATE',
            'tmt_cpns' => '2020-01-01',
            'no_sk_pns' => 'SKPNS-UPDATE',
            'tmt_pns' => '2022-01-01',
            'gol_awal' => 'III/a',
            'nilai_tpp' => 0,
        ], $overrides);
    }

    private function validMutasiData(Pegawai $pegawai, array $overrides = []): array
    {
        return array_merge([
            'pegawai_id' => $pegawai->id,
            'jenis_mutasi' => 'Masuk',
            'instansi_tujuan' => 'BKD Tujuan',
            'no_sk_mutasi' => 'SK-MUTASI-001',
            'tgl_sk_mutasi' => '2025-01-01',
            'file_sk_mutasi' => '/storage/document/existing-mutasi.pdf',
        ], $overrides);
    }

    private function validSeminarData(Pegawai $pegawai, array $overrides = []): array
    {
        return array_merge([
            'pegawai_id' => $pegawai->id,
            'nama_seminar' => 'Seminar Kepegawaian',
            'tingkat_kegiatan' => 'Nasional',
            'tempat_seminar' => 'Jakarta',
            'tgl_seminar' => '2025-01-01',
            'penyelenggara' => 'BPSDM',
            'jumlah_jam' => '8',
            'no_piagam' => 'PIAGAM-001',
            'tgl_piagam' => '2025-01-02',
            'file_piagam' => UploadedFile::fake()->create('piagam.pdf', 128, 'application/pdf'),
        ], $overrides);
    }
}
