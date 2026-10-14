<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\RiwayatPendidikanSekolah;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendidikanSekolahFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/riwayat_pendidikan/sekolah';

    private const INPUTS = [
        'nama_sekolah_universitas', 'no_ijazah', 'tgl_ijazah',
        'lokasi', 'jurusan', 'nama_kepsek_rektor',
    ];

    private const JENJANG = [
        'SD', 'MI', 'SMP', 'MTS', 'SMK', 'SMA', 'MA', 'D3', 'S1', 'S2', 'S3', 'Profesi',
    ];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Sekolah',
        ]);
        $sekolah = RiwayatPendidikanSekolah::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenjang_pendidikan' => 'S2',
            'tgl_ijazah' => '2026-02-01',
        ]);

        $create = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_tambah_pendidikan_sekolah')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_pendidikan_sekolah');
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

        $edit = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_edit_pendidikan_sekolah/' . $sekolah->id)
            ->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_pendidikan_sekolah/' . $sekolah->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($editForm, 'jenjang_pendidikan', 'S2');
        foreach (self::INPUTS as $name) {
            $input = $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame($sekolah->{$name}, $input->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $editXpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Simpan Perubahan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data pendidikan sekolah ini?" and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
        $this->assertSame(0, $editXpath->query('.//button[@type="submit"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai_but_not_jenjang(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $sekolah = RiwayatPendidikanSekolah::factory()->create([
            'pegawai_id' => $pegawai->id,
            'jenjang_pendidikan' => 'S1',
        ]);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'jenjang_pendidikan' => 'D3',
            'nama_sekolah_universitas' => 'Sekolah lama',
            'no_ijazah' => 'OLD/002/2026',
            'tgl_ijazah' => '2026-03-04',
            'lokasi' => 'Lokasi lama',
            'jurusan' => 'Jurusan lama',
            'nama_kepsek_rektor' => 'Rektor lama',
        ];
        $edit = $this->actingAs($admin)
            ->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_pendidikan_sekolah/' . $sekolah->id)
            ->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_pendidikan_sekolah/' . $sekolah->id);
        $this->assertSelected($form, 'pegawai_id', (string) $otherPegawai->id);
        // Jenjang's edit options use the model directly, unlike pegawai and text inputs.
        $this->assertSelected($form, 'jenjang_pendidikan', 'S1');
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
        foreach (array_merge(['pegawai_id' => 'select', 'jenjang_pendidikan' => 'select'], array_fill_keys(self::INPUTS, 'input')) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $labelFor = $name === 'tgl_ijazah' ? 'no_ijazah' : $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
        foreach (self::JENJANG as $level) {
            $this->assertSame(1, $xpath->query('.//select[@name="jenjang_pendidikan"]/option[@value="' . $level . '"]', $form)->length);
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
