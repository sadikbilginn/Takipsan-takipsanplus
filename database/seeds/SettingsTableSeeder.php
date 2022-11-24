<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'group_key'     => 'company',
                'required'      => 0,
                'area_type'     => 'file',
                'title'         => 'Firma Logo',
                'description'   => NULL,
                'key'           => 'company_logo',
                'value'         => 'company_logo-img.png',
                'locale'        => 0,
                'sort'          => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'group_key'     => 'company',
                'required'      => 0,
                'area_type'     => 'input',
                'title'         => 'Firma Adı',
                'description'   => NULL,
                'key'           => 'company_name',
                'value'         => 'Takipsan Bilişim Sistemleri',
                'locale'        => 0,
                'sort'          => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'group_key'     => 'company',
                'required'      => 0,
                'area_type'     => 'input',
                'title'         => 'Telefon',
                'description'   => NULL,
                'key'           => 'company_phone',
                'value'         => '0 850 441 6789',
                'locale'        => 0,
                'sort'          => 2,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'group_key'     => 'company',
                'required'      => 0,
                'area_type'     => 'input',
                'title'         => 'E-Mail',
                'description'   => NULL,
                'key'           => 'company_email',
                'value'         => 'info@takipsan.com',
                'locale'        => 0,
                'sort'          => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'group_key'     => 'company',
                'required'      => 0,
                'area_type'     => 'input',
                'title'         => 'Adres',
                'description'   => NULL,
                'key'           => 'company_address',
                'value'         => 'Dokuz Eylül Üniversitesi, Depark Sağlık Teknopark Ofis Z3 Balçova - İZMİR / TURKEY',
                'locale'        => 0,
                'sort'          => 4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'group_key'     => 'company',
                'required'      => 0,
                'area_type'     => 'input',
                'title'         => 'Koordinat',
                'description'   => NULL,
                'key'           => 'company_coordinate',
                'value'         => '38.393867, 27.035379',
                'locale'        => 0,
                'sort'          => 5,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'group_key'     => 'general',
                'required'      => 0,
                'area_type'     => 'input',
                'title'         => 'Portal Adı',
                'description'   => NULL,
                'key'           => 'title',
                'value'         => '{"tr":"Portal | Takipsan RFID", "en":"Portal | Takipsan RFID"}',
                'locale'        => 1,
                'sort'          => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
