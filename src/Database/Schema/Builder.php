<?php

declare(strict_types=1);

namespace ComputedColumns\Database\Schema;

use ComputedColumns\Database\Blueprint;
use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\Builder as SchemaBuilder;

class Builder extends SchemaBuilder
{
    /**
     * Execute the blueprint to build / modify the table.
     *
     * @param  \App\Support\Database\Blueprint|\Illuminate\Database\Schema\Blueprint  $blueprint
     * @return void
     */
    protected function build(Blueprint|BaseBlueprint $blueprint): void
    {
        $blueprint->build($this->connection, $this->grammar);
    }
}
