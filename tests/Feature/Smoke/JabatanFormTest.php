<?php

namespace Tests\Feature\Smoke;

use App\Models\Jabatan;
use App\Models\MasterEselon;
use App\Models\MasterJabatan;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JabatanFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_add_and_edit_forms_render_shared_fields_and_distinct_controls(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai Terpilih',
        ]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $other = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai Lain']);
        $master = MasterJabatan::factory()->create(['nama_jabatan' => 'Kepala Bidang']);
        $eselon = MasterEselon::factory()->create(['nama_eselon' => 'Eselon III']);
        $jabatan = Jabatan::factory()->create([
            'pegawai_id' => $pegawai->id,
            'master_jabatan_id' => $master->id,
            'master_eselon_id' => $eselon->id,
            'jenis_jabatan' => 'Jabatan Fungsional Tertentu',
            'periode' => 'II',
            'tmt_jabatan_mulai' => '2025-02-03',
            'tmt_jabatan_selesai' => '2026-02-03',
            'no_sk' => 'SK-JAB-123',
            'tgl_sk' => '2025-01-20',
            'terbit' => 'Bupati Pengujian',
        ]);

        $create = $this->actingAs($admin)->get('/kepegawaian/jabatan/view_tambah_jabatan')->assertOk();
        $createForm = $this->formFor($create->getContent(), '/kepegawaian/jabatan/tambah_data_jabatan');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertCount(0, $this->find($createForm, './/input[@name="_method"]'));
        $this->assertSharedFields($createForm);
        $this->assertOption($createForm, 'pegawai_id', (string) $pegawai->id, 'Pegawai Terpilih', false);
        $this->assertOption($createForm, 'pegawai_id', (string) $other->id, 'Pegawai Lain', false);
        $this->assertOption($createForm, 'master_jabatan_id', (string) $master->id, 'Kepala Bidang', false);
        $this->assertOption($createForm, 'master_eselon_id', (string) $eselon->id, 'Eselon III', false);
        $this->assertOption($createForm, 'jenis_jabatan', 'Jabatan Struktural', 'Jabatan Struktural', false);
        $this->assertSame('', $this->input($createForm, 'no_sk')->getAttribute('value'));
        $this->assertCount(1, $this->find($createForm, './/button[@type="submit" and normalize-space(.)="Simpan"]'));
        $this->assertCount(0, $this->find($createForm, './/button[contains(concat(" ", normalize-space(@class), " "), " confirm-save ")]'));

        $edit = $this->actingAs($admin)->get('/kepegawaian/jabatan/view_form_edit_jabatan/' . $jabatan->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), '/kepegawaian/jabatan/edit_data_jabatan/' . $jabatan->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertCount(1, $this->find($editForm, './/input[@name="_method" and @value="PUT"]'));
        $this->assertSharedFields($editForm);
        $this->assertOption($editForm, 'pegawai_id', (string) $pegawai->id, 'Pegawai Terpilih', true);
        $this->assertOption($editForm, 'pegawai_id', (string) $other->id, 'Pegawai Lain', false);
        $this->assertOption($editForm, 'master_jabatan_id', (string) $master->id, 'Kepala Bidang', true);
        $this->assertOption($editForm, 'jenis_jabatan', 'Jabatan Fungsional Tertentu', 'Jabatan Fungsional Tertentu', true);
        $this->assertOption($editForm, 'periode', 'II', 'II', true);
        foreach (['tmt_jabatan_mulai' => '2025-02-03', 'tmt_jabatan_selesai' => '2026-02-03',
            'no_sk' => 'SK-JAB-123', 'tgl_sk' => '2025-01-20', 'terbit' => 'Bupati Pengujian'] as $name => $value) {
            $this->assertSame($value, $this->input($editForm, $name)->getAttribute('value'), $name);
        }
        $this->assertCount(1, $this->find($editForm, './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and normalize-space(.)="Simpan"]'));
        $this->assertCount(0, $this->find($editForm, './/button[@type="submit" and normalize-space(.)="Simpan"]'));
    }

    public function test_edit_form_prefers_flashed_old_input_for_shared_field_and_pegawai(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $other = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $jabatan = Jabatan::factory()->create(['pegawai_id' => $pegawai->id, 'no_sk' => 'Model SK']);

        $response = $this->actingAs($admin)->withSession(['_old_input' => [
            'pegawai_id' => $other->id, 'no_sk' => 'Old SK', 'terbit' => 'Old Issuer',
        ]])->get('/kepegawaian/jabatan/view_form_edit_jabatan/' . $jabatan->id)->assertOk();
        $form = $this->formFor($response->getContent(), '/kepegawaian/jabatan/edit_data_jabatan/' . $jabatan->id);
        $this->assertOption($form, 'pegawai_id', (string) $other->id, trim($other->nama), true);
        $this->assertOption($form, 'pegawai_id', (string) $pegawai->id, trim($pegawai->nama), false);
        $this->assertSame('Old SK', $this->input($form, 'no_sk')->getAttribute('value'));
        $this->assertSame('Old Issuer', $this->input($form, 'terbit')->getAttribute('value'));
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

    private function find(DOMElement $form, string $expression): \DOMNodeList
    {
        return (new DOMXPath($form->ownerDocument))->query($expression, $form);
    }

    private function input(DOMElement $form, string $name): DOMElement
    {
        $inputs = $this->find($form, './/input[@name="' . $name . '"]');
        $this->assertCount(1, $inputs, "Expected input {$name}");

        return $inputs->item(0);
    }

    private function assertSharedFields(DOMElement $form): void
    {
        foreach (['jenis_jabatan', 'periode', 'tahun_ke'] as $name) {
            $this->assertCount(1, $this->find($form, './/select[@name="' . $name . '" and @id="' . $name . '"]'), $name);
        }
        foreach (['tmt_jabatan_mulai', 'tmt_jabatan_selesai', 'no_sk', 'tgl_sk', 'terbit'] as $name) {
            $this->assertSame($name, $this->input($form, $name)->getAttribute('id'));
        }
    }

    private function assertOption(DOMElement $form, string $select, string $value, string $label, bool $selected): void
    {
        $options = $this->find($form, './/select[@name="' . $select . '"]/option[@value="' . $value . '"]');
        $this->assertCount(1, $options, "Expected {$select} option {$value}");
        $this->assertSame($label, trim($options->item(0)->textContent));
        $this->assertSame($selected, $options->item(0)->hasAttribute('selected'), "Selection for {$select}: {$value}");
    }
}
