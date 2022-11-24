<?php

use Illuminate\Database\Seeder;

class ConsignmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory('App\Order', 100)->create()->each(function ($order) {
            $order->consignments()->saveMany(factory('App\Consignment', 10)->make());
        });
        factory('App\Package', 1000)->create();
        factory('App\Item', 15000)->create();
    }

}
