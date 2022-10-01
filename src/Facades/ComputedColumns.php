<?php

namespace ComputedColumns\ComputedColumns\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ComputedColumns\ComputedColumns\ComputedColumns
 */
class ComputedColumns extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ComputedColumns\ComputedColumns\ComputedColumns::class;
    }
}
