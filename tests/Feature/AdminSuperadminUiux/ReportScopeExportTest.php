<?php

namespace Tests\Feature\AdminSuperadminUiux;

use App\Exports\DiklatGapExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\Feature\Diklat\DiklatGapReportFixtures;
use Tests\TestCase;

class ReportScopeExportTest extends TestCase
{
    use RefreshDatabase;
    use DiklatGapReportFixtures;

    public function test_admin_cannot_forge_global_or_other_unit_diklat_report_params(): void
    {
        $data = $this->seedGapReportFixtures();

        $forgedUnitPage = $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        $forgedUnitPage->assertOk()
            ->assertSeeHtml('data-testid="diklat-gap-unit-select" data-selected="' . $data['unitA']->id . '"')
            ->assertSee('Unit A Gap')
            ->assertSee('Rencana A Planned')
            ->assertDontSee('Rencana B Realized');

        $forgedEmployeePage = $this->actingAs($data['admin'])->get(route('report.diklat_gap', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
            'pegawai_id' => $data['pegawaiB']->id,
        ]));

        $forgedEmployeePage->assertOk()
            ->assertSeeHtml('data-testid="diklat-gap-unit-select" data-selected="' . $data['unitA']->id . '"')
            ->assertSee('Pegawai Gap A')
            ->assertDontSee('Pegawai Gap B')
            ->assertSee('Rencana A Planned')
            ->assertDontSee('Rencana B Realized');
    }

    public function test_superadmin_can_filter_diklat_report_globally_and_by_intended_unit(): void
    {
        $data = $this->seedGapReportFixtures();

        $globalPage = $this->actingAs($data['superadmin'])->get(route('report.diklat_gap.unit', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
        ]));

        $globalPage->assertOk()
            ->assertSee('Unit A Gap')
            ->assertSee('Unit B Gap')
            ->assertSee('Rencana A Planned')
            ->assertSee('Rencana B Realized')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned" data-value="3"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-out_of_plan" data-value="2"');

        $unitBPage = $this->actingAs($data['superadmin'])->get(route('report.diklat_gap.unit', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        $unitBPage->assertOk()
            ->assertSeeHtml('data-testid="diklat-gap-unit-select" data-selected="' . $data['unitB']->id . '"')
            ->assertDontSee('Rencana A Planned')
            ->assertSee('Rencana B Realized')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-out_of_plan" data-value="1"');
    }

    public function test_diklat_print_scope_matches_unit_report_page_scope(): void
    {
        $data = $this->seedGapReportFixtures();

        $query = [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ];

        $page = $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit', $query));
        $print = $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit.print', $query));

        foreach ([$page, $print] as $response) {
            $response->assertOk()
                ->assertSee('Unit A Gap')
                ->assertDontSee('Unit B Gap')
                ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned" data-value="2"')
                ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-out_of_plan" data-value="1"')
                ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-hour_gap" data-value="42"');
        }
    }

    public function test_diklat_export_scope_matches_page_scope_for_admin_and_superadmin(): void
    {
        $data = $this->seedGapReportFixtures();

        Excel::fake();

        $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit.export', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        Excel::assertDownloaded('DiklatGapUnitReport_2026_2027.xlsx', function (DiklatGapExport $export): bool {
            $rows = $export->collection();

            $this->assertCount(3, $rows);
            $this->assertSame(['Unit A Gap'], $rows->pluck('Unit')->unique()->values()->all());
            $this->assertContains('Rencana A Planned', $rows->pluck('Nama Rencana')->all());
            $this->assertNotContains('Rencana B Realized', $rows->pluck('Nama Rencana')->all());

            return true;
        });

        Excel::fake();

        $this->actingAs($data['superadmin'])->get(route('report.diklat_gap.unit.export', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
        ]));

        Excel::assertDownloaded('DiklatGapUnitReport_2026_2027.xlsx', function (DiklatGapExport $export): bool {
            $rows = $export->collection();

            $this->assertCount(5, $rows);
            $this->assertEqualsCanonicalizing(['Unit A Gap', 'Unit B Gap'], $rows->pluck('Unit')->unique()->values()->all());
            $this->assertContains('Rencana A Planned', $rows->pluck('Nama Rencana')->all());
            $this->assertContains('Rencana B Realized', $rows->pluck('Nama Rencana')->all());

            return true;
        });
    }

    public function test_invalid_diklat_filters_fail_safely_without_leaking_other_unit_scope(): void
    {
        $data = $this->seedGapReportFixtures();

        $response = $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit', [
            'tahun_rencana' => 'not-a-year',
            'tahun_realisasi' => 'also-bad',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        $response->assertOk()
            ->assertSeeHtml('data-testid="diklat-gap-unit-select" data-selected="' . $data['unitA']->id . '"')
            ->assertSee('Pilih tahun rencana untuk menampilkan laporan kesenjangan Diklat per unit.')
            ->assertDontSeeHtml('data-testid="diklat-gap-unit-summary-value-planned"')
            ->assertDontSee('Rencana A Planned')
            ->assertDontSee('Rencana B Realized')
            ->assertDontSee('Diklat B Out of Plan');
    }

    public function test_legacy_report_admin_scope_is_enforced_for_page_and_print(): void
    {
        $data = $this->seedGapReportFixtures();

        $page = $this->actingAs($data['admin'])->get(route('report.nominatif', [
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        $print = $this->actingAs($data['admin'])->get(route('report.nominatif.print', [
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        foreach ([$page, $print] as $response) {
            $response->assertOk()
                ->assertSee('Pegawai Gap A')
                ->assertSee('Unit A Gap')
                ->assertDontSee('Pegawai Gap B')
                ->assertDontSee('Unit B Gap');
        }
    }
}
