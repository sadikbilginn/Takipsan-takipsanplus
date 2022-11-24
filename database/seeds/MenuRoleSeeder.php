<?php

use Illuminate\Database\Seeder;

class MenuRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu_role')->insert([
            [
                'menu_id'           => '1',
                'role_id'           => '1',
            ],
            [
                'menu_id'           => '2',
                'role_id'           => '1',
            ],
            [
                'menu_id'           => '3',
                'role_id'           => '1',
            ],
            [
                'menu_id'           => '4',
                'role_id'           => '1',
            ],
            [
                'menu_id'           => '5',
                'role_id'           => '1',
            ],
            [
                'menu_id'           => '6',
                'role_id'           => '1',
            ],
        ]);
    }
}
