<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupDatabaseController extends Controller
{
    public function BackupDatabasePages()
    {
        return view("pages.dashboard.backup_database.indexBackupDatabase");
    }

    public function download()
    {
        $connection = DB::connection();
        $schema = Schema::connection($connection->getName());
        $driver = $connection->getDriverName();
        $dbName = $connection->getDatabaseName() ?: $driver;
        $backupName = $driver === 'sqlite' ? pathinfo($dbName, PATHINFO_FILENAME) : basename($dbName);

        $filename = 'backup_' . $backupName . '_' . date('Y-m-d_H-i-s') . '.sql';

        $tables = $this->tableNames($schema->getTables(), $driver);

        $sql = "-- Database Backup: {$dbName}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Driver: {$driver}\n\n";
        $sql .= $this->disableForeignKeyChecks($driver);

        foreach ($tables as $table) {
            $columns = $schema->getColumns($table);
            $indexes = method_exists($schema, 'getIndexes') ? $schema->getIndexes($table) : [];
            $foreignKeys = method_exists($schema, 'getForeignKeys') ? $schema->getForeignKeys($table) : [];

            $sql .= "-- ----------------------------\n";
            $sql .= "-- Table structure for {$this->quoteIdentifier($table, $driver)}\n";
            $sql .= "-- ----------------------------\n";
            $sql .= "DROP TABLE IF EXISTS {$this->quoteIdentifier($table, $driver)};\n";
            $sql .= $this->createTableSql($table, $columns, $indexes, $foreignKeys, $driver) . "\n";
            $sql .= $this->createIndexSql($table, $indexes, $driver) . "\n";

            $rows = $connection->table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "-- ----------------------------\n";
                $sql .= "-- Records of {$this->quoteIdentifier($table, $driver)}\n";
                $sql .= "-- ----------------------------\n";

                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columnsSql = implode(', ', array_map(fn ($column) => $this->quoteIdentifier($column, $driver), array_keys($rowArray)));
                    $valuesSql = implode(', ', array_map(fn ($value) => $this->quoteValue($value), array_values($rowArray)));

                    $sql .= "INSERT INTO {$this->quoteIdentifier($table, $driver)} ({$columnsSql}) VALUES ({$valuesSql});\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= $this->enableForeignKeyChecks($driver);

        return response($sql, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($sql),
        ]);
    }

    private function tableNames(array $tables, string $driver): array
    {
        return collect($tables)
            ->map(function ($table) {
                if (is_string($table)) {
                    return $table;
                }

                if (is_object($table)) {
                    $table = (array) $table;
                }

                return $table['name'] ?? $table['table'] ?? null;
            })
            ->filter()
            ->map(fn (string $table) => str_contains($table, '.') ? substr($table, strrpos($table, '.') + 1) : $table)
            ->reject(fn (string $table) => $driver === 'sqlite' && str_starts_with($table, 'sqlite_'))
            ->values()
            ->all();
    }

    private function createTableSql(string $table, array $columns, array $indexes, array $foreignKeys, string $driver): string
    {
        $definitions = collect($columns)
            ->map(fn (array $column) => $this->columnDefinition($column, $driver))
            ->all();

        $primaryKey = collect($indexes)->first(fn (array $index) => (bool) ($index['primary'] ?? false));
        $hasAutoIncrementPrimaryKey = collect($columns)->contains(fn (array $column) => (bool) ($column['auto_increment'] ?? false));

        if ($primaryKey && ! $hasAutoIncrementPrimaryKey) {
            $definitions[] = 'PRIMARY KEY (' . $this->columnListSql($primaryKey['columns'] ?? [], $driver) . ')';
        }

        foreach ($foreignKeys as $foreignKey) {
            $definitions[] = $this->foreignKeySql($foreignKey, $driver);
        }

        $definitionsSql = collect($definitions)
            ->filter()
            ->map(fn (string $definition) => '    ' . $definition)
            ->implode(",\n");

        return "CREATE TABLE {$this->quoteIdentifier($table, $driver)} (\n{$definitionsSql}\n);";
    }

    private function createIndexSql(string $table, array $indexes, string $driver): string
    {
        return collect($indexes)
            ->reject(fn (array $index) => (bool) ($index['primary'] ?? false))
            ->map(function (array $index) use ($table, $driver) {
                $columns = $index['columns'] ?? [];
                if (empty($columns)) {
                    return null;
                }

                $unique = (bool) ($index['unique'] ?? false) ? 'UNIQUE ' : '';
                $name = $index['name'] ?? implode('_', array_merge([$table], $columns, ['index']));

                return "CREATE {$unique}INDEX {$this->quoteIdentifier($name, $driver)} ON {$this->quoteIdentifier($table, $driver)} ({$this->columnListSql($columns, $driver)});";
            })
            ->filter()
            ->implode("\n");
    }

    private function columnDefinition(array $column, string $driver): string
    {
        $name = $column['name'];
        $nullable = (bool) ($column['nullable'] ?? true);
        $autoIncrement = (bool) ($column['auto_increment'] ?? false);
        $default = $column['default'] ?? null;

        if ($autoIncrement) {
            return $this->quoteIdentifier($name, $driver) . ($driver === 'sqlite' ? ' INTEGER PRIMARY KEY AUTOINCREMENT' : ' ' . $this->columnTypeSql($column, $driver) . ' AUTO_INCREMENT PRIMARY KEY');
        }

        $definition = $this->quoteIdentifier($name, $driver) . ' ' . $this->columnTypeSql($column, $driver);

        if (! $nullable) {
            $definition .= ' NOT NULL';
        }

        if ($default !== null) {
            $definition .= ' DEFAULT ' . $this->defaultValueSql($default);
        }

        return $definition;
    }

    private function columnTypeSql(array $column, string $driver): string
    {
        $type = strtolower((string) ($column['type'] ?? ''));
        $typeName = strtolower((string) ($column['type_name'] ?? $type ?: 'text'));
        $length = $column['length'] ?? null;
        $precision = $column['precision'] ?? null;
        $scale = $column['scale'] ?? null;

        if ($driver === 'sqlite') {
            return match ($typeName) {
                'bigint', 'integer', 'int', 'smallint', 'tinyint', 'boolean', 'bool' => 'INTEGER',
                'decimal', 'double', 'float', 'real' => 'REAL',
                'blob', 'binary' => 'BLOB',
                default => 'TEXT',
            };
        }

        if ($type && ($type !== $typeName || str_contains($type, '(') || str_contains($type, 'unsigned'))) {
            return $type;
        }

        return match ($typeName) {
            'varchar', 'char' => $typeName . ($length ? "({$length})" : ''),
            'decimal', 'numeric', 'double', 'float' => $precision ? $typeName . "({$precision}" . ($scale !== null ? ",{$scale}" : '') . ')' : $typeName,
            'tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint' => ($typeName === 'integer' ? 'int' : $typeName),
            'boolean', 'bool' => 'tinyint(1)',
            default => $typeName,
        };
    }

    private function foreignKeySql(array $foreignKey, string $driver): ?string
    {
        $columns = $foreignKey['columns'] ?? [];
        $foreignTable = $foreignKey['foreign_table'] ?? null;
        $foreignColumns = $foreignKey['foreign_columns'] ?? [];

        if (empty($columns) || ! $foreignTable || empty($foreignColumns)) {
            return null;
        }

        $sql = '';
        if (! empty($foreignKey['name'])) {
            $sql .= 'CONSTRAINT ' . $this->quoteIdentifier($foreignKey['name'], $driver) . ' ';
        }

        $sql .= 'FOREIGN KEY (' . $this->columnListSql($columns, $driver) . ') REFERENCES ' . $this->quoteIdentifier($foreignTable, $driver) . ' (' . $this->columnListSql($foreignColumns, $driver) . ')';

        if (! empty($foreignKey['on_delete'])) {
            $sql .= ' ON DELETE ' . strtoupper($foreignKey['on_delete']);
        }

        if (! empty($foreignKey['on_update'])) {
            $sql .= ' ON UPDATE ' . strtoupper($foreignKey['on_update']);
        }

        return $sql;
    }

    private function columnListSql(array $columns, string $driver): string
    {
        return implode(', ', array_map(fn (string $column) => $this->quoteIdentifier($column, $driver), $columns));
    }

    private function defaultValueSql(mixed $default): string
    {
        $default = trim((string) $default);

        if ($default === '') {
            return "''";
        }

        if (strtoupper($default) === 'NULL') {
            return 'NULL';
        }

        if (is_numeric($default)) {
            return $default;
        }

        if (preg_match('/^CURRENT_(TIMESTAMP|DATE|TIME)(\(\))?$/i', $default)) {
            return strtoupper($default);
        }

        if ((str_starts_with($default, "'") && str_ends_with($default, "'")) || (str_starts_with($default, '(') && str_ends_with($default, ')'))) {
            return $default;
        }

        return $this->quoteValue($default);
    }

    private function quoteIdentifier(string $identifier, string $driver): string
    {
        $quote = $driver === 'mysql' ? '`' : '"';

        return collect(explode('.', $identifier))
            ->map(fn (string $part) => $quote . str_replace($quote, $quote . $quote, $part) . $quote)
            ->implode('.');
    }

    private function quoteValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return DB::connection()->getPdo()->quote((string) $value);
    }

    private function disableForeignKeyChecks(string $driver): string
    {
        return match ($driver) {
            'mysql' => "SET FOREIGN_KEY_CHECKS=0;\n\n",
            'sqlite' => "PRAGMA foreign_keys=OFF;\n\n",
            default => '',
        };
    }

    private function enableForeignKeyChecks(string $driver): string
    {
        return match ($driver) {
            'mysql' => "SET FOREIGN_KEY_CHECKS=1;\n",
            'sqlite' => "PRAGMA foreign_keys=ON;\n",
            default => '',
        };
    }
}
