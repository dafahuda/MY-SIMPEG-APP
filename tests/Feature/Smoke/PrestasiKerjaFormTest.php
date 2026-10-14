<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\PrestasiKerja;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrestasiKerjaFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/skp_prestasi_kerja';

    private const INPUTS = [
        'periode_nilai_dari', 'periode_nilai_sampai', 'tahun_periode',
        'nama_pejabat_nilai', 'nama_atasan_pejabat_penilai', 'skp',
        'orientasi_pelayanan', 'integritas', 'komitmen', 'disiplin', 'kerjasama',
        'kepemimpinan', 'tgl_keberatan_pegawai', 'tgl_pejabat_penilai',
        'tgl_keputusan_atasan_pejabat_penilai', 'rekomendasi',
        'tgl_diterima_pegawai', 'tgl_diterima_atasan',
    ];

    private const TEXTAREAS = ['isi_keberatan', 'isi_tanggapan', 'isi_keputusan'];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $prestasi = PrestasiKerja::factory()->create([
            'pegawai_id' => $pegawai->id,
            'skp' => 83, 'orientasi_pelayanan' => 84, 'integritas' => 85,
            'komitmen' => 86, 'disiplin' => 87, 'kerjasama' => 88, 'kepemimpinan' => 89,
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_prestasi_kerja')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_prestasi_kerja');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//button[contains(@class, "confirm-save")]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_prestasi_kerja/' . $prestasi->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), self::BASE . '/edit_prestasi_kerja/' . $prestasi->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $xpath = new DOMXPath($editForm->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm);
        $this->assertSelected($editForm, (string) $pegawai->id);
        foreach (self::INPUTS as $name) {
            $this->assertSame((string) $prestasi->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $editForm)->item(0)->getAttribute('value'), "Expected {$name} model default");
        }
        foreach (self::TEXTAREAS as $name) {
            $this->assertSame($prestasi->{$name}, $xpath->query('.//textarea[@name="' . $name . '"]', $editForm)->item(0)->textContent);
        }
        $this->assertSame(1, $xpath->query('.//button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and @data-title="Konfirmasi Simpan" and @data-message="Apakah Anda yakin ingin menyimpan perubahan data prestasi kerja ini?" and normalize-space(.)="Simpan"]', $editForm)->length);
        $this->assertSame(0, $xpath->query('.//button[@type="submit"]', $editForm)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $otherPegawai = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $prestasi = PrestasiKerja::factory()->create(['pegawai_id' => $pegawai->id]);

        $old = [
            'pegawai_id' => $otherPegawai->id,
            'periode_nilai_dari' => '2026-03-04',
            'nama_pejabat_nilai' => 'Pejabat baru',
            'skp' => '94',
            'tgl_keberatan_pegawai' => '2026-04-05',
            'isi_keberatan' => 'Keberatan baru',
            'rekomendasi' => 'Rekomendasi baru',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_prestasi_kerja/' . $prestasi->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_prestasi_kerja/' . $prestasi->id);
        $this->assertSelected($form, (string) $otherPegawai->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['periode_nilai_dari', 'nama_pejabat_nilai', 'skp', 'tgl_keberatan_pegawai', 'rekomendasi'] as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
        $this->assertSame($old['isi_keberatan'], $xpath->query('.//textarea[@name="isi_keberatan"]', $form)->item(0)->textContent);
        $this->assertSame($prestasi->periode_nilai_sampai, $xpath->query('.//input[@name="periode_nilai_sampai"]', $form)->item(0)->getAttribute('value'));
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
        foreach (array_merge(['pegawai_id' => 'select'], array_fill_keys(self::INPUTS, 'input'), array_fill_keys(self::TEXTAREAS, 'textarea')) as $name => $tag) {
            $fields = $xpath->query('.//' . $tag . '[@name="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} field");
            $this->assertSame($name, $fields->item(0)->getAttribute('id'));
            $labelFor = [
                'periode_nilai_sampai' => 'periode_nilai_dari',
                'tahun_periode' => 'periode_nilai_dari',
                'isi_keberatan' => 'tgl_keberatan_pegawai',
                'isi_tanggapan' => 'tgl_pejabat_penilai',
                'isi_keputusan' => 'tgl_keputusan_atasan_pejabat_penilai',
            ][$name] ?? $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
    }

    private function assertSelected(DOMElement $form, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="pegawai_id"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
