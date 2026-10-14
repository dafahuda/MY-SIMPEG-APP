<?php

namespace Tests\Feature\Smoke;

use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitKerjaFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_edit_forms_render_their_actions_fields_and_buttons(): void
    {
        $unitKerja = UnitKerja::factory()->create([
            'nama_unit' => 'Unit Kerja Pengujian',
            'alamat' => 'Jalan Pengujian 42',
        ]);
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'unit_kerja_id' => $unitKerja->id,
        ]);

        $create = $this->actingAs($superadmin)
            ->get('/manajemen_setup/view_form_tambah_unitkerja')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), '/manajemen_setup/opd_skd_unitkerja/tambah_unit_kerja');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        foreach (['nama_unit', 'alamat'] as $name) {
            $input = $createXpath->query('.//input[@name="' . $name . '"]', $createForm)->item(0);
            $this->assertSame($name, $input->getAttribute('id'));
            $this->assertCount(1, $createXpath->query('.//label[@for="' . $input->getAttribute('id') . '"]', $createForm));
        }
        $this->assertSame(1, (new DOMXPath($createForm->ownerDocument))
            ->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);

        $edit = $this->actingAs($superadmin)
            ->get('/manajemen_setup/view_form_edit_unitkerja/' . $unitKerja->id)
            ->assertOk();
        $editForm = $this->formFor($edit->getContent(), '/manajemen_setup/opd_skd_unitkerja/edit_unit_kerja/' . $unitKerja->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertSame(1, (new DOMXPath($editForm->ownerDocument))
            ->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm, $unitKerja);
        $this->assertSame(1, (new DOMXPath($editForm->ownerDocument))
            ->query('.//button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and normalize-space(.)="Simpan"]', $editForm)->length);
    }

    public function test_create_form_renders_flashed_old_input(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'unit_kerja_id' => $unitKerja->id,
        ]);

        $create = $this->actingAs($superadmin)
            ->withSession(['_old_input' => ['nama_unit' => 'Old Name', 'alamat' => 'Old Address']])
            ->get('/manajemen_setup/view_form_tambah_unitkerja')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), '/manajemen_setup/opd_skd_unitkerja/tambah_unit_kerja');
        $xpath = new DOMXPath($createForm->ownerDocument);
        foreach (['nama_unit' => 'Old Name', 'alamat' => 'Old Address'] as $name => $value) {
            $inputs = $xpath->query('.//input[@name="' . $name . '"]', $createForm);
            $this->assertCount(1, $inputs, "Expected {$name} input");
            $this->assertSame($value, $inputs->item(0)->getAttribute('value'));
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

    private function assertFields(DOMElement $form, ?UnitKerja $unitKerja = null): void
    {
        $xpath = new DOMXPath($form->ownerDocument);

        foreach (['nama_unit', 'alamat'] as $name) {
            $inputs = $xpath->query('.//input[@name="' . $name . '"]', $form);
            $this->assertCount(1, $inputs, "Expected {$name} input");
            if ($unitKerja !== null) {
                $this->assertSame($unitKerja->{$name}, $inputs->item(0)->getAttribute('value'));
            }
        }
    }
}
