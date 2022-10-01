# Adds the methods: md5AsComputed, jsonFieldStoredAs, manyJsonFieldsStoredAs, concatWsStoredAs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/patrickcurl/laravel-computed-columns.svg?style=flat-square)](https://packagist.org/packages/patrickcurl/laravel-computed-columns)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/patrickcurl/laravel-computed-columns/run-tests?label=tests)](https://github.com/patrickcurl/laravel-computed-columns/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/patrickcurl/laravel-computed-columns/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/patrickcurl/laravel-computed-columns/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/patrickcurl/laravel-computed-columns.svg?style=flat-square)](https://packagist.org/packages/patrickcurl/laravel-computed-columns)

This package is an extension to laravel migrations to add some more optimized virtual/storedAs fields based on common use cases (at least my own);

I'm planning on better documentation but the simplest way to jump in is look at some code.

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
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

            $table->manyJsonFieldsStoredAs(
                'data', // the json column to extract data from.
                [
                    'type',
                    'country',
                    'country_code',
                    'state',
                    'postcode',
                    'city',
                    'lat',
                    'lng',
                ], // loop over these fields and run the storedAs method see example below.
                true // should each field be nullable?
            );

            // This is the equivalent of running:
            // $field = 'data->';
            // $path = 'country';
            // $this->string('type')->storedAs('json_unquote(json_extract('.$field.$path.'))');


            // concatWsStoredAs($column, $default, $separator, ...$fields)
            $table->concatWsStoredAs(
                'label',
                'data->label',
                ', ',
                'city',
                'state',
                'country',
                'postcode'
            ); // This will use the default if it exists, otherwise it'll create a default either from json nested keys where fields are ['data->city', 'data->state', etc....];

            // This one simply creates an id, my location example basically does a lookup of data via openstreetmaps, and caches the label, and then if someone adds that to their profile as their location it'll pull it all in at that point either from the api, or the cache, or an existing location as we're only dealing with city/state not actual full addresses.

            // This isn't the most secure thing, for an id that's not critical that it's secret this is fine, but I wouldn't use it to hash passwords!
            $table->md5StoredAs('uid', 'label');

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



## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-computed-columns.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-computed-columns)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require patrickcurl/laravel-computed-columns
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-computed-columns-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-computed-columns-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-computed-columns-views"
```

## Usage

```php
$computedColumns = new ComputedColumns\ComputedColumns();
echo $computedColumns->echoPhrase('Hello, ComputedColumns!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Patrick Curl](https://github.com/patrickcurl)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
