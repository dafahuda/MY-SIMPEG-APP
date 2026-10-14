<?php

namespace Tests\Feature\Smoke;

use App\Models\MasterGolongan;
use App\Models\MasterPangkat;
use App\Models\Pangkat;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PangkatFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/pangkat';

    private const INPUTS = [
        'jenis_pangkat', 'tmt_pangkat_mulai', 'tmt_pangkat_selesai',
        'no_sk', 'tgl_sk', 'pejabat_pengesah_sk',
    ];

    private const SELECTS = ['pegawai_id', 'master_pangkat_id', 'master_golongan_id'];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Pangkat',
        ]);
        $masterPangkat = MasterPangkat::factory()->create();
        $masterGolongan = MasterGolongan::factory()->create();
        $pangkat = Pangkat::factory()->create([
            'pegawai_id' => $pegawai->id,
            'master_pangkat_id' => $masterPangkat->id,
            'master_golongan_id' => $masterGolongan->id,
            'tmt_pangkat_mulai' => '2026-02-01',
            'tmt_pangkat_selesai' => '2026-12-31',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_pangkat')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_data_pangkat');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $options = [
            'pegawai_id' => $pegawai->id,
            'master_pangkat_id' => $masterPangkat->id,
            'master_golongan_id' => $masterGolongan->id,
        ];
        foreach ($options as $name => $id) {
            $this->assertSame(1, $createXpath->query('.//select[@name="' . $name . '"]/option[@value="' . $id . '"]', $createForm)->length);
        }
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(concat(" ", normalize-space(@class), " "), " confirm-save ")]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_data_pangkat/' . $pangkat->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_data_pangkat/' . $pangkat->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $xpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm);
        foreach (self::SELECTS as $name) {
            $this->assertSelected($editForm, $name, (string) $pangkat->{$name});
        }
        foreach (self::INPUTS as $name) {
            $input = $xpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame($pangkat->{$name}, $input->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $xpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Simpan Perubahan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data pangkat ini?" and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
        $this->assertSame(0, $xpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_selections(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $masterPangkat = MasterPangkat::factory()->create();
        $otherMasterPangkat = MasterPangkat::factory()->create(['nama_pangkat' => 'Pembina']);
        $masterGolongan = MasterGolongan::factory()->create();
        $otherMasterGolongan = MasterGolongan::factory()->create(['nama_golongan' => 'IV/a']);
        $pangkat = Pangkat::factory()->create([
            'pegawai_id' => $pegawai->id,
            'master_pangkat_id' => $masterPangkat->id,
            'master_golongan_id' => $masterGolongan->id,
            'tmt_pangkat_mulai' => '2026-02-01',
            'tmt_pangkat_selesai' => '2026-12-31',
        ]);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'master_pangkat_id' => $otherMasterPangkat->id,
            'master_golongan_id' => $otherMasterGolongan->id,
            'jenis_pangkat' => 'Pangkat lama',
            'tmt_pangkat_mulai' => '2026-03-04',
            'tmt_pangkat_selesai' => '2026-11-30',
            'no_sk' => 'OLD/002/2026',
            'tgl_sk' => '2026-03-05',
            'pejabat_pengesah_sk' => 'Pejabat lama',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_data_pangkat/' . $pangkat->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_pangkat/' . $pangkat->id);
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
            $labelFor = ['tmt_pangkat_selesai' => 'tmt_pangkat_mulai', 'tgl_sk' => 'no_sk'][$name] ?? $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query(
            './/select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form
        )->length);
    }
}
