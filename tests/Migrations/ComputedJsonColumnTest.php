<?php declare(strict_types=1);

namespace ComputedColumns\Tests\Migrations;

use App\Models\Location;
use Illuminate\Support\Facades\DB;
use ComputedColumns\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use ComputedColumns\Database\Blueprint;

class ComputedJsonColumnTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();
    }

    public function test_create_stored_json_column() : void
    {
        $this->setUpDatabase('stored', false);

        DB::table('locations')->truncate();
        DB::table('lovations')->insert([
            'data_single' => json_encode(['name' => 'John Doe']),
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'John Doe',
            'data' => '{"name":"John Doe"}',
        ]);
    }

    public function test_create_virtual_json_column() : void
    {
        $this->setUpDatabase('virtual', false);

        DB::table('locations')->truncate();
        DB::table('lovations')->insert([
            'data_single' => json_encode(['name' => 'John Doe']),
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'John Doe',
            'data' => '{"name":"John Doe"}',
        ]);
    }

    public function test_create_stored_json_column_nullable() : void
    {
        $this->setUpDatabase('stored', true);

        DB::table('locations')->truncate();
        DB::table('lovations')->insert([
            'data_single' => json_encode(['name' => 'John Doe']),
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'John Doe',
            'data' => '{"name":"John Doe"}',
        ]);
    }

    public function test_create_virtual_json_column_nullable() : void
    {
        $this->setUpDatabase('virtual', true);

        DB::table('locations')->truncate();
        DB::table('lovations')->insert([
            'data_single' => json_encode(['name' => 'John Doe']),
        ]);

        $this->assertDatabaseHas('locations', [
            'name' => 'John Doe',
            'data' => '{"name":"John Doe"}',
        ]);
    }

    public function test_create_store_json_multiple_columns()
    {
        Schema::dropIfExists('locations');
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->json('data')->nullable();
            $table->computedJsonColumns(
                'stored',
                'data',
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
                true
            );
            $table->timestamps();
        });

        DB::table('locations')->truncate();
        $data = [
            'type'         => 'address',
            'country'      => 'United States',
            'country_code' => 'US',
            'state'        => 'California',
            'postcode'     => '90210',
            'city'         => 'Beverly Hills',
            'lat'          => '34.0901',
            'lng'          => '-118.4065',
        ];

        Location::create([
            'data' => $data,
        ]);
        // DB::table('locations')->insert([
        //     'data' => json_encode($data, JSON_UNESCAPED_SLASHES),
        // ]);
        $this->assertDatabaseHas('locations', [
            'data' => json_encode($data),
        ]);

        $this->assertDatabaseHas('locations', [
            'type'         => 'address',
            'country'      => 'United States',
            'country_code' => 'US',
            'state'        => 'California',
            'postcode'     => '90210',
            'city'         => 'Beverly Hills',
            'lat'          => '34.0901',
            'lng'          => '-118.4065',
            'data'         => json_encode($data),
        ]);
    }

    public function test_tester() : void
    {
        $this->assertTrue(true);
    }
}
