<?php declare(strict_types=1);

namespace ComputedColumns\Tests\Migrations;

use Illuminate\Support\Facades\DB;
use ComputedColumns\Tests\TestCase;

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

    public function test_tester() : void
    {
        $this->assertTrue(true);
    }
}
