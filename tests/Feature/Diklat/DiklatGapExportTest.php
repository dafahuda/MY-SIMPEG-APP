<?php

namespace Tests\Feature\Diklat;

use App\Exports\DiklatGapExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class DiklatGapExportTest extends TestCase
{
    use RefreshDatabase;
    use DiklatGapReportFixtures;

    public function test_admin_export_is_restricted_to_own_unit_even_when_query_is_forged(): void
    {
        $data = $this->seedGapReportFixtures();

        Excel::fake();

        $this->actingAs($data['admin'])->get(route('report.diklat_gap.unit.export', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        Excel::assertDownloaded('DiklatGapUnitReport_2026_2027.xlsx', function (DiklatGapExport $export) use ($data) {
            $rows = $export->collection();

            $this->assertCount(3, $rows);
            $this->assertSame('Pegawai Gap A', $rows[0]['Pegawai']);
            $this->assertSame('Unit A Gap', $rows[0]['Unit']);
            $this->assertSame('Rencana aktif', $rows[0]['Bucket Status']);
            $this->assertSame('Pegawai Gap A', $rows[1]['Pegawai']);
            $this->assertSame('Realisasi lintas tahun', $rows[1]['Bucket Status']);
            $this->assertSame('Diklat A Out of Plan', $rows[2]['Nama Realisasi']);

            foreach ($rows as $row) {
                $this->assertSame('Unit A Gap', $row['Unit']);
            }

            return true;
        });
    }

    public function test_superadmin_export_can_filter_specific_unit(): void
    {
        $data = $this->seedGapReportFixtures();

        Excel::fake();

        $this->actingAs($data['superadmin'])->get(route('report.diklat_gap.unit.export', [
            'tahun_rencana' => '2026',
            'tahun_realisasi' => '2027',
            'unit_kerja_id' => $data['unitB']->id,
        ]));

        Excel::assertDownloaded('DiklatGapUnitReport_2026_2027.xlsx', function (DiklatGapExport $export) use ($data) {
            $rows = $export->collection();

            $this->assertCount(2, $rows);
            $this->assertSame('Unit B Gap', $rows[0]['Unit']);
            $this->assertSame('Rencana B Realized', $rows[0]['Nama Rencana']);
            $this->assertSame('Realisasi lintas tahun', $rows[0]['Bucket Status']);
            $this->assertSame(50, $rows[0]['Target Jam']);
            $this->assertSame(40, $rows[0]['Realisasi Jam']);
            $this->assertSame(10, $rows[0]['Gap Jam']);
            $this->assertSame('Di luar rencana', $rows[1]['Bucket Status']);
            $this->assertSame('Diklat B Out of Plan', $rows[1]['Nama Realisasi']);

            return true;
        });
    }
}
