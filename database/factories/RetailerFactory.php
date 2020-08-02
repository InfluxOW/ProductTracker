<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Retailer;
use Faker\Generator as Faker;

$factory->define(Retailer::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->state(Retailer::class, 'BestBuy', function (Faker $faker) {
    return [
        'name' => 'BestBuy',
    ];
});
