<?php

namespace Tests\Feature\Smoke;

use App\Models\IzinKawin;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IzinKawinFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/izin_kawin';

    private const INPUTS = [
        'no_surat_izin_perkawinan',
        'tgl_izin_surat_perkawinan',
        'kebangsaan_pegawai',
        'nama_wali_bapak_pegawai',
        'pekerjaan_wali_bapak_pegawai',
        'alamat_wali_bapak',
        'nama_wali_ibu_pegawai',
        'pekerjaan_wali_ibu_pegawai',
        'alamat_wali_ibu_pegawai',
        'nama_calon_suami_istri',
        'tempat_lahir_calon_suami_istri',
        'tgl_lahir_calon_suami_istri',
        'pekerjaan_calon_suami_istri',
        'nip_nik_calon_suami_istri',
        'pangkat_golongan_calon_suami_istri',
        'jabatan_calon_suami_istri',
        'instansi_calon_suami_istri',
        'kebangsaan_calon_suami_istri',
        'agama_calon_suami_istri',
        'alamat_calon_suami_istri',
        'nama_wali_bapak_calon_suami_istri',
        'pekerjaan_wali_bapak_calon_suami_istri',
        'alamat_wali_bapak_calon_suami_istri',
        'nama_wali_ibu_calon_suami_istri',
        'pekerjaan_wali_ibu_calon_suami_istri',
        'alamat_wali_ibu_calon_suami_istri',
        'tempat_perkawinan',
        'tgl_perkawinan',
        'tgl_ditetapkan_perkawinan',
    ];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id,
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Pegawai Izin Kawin',
        ]);
        $izinKawin = IzinKawin::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tgl_perkawinan' => '2026-02-01',
        ]);

        $create = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_tambah_izin_kawin')
            ->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_izin_kawin');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//select[@name="pegawai_id"]/option[@selected]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(concat(" ", normalize-space(@class), " "), " confirm-save ")]', $createForm)->length);

        $edit = $this->actingAs($admin)
            ->get(self::BASE . '/view_form_edit_izin_kawin/' . $izinKawin->id)
            ->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_izin_kawin/' . $izinKawin->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $editXpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $editXpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, (string) $pegawai->id);
        foreach (self::INPUTS as $name) {
            $input = $editXpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0);
            $this->assertSame($izinKawin->{$name}, $input->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $editXpath->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Konfirmasi Simpan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data izin kawin ini?" and normalize-space(.)="Simpan"]',
            $editForm
        )->length);
        $this->assertSame(0, $editXpath->query('.//button[@type="submit"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unitKerja = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unitKerja->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unitKerja->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unitKerja->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unitKerja->id]);
        $izinKawin = IzinKawin::factory()->create([
            'pegawai_id' => $pegawai->id,
            'tgl_perkawinan' => '2026-02-01',
        ]);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'no_surat_izin_perkawinan' => 'OLD/002/2026',
            'tgl_izin_surat_perkawinan' => '2026-03-04',
            'pekerjaan_wali_ibu_pegawai' => 'Pekerjaan lama',
        ];
        $edit = $this->actingAs($admin)
            ->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_izin_kawin/' . $izinKawin->id)
            ->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_izin_kawin/' . $izinKawin->id);
        $this->assertSelected($form, (string) $otherPegawai->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['no_surat_izin_perkawinan', 'tgl_izin_surat_perkawinan', 'pekerjaan_wali_ibu_pegawai'] as $name) {
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
        foreach (array_merge(['pegawai_id' => 'select'], array_fill_keys(self::INPUTS, 'input')) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            // Paired date inputs share the label of their companion field.
            $labelFor = [
                'tgl_izin_surat_perkawinan' => 'no_surat_izin_perkawinan',
                'tgl_lahir_calon_suami_istri' => 'tempat_lahir_calon_suami_istri',
                'tgl_perkawinan' => 'tempat_perkawinan',
            ][$name] ?? $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
    }

    private function assertSelected(DOMElement $form, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query(
            './/select[@name="pegawai_id"]/option[@value="' . $value . '" and @selected]',
            $form
        )->length);
    }
}
