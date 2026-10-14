<?php

namespace Tests\Feature\Smoke;

use App\Models\Pegawai;
use App\Models\Tpp;
use App\Models\UnitKerja;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TppFormTest extends TestCase
{
    use RefreshDatabase;

    private const MONTHS = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    private const NUMBERS = [
        'jml_hari_kerja', 'tidak_ada_produktifitas',
        'terlambat_1_30', 'terlambat_31_60', 'terlambat_61_90', 'terlambat_91_lebih',
        'pulang_awal_1_30', 'pulang_awal_31_60', 'pulang_awal_61_90', 'pulang_awal_91_lebih',
        'tidak_masuk_kerja',
    ];

    public function test_admin_create_and_edit_forms_render_actions_fields_options_values_and_buttons(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create([
            'user_id' => $admin->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai TPP',
        ]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $otherPegawai = Pegawai::factory()->create([
            'user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id, 'nama' => 'Pegawai Kedua',
        ]);
        $outsideUnit = UnitKerja::factory()->create();
        $outsideUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $outsideUnit->id]);
        $outsidePegawai = Pegawai::factory()->create([
            'user_id' => $outsideUser->id, 'unit_kerja_id' => $outsideUnit->id,
        ]);

        $values = array_combine(self::NUMBERS, range(11, 21));
        $tpp = Tpp::factory()->create(array_merge($values, [
            'pegawai_id' => $pegawai->id, 'periode' => 'Mei', 'tahun' => '2022',
        ]));

        $create = $this->actingAs($admin)->get('/tpp/tambah_tpp')->assertOk();
        $createForm = $this->formFor($create->getContent(), '/tpp/simpan_tpp');
        $this->assertSame('POST', $createForm->getAttribute('method'));
        $this->assertFields($createForm, array_fill_keys(self::NUMBERS, '0'));
        $this->assertOptions($createForm, $pegawai, $otherPegawai, $outsidePegawai);
        $this->assertSelected($createForm, 'tahun', date('Y'));
        $this->assertSame(0, $this->xpath($createForm)->query('.//select[@name="pegawai_id"]/option[@selected]', $createForm)->length);
        $this->assertSame(0, $this->xpath($createForm)->query('.//select[@name="periode"]/option[@selected]', $createForm)->length);
        $this->assertSame(0, $this->xpath($createForm)->query('.//input[@name="_method"]', $createForm)->length);
        $this->assertSame(1, $this->xpath($createForm)->query('.//button[@type="submit" and normalize-space(.)="Simpan"]', $createForm)->length);
        $this->assertCancel($createForm);

        $edit = $this->actingAs($admin)->get('/tpp/edit_tpp/' . $tpp->id)->assertOk();
        $editForm = $this->formFor($edit->getContent(), '/tpp/update_tpp/' . $tpp->id);
        $this->assertSame('POST', $editForm->getAttribute('method'));
        $this->assertSame(1, $this->xpath($editForm)->query('.//input[@name="_method" and @value="PUT"]', $editForm)->length);
        $this->assertFields($editForm, $values);
        $this->assertOptions($editForm, $pegawai, $otherPegawai, $outsidePegawai);
        $this->assertSelected($editForm, 'pegawai_id', (string) $pegawai->id);
        $this->assertSelected($editForm, 'periode', 'Mei');
        $this->assertSelected($editForm, 'tahun', '2022');
        $this->assertSame(1, $this->xpath($editForm)->query(
            './/button[@type="button" and contains(concat(" ", normalize-space(@class), " "), " confirm-save ") and normalize-space(.)="Update"]',
            $editForm
        )->length);
        $this->assertSame(0, $this->xpath($editForm)->query('.//button[@type="submit"]', $editForm)->length);
        $this->assertCancel($editForm);
    }

    public function test_admin_edit_form_prefers_flashed_old_selections_and_numbers(): void
    {
        $unit = UnitKerja::factory()->create();
        $admin = User::factory()->create(['role' => 'admin', 'unit_kerja_id' => $unit->id]);
        $pegawai = Pegawai::factory()->create(['user_id' => $admin->id, 'unit_kerja_id' => $unit->id]);
        $otherUser = User::factory()->create(['role' => 'pegawai', 'unit_kerja_id' => $unit->id]);
        $other = Pegawai::factory()->create(['user_id' => $otherUser->id, 'unit_kerja_id' => $unit->id]);
        $tpp = Tpp::factory()->create([
            'pegawai_id' => $pegawai->id, 'periode' => 'Januari', 'tahun' => '2022',
            'jml_hari_kerja' => 18, 'terlambat_61_90' => 3, 'pulang_awal_91_lebih' => 4,
        ]);

        $old = [
            'pegawai_id' => $other->id, 'periode' => 'Oktober', 'tahun' => '2023',
            'jml_hari_kerja' => '23', 'terlambat_61_90' => '7', 'pulang_awal_91_lebih' => '9',
        ];
        $edit = $this->actingAs($admin)->withSession(['_old_input' => $old])
            ->get('/tpp/edit_tpp/' . $tpp->id)->assertOk();
        $form = $this->formFor($edit->getContent(), '/tpp/update_tpp/' . $tpp->id);
        $this->assertSelected($form, 'pegawai_id', (string) $other->id);
        $this->assertSelected($form, 'periode', 'Oktober');
        $this->assertSelected($form, 'tahun', '2023');
        foreach (['jml_hari_kerja', 'terlambat_61_90', 'pulang_awal_91_lebih'] as $name) {
            $this->assertSame($old[$name], $this->xpath($form)->query('.//input[@name="' . $name . '"]', $form)->item(0)->getAttribute('value'));
        }
    }

    private function formFor(string $html, string $action): DOMElement
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        $forms = (new DOMXPath($document))->query('//form[@action="' . url($action) . '"]');
        $this->assertCount(1, $forms, "Expected form action {$action}");

        return $forms->item(0);
    }

    private function xpath(DOMElement $form): DOMXPath
    {
        return new DOMXPath($form->ownerDocument);
    }

    private function assertFields(DOMElement $form, array $values): void
    {
        $xpath = $this->xpath($form);
        foreach (['pegawai_id', 'periode', 'tahun'] as $name) {
            $fields = $xpath->query('.//select[@name="' . $name . '" and @id="' . $name . '"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} select");
        }
        foreach (self::NUMBERS as $name) {
            $fields = $xpath->query('.//input[@name="' . $name . '" and @id="' . $name . '" and @type="number" and @min="0"]', $form);
            $this->assertCount(1, $fields, "Expected {$name} number field");
            $this->assertSame((string) $values[$name], $fields->item(0)->getAttribute('value'), "Expected {$name} default");
            $this->assertCount(1, $xpath->query('.//label[@for="' . $name . '"]', $form));
        }
        $this->assertCount(1, $xpath->query('.//label[@for="pegawai_id"]', $form));
        $names = [];
        foreach ($xpath->query('.//input[@name] | .//select[@name] | .//textarea[@name]', $form) as $field) {
            if (!in_array($field->getAttribute('name'), ['_token', '_method'], true)) {
                $names[] = $field->getAttribute('name');
            }
        }
        $this->assertEqualsCanonicalizing(array_merge(['pegawai_id', 'periode', 'tahun'], self::NUMBERS), $names);
        $this->assertSame(1, $xpath->query('.//input[@name="_token" and @type="hidden"]', $form)->length);
    }

    private function assertOptions(DOMElement $form, Pegawai $pegawai, Pegawai $other, Pegawai $outside): void
    {
        $xpath = $this->xpath($form);
        $options = $xpath->query('.//select[@name="pegawai_id"]/option', $form);
        $this->assertCount(3, $options);
        $this->assertSame(['', (string) $other->id, (string) $pegawai->id], array_map(
            static fn (DOMElement $option) => $option->getAttribute('value'), iterator_to_array($options)
        ));
        $this->assertSame('Pilih Pegawai', trim($options->item(0)->textContent));
        $this->assertSame($other->nama, trim($options->item(1)->textContent));
        $this->assertSame($pegawai->nama, trim($options->item(2)->textContent));
        $this->assertSame(0, $xpath->query('.//select[@name="pegawai_id"]/option[@value="' . $outside->id . '"]', $form)->length);
        $this->assertSame(self::MONTHS, $this->optionValues($form, 'periode'));
        $this->assertSame(array_map('strval', range((int) date('Y'), 2020)), $this->optionValues($form, 'tahun'));
    }

    private function optionValues(DOMElement $form, string $name): array
    {
        return array_map(static fn (DOMElement $option) => $option->getAttribute('value'),
            iterator_to_array($this->xpath($form)->query('.//select[@name="' . $name . '"]/option', $form)));
    }

    private function assertSelected(DOMElement $form, string $name, string $value): void
    {
        $this->assertSame(1, $this->xpath($form)->query(
            './/select[@name="' . $name . '"]/option[@value="' . $value . '" and @selected]', $form
        )->length, "Expected {$name}={$value} selected");
        $this->assertSame(1, $this->xpath($form)->query('.//select[@name="' . $name . '"]/option[@selected]', $form)->length);
    }

    private function assertCancel(DOMElement $form): void
    {
        $this->assertSame(1, $this->xpath($form)->query(
            './/a[@href="' . url('/tpp/input_tpp') . '" and normalize-space(.)="Batal"]', $form
        )->length);
    }
}
