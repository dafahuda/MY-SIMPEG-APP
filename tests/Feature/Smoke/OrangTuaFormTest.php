<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\RiwayatKeluargaOrangtua;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangTuaFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/riwayat_keluarga/orang_tua';

    private const INPUTS = ['nik', 'nama', 'tempat_lahir', 'tgl_lahir', 'pekerjaan'];

    private const SELECTS = ['pegawai_id', 'jenis_kelamin', 'pendidikan', 'status_hubungan'];

    public function test_admin_create_and_edit_forms_render_fields_defaults_and_save_behavior(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $orangTua = RiwayatKeluargaOrangtua::factory()->create([
            'pegawai_id' => $pegawai->id,
            'nik' => '3273012501600002',
            'nama' => 'Ibu Contoh',
            'tempat_lahir' => 'Bandung',
            'tgl_lahir' => '1960-01-25',
            'jenis_kelamin' => 'perempuan',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Guru',
            'status_hubungan' => 'Ibu Kandung',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_orang_tua')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_data_orang_tua');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(concat(" ", normalize-space(@class), " "), " confirm-save ")]', $createForm)->length);
        foreach (self::INPUTS as $name) {
            $this->assertSame('', $createXpath->query('.//input[@name="' . $name . '"]', $createForm)->item(0)->getAttribute('value'));
        }

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_orang_tua/' . $orangTua->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_data_orang_tua/' . $orangTua->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertFields($editForm);
        $xpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        foreach (self::INPUTS as $name) {
            $this->assertSame($orangTua->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0)->getAttribute('value'), "Expected {$name} model default");
        }
        foreach (['pegawai_id' => (string) $pegawai->id, 'jenis_kelamin' => 'perempuan', 'pendidikan' => 'S1', 'status_hubungan' => 'Ibu Kandung'] as $name => $value) {
            $this->assertSelected($editForm, $name, $value);
        }
        $this->assertSame(1, $xpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Simpan Perubahan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data orang tua ini?" and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
        $this->assertSame(0, $xpath->query('.//button[@type="submit"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_values_where_supported(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $orangTua = RiwayatKeluargaOrangtua::factory()->create(['pegawai_id' => $pegawai->id, 'nama' => 'Nama Model']);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'nik' => 'OLD-NIK',
            'nama' => 'Nama Lama',
            'tempat_lahir' => 'Jakarta',
            'tgl_lahir' => '1970-02-03',
            'pekerjaan' => 'Dokter',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_orang_tua/' . $orangTua->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_orang_tua/' . $orangTua->id);
        $this->assertSelected($form, 'pegawai_id', (string) $otherPegawai->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['nik', 'tempat_lahir', 'tgl_lahir', 'pekerjaan'] as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
        // The existing edit form intentionally uses the model name rather than old('nama').
        $this->assertSame('Nama Model', $xpath->query('.//input[@name="nama"]', $form)->item(0)->getAttribute('value'));
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
            $this->assertCount(1, $xpath->query('.//' . $tag . '[@name="' . $name . '" and @id="' . $name . '"]', $form), "Expected {$name} field");
            $labelFor = $name === 'tgl_lahir' ? 'tempat_lahir' : $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
