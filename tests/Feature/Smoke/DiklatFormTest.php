<?php

namespace Tests\Feature\Smoke;

use App\Models\Diklat;
use App\Models\Pegawai;
use App\Models\RencanaDiklat;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/kepegawaian/diklat';

    private const INPUTS = [
        'nama_diklat', 'jumlah_jam', 'penyelenggara', 'tempat', 'angkatan',
        'tahun', 'no_sttpp', 'tgl_sttpp', 'file_sertifikat_diklat',
    ];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        [$admin, $pegawai] = $this->adminAndPegawai();
        $current = $this->plan($pegawai, 'Rencana Terhubung');
        $eligible = $this->plan($pegawai, 'Rencana Tersedia');
        $diklat = Diklat::factory()->create([
            'pegawai_id' => $pegawai->id,
            'rencana_diklat_id' => $current->id,
            'file_sertifikat_diklat' => '/storage/document/sertifikat.pdf',
        ]);
        $current->update(['status' => 'realized']);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_diklat')->assertOk();
        $createForm = $this->formFor($create->getContent(), self::BASE . '/tambah_diklat');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm);
        $createXpath = new DOMXPath($createForm->ownerDocument);
        $this->assertSame(1, $createXpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//select[@name="rencana_diklat_id"]/option[@value="' . $eligible->id . '" and @data-pegawai-id="' . $pegawai->id . '" and @data-tahun-rencana="2026"]', $createForm)->length);
        $this->assertSame(0, $createXpath->query('.//input[@name="_method" or @name="fileLama"]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//input[@name="file_sertifikat_diklat" and @required]', $createForm)->length);
        $this->assertSame(1, $createXpath->query('.//button[@type="submit" and not(contains(@class,"confirm-save")) and normalize-space(.)="Simpan"]', $createForm)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_form_edit_diklat/' . $diklat->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_diklat/' . $diklat->id);
        $this->assertSame('POST', $form->getAttribute('method'));
        $this->assertFields($form);
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $form)->length);
        $this->assertSame(1, $xpath->query('.//input[@name="fileLama" and @value="/storage/document/sertifikat.pdf"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//input[@name="file_sertifikat_diklat" and @required]', $form)->length);
        $this->assertSelected($form, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($form, 'rencana_diklat_id', (string) $current->id);
        $this->assertSame(1, $xpath->query('.//select[@name="rencana_diklat_id"]/option[@data-current-link="true" and @value="' . $current->id . '"]', $form)->length);
        $this->assertSame(1, $xpath->query('.//select[@name="rencana_diklat_id"]/option[@value="' . $eligible->id . '"]', $form)->length);
        foreach (array_diff(self::INPUTS, ['file_sertifikat_diklat']) as $name) {
            $this->assertSame((string) $diklat->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'), "Expected {$name} model default");
        }
        $this->assertSame(1, $xpath->query('.//button[@type="submit" and contains(concat(" ",normalize-space(@class)," ")," confirm-save ") and @data-title="Konfirmasi perubahan" and @data-message="Apakah yakin dengan perubahan data diklat ini?" and normalize-space(.)="Simpan"]', $form)->length);
    }

    public function test_flashed_old_values_and_plan_selection_are_preserved(): void
    {
        [$admin, $pegawai] = $this->adminAndPegawai();
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $admin->unit_kerja_id]);
        $other = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $admin->unit_kerja_id]);
        $current = $this->plan($pegawai, 'Rencana Terhubung');
        $oldPlan = $this->plan($other, 'Rencana Pilihan');
        $diklat = Diklat::factory()->create(['pegawai_id' => $pegawai->id, 'rencana_diklat_id' => $current->id]);
        $current->update(['status' => 'realized']);

        $old = [
            'pegawai_id' => $other->id,
            'rencana_diklat_id' => $oldPlan->id,
            'tahun' => '2026',
            'nama_diklat' => 'Diklat Lama',
            'penyelenggara' => 'Penyelenggara Lama',
            'tgl_sttpp' => '2026-03-04',
            'jumlah_jam' => '99',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_edit_diklat/' . $diklat->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_diklat/' . $diklat->id);
        $this->assertSelected($form, 'pegawai_id', (string) $other->id);
        $this->assertSelected($form, 'rencana_diklat_id', (string) $oldPlan->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (['nama_diklat', 'penyelenggara', 'tahun', 'tgl_sttpp'] as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
        // Existing edit behavior uses the model, not old input, for jumlah_jam.
        $this->assertSame((string) $diklat->jumlah_jam, $xpath->query('.//input[@name="jumlah_jam"]', $form)->item(0)->getAttribute('value'));

        $add = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_form_tambah_diklat')->assertOk();
        $addForm = $this->formFor($add->getContent(), self::BASE . '/tambah_diklat');
        $this->assertSelected($addForm, 'rencana_diklat_id', (string) $oldPlan->id);
        $this->assertSame('', (new DOMXPath($addForm->ownerDocument))->query('.//input[@name="nama_diklat"]', $addForm)->item(0)->getAttribute('value'));
    }

    private function adminAndPegawai(): array
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);

        return [$admin, $pegawai];
    }

    private function plan(Pegawai $pegawai, string $name): RencanaDiklat
    {
        return RencanaDiklat::create([
            'pegawai_id' => $pegawai->id,
            'tahun_rencana' => '2026',
            'nama_diklat_rencana' => $name,
            'target_kompetensi' => 'Kompetensi',
            'kategori_diklat' => 'Struktural',
            'prioritas' => 'Tinggi',
            'target_jam' => 40,
            'target_penyelenggara' => 'BPSDM',
            'alasan_kebutuhan' => 'Pengembangan',
            'status' => 'planned',
        ]);
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
        foreach (array_merge(['pegawai_id' => 'select', 'rencana_diklat_id' => 'select'], array_fill_keys(self::INPUTS, 'input')) as $name => $tag) {
            $this->assertCount(1, $xpath->query('.//' . $tag . '[@name="' . $name . '" and @id="' . $name . '"]', $form), "Expected {$name} field");
            $labelFor = $name === 'tgl_sttpp' ? 'no_sttpp' : $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $labelFor . '"]', $form));
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
