<?php

declare(strict_types=1);

namespace ComputedColumns\Database;

use ComputedColumns\Database\Concerns\HasModifiersAndWrappers;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;

class Blueprint extends BaseBlueprint
{
    use HasModifiersAndWrappers;

    public function computedWrappedColumn(
        string $type,
        string $sqlFunc,
        string $column,
        string $path
    ): ColumnDefinition {
        $path = $this->wrap($path);
        $sql = $this->wrapSql($sqlFunc, $path);

        return $this->addComputedColumn($type, $column, $sql);
    }

    public function computedJsonColumn(string $type, string $column, string $path, bool $nullable): ColumnDefinition
    {
        $sql = $this->jsonColumnSql($path, $nullable);

        return $this->addComputed($type, $column, $sql);
    }

    public function computedJsonColumns(
        string $type,
        string $path,
        array $columns,
        bool $nullable = false
    ): Blueprint {
        $computed = [];
        foreach ($columns as $column) {
            $this->computedJsonColumn($type, $column, $path, $nullable);
        }

        return $this;
    }

    public function computedConcatWsColumn(
        string $type,
        string $column,
        string $default,
        string $separator = ', ',
        string|array ...$columns
    ): ColumnDefinition {
        $sql = $this->ifNull(
            $this->concatWsSql($separator, ...$columns),
            $this->wrap($default)
        );

        return $this->addComputed($type, $column, $sql);
    }

    public function computedMd5Column(string $type, string $column, string $path): ColumnDefinition
    {
        return $this->computedWrappedColumn($type, 'MD5', $column, $path);
    }
}
