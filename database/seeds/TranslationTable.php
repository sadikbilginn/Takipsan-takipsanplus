<?php

use Illuminate\Database\Seeder;

class TranslationTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];

        foreach (\Illuminate\Support\Facades\Lang::get('station') as $item => $value){
            $tr = \Illuminate\Support\Facades\Lang::get('station.'.$item, [], 'tr');
            $en = \Illuminate\Support\Facades\Lang::get('station.'.$item, [], 'en');
            $data[] =  [
                'group'         => 'station',
                'key'           => $item,
                'value'         => '{"tr":"'.$tr.'","en":"'.$en.'"}',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
        }

        DB::table('translations')->insert($data);


        $data = [];
        foreach (\Illuminate\Support\Facades\Lang::get('portal') as $item => $value){
            $tr = \Illuminate\Support\Facades\Lang::get('portal.'.$item, [], 'tr');
            $en = \Illuminate\Support\Facades\Lang::get('portal.'.$item, [], 'en');
            $data[] =  [
                'group'         => 'portal',
                'key'           => $item,
                'value'         => '{"tr":"'.$tr.'","en":"'.$en.'"}',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
        }

        DB::table('translations')->insert($data);
    }
}
