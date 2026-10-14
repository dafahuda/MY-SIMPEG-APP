<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\RiwayatPendidikanBahasa;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendidikanBahasaFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/riwayat_pendidikan/pendidikan_bahasa';

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Bahasa',
        ]);
        $bahasa = RiwayatPendidikanBahasa::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_bahasa' => 'Asing',
            'bahasa' => 'Jepang',
            'kemampuan_bicara' => 'Pasif',
        ]);

        $create = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_tambah_pendidikan_bahasa')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_data_bahasa');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);

        $edit = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_edit_pendidikan_bahasa/' . $bahasa->id)
            ->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_data_bahasa/' . $bahasa->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($editForm, 'kemampuan_bicara', 'Pasif');
        foreach (['jenis_bahasa', 'bahasa'] as $name) {
            $input = $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame($bahasa->{$name}, $input->getAttribute('value'));
        }
        $this->assertSame(1, $editXpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
    }

    public function test_edit_form_prefers_flashed_old_text_and_pegawai_input(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $bahasa = RiwayatPendidikanBahasa::factory()->create(['pegawai_id' => $pegawai->id]);

        $edit = $this->actingAs($admin)
            ->withSession(['_old_input' => [
                'pegawai_id' => $otherPegawai->id,
                'jenis_bahasa' => 'Old Jenis',
                'bahasa' => 'Old Bahasa',
            ]])
            ->get(self::BASE . '/view_form_edit_pendidikan_bahasa/' . $bahasa->id)
            ->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_bahasa/' . $bahasa->id);
        $this->assertSelected($form, 'pegawai_id', (string) $otherPegawai->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['jenis_bahasa' => 'Old Jenis', 'bahasa' => 'Old Bahasa'] as $name => $value) {
            $this->assertSame($value, $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
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
        foreach (['pegawai_id' => 'select', 'jenis_bahasa' => 'input', 'bahasa' => 'input', 'kemampuan_bicara' => 'select'] as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $this->assertCount(1, $xpath->query('.//label[@for="' . $name . '"]', $form));
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
