<?php

declare(strict_types=1);

namespace ComputedColumns\Database\Concerns;

use ComputedColumns\Database\Blueprint;
use Illuminate\Database\Concerns\CompilesJsonPaths;
use Illuminate\Support\Facades\DB;
use Exception;

trait HasModifiersAndWrappers
{
    use CompilesJsonPaths;

    public static function getWrappedFields(array | string $fields)
    {
        if (\is_string($fields)) {
            $fields = [$fields];
        }
        if (\is_array($fields)) {
            foreach ($fields as $field) {
                $wrapped[] = (new Blueprint('label'))->wrap($field);
            }
            $fields = \implode(', ', $wrapped);
        }

        return $fields;
    }

    protected function ifNull(string $column, $replacement): string
    {
        return 'IFNULL('.$column.', '.$replacement.')';
    }

    /**
     * Determine if the given string is a JSON selector.
     *
     * @param  string  $value
     * @return bool
     */
    protected function isJsonSelector($value)
    {
        return \str_contains($value, '->');
    }

    /**
     * Wrap a value in keyword identifiers.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $value
     * @param  bool  $prefixAlias
     * @return string
     */
    public static function wrap($value, $prefixAlias = false)
    {
        /** @var MySqlGrammar $grammar */
        $grammar = DB::connection()->getQueryGrammar();

        return \is_string($value) ? $grammar->wrap($value) : $value;
    }

    public function concatWsSql($separator, ...$columns)
    {
        $sql = "CONCAT_WS('{$separator}', ";
        $sql .= implode(', ', $columns);
        $sql .= ')';

        return $sql;
    }

    protected function jsonColumnSql(string $path, $nullable): string
    {
        $sql = $this->wrap($path);
        if ($nullable === true) {
            return $this->ifNull($sql, '');
        }

        return $sql;
    }

    protected function addComputedColumn($type, $column, $sql)
    {
        if (in_array($type, ['virtual', 'stored'])) {
            throw new Exception('Type of computed column must be either virtual or stored.', 1);
        }
        $tableColumn = $this->string($column);
        if ($type === 'virtual') {
            return $tableColumn->virtualAs($sql);
        }

        return $tableColumn->storedAs($sql);
    }

    public function wrapSql(string $sqlFunc, string $sql): string
    {
        return "{$sqlFunc}({$sql})";
    }
}
