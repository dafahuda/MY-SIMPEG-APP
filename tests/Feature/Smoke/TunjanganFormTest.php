<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\Tunjangan;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TunjanganFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/tunjangan';

    private const INPUTS = [
        'no_tunjangan', 'jenis_tunjangan_anak', 'tgl_tunjangan', 'terhitung_mulai',
        'akta_perkawinan_dari', 'no_akta_perkawinan', 'tgl_akta_perkawinan',
        'akta_kelahiran_dari', 'no_akta_kelahiran', 'tgl_akta_kelahiran', 'tebusan',
    ];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Tunjangan',
        ]);
        $tunjangan = Tunjangan::factory()->create([
            'pegawai_id' => $pegawai->id,
            'terhitung_mulai' => '2026-02-01',
        ]);

        $create = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_tambah_tunjangan')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_tunjangan');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);

        $edit = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_edit_tunjangan/' . $tunjangan->id)
            ->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_tunjangan/' . $tunjangan->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, (string) $pegawai->id);
        foreach (self::INPUTS as $name) {
            $input = $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame($tunjangan->{$name}, $input->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $editXpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and normalize-space(.)="Simpan"]',
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
        $tunjangan = Tunjangan::factory()->create([
            'pegawai_id' => $pegawai->id,
            'terhitung_mulai' => '2026-02-01',
        ]);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'no_tunjangan' => 'OLD/002/2026',
            'tgl_tunjangan' => '2026-03-04',
            'tebusan' => 'Tebusan lama',
        ];
        $edit = $this->actingAs($admin)
            ->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_tunjangan/' . $tunjangan->id)
            ->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_tunjangan/' . $tunjangan->id);
        $this->assertSelected($form, (string) $otherPegawai->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['no_tunjangan', 'tgl_tunjangan', 'tebusan'] as $name) {
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
        foreach (array_merge(['pegawai_id' => 'select'], array_fill_keys(self::INPUTS, 'input')) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            // Date inputs share a visible label with the number input beside them.
            $labelFor = [
                'tgl_tunjangan' => 'no_tunjangan',
                'tgl_akta_perkawinan' => 'no_akta_perkawinan',
                'tgl_akta_kelahiran' => 'no_akta_kelahiran',
            ][$name] ?? $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
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
