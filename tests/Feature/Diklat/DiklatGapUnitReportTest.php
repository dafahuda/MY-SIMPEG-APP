<?php

namespace Tests\Feature\Diklat;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatGapUnitReportTest extends TestCase
{
    use RefreshDatabase;
    use DiklatGapReportFixtures;

    public function test_admin_is_pinned_to_own_unit_even_when_query_is_forged(): void
    {
        $data = $this->seedGapReportFixtures();

        $response = $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        $response->assertOk()
            ->assertSeeHtml('data-testid="diklat-gap-unit-select" data-selected="' . $data['unitA']->id . '"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned" data-value="2"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-not_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-out_of_plan" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-cross_year_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned_hours" data-value="70"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-realized_linked_hours" data-value="28"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-hour_gap" data-value="42"')
            ->assertSee('Rencana A Planned')
            ->assertSee('Diklat A Out of Plan')
            ->assertDontSee('Rencana B Realized')
            ->assertDontSee('Diklat B Out of Plan');
    }

    public function test_superadmin_can_filter_a_unit_or_view_all_units(): void
    {
        $data = $this->seedGapReportFixtures();

        $filtered = $this->actingAs($data['superadmin'])->get(route('report.diklat_gap.unit', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        $filtered->assertOk()
            ->assertSeeHtml('data-testid="diklat-gap-unit-select" data-selected="' . $data['unitB']->id . '"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-not_realized" data-value="0"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-out_of_plan" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-cross_year_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned_hours" data-value="50"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-realized_linked_hours" data-value="40"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-hour_gap" data-value="10"')
            ->assertDontSee('Rencana A Planned')
            ->assertSee('Rencana B Realized');

        $allUnits = $this->actingAs($data['superadmin'])->get(route('report.diklat_gap.unit', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
        ]));

        $allUnits->assertOk()
            ->assertSee('Rencana A Planned')
            ->assertSee('Rencana B Realized')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned" data-value="3"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-realized" data-value="2"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-not_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-out_of_plan" data-value="2"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-cross_year_realized" data-value="2"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned_hours" data-value="120"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-realized_linked_hours" data-value="68"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-hour_gap" data-value="52"');
    }
}
