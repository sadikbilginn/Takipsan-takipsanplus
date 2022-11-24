<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Package;
use Faker\Generator as Faker;

$factory->define(Package::class, function (Faker $faker) {
    return [
        'company_id'            => $faker->numberBetween(1,2),
        'order_id'              => $faker->numberBetween(1, 100),
        'consignment_id'        => $faker->numberBetween(1, 1000),
        'package_no'            => $faker->numberBetween(1,100),
        'device_id'             => $faker->numberBetween(1,3),
        'created_user_id'       => 1,
        'created_at'            => now()
    ];
});
