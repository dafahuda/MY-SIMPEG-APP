<?php

namespace Tests\Feature\Smoke;

use App\Models\Hukuman;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HukumanFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/hukuman';

    private const INPUTS = [
        'pejabat_pengesahan_sk_hukuman', 'no_sk', 'tgl_pengesahan_sk',
        'tmt_hukuman_mulai', 'tmt_hukuman_pemulihan', 'pejabat_pemulihan_hukuman',
        'no_pemulihan_hukuman', 'tgl_pemulihan_hukuman',
    ];

    private const TEXTAREAS = ['pelanggaran_yg_dilakukan', 'isi_teguran'];

    private const SELECTS = ['pegawai_id', 'tingkat_hukuman', 'jenis_hukuman'];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai Hukuman',
        ]);
        $hukuman = Hukuman::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tingkat_hukuman' => 'Berat',
            'jenis_hukuman' => 'Pemberhentian',
            'tmt_hukuman_mulai' => '2026-02-01',
            'tmt_hukuman_pemulihan' => '2026-03-01',
            'file_sk_hukuman' => '/storage/document/sk.pdf',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_hukuman')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_data_hukuman');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertSame('multipart/form-data', $createForm->getAttribute('enctype'));
        $this->assertFields($createForm);
        $xpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $xpath->query('.//input[@name="_method" or @name="fileLama"]', $createForm)->length);
        $this->assertSame(1, $xpath->query('.//input[@name="file_sk_hukuman" and @required]', $createForm)->length);
        $this->assertSame(1, $xpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $xpath->query('.//button[contains(@class,"confirm-save")]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_hukuman/' . $hukuman->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_hukuman/' . $hukuman->id);
        $this->assertSame('POST', $form->getAttribute('method'));
        $this->assertSame('multipart/form-data', $form->getAttribute('enctype'));
        $this->assertFields($form);
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $form)->length);
        $this->assertSame(1, $xpath->query('.//input[@name="fileLama" and @value="/storage/document/sk.pdf"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//input[@name="file_sk_hukuman" and @required]', $form)->length);
        foreach (['pegawai_id' => $pegawai->id, 'tingkat_hukuman' => 'Berat', 'jenis_hukuman' => 'Pemberhentian'] as $name => $value) {
            $this->assertSelected($form, $name, (string) $value);
        }
        foreach (self::INPUTS as $name) {
            $this->assertSame($hukuman->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'), $name);
        }
        foreach (self::TEXTAREAS as $name) {
            $this->assertSame($hukuman->{$name}, $xpath->query('.//textarea[@name="' . $name . '"]', $form)->item(0)->textContent, $name);
        }
        $this->assertSame(1, $xpath->query('.//button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Simpan Perubahan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data hukuman ini?"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//button[@type="submit"]', $form)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $hukuman = Hukuman::factory()->create(['pegawai_id' => $pegawai->id]);
        $old = [
            'pegawai_id' => $otherPegawai->id,
            'no_sk' => 'OLD/002/2026',
            'tgl_pengesahan_sk' => '2026-03-04',
            'isi_teguran' => 'Teguran lama',
            'pelanggaran_yg_dilakukan' => 'Pelanggaran lama',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_hukuman/' . $hukuman->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_hukuman/' . $hukuman->id);
        $this->assertSelected($form, 'pegawai_id', (string) $otherPegawai->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['no_sk', 'tgl_pengesahan_sk'] as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
        foreach (self::TEXTAREAS as $name) {
            $this->assertSame($old[$name], $xpath->query('.//textarea[@name="' . $name . '"]', $form)->item(0)->textContent);
        }
        // The original form selects these options from the model rather than old input.
        $this->assertSelected($form, 'tingkat_hukuman', $hukuman->tingkat_hukuman);
        $this->assertSelected($form, 'jenis_hukuman', $hukuman->jenis_hukuman);
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
        foreach (array_merge(array_fill_keys(self::SELECTS, 'select'), array_fill_keys(self::TEXTAREAS, 'textarea'), array_fill_keys(self::INPUTS, 'input'), ['file_sk_hukuman' => 'input']) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $labelFor = [
                'tgl_pengesahan_sk' => 'no_sk', 'tmt_hukuman_pemulihan' => 'tmt_hukuman_mulai',
                'tgl_pemulihan_hukuman' => 'no_sk', 'no_pemulihan_hukuman' => 'no_sk',
            ][$name] ?? $name;
            $this->assertNotEmpty($xpath->query('.//label[@for="' . $labelFor . '"]', $form)->length, "Expected {$name} label");
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
