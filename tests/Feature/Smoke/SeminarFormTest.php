<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\Seminar;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeminarFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/seminar';

    private const INPUTS = [
        'tempat_seminar', 'tgl_seminar', 'penyelenggara', 'jumlah_jam', 'no_piagam', 'tgl_piagam',
    ];

    public function test_admin_create_and_edit_forms_render_fields_model_values_and_distinct_save_controls(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai Seminar',
        ]);
        $seminar = Seminar::factory()->create([
            'pegawai_id' => $pegawai->id,
            'nama_seminar' => 'Seminar Pengujian',
            'tingkat_kegiatan' => 'Regional',
            'tempat_seminar' => 'Aula Pengujian',
            'tgl_seminar' => '2026-02-03',
            'penyelenggara' => 'Penyelenggara Pengujian',
            'jumlah_jam' => '12',
            'no_piagam' => 'PIAGAM/123',
            'tgl_piagam' => '2026-02-04',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_seminar')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_seminar');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertSame('multipart/form-data', $createForm->getAttribute('enctype'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//input[@name="file_piagam" and @type="file" and @required]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(concat(" ", normalize-space(@class), " "), " confirm-save ")]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_seminar/' . $seminar->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_seminar/' . $seminar->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertSame('multipart/form-data', $editForm->getAttribute('enctype'));
        $this->assertFields($editForm);
        $xpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertSelected($editForm, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($editForm, 'tingkat_kegiatan', 'Regional');
        $this->assertSame($seminar->nama_seminar, trim($xpath->query('.//textarea[@name="nama_seminar"]', $editForm)->item(0)->textContent));
        foreach (self::INPUTS as $name) {
            $this->assertSame((string) $seminar->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0)->getAttribute('value'), "Expected {$name} model value");
        }
        $this->assertSame(1, $xpath->query('.//input[@name="file_piagam" and @type="file" and not(@required) and not(@value)]', $editForm)->length);
        $this->assertSame(0, $xpath->query('.//button[@type="submit"]', $editForm)->length);
        $this->assertSame(1, $xpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and normalize-space(.)="Simpan" and @data-title="Konfirmasi Simpan" and @data-message]',
            $editForm
        )->length);
    }

    public function test_edit_form_prefers_flashed_old_pegawai_and_text_and_date_values(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $other = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $seminar = Seminar::factory()->create(['pegawai_id' => $pegawai->id]);
        $old = [
            'pegawai_id' => $other->id,
            'nama_seminar' => 'Nama lama dari input',
            'tempat_seminar' => 'Tempat lama',
            'tgl_seminar' => '2025-03-04',
            'penyelenggara' => 'Penyelenggara lama',
            'jumlah_jam' => '24',
            'no_piagam' => 'LAMA/456',
            'tgl_piagam' => '2025-03-05',
        ];

        $response = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_seminar/' . $seminar->id)->assertOk();
        $form = $this->formFor($response->getContent(), self::BASE . '/edit_seminar/' . $seminar->id);
        $this->assertSelected($form, 'pegawai_id', (string) $other->id);
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame($old['nama_seminar'], trim($xpath->query('.//textarea[@name="nama_seminar"]', $form)->item(0)->textContent));
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
        $fields = [
            'pegawai_id' => 'select',
            'nama_seminar' => 'textarea',
            'tingkat_kegiatan' => 'select',
            'tempat_seminar' => 'input',
            'tgl_seminar' => 'input',
            'penyelenggara' => 'input',
            'jumlah_jam' => 'input',
            'no_piagam' => 'input',
            'tgl_piagam' => 'input',
            'file_piagam' => 'input',
        ];
        foreach ($fields as $name => $tag) {
            $nodes = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $nodes, "Expected {$name} field");
            $this->assertSame($name, $nodes->item(0)->getAttribute('id'));
        }
        foreach (['pegawai_id', 'nama_seminar', 'tingkat_kegiatan', 'tempat_seminar', 'penyelenggara', 'jumlah_jam', 'no_piagam', 'file_piagam'] as $name) {
            $this->assertCount(1, $xpath->query('.//label[@for="' . $name . '"]', $form));
        }
        foreach (['tgl_seminar', 'tgl_piagam'] as $name) {
            $this->assertSame(1, $xpath->query('.//input[@name="' . $name . '" and @type="date"]', $form)->length);
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
