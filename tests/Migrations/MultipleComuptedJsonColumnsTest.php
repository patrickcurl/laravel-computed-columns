<?php declare(strict_types=1);

namespace ComputedColumns\Tests\Migrations;

use ComputedColumns\Tests\TestCase;

class MultipleComuptedJsonColumnsTest extends TestCase
{
    public function test_create_store_json_multiple_columns()
    {
        $this->setUpDatabase('stored', true);
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
}
