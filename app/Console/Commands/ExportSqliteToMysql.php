<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Exports all data from the local SQLite database as a MySQL-compatible
 * .sql file (data only) that you can import with phpMyAdmin.
 *
 * Usage (locally, with DB_CONNECTION=sqlite in your local .env):
 *   php artisan db:export-mysql
 *   php artisan db:export-mysql --skip=sessions,cache,cache_locks,jobs
 */
class ExportSqliteToMysql extends Command
{
    protected $signature = 'db:export-mysql
        {--file=database/mysql-export.sql : Output file path, relative to project root}
        {--skip=sessions,cache,cache_locks,password_reset_tokens : Comma-separated tables to skip}';

    protected $description = 'Export SQLite data as MySQL-compatible INSERT statements';

    public function handle(): int
    {
        $skip = array_merge(
            ['migrations'],
            array_filter(array_map('trim', explode(',', (string) $this->option('skip'))))
        );

        $path = base_path($this->option('file'));
        $out = fopen($path, 'w');

        if (! $out) {
            $this->error("Cannot write to {$path}");
            return self::FAILURE;
        }

        fwrite($out, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        foreach (Schema::getTables() as $table) {
            $name = $table['name'];

            if (str_starts_with($name, 'sqlite_') || in_array($name, $skip, true)) {
                continue;
            }

            $count = 0;
            $batch = [];
            $columns = null;

            foreach (DB::table($name)->cursor() as $row) {
                $row = (array) $row;
                $columns ??= '`' . implode('`, `', array_keys($row)) . '`';
                $batch[] = '(' . implode(', ', array_map([$this, 'quote'], $row)) . ')';
                $count++;

                if (count($batch) >= 100) {
                    $this->flush($out, $name, $columns, $batch);
                    $batch = [];
                }
            }

            if ($batch) {
                $this->flush($out, $name, $columns, $batch);
            }

            $this->line("{$name}: {$count} rows");
        }

        fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($out);

        $this->info("Done: {$path}");
        return self::SUCCESS;
    }

    private function flush($out, string $table, string $columns, array $batch): void
    {
        fwrite($out, "INSERT INTO `{$table}` ({$columns}) VALUES\n" . implode(",\n", $batch) . ";\n");
    }

    private function quote(mixed $value): string
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
        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        return "'" . str_replace(
            ['\\', "'", "\0", "\n", "\r", "\x1a"],
            ['\\\\', "\\'", '\\0', '\\n', '\\r', '\\Z'],
            (string) $value
        ) . "'";
    }
}
