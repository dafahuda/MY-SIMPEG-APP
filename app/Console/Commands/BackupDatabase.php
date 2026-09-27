<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database
                            {--days=30 : Retensi backup (hari), backup lebih lama dihapus}';

    protected $description = 'Buat backup database SQL ke disk "backup" dan hapus backup melewati retensi';

    public function handle(): int
    {
        $dbName   = basename((string) config('database.connections.' . config('database.default') . '.database'));
        $filename = 'backup_' . $dbName . '_' . now()->format('Y-m-d_His') . '.sql';

        $sql = $this->buildSqlDump($dbName);

        Storage::disk('backup')->put($filename, $sql);
        $this->info("Backup tersimpan: {$filename} (" . number_format(strlen($sql) / 1024, 1) . " KB)");

        $this->pruneOldBackups((int) $this->option('days'));

        return self::SUCCESS;
    }

    private function buildSqlDump(string $dbName): string
    {
        $dbHost = config('database.connections.mysql.host');

        $sql  = "-- Database Backup: {$dbName}\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Host: {$dbHost}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($this->daftarTabel($dbName) as $table) {
            $sql .= "-- ------------------------------------------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- ------------------------------------------------------------\n";

            if (DB::connection()->getDriverName() !== 'sqlite') {
                $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
                $createSql    = $createResult[0]->{'Create Table'};

                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createSql . ";\n\n";
            }

            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "-- ------------------------------------------------------------\n";
                $sql .= "-- Records of `table` `{$table}`\n";
                $sql .= "-- ------------------------------------------------------------\n";

                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns  = '`' . implode('`, `', array_keys($rowArray)) . '`';
                    $values   = implode(', ', array_map(function ($val) {
                        if (is_null($val)) {
                            return 'NULL';
                        }
                        return "'" . addslashes($val) . "'";
                    }, array_values($rowArray)));

                    $sql .= "INSERT INTO `{$table}` ({$columns}) VALUES ({$values});\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    /**
     * Daftar nama tabel — mendukung MySQL (produksi) dan SQLite (test).
     */
    private function daftarTabel(string $dbName): array
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return array_map(
                fn ($r) => $r->name,
                DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")
            );
        }

        $tableKey = 'Tables_in_' . $dbName;

        return array_map(
            fn ($r) => $r->$tableKey,
            DB::select('SHOW TABLES')
        );
    }

    private function pruneOldBackups(int $days): void
    {
        if ($days <= 0) {
            return;
        }

        $batas = now()->subDays($days)->getTimestamp();
        $dihapus = 0;

        foreach (Storage::disk('backup')->files() as $file) {
            if (str_ends_with($file, '.sql') && Storage::disk('backup')->lastModified($file) < $batas) {
                Storage::disk('backup')->delete($file);
                $dihapus++;
            }
        }

        if ($dihapus > 0) {
            $this->info("Retensi {$days} hari: {$dihapus} backup lama dihapus.");
        }
    }
}
