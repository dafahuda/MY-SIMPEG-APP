<?php

namespace Tests\Feature\Smoke;

use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAdminFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/manajemen_setup';

    public function test_superadmin_create_and_edit_forms_render_actions_fields_defaults_and_buttons(): void
    {
        $unit = UnitKerja::factory()->create(['nama_unit' => 'Unit Pengujian']);
        $otherUnit = UnitKerja::factory()->create(['nama_unit' => 'Unit Lain']);
        $superadmin = User::factory()->create(['role' => 'superadmin', 'unit_kerja_id' => $otherUnit->id]);
        $admin = User::factory()->create([
            'role' => 'admin', 'unit_kerja_id' => $unit->id,
            'username' => 'admin_uji', 'name' => 'Admin Pengujian', 'email' => 'admin-uji@example.test',
        ]);

        $create = $this->actingAs($superadmin)->get(self::BASE . '/view_form_tambah_user_admin')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_user_admin');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        foreach (['username', 'name', 'email'] as $name) {
            $this->assertFalse($createXpath->query('.//input[@name="' . $name . '"]', $createForm)->item(0)->hasAttribute('value'));
        }
        $this->assertSame(1, $createXpath->query('.//input[@name="password" and @type="password" and @required and not(@value)]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//select[@name="unit_kerja_id"]/option[@value="' . $unit->id . '" and normalize-space(.)="Unit Pengujian"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//select[@name="unit_kerja_id"]/option[@selected]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(concat(" ", normalize-space(@class), " "), " confirm-save ")]', $createForm)->length);

        $edit = $this->actingAs($superadmin)->get(self::BASE . '/view_form_edit_user_admin/' . $admin->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_data_user_admin/' . $admin->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertFields($editForm);
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        foreach (['username', 'name', 'email'] as $name) {
            $this->assertSame($admin->{$name}, $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0)->getAttribute('value'));
        }
        $this->assertSelected($editForm, (string) $unit->id);
        $this->assertSame(0, $editXpath->query('.//input[@name="password"]', $editForm)->length);
        $this->assertSame(1, $editXpath->query('.//button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Simpan Perubahan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data user admin ini?" and normalize-space(.)="Simpan"]', $editForm)->length);
        $this->assertSame(0, $editXpath->query('.//button[@type="submit"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_identity_and_unit(): void
    {
        $unit = UnitKerja::factory()->create();
        $otherUnit = UnitKerja::factory()->create();
        $superadmin = User::factory()->create(['role' => 'superadmin', 'unit_kerja_id' => $unit->id]);
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $old = [
            'username' => 'old_admin', 'name' => 'Nama Lama',
            'email' => 'old@example.test', 'unit_kerja_id' => $otherUnit->id,
        ];

        $edit = $this->actingAs($superadmin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_user_admin/' . $admin->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_user_admin/' . $admin->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['username', 'name', 'email'] as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
        $this->assertSelected($form, (string) $otherUnit->id);
        $this->assertSame(0, $xpath->query('.//select[@name="unit_kerja_id"]/option[@value="' . $unit->id . '" and @selected]', $form)->length);
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
        foreach (['username', 'name', 'email', 'unit_kerja_id'] as $name) {
            $tag = $name === 'unit_kerja_id' ? 'select' : 'input';
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $this->assertCount(1, $xpath->query('.//label[@for="' . $name . '"]', $form));
        }
    }

    private function assertSelected(DOMElement $form, string $id): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="unit_kerja_id"]/option[@value="' . $id . '" and @selected]', $form)->length);
    }
}
