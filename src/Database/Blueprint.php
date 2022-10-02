<?php

declare(strict_types=1);

namespace ComputedColumns\Database;

use ComputedColumns\Database\Concerns\HasModifiersAndWrappers;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Exception;
use Throwable;

class Blueprint extends BaseBlueprint
{
    use HasModifiersAndWrappers;



    public function concatWsSql($separator, ...$columns){
        $sql = "CONCAT_WS('{$separator}', ";
        $sql .= implode(', ', $columns);
        $sql .= ')';
        return $sql;
    }

    protected function jsonColumnSql(string $path, $nullable) : string{
        $sql = $this->wrap($path);
        if ($nullable === true) {
            return $this->ifNull($sql, '');
        }
        return $sql;
    }

    public function computedJsonColumn(string $type, string $column, string $path, bool $nullable): ColumnDefinition
    {

        $sql = $this->jsonColumnSql($path, $nullable);
        return $this->addComputed($type, $column, $sql);
    }

    protected function addComputedColumn($type, $column, $sql){
        if(in_array($type, ['virtual', 'stored'])){
            throw new Throwable("Type of computed column must be either virtual or stored.", 1);
        }
        $tableColumn = $this->string($column);
        if ($type === 'virtual') {
            return $tableColumn->virtualAs($sql);
        }
        return $tableColumn->storedAs($sql);
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

    public function manyComputedJsonColumns(
        string $type,
        string $path,
        array $columns,
        bool $nullable = false
    ): Blueprint {
        $computed = [];
        foreach ($columns as $column) {
            $this->addComputed($type, $column, $this->jsonColumnSql($path, $nullable));
        }
        return $this;
    }

    public function wrapSql(string $sqlFunc, string $sql): string{
        return "{$sqlFunc}({$sql})";
    }

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

    public function computedMd5Column(string $type, string $column, string $path): ColumnDefinition
    {
        return $this->computedWrappedColumn($type, 'MD5', $column, $path);
    }
}
