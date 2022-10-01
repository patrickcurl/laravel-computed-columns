<?php

declare(strict_types=1);

namespace ComputedColumns\Database\Concerns;

use Exception;
use Illuminate\Support\Facades\DB;
use ComputedColumns\Database\Blueprint;
use Illuminate\Database\Concerns\CompilesJsonPaths;

trait HasModifiersAndWrappers
{
    use CompilesJsonPaths;

    protected function concatWsFields(array|string $fields, $separator = ' '): string
    {
        $concatFields = \is_string($fields) ? $fields : '';
        if (\is_array($fields)) {
            if (\count($fields) === 1 && \is_array($fields[0])) {
                $fields = $fields[0];
            }
            foreach ($fields as $key => $field) {
                $start = $key > 0 ? ', ' : '';
                $concatFields .= $start.$this->wrap($field);
            }
        }

        return "CONCAT_WS('{$separator}', {$concatFields})";
    }

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

    /**
     * Wrap the given JSON selector.
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonSelector($value)
    {
        [$field, $path] = $this->wrapJsonFieldAndPath($value);

        return 'json_unquote(json_extract('.$field.$path.'))';
    }
}
