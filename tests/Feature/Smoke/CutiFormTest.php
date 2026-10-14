<?php

namespace Tests\Feature\Smoke;

use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CutiFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/cuti';

    private const INPUTS = [
        'no_surat_cuti', 'tgl_surat_cuti', 'pelaksanaan_cuti_mulai',
        'pelaksanaan_cuti_selesai', 'durasi_cuti', 'ketentuan_a',
        'ketentuan_b', 'ketentuan_c', 'tebusan',
    ];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Cuti',
        ]);
        $cuti = Cuti::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_cuti' => 'Sakit',
            'file_surat_cuti' => '/storage/document/surat-cuti.pdf',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_cuti')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_cuti');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method" or @name="fileLama"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(@class,"confirm-save")]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_cuti/' . $cuti->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_riwayat_cuti/' . $cuti->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertFields($editForm);
        $xpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertSame(1, $xpath->query('.//input[@name="fileLama" and @value="/storage/document/surat-cuti.pdf"]', $editForm)->length);
        $this->assertSame(1, $xpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '" and @selected]', $editForm)->length);
        $this->assertSame(1, $xpath->query('.//select[@name="jenis_cuti"]/option[@value="Sakit" and @selected]', $editForm)->length);
        foreach (self::INPUTS as $name) {
            $input = $xpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame((string) $cuti->{$name}, $input->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $xpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Konfirmasi Simpan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data cuti ini?" and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
        $this->assertSame(0, $xpath->query('.//button[@type="submit"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $cuti = Cuti::factory()->create(['pegawai_id' => $pegawai->id]);
        $old = [
            'pegawai_id' => $otherPegawai->id,
            'no_surat_cuti' => 'OLD/002/2026',
            'tgl_surat_cuti' => '2026-03-04',
            'pelaksanaan_cuti_mulai' => '2026-03-05',
            'pelaksanaan_cuti_selesai' => '2026-03-06',
            'durasi_cuti' => '2',
            'ketentuan_a' => 'A lama',
            'ketentuan_b' => 'B lama',
            'ketentuan_c' => 'C lama',
            'tebusan' => 'Tebusan lama',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_cuti/' . $cuti->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_riwayat_cuti/' . $cuti->id);
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="pegawai_id"]/option[@value="' . $otherPegawai->id . '" and @selected]', $form)->length);
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
        foreach (array_merge(['pegawai_id' => 'select', 'jenis_cuti' => 'select'], array_fill_keys(self::INPUTS, 'input'), ['file_surat_cuti' => 'input']) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $labelFor = [
                'tgl_surat_cuti' => 'no_surat_cuti',
                'pelaksanaan_cuti_selesai' => 'pelaksanaan_cuti_mulai',
            ][$name] ?? $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
        $this->assertSame(1, $xpath->query('.//input[@name="file_surat_cuti" and @type="file" and @required]', $form)->length);
    }
}
