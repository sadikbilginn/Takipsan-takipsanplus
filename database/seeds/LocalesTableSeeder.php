<?php

use Illuminate\Database\Seeder;

class LocalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locales')->insert([
            [
                'title'     => 'Türkçe',
                'title_glb' => 'Turkish',
                'abbr'      => 'tr',
                'path'      => 'tr',
                'default'   => 1,
            ],
            [
                'title'     => 'İngilizce',
                'title_glb' => 'English',
                'abbr'      => 'en',
                'path'      => 'en',
                'default'   => 0,
            ]
        ]);
    }
}
