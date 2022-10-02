<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
        Schema::dropIfExists('locateables');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->json('data')->nullable();

            $table->manyComputedJsonColumns(
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
                'label',
                'data->label',
                ', ',
                'city',
                'state',
                'country',
                'postcode'
            );
            $table->computedMd5Column('stored', 'uid', 'label');

            $table->timestamps();
        });

        Schema::create('locateables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained();
            $table->morphs('locateable');
            $table->unique(['location_id', 'locateable_id', 'locateable_type']);
        });
    }
};
