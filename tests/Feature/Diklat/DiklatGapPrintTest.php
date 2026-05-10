<?php

namespace Tests\Feature\Diklat;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiklatGapPrintTest extends TestCase
{
    use RefreshDatabase;
    use DiklatGapReportFixtures;

    public function test_unit_print_page_is_locked_to_own_scope(): void
    {
        $data = $this->seedGapReportFixtures();

        $response = $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit.print', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        $response->assertOk()
            ->assertSee('Diklat Gap Unit/Tahun')
            ->assertSee('Unit A Gap')
            ->assertDontSee('Unit B Gap')
            ->assertSeeHtml('data-testid="diklat-gap-unit-print-row-0"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-planned" data-value="2"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-not_realized" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-out_of_plan" data-value="1"')
            ->assertSeeHtml('data-testid="diklat-gap-unit-summary-value-cross_year_realized" data-value="1"');
    }
}
