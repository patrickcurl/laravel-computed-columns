<?php

declare(strict_types=1);

namespace ComputedColumns\Database\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Support\Database\Schema\Builder create(string $table, \Closure $callback)
 * @method static \App\Support\Database\Schema\Builder createDatabase(string $name)
 * @method static \App\Support\Database\Schema\Builder disableForeignKeyConstraints()
 * @method static \App\Support\Database\Schema\Builder drop(string $table)
 * @method static \App\Support\Database\Schema\Builder dropDatabaseIfExists(string $name)
 * @method static \App\Support\Database\Schema\Builder dropIfExists(string $table)
 * @method static \App\Support\Database\Schema\Builder enableForeignKeyConstraints()
 * @method static \App\Support\Database\Schema\Builder rename(string $from, string $to)
 * @method static \App\Support\Database\Schema\Builder table(string $table, \Closure $callback)
 * @method static bool hasColumn(string $table, string $column)
 * @method static bool hasColumns(string $table, array $columns)
 * @method static bool dropColumns(string $table, array $columns)
 * @method static void whenTableHasColumn(string $table, string $column, \Closure $callback)
 * @method static void whenTableDoesntHaveColumn(string $table, string $column, \Closure $callback)
 * @method static bool hasTable(string $table)
 * @method static void defaultStringLength(int $length)
 * @method static array getColumnListing(string $table)
 * @method static string getColumnType(string $table, string $column)
 * @method static void morphUsingUuids()
 * @method static \Illuminate\Database\Connection getConnection()
 * @method static \App\Support\Database\Schema\Builder setConnection(\Illuminate\Database\Connection $connection)
 *
 * @see \App\Support\Database\Schema\Builder
 */
class Schema extends Facade
{
    /**
     * Indicates if the resolved facade should be cached.
     *
     * @var bool
     */
    protected static $cached = false;

    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string|null  $name
     * @return \App\Support\Database\Schema\Builder
     */
    public static function connection($name)
    {
        return static::$app['db']->connection($name)->getSchemaBuilder();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db.schema';
    }
}
