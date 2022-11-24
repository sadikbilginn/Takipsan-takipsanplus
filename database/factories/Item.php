<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Item;
use Faker\Generator as Faker;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'company_id'            => $faker->numberBetween(1,2),
        'order_id'              => $faker->numberBetween(1,100),
        'consignment_id'        => $faker->numberBetween(1,1000),
        'package_id'            => $faker->numberBetween(1,100),
        'epc'                   => $faker->iban(),
        'device_id'             => 1,
        'created_user_id'       => 1,
        'created_at'            => now()
    ];
});
