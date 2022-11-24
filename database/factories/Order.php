<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Order;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'consignee_id'          => $faker->numberBetween(1, 2),
        'order_code'            => Str::random(9),
        'po_no'                 => Str::random(10),
        'name'                  => $faker->name,
        'item_count'            => $faker->numberBetween(500, 10000),
        'created_user_id'       => 1
    ];
});
