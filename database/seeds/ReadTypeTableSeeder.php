<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReadTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('read_types')->insert([
            [
                'name'                  => 'Ayar Grubu 1',
                'name_en'               => 'Setting Group 1',
                'reader'                => 'impinj',
                'reader_mode'           => 'DenseReaderM4',
                'estimated_population'  => 0,
                'search_mode'           => 'SingleTarget',
                'session'               => 1,
                'string_set'            => '',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Ayar Grubu 2',
                'name_en'               => 'Setting Group 2',
                'reader'                => 'impinj',
                'reader_mode'           => 'DenseReaderM4',
                'estimated_population'  => 500,
                'search_mode'           => 'SingleTarget',
                'session'               => 2,
                'string_set'            => '',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Ayar Grubu 3',
                'name_en'               => 'Setting Group 3',
                'reader'                => 'thingmagic',
                'reader_mode'           => '',
                'estimated_population'  => 0,
                'search_mode'           => '',
                'session'               => 1,
                'string_set'            => '',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'name'                  => 'Ayar Grubu 4',
                'name_en'               => 'Setting Group 4',
                'reader'                => 'thingmagic',
                'reader_mode'           => '',
                'estimated_population'  => 500,
                'search_mode'           => '',
                'session'               => 2,
                'string_set'            => '',
                'status'                => 1,
                'created_user_id'       => 1,
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
