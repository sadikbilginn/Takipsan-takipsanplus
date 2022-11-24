<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Consignment;
use Faker\Generator as Faker;

$factory->define(Consignment::class, function (Faker $faker) {
    $x = $faker->numberBetween(1, 4);
    return [
        'company_id'            => $x,
        'consignee_id'          => $faker->numberBetween(1, 2),
        'name'                  => $faker->name,
        'item_count'            => $faker->numberBetween(10, 500),
        'delivery_date'         => $faker->date('Y-m-d', 'now'),
        'status'                => $faker->numberBetween(0, 1),
        'created_user_id'       => 1,
        'created_at'            => $faker->dateTimeBetween('2019-09-01', 'now')
    ];
});
