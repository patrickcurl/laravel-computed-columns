<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table    = 'locations';
    protected $fillable = [
        'data',
    ];
    protected $casts = [
        'city'         => 'string',
        'country'      => 'string',
        'country_code' => 'string',
        'data'         => 'json',
        'lat'          => 'float',
        'lng'          => 'float',
        'postcode'     => 'string',
        'state'        => 'string',
        'type'         => 'string',
    ];
}
