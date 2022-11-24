<?php

use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('devices')->insert([
            [
                'company_id'            => 1,
                'device_type'           => 'box_station',
                'name'                  => 'box_station',
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'company_id'            => 1,
                'device_type'           => 'donkey_station',
                'name'                  => 'donkey_station',
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'company_id'            => 1,
                'device_type'           => 'tunnel_station',
                'name'                  => 'tunnel_station',
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'company_id'            => 1,
                'device_type'           => 'box_station2',
                'name'                  => 'box_station2',
                'package_timeout'       => '6',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
