<?php declare(strict_types=1);

namespace ComputedColumns\Tests\Migrations;

use App\Models\Location;
use Illuminate\Support\Facades\DB;
use ComputedColumns\Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use ComputedColumns\Database\Blueprint;

class ConcatWSTest extends TestCase
{
    public function test_create_stored_from_cat_ws()
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
            $table->computedConcatWsColumn(
                'stored',
                'label',
                'data->label',
                ', ',
                'city',
                'state',
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

        $this->assertDatabaseHas('locations', [
            'label' => 'Beverly Hills, California',
        ]);
    }

    public function test_create_virtual_from_cat_ws()
    {
        Schema::dropIfExists('locations');
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->json('data')->nullable();
            $table->computedJsonColumns(
                'virtual',
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
            $table->computedConcatWsColumn(
                'virtual',
                'label',
                'data->label',
                ', ',
                'city',
                'state',
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

        $this->assertDatabaseHas('locations', [
            'label' => 'Beverly Hills, California',
        ]);
    }
}
