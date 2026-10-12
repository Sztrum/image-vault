<?php

declare(strict_types=1);

namespace App\Shared\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

abstract class AbstractMigration extends Migration
{
    protected string $table_name;

    abstract public function up(): void;

    public function down(): void
    {
        Schema::dropIfExists($this->table_name);
    }

    public function hasForeignKey(string $table, string $column): bool
    {
        return collect(Schema::getForeignKeys($table))
            ->pluck('columns')
            ->flatten()
            ->contains($column);
    }

    /**
     * Rename a top-level value inside a JSON column while preserving the remaining payload.
     *
     * @param  array<string, mixed>  $whereEquals
     *
     * @throws \JsonException
     */
    protected function renameJsonColumnValue(
        string $table,
        string $jsonColumn,
        string $jsonKey,
        string $from,
        string $to,
        array $whereEquals = [],
        string $primaryKey = 'id',
        int $chunkSize = 200,
    ): void {
        $query = DB::table($table)
            ->where($jsonColumn . '->' . $jsonKey, $from);

        foreach ($whereEquals as $column => $value) {
            $query->where($column, $value);
        }

        $query
            ->orderBy($primaryKey)
            ->select([$primaryKey, $jsonColumn])
            ->chunkById(
                $chunkSize,
                static function ($rows) use ($table, $jsonColumn, $jsonKey, $from, $to, $primaryKey): void {
                    /** @var stdClass $row */
                    foreach ($rows as $row) {
                        $customProperties = json_decode((string) $row->{$jsonColumn}, true);

                        if (!is_array($customProperties) || ($customProperties[$jsonKey] ?? null) !== $from) {
                            continue;
                        }

                        $customProperties[$jsonKey] = $to;

                        DB::table($table)
                            ->where($primaryKey, $row->{$primaryKey})
                            ->update([
                                $jsonColumn => json_encode(
                                    $customProperties,
                                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
                                ),
                            ]);
                    }
                },
                $primaryKey,
            );
    }
}
