<?php declare(strict_types=1);

namespace ComputedColumns\Tests;

use PDO;
use Closure;
use Illuminate\Database\Connection;
use ComputedColumns\Database\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ComputedColumns\ComputedColumnsServiceProvider;

class TestCase extends Orchestra
{
    // use RefreshDatabase;

    // public function setUp() : void
    // {
    //     parent::setUp();

    //     $this->setUpDatabase();
    // }

    public function tearDown() : void
    {
        $this->app['db']->connection()->getSchemaBuilder()->dropIfExists('locations');

        parent::tearDown();
    }

    public function setupDatabase($type = 'stored', $nullable = false)
    {
        $this->app['db']->connection()->getSchemaBuilder()->dropIfExists('locations');
        $this->app['db']->connection()->getSchemaBuilder()->create('locations', function (Blueprint $table) use ($type, $nullable) {
            $table->id();
            $table->json('data_multiple')->nullable();
            $table->json('data_single')->nullable();
            $table->computedJsonColumns(
                $type,
                'data_multiple',
                [
                    'type',
                    'country',
                    'country_code',
                    'state',
                    'postcode',
                    'city',
                    'lat',
                    'lng',
                ],
                $nullable
            );

            $table->computedJsonColumn(
                $type,
                'data_single',
                'data->name',
                $nullable
            );

            $table->computedConcatWsColumn(
                $type,
                'label',
                'data->label',
                ', ',
                'city',
                'state',
                'country',
                'postcode'
            );
            $table->computedMd5Column($type, 'uid', 'label');

            $table->timestamps();
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            ComputedColumnsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql', [
            'driver'         => 'mysql',
            'host'           => env('DB_HOST', 'localhost'),
            'database'       => env('DB_DATABASE', 'laravel_test'),
            'username'       => env('DB_USERNAME', 'laravel_test'),
            'password'       => env('DB_PASSWORD', 'test1234'),
            'unix_socket'    => env('DB_SOCKET', ''),
            'charset'        => 'utf8mb4',
            'collation'      => 'utf8mb4_unicode_ci',
            'prefix'         => '',
            'prefix_indexes' => true,
            'strict'         => true,
            'engine'         => null,
            'options'        => \extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]);

        $migrations = [
            'locations' => include __DIR__.'/../database/migrations/create_locations_table.php',
        ];
        // foreach ($migrations as $migration) {
        //     $migration->up();
        // }
        // /*
        // $migration = include __DIR__.'/../database/migrations/create_laravel-computed-columns_table.php.stub';
        // $migration->up();
        // */
    }

    protected function connectionsToTransact() : array
    {
        return ['mysql'];
    }

    /**
     * Get the database connection.
     * @param mixed|null $connection
     * @param mixed|null $table
     */
    protected function getConnection($connection = null, $table = null) : Connection
    {
        return parent::getConnection($connection, $table);
    }

    protected function withQueryLog(Closure $fn) : array
    {
        $this->getConnection()->flushQueryLog();
        $this->getConnection()->enableQueryLog();
        $fn();

        return $this->getConnection()->getQueryLog();
    }

    // protected function getBasePath()
    // {
    //     return __DIR__.'/../skeleton';
    // }
}
