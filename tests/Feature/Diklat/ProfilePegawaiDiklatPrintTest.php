<?php

namespace Tests\Feature\Diklat;

use App\Models\UnitKerja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePegawaiDiklatPrintTest extends TestCase
{
    use RefreshDatabase;
    use DiklatGapReportFixtures;

    public function test_print_page_includes_diklat_section_and_excludes_other_pegawai(): void
    {
        $currentYear = (string) now()->year;

        $unitA = UnitKerja::create([
            'nama_unit' => 'Unit Print Diklat A',
            'alamat' => 'Jl. Print A',
        ]);

        $unitB = UnitKerja::create([
            'nama_unit' => 'Unit Print Diklat B',
            'alamat' => 'Jl. Print B',
        ]);

        $pegawaiA = $this->createPegawai('pegawai-print-diklat-a', 'pegawai.print.diklat.a@example.test', 'Pegawai Print Diklat A', $unitA);
        $pegawaiB = $this->createPegawai('pegawai-print-diklat-b', 'pegawai.print.diklat.b@example.test', 'Pegawai Print Diklat B', $unitB);

        $this->createRencana($pegawaiA, $currentYear, 'Rencana A Print Planned', 'planned', 36);
        $rencanaRealized = $this->createRencana($pegawaiA, $currentYear, 'Rencana A Print Realized', 'realized', 28);
        $this->createDiklat($pegawaiA, $rencanaRealized, $currentYear, 'Diklat A Print Linked', 24);
        $this->createDiklat($pegawaiA, null, $currentYear, 'Diklat A Print Out of Plan', 12);

        $this->createRencana($pegawaiB, $currentYear, 'Rencana B Print Hidden', 'realized', 42);
        $this->createDiklat($pegawaiB, null, $currentYear, 'Diklat B Print Hidden', 18);

        $response = $this->actingAs($pegawaiA->user)->get(route('profile.pegawai.print'));

        $response->assertOk()
            ->assertSee('Rencana dan Realisasi Diklat Saya')
            ->assertSeeHtml('data-testid="print-diklat-section"')
            ->assertSeeHtml('data-testid="print-diklat-summary-planned" data-value="2"')
            ->assertSeeHtml('data-testid="print-diklat-summary-realized" data-value="1"')
            ->assertSeeHtml('data-testid="print-diklat-summary-not-realized" data-value="1"')
            ->assertSeeHtml('data-testid="print-diklat-summary-out-of-plan" data-value="1"')
            ->assertSee('No. Sertifikat / STTPP')
            ->assertSee('STTPP-Diklat A Print Linked')
            ->assertSee('STTPP-Diklat A Print Out of Plan')
            ->assertSee('Rencana A Print Planned')
            ->assertSee('Rencana A Print Realized')
            ->assertSee('Diklat A Print Linked')
            ->assertSee('Diklat A Print Out of Plan')
            ->assertDontSee('Rencana B Print Hidden')
            ->assertDontSee('Diklat B Print Hidden');
    }
}
