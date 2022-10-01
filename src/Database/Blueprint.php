<?php

declare(strict_types=1);

namespace ComputedColumns\Database;

use ComputedColumns\Database\Concerns\HasModifiersAndWrappers;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;

class Blueprint extends BaseBlueprint
{
    use HasModifiersAndWrappers;

    public function concatWsStoredAs(
        string $column,
        string $default,
        string $separator = ', ',
        string|array ...$fields
    ): ColumnDefinition {
        return $this->string($column)->storedAs(
            $this->ifNull(
                $this->wrap($default),
                $this->concatWsFields($fields, $separator)
            )
        );
    }

    public function jsonFieldStoredAs(string $column, string $source, bool $nullable): ColumnDefinition
    {
        $sql = "
                JSON_UNQUOTE(JSON_EXTRACT(
                    {$source},
                    \"$.{$column}\"
                ))
        ";
        $field = $this->wrap($source);
        if ($nullable === true) {
            $field = $this->ifNull($field, '');

            return $this->string($column)->storedAs($sql)->nullable();
        }

        return $this->string($column)->storedAs($sql);
    }

    public function manyJsonFieldsStoredAs(
        string $source,
        array $fields,
        bool $nullable = false
    ): array {
        $stored = [];
        foreach ($fields as $field) {
            $stored[$field] = $this->jsonFieldStoredAs($field, $source, $nullable);
        }

        return $stored;
    }

    public function md5StoredAs(
        string $column,
        string $source
    ): ColumnDefinition {
        $source = $this->wrap($source);

        return $this->string($column)->storedAs("MD5({$source})");
    }
}
