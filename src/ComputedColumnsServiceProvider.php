<?php

namespace ComputedColumns;

use ComputedColumns\Database\Blueprint;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ComputedColumnsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-computed-columns');
    }

    public function boot()
    {
        parent::boot();
        $schema = DB::connection()->getSchemaBuilder();
        $schema->blueprintResolver(fn ($table, $callback) => new Blueprint($table, $callback));
        $this->app->instance('db.schema', $schema);
    }
}
