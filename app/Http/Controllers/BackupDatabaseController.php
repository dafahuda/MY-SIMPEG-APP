<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BackupDatabaseController extends Controller
{
    public function BackupDatabasePages()
    {
        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            abort(403);
        }

        return view("pages.dashboard.backup_database.indexBackupDatabase");
    }

    public function download()
    {
        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            abort(403);
        }

        $dbName = (string) config('database.connections.mysql.database');
        $filename = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';

        return response()->streamDownload(function () use ($dbName): void {
            if (! $this->writeMysqldump($dbName)) {
                $this->writePhpStreamingDump($dbName);
            }
        }, $filename, [
            'Content-Type' => 'application/sql; charset=UTF-8',
        ]);
    }

    private function writeMysqldump(string $dbName): bool
    {
        $binary = trim((string) config('database.backup.mysqldump_path', 'mysqldump'));

        if ($binary === '') {
            return false;
        }

        $connection = config('database.default', 'mysql');
        $databaseConfig = config("database.connections.{$connection}", []);

        if (($databaseConfig['driver'] ?? null) !== 'mysql') {
            return false;
        }

        $command = [
            $binary,
            '--single-transaction',
            '--skip-lock-tables',
            '--quick',
            '--host=' . (string) ($databaseConfig['host'] ?? '127.0.0.1'),
            '--port=' . (string) ($databaseConfig['port'] ?? '3306'),
            '--user=' . (string) ($databaseConfig['username'] ?? ''),
            $dbName,
        ];

        $socket = $databaseConfig['unix_socket'] ?? null;
        if (is_string($socket) && $socket !== '') {
            $command[] = '--socket=' . $socket;
        }

        $env = null;
        $password = (string) ($databaseConfig['password'] ?? '');
        if ($password !== '') {
            $env = ['MYSQL_PWD' => $password];
        }

        $descriptorSpec = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = @proc_open($command, $descriptorSpec, $pipes, null, $env);

        if (! is_resource($process)) {
            return false;
        }

        while (! feof($pipes[1])) {
            echo fread($pipes[1], 8192);
        }

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException('mysqldump failed: ' . trim((string) $stderr));
        }

        return true;
    }

    private function writePhpStreamingDump(string $dbName): void
    {
        $dbHost = (string) config('database.connections.mysql.host');
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $dbName;

        echo "-- Database Backup: {$dbName}\n";
        echo "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Host: {$dbHost}\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $table = (string) ($tableObj->{$tableKey} ?? reset($tableObj));

            if ($table === '') {
                continue;
            }

            $quotedTable = $this->quoteIdentifier($table);
            $createResult = DB::select('SHOW CREATE TABLE ' . $quotedTable);
            $createSql = (string) ($createResult[0]->{'Create Table'} ?? '');

            echo "-- ----------------------------\n";
            echo "-- Table structure for {$quotedTable}\n";
            echo "-- ----------------------------\n";
            echo "DROP TABLE IF EXISTS {$quotedTable};\n";
            echo $createSql . ";\n\n";

            echo "-- ----------------------------\n";
            echo "-- Records of {$quotedTable}\n";
            echo "-- ----------------------------\n";

            foreach (DB::table($table)->cursor() as $row) {
                $rowArray = (array) $row;

                if ($rowArray === []) {
                    continue;
                }

                $columns = implode(', ', array_map([$this, 'quoteIdentifier'], array_keys($rowArray)));
                $values = implode(', ', array_map([$this, 'quoteValue'], array_values($rowArray)));

                echo "INSERT INTO {$quotedTable} ({$columns}) VALUES ({$values});\n";
            }

            echo "\n";
        }

        echo "SET FOREIGN_KEY_CHECKS=1;\n";
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function quoteValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return "'" . str_replace(["\\", "'"], ["\\\\", "''"], (string) $value) . "'";
    }
}
