<?php

declare(strict_types=1);

namespace ComputedColumns;

use ComputedColumns\Database\Blueprint;
use Database\Concerns\HasModifiersAndWrappers;
use Illuminate\Database\Schema\ColumnDefinition;
use Throwable;

class ComputedColumn
{
    use HasModifiersAndWrappers;

    public string $column;

    public ?string $path;

    public bool $isNullable = false;

    public function __construct(string $column, ?string $path = null)
    {
        $this->column = $column;
        $this->path = $path;
    }

    public function setAttribute(string $column, $value): self
    {
        if (property_exists($this, $column)) {
            $this->{$column} = $value;
        }

        return $this;
    }

    public function column(?string $column): string
    {
        $this->setAttribute('column', $column);

        return $this->column;
    }

    public function path(?string $path): string
    {
        $this->setAttribute('path', $path);

        return $this->path;
    }

    public function isNullable(bool $isNullable = false): bool
    {
        $this->setAttribute('isNullable', $isNullable);

        return $this->isNullable;
    }

    public function wrappedJsonPath()
    {
        $sql = "
                JSON_UNQUOTE(JSON_EXTRACT(
                    {$this->path},
                    \"$.{$this->column}\"
                ))
        ";
        $wrapped = $this->wrap($path);
        if ($this->isNullable === true) {
            $wrapped = $this->ifNull($wrapped, '');
        }

        return $wrapped;
    }

    protected function checkComputedType($type): void
    {
        if (in_array($type, ['virtual', 'stored'])) {
            throw new Throwable('Type of computed column must be either virtual or stored.', 1);
        }
    }

    public function computedJsonField(string $type, string $column, string $path, bool $nullable): ColumnDefinition
    {
        $field = $this->getJsonField($column, $path, $nullable);
        $tableColumn = $this->string($column);
        if ($type === 'virtual') {
            return $tableColumn->virtualAs($column, $field);
        }

        return $tableColumn->storedAs($field);
    }

    public function addComputedStoredAs(Blueprint $table, string $computed)
    {
        $this->checkComputedType($this->type);
        $table->string($this->column)->storedAs($computed);
    }
}
