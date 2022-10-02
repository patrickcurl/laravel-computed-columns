<?php

namespace ComputedColumns\ComputedColumns\Tests;

use ComputedColumns\ComputedColumns\ComputedColumnsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ComputedColumns\\ComputedColumns\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ComputedColumnsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migrations = [
            'locations' => include __DIR__.'/../database/migrations/create_locations_table.php',
        ];
        foreach ($migrations as $migration) {
            $migration->up();
        }
        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-computed-columns_table.php.stub';
        $migration->up();
        */
    }
}
