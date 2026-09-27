<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationDinamisTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'superadmin']);

        // 12 pegawai supaya pagination relevan (default 10/halaman)
        $unit = \App\Models\UnitKerja::factory()->create();
        for ($i = 0; $i < 12; $i++) {
            $u = User::factory()->create(['role' => 'pegawai']);
            Pegawai::factory()->create([
                'user_id' => $u->id,
                'unit_kerja_id' => $unit->id,
            ]);
        }
    }

    public function test_default_10_per_halaman(): void
    {
        $res = $this->actingAs($this->admin)->get('/data_pegawai/pegawai');

        $res->assertOk();
        $res->assertViewHas('pegawai', fn ($p) => $p->perPage() === 10 && $p->count() === 10);
    }

    public function test_per_page_25_dari_query_string(): void
    {
        $res = $this->actingAs($this->admin)->get('/data_pegawai/pegawai?per_page=25');

        $res->assertOk();
        $res->assertViewHas('pegawai', fn ($p) => $p->perPage() === 25 && $p->count() === 12);
    }

    public function test_per_page_3_semuanya_tampil_satu_halaman(): void
    {
        $res = $this->actingAs($this->admin)->get('/data_pegawai/pegawai?per_page=3');

        $res->assertOk();
        $res->assertViewHas('pegawai', fn ($p) => $p->perPage() === 3);
    }

    public function test_per_page_lebih_besar_dari_100_dibatasi(): void
    {
        $res = $this->actingAs($this->admin)->get('/data_pegawai/pegawai?per_page=99999');

        $res->assertOk();
        $res->assertViewHas('pegawai', fn ($p) => $p->perPage() === 100);
    }

    public function test_per_page_tidak_valid_fallback_aman(): void
    {
        $res = $this->actingAs($this->admin)->get('/data_pegawai/pegawai?per_page=abc');

        $res->assertOk();
        $res->assertViewHas('pegawai', fn ($p) => $p->perPage() === 10);
    }

    public function test_dropdown_per_halaman_muncul_di_halaman_daftar(): void
    {
        $res = $this->actingAs($this->admin)->get('/data_pegawai/pegawai');

        $res->assertOk();
        $res->assertSee('Tampil:', false);
        $res->assertSee('per_page=25', false);
        $res->assertSee('per_page=50', false);
        $res->assertSee('per_page=100', false);
    }
}
