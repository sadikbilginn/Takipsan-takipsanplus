<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        DB::table('users')->insert([
            [
                'company_id'    => 0,
                'name'          => 'Takipsan Bilişim',
                'username'      => 'takipsan',
                'email'         => 'mehmet.karabulut@takipsan.com',
                'password'      => bcrypt('takipsan20!'),
                'is_admin'      => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
//            [
//                'company_id'    => 1,
//                'name'          => 'Mehmet Karabulut',
//                'username'      => 'karabulut',
//                'email'         => 'mehmet.karabulut@takipsan.com',
//                'password'      => bcrypt('12121212'),
//                'is_admin'      => 0,
//                'created_at'    => date('Y-m-d H:i:s'),
//                'updated_at'    => date('Y-m-d H:i:s'),
//            ],
//            [
//                'company_id'    => 1,
//                'name'          => 'Firma',
//                'username'      => 'firma',
//                'email'         => 'mehmet.karabulut@takipsan.com',
//                'password'      => bcrypt('firma'),
//                'is_admin'      => 0,
//                'created_at'    => date('Y-m-d H:i:s'),
//                'updated_at'    => date('Y-m-d H:i:s'),
//            ]
        ]);

        //factory('App\User', 10000000)->create();
    }
}
