<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackupDatabaseController extends Controller
{
    public function BackupDatabasePages()
    {
        return view("pages.dashboard.backup_database.indexBackupDatabase");
    }

    public function download()
    {
        $dbHost     = config('database.connections.mysql.host');
        $dbPort     = config('database.connections.mysql.port');
        $dbName     = config('database.connections.mysql.database');
        $dbUser     = config('database.connections.mysql.username');
        $dbPassword = config('database.connections.mysql.password');

        $filename = 'backup_' . $dbName . '_' . date('Y-m-d_H-i-s') . '.sql';

        // Build the SQL dump manually using Laravel's DB connection
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $dbName;

        $sql = "-- Database Backup: {$dbName}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Host: {$dbHost}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey;

            // Drop + Create table
            $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql    = $createResult[0]->{'Create Table'};

            $sql .= "-- ----------------------------\n";
            $sql .= "-- Table structure for `{$table}`\n";
            $sql .= "-- ----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createSql . ";\n\n";

            // Dump rows
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "-- ----------------------------\n";
                $sql .= "-- Records of `{$table}`\n";
                $sql .= "-- ----------------------------\n";

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

        return response($sql, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($sql),
        ]);
    }
}
