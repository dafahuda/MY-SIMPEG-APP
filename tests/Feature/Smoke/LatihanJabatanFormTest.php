<?php

namespace Tests\Feature\Smoke;

use App\Models\LatihanJabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LatihanJabatanFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/latihan_jabatan';

    private const INPUTS = [
        'nama_pelatih', 'tempat_latihan', 'waktu_latihan', 'tahun_latihan',
        'jumlah_jam', 'nomor_sertifikat', 'tgl_sertifikat',
    ];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Latihan',
        ]);
        $latihanJabatan = LatihanJabatan::factory()->create([
            'pegawai_id' => $pegawai->id,
            'file_sertifikat' => 'sertifikat.pdf',
        ]);

        $create = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_tambah_latihan_jabatan')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_latihan_jabatan');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="fileLama"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(@class,"confirm-save")]', $createForm)->length);

        $edit = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_edit_latihan_jabatan/' . $latihanJabatan->id)
            ->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_latihan_jabatan/' . $latihanJabatan->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertSame(1, $editXpath->query('.//input[@type="hidden" and @name="fileLama"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, (string) $pegawai->id);
        foreach (self::INPUTS as $name) {
            $input = $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame((string) $latihanJabatan->{$name}, $input->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $editXpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Konfirmasi Simpan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data latihan jabatan ini?" and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $latihanJabatan = LatihanJabatan::factory()->create(['pegawai_id' => $pegawai->id]);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'nama_pelatih' => 'Pelatih lama',
            'waktu_latihan' => '2026-03-04',
            'nomor_sertifikat' => 'OLD/002/2026',
        ];
        $edit = $this->actingAs($admin)
            ->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_latihan_jabatan/' . $latihanJabatan->id)
            ->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_latihan_jabatan/' . $latihanJabatan->id);
        $this->assertSelected($form, (string) $otherPegawai->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['nama_pelatih', 'waktu_latihan', 'nomor_sertifikat'] as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
    }

    private function formFor(string $html, string $action): DOMElement
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $forms = (new DOMXPath($document))->query('//form[@action="' . $action . '"]');
        $this->assertCount(1, $forms, "Expected form action {$action}");

        return $forms->item(0);
    }

    private function assertFields(DOMElement $form): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (array_merge(['pegawai_id' => 'select'], array_fill_keys([...self::INPUTS, 'file_sertifikat'], 'input')) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $labelFor = ['waktu_latihan' => 'tempat_latihan', 'tgl_sertifikat' => 'nomor_sertifikat'][$name] ?? $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
        $this->assertSame(1, $xpath->query('.//input[@name="file_sertifikat" and @type="file" and @required]', $form)->length);
        $this->assertSame('multipart/form-data', $form->getAttribute('enctype'));
    }

    private function assertSelected(DOMElement $form, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query(
            './/select[@name="pegawai_id"]/option[@value="' . $value . '" and @selected]',
            $form
        )->length);
    }
}
