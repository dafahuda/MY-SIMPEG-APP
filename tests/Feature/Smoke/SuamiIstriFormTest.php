<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\RiwayatKeluargaSuamiIstri;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuamiIstriFormTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = '/riwayat_keluarga/suami_istri';

    private const INPUTS = ['no_ktp_nik', 'nama', 'tempat_lahir', 'tgl_lahir', 'pekerjaan'];

    public function test_admin_create_and_edit_forms_render_fields_values_and_save_behavior(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai Pasangan']);
        $spouse = RiwayatKeluargaSuamiIstri::factory()->create([
            'pegawai_id' => $pegawai->id, 'pendidikan' => 'S2', 'status_hubungan' => 'Istri',
        ]);

        $create = $this->actingAs($admin)->get(self::BASE . '/view_form_tambah_suami_istri')->assertOk();
        $form = $this->formFor($create->getContent(), self::BASE . '/tambah_data_suami_istri');
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame('POST', $form->getAttribute('method'));
        $this->assertFields($form, 'select');
        $this->assertSame(1, $xpath->query('.//select[@name="pegawai_id"]/option[@value="' . $pegawai->id . '"]', $form)->length);
        $this->assertSame(1, $xpath->query('.//select[@name="pendidikan"]/option[@value="S2"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//input[@name="_method"]', $form)->length);
        $this->assertSame(1, $xpath->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//button[contains(@class,"confirm-save")]', $form)->length);

        $edit = $this->actingAs($admin)->get(self::BASE . '/view_edit_data_suami_istri/' . $spouse->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_suami_istri/' . $spouse->id);
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame('POST', $form->getAttribute('method'));
        $this->assertSame(1, $xpath->query('.//input[@name="_method" and @value="PUT"]', $form)->length);
        $this->assertFields($form, 'input');
        $this->assertSelected($form, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($form, 'status_hubungan', 'Istri');
        foreach (array_merge(self::INPUTS, ['pendidikan']) as $name) {
            $this->assertSame($spouse->{$name}, $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'), $name);
        }
        $this->assertSame(1, $xpath->query('.//button[@type="button" and contains(concat(" ",normalize-space(@class)," ")," confirm-save ") and @data-title="Simpan Perubahan" and @data-message and normalize-space(.)="Simpan"]', $form)->length);
        $this->assertSame(0, $xpath->query('.//button[@type="submit"]', $form)->length);
    }

    public function test_edit_form_prefers_flashed_old_fields_and_pegawai(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $other = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $spouse = RiwayatKeluargaSuamiIstri::factory()->create(['pegawai_id' => $pegawai->id]);
        $old = [
            'pegawai_id' => $other->id, 'no_ktp_nik' => '12345', 'nama' => 'Nama Lama',
            'tempat_lahir' => 'Bandung', 'tgl_lahir' => '2000-02-03',
            'pendidikan' => 'D3', 'pekerjaan' => 'Pekerjaan Lama',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get(self::BASE . '/view_edit_data_suami_istri/' . $spouse->id)->assertOk();
        $form = $this->formFor($edit->getContent(), self::BASE . '/edit_data_suami_istri/' . $spouse->id);
        $this->assertSelected($form, 'pegawai_id', (string) $other->id);
        $xpath = new DOMXPath($form->ownerDocument);
        foreach (array_merge(self::INPUTS, ['pendidikan']) as $name) {
            $this->assertSame($old[$name], $xpath->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'), $name);
        }
        // The existing relationship selector uses the model, not flashed old input.
        $this->assertSelected($form, 'status_hubungan', $spouse->status_hubungan);
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

    private function assertFields(DOMElement $form, string $educationTag): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $fields = ['pegawai_id' => 'select', 'pendidikan' => $educationTag, 'status_hubungan' => 'select'];
        foreach (self::INPUTS as $name) {
            $fields[$name] = 'input';
        }
        foreach ($fields as $name => $tag) {
            $this->assertCount(1, $xpath->query('.//' . $tag . '[@name="' . $name . '" and @id="' . $name . '"]', $form), $name);
            $label = $name === 'tgl_lahir' ? 'tempat_lahir' : $name;
            $this->assertCount(1, $xpath->query('.//label[@for="' . $label . '"]', $form), $name);
        }
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $xpath = new DOMXPath($form->ownerDocument);
        $this->assertSame(1, $xpath->query('.//select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form)->length);
    }
}
