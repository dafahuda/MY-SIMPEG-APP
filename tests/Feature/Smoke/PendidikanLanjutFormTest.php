<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\RiwayatPendidikanLanjut;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendidikanLanjutFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/riwayat_pendidikan/sekolah_lanjut';

    private const INPUTS = ['nama_sekolah_universitas', 'jurusan', 'thn_mulai', 'thn_selesai'];

    private const SELECTS = ['pegawai_id', 'jenjang_pendidikan', 'status'];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id, 'nama' => 'Pegawai Pendidikan Lanjut',
        ]);
        $record = RiwayatPendidikanLanjut::factory()->create([
            'pegawai_id' => $pegawai->id, 'jenjang_pendidikan' => 'S2', 'status' => 'Ijin Belajar',
            'thn_mulai' => '2022', 'thn_selesai' => '2026',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_pendidikan_lanjut')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_pendidikan_lanjut');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(@class, "confirm-save")]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_pendidikan_lanjut/' . $record->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_pendidikan_lanjut/' . $record->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($editForm, 'jenjang_pendidikan', $record->jenjang_pendidikan);
        $this->assertSelected($editForm, 'status', $record->status);
        foreach (self::INPUTS as $name) {
            $this->assertSame($record->{$name}, $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0)->getAttribute('value'), "Expected {$name} model default");
        }
        $buttons = $editXpath->query('.//button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and normalize-space(.)="Simpan"]', $editForm);
        $this->assertCount(1, $buttons);
        $this->assertSame('Simpan Perubahan', $buttons->item(0)->getAttribute('data-title'));
        $this->assertSame('Apakah Anda yakin ingin menyimpan perubahan data pendidikan lanjut ini?', $buttons->item(0)->getAttribute('data-message'));
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $record = RiwayatPendidikanLanjut::factory()->create(['pegawai_id' => $pegawai->id]);
        $old = [
            'pegawai_id' => $otherPegawai->id,
            'jenjang_pendidikan' => 'S3',
            'status' => 'Ijin Belajar',
            'nama_sekolah_universitas' => 'Universitas Lama',
            'jurusan' => 'Jurusan Lama',
            'thn_mulai' => '2020',
            'thn_selesai' => '2024',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_pendidikan_lanjut/' . $record->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_pendidikan_lanjut/' . $record->id);
        foreach (self::SELECTS as $name) {
            $this->assertSelected($form, $name, (string) $old[$name]);
        }
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
        foreach (array_merge(array_fill_keys(self::SELECTS, 'select'), array_fill_keys(self::INPUTS, 'input')) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $this->assertCount(1, $xpath->query('.//label[@for="' . ($name === 'thn_selesai' ? 'thn_mulai' : $name) . '"]', $form));
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
