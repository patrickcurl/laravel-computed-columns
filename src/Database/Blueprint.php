<?php declare(strict_types=1);

namespace ComputedColumns\Database;

use Exception;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use ComputedColumns\Database\Concerns\HasModifiersAndWrappers;

class Blueprint extends BaseBlueprint
{
    use HasModifiersAndWrappers;

    public function computedWrappedColumn(
        string $type,
        string $sqlFunc,
        string $column,
        string $path
    ) : ColumnDefinition {
        $path = $this->wrap($path);
        $sql  = $this->wrapSql($sqlFunc, $path);

        return $this->addComputedColumn($type, $column, $sql);
    }

    public function computedJsonColumn(string $type, string $column, string $path, bool $nullable = false) : ColumnDefinition
    {
        $sql = $this->jsonColumnSql($path, true);

        return $this->addComputedColumn($type, $column, $sql);
    }

    public function computedJsonColumns(
        string $type,
        string $path,
        array $columns,
        bool $nullable = false
    ) : self {
        $computed = [];
        foreach ($columns as $column) {
            $this->computedJsonColumn($type, $column, "{$path}->{$column}", $nullable);
        }

        return $this;
    }

    public function computedConcatWsColumn(
        string $type,
        string $column,
        string $default,
        string $separator = ', ',
        string|array ...$columns
    ) : ColumnDefinition {
        $sql = $this->ifNull(
            $this->concatWsSql($separator, ...$columns),
            $this->wrap($default)
        );

        return $this->addComputedColumn($type, $column, $sql);
    }

    public function computedMd5Column(string $type, string $column, string $path) : ColumnDefinition
    {
        return $this->computedWrappedColumn($type, 'MD5', $column, $path);
    }

    protected function addComputedColumn($type, $column, $sql)
    {
        if (!\in_array($type, ['virtual', 'stored'], true)) {
            throw new Exception('Type of computed column must be either virtual or stored.', 1);
        }
        $tableColumn = $this->string($column, 255);
        if ($type === 'virtual') {
            return $tableColumn->virtualAs($sql);
        }

        return $tableColumn->storedAs($sql);
    }
}
