<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PegawaiFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/data_pegawai';

    private const INPUTS = [
        'nip', 'nama', 'gelar_depan', 'gelar', 'tmpt_lahir', 'tgl_lahir',
        'nik', 'no_hp', 'email', 'email_gov', 'alamat', 'no_npwp', 'no_bpjs',
        'karpeg', 'no_sk_cpns', 'tmt_cpns', 'no_sk_pns', 'tmt_pns',
        'gol_awal', 'foto', 'nilai_tpp',
    ];

    private const SELECTS = [
        'user_id', 'jenis_kelamin', 'agama', 'golongan_darah',
        'status_pernikahan', 'status_kepegawaian', 'unit_kerja_id',
    ];

    public function test_superadmin_create_and_edit_forms_render_fields_and_save_behavior(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'superadmin', 'unit_kerja_id' => $unit->id]);
        $account = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $account->id,
            'unit_kerja_id' => $unit->id,
            'nama' => 'Pegawai Form',
            'agama' => 'Hindu',
            'status_pernikahan' => 'Nikah',
            'foto' => 'foto/pegawai.jpg',
            'nilai_tpp' => 5000,
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_data_pegawai')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_data_pegawai');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//input[@name="foto" and @type="file" and @required]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_data_pegawai/' . $pegawai->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/ubah_data_pegawai/' . $pegawai->id);
        $this->assertSame('POST', $form->getAttribute('method'));
        $this->assertFields($form);
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $form)->length);
        foreach (['nip', 'nama', 'nik', 'no_hp', 'email', 'alamat', 'nilai_tpp'] as $name) {
            $this->assertSame((string) $pegawai->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'), $name);
        }
        foreach (['user_id' => $account->id, 'agama' => 'Hindu', 'status_pernikahan' => 'Nikah', 'unit_kerja_id' => $unit->id] as $name => $value) {
            $this->assertSelected($form, $name, (string) $value);
        }
        $this->assertSame(1, $xpath->query('.//input[@name="foto" and @type="file" and not(@required)]', $form)->length);
        // The existing edit view places its previous-photo value before the form.
        $this->assertSame(1, $xpath->query('//input[@name="gambarLama" and @value="foto/pegawai.jpg"]')->length);
        $this->assertSame(1, $xpath->query('.//button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Simpan Perubahan" and @data-message and normalize-space(.)="Simpan"]', $form)->length);
    }

    public function test_edit_form_prefers_flashed_old_values_and_account(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $account = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $other = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $account->id, 'unit_kerja_id' => $unit->id]);
        $old = ['user_id' => $other->id, 'nama' => 'Nama dari input lama', 'nip' => '123456789', 'nilai_tpp' => '9000'];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_data_pegawai/' . $pegawai->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/ubah_data_pegawai/' . $pegawai->id);
        $this->assertSelected($form, 'user_id', (string) $other->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['nama', 'nip', 'nilai_tpp'] as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
        $this->assertSame((string) $unit->id, $xpath->query('.//select[@name="unit_kerja_id"]/option[@selected]', $form)->item(0)->getAttribute('value'));
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
        foreach (array_merge(array_fill_keys(self::INPUTS, 'input'), array_fill_keys(self::SELECTS, 'select')) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name}");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
