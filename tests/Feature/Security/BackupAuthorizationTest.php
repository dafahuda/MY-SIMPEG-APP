<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BackupAuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['session.driver' => 'array']);
    }

    public function test_guest_redirected_to_login_for_backup_data_page(): void
    {
        $this->get('/backup_data')
            ->assertRedirect('/login');
    }

    public function test_guest_redirected_to_login_for_backup_data_download(): void
    {
        $this->get('/backup_data/download')
            ->assertRedirect('/login');
    }

    public function test_admin_and_pegawai_are_forbidden_from_backup_routes(): void
    {
        foreach (['admin', 'pegawai'] as $role) {
            $user = User::factory()->make([
                'role' => $role,
                'email_verified_at' => now(),
            ]);

            $this->actingAs($user)
                ->get('/backup_data')
                ->assertForbidden();

            $this->actingAs($user)
                ->get('/backup_data/download')
                ->assertForbidden();
        }
    }

    public function test_superadmin_can_access_backup_data_page(): void
    {
        $user = User::factory()->make([
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/backup_data')
            ->assertOk();
    }

    public function test_superadmin_can_download_backup_with_small_stubbed_dataset_baseline(): void
    {
        $user = User::factory()->make([
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $dbName = 'simpeg_test';
        config([
            'database.connections.mysql.database' => $dbName,
            'database.backup.mysqldump_path' => '',
        ]);

        $tables = [(object) ["Tables_in_{$dbName}" => 'tb_ping']];
        $create = [(object) ['Create Table' => 'CREATE TABLE `tb_ping` (`id` int NOT NULL, `name` varchar(255) DEFAULT NULL, `note` varchar(255) DEFAULT NULL)']];
        $rows = new \ArrayIterator([
            (object) ['id' => 1, 'name' => 'ok', 'note' => null],
            (object) ['id' => 2, 'name' => "O'Reilly", 'note' => 'back\\slash'],
        ]);

        DB::shouldReceive('select')->once()->with('SHOW TABLES')->andReturn($tables);
        DB::shouldReceive('select')->once()->with('SHOW CREATE TABLE `tb_ping`')->andReturn($create);
        DB::shouldReceive('table')->once()->with('tb_ping')->andReturnSelf();
        DB::shouldReceive('cursor')->once()->andReturn($rows);
        DB::shouldNotReceive('get');

        $response = $this->actingAs($user)->get('/backup_data/download');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/sql; charset=UTF-8');
        $response->assertHeader('Content-Disposition');

        $payload = $response->streamedContent();
        $this->assertIsString($payload);
        $this->assertStringContainsString('CREATE TABLE `tb_ping`', $payload);
        $this->assertStringContainsString("INSERT INTO `tb_ping` (`id`, `name`, `note`) VALUES (1, 'ok', NULL);", $payload);
        $this->assertStringContainsString("INSERT INTO `tb_ping` (`id`, `name`, `note`) VALUES (2, 'O''Reilly', 'back\\\\slash');", $payload);
        $this->assertLessThan(5000, strlen($payload), 'Baseline fixture must stay lightweight.');
    }

    public function test_backup_routes_use_consistent_superadmin_middleware_stack(): void
    {
        $pageRoute = Route::getRoutes()->match(Request::create('/backup_data', 'GET'));
        $downloadRoute = Route::getRoutes()->match(Request::create('/backup_data/download', 'GET'));

        $pageMiddleware = $pageRoute->gatherMiddleware();
        $downloadMiddleware = $downloadRoute->gatherMiddleware();

        foreach (['auth:sanctum', 'verified', 'role:superadmin'] as $requiredMiddleware) {
            $this->assertContains($requiredMiddleware, $pageMiddleware);
            $this->assertContains($requiredMiddleware, $downloadMiddleware);
        }

        $this->assertEqualsCanonicalizing($pageMiddleware, $downloadMiddleware);
    }
}
