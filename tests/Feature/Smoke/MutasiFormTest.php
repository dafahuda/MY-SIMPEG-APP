<?php

namespace Tests\Feature\Smoke;

use App\Models\Mutasi;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MutasiFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/mutasi';

    private const INPUTS = ['instansi_tujuan', 'no_sk_mutasi', 'tgl_sk_mutasi', 'file_sk_mutasi'];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai Mutasi',
        ]);
        $mutasi = Mutasi::factory()->create([
            'pegawai_id' => $pegawai->id, 'jenis_mutasi' => 'Pensiun',
            'instansi_tujuan' => 'Instansi Baru', 'no_sk_mutasi' => 'SK/123',
            'tgl_sk_mutasi' => '2026-02-01', 'file_sk_mutasi' => '/storage/document/sk.pdf',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_mutasi')->assertOk();
        $form = $this->formFor($create->getContent(), self::BASE . '/tambah_mutasi');
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame('POST', $form->getAttribute('method'));
        $this->assertSame('multipart/form-data', $form->getAttribute('enctype'));
        $this->assertFields($form);
        $this->assertSame(1, $xpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//input[@name="_method"]', $form)->length);
        $this->assertSame(1, $xpath->query('.//input[@name="file_sk_mutasi" and @required]', $form)->length);
        $this->assertSame(1, $xpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//button[contains(@class,"confirm-save")]', $form)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_mutasi/' . $mutasi->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_mutasi/' . $mutasi->id);
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame('POST', $form->getAttribute('method'));
        $this->assertSame('multipart/form-data', $form->getAttribute('enctype'));
        $this->assertFields($form);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $form)->length);
        $this->assertSame(1, $xpath->query('.//input[@name="fileLama" and @type="hidden" and @value="/storage/document/sk.pdf"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//input[@name="file_sk_mutasi" and @required]', $form)->length);
        $this->assertSelected($form, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($form, 'jenis_mutasi', 'Pensiun');
        foreach (['instansi_tujuan', 'no_sk_mutasi', 'tgl_sk_mutasi'] as $name) {
            $this->assertSame($mutasi->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
        $this->assertSame(1, $xpath->query('.//button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Konfirmasi Simpan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data mutasi ini?" and normalize-space(.)="Simpan"]', $form)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $mutasi = Mutasi::factory()->create(['pegawai_id' => $pegawai->id, 'jenis_mutasi' => 'Keluar']);
        $old = [
            'pegawai_id' => $otherPegawai->id, 'instansi_tujuan' => 'Instansi Lama',
            'no_sk_mutasi' => 'OLD/002/2026', 'tgl_sk_mutasi' => '2026-03-04',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_mutasi/' . $mutasi->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_mutasi/' . $mutasi->id);
        $this->assertSelected($form, 'pegawai_id', (string) $otherPegawai->id);
        $this->assertSelected($form, 'jenis_mutasi', 'Keluar');
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['instansi_tujuan', 'no_sk_mutasi', 'tgl_sk_mutasi'] as $name) {
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
        foreach (array_merge(['pegawai_id' => 'select', 'jenis_mutasi' => 'select'], array_fill_keys(self::INPUTS, 'input')) as $name => $tag) {
            $this->assertCount(1, $xpath->query('.//' . $tag . '[@name="' . $name . '" and @id="' . $name . '"]', $form), "Expected {$name} field");
            $labelFor = $name === 'tgl_sk_mutasi' ? 'no_sk_mutasi' : $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
        foreach (['Masuk', 'Keluar', 'Pindah Antar Instansi', 'Pensiun', 'Wafat', 'Kenaikan Pangkat'] as $value) {
            $this->assertSame(1, $xpath->query('.//select[@name="jenis_mutasi"]/option[@value="' . $value . '"]', $form)->length);
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
