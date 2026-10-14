<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\Penghargaan;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenghargaanFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/penghargaan';

    private const INPUTS = [
        'nama_penghargaan', 'instansi_pemberi', 'tempat_penghargaan',
        'tgl_penghargaan', 'tahun', 'no_sertifikat',
    ];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Penghargaan',
        ]);
        $penghargaan = Penghargaan::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tingkat_kegiatan' => 'Regional',
            'file_sertifikat_penghargaan' => '/storage/document/sertifikat.pdf',
        ]);

        $create = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_tambah_penghargaan')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_penghargaan');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertSame('multipart/form-data', $createForm->getAttribute('enctype'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method" or @name="fileLama"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(@class, "confirm-save")]', $createForm)->length);

        $edit = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_edit_penghargaan/' . $penghargaan->id)
            ->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_penghargaan/' . $penghargaan->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertSame('multipart/form-data', $editForm->getAttribute('enctype'));
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertSame(1, $editXpath->query('.//input[@name="fileLama" and @value="/storage/document/sertifikat.pdf"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($editForm, 'tingkat_kegiatan', 'Regional');
        foreach (self::INPUTS as $name) {
            $input = $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame((string) $penghargaan->{$name}, $input->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $editXpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Simpan Perubahan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data penghargaan ini?" and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
        $this->assertSame(0, $editXpath->query('.//button[@type="submit"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $penghargaan = Penghargaan::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tingkat_kegiatan' => 'Nasional',
        ]);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'nama_penghargaan' => 'Penghargaan lama',
            'instansi_pemberi' => 'Instansi lama',
            'tempat_penghargaan' => 'Bandung',
            'tgl_penghargaan' => '2026-03-04',
            'tahun' => '2025',
            'no_sertifikat' => 'OLD/002/2026',
        ];
        $edit = $this->actingAs($admin)
            ->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_penghargaan/' . $penghargaan->id)
            ->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_penghargaan/' . $penghargaan->id);
        $this->assertSelected($form, 'pegawai_id', (string) $otherPegawai->id);
        $this->assertSelected($form, 'tingkat_kegiatan', 'Nasional');
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (self::INPUTS as $name) {
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
        $fields = ['pegawai_id' => 'select', 'tingkat_kegiatan' => 'select',
            'file_sertifikat_penghargaan' => 'input'] + array_fill_keys(self::INPUTS, 'input');
        foreach ($fields as $name => $tag) {
            $found = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $found, "Expected {$name} field");
            $this->assertSame($name, $found->item(0)->getAttribute('id'));
            $labelFor = $name === 'tgl_penghargaan' ? 'tempat_penghargaan' : $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
        $this->assertSame('file', $xpath->query('.//input[@name="file_sertifikat_penghargaan"]', $form)->item(0)->getAttribute('type'));
        foreach (['Lokal', 'Regional', 'Nasional', 'Internasional'] as $level) {
            $this->assertSame(1, $xpath->query('.//select[@name="tingkat_kegiatan"]/option[@value="' . $level . '"]', $form)->length);
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query(
            './/select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]',
            $form
        )->length);
    }
}
