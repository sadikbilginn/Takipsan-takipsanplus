<?php

use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            [
                'name'              => 'Yılmaz Tekstil',
                'title'             => 'Yılmaz Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'mehmet.karabulut@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.394517',
                'longitude'         => '27.035748',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'name'              => 'Can Tekstil',
                'title'             => 'Can Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'mehmet.karabulut@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.474253',
                'longitude'         => '27.075457',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'name'              => 'Özgür Tekstil',
                'title'             => 'Özgür Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'mehmet.karabulut@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.474253',
                'longitude'         => '27.075457',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'name'              => 'Svs Tekstil',
                'title'             => 'Svs Tekstil San. Ve. Tic. A.Ş',
                'logo'              => 'company.jpg',
                'phone'             => '+90 850 441 6789',
                'email'             => 'mehmet.karabulut@takipsan.com',
                'address'           => 'Fatih Mahallesi, 1198 Sokak No: 1/A Sarnıç, Gaziemir İzmir / TÜRKİYE',
                'latitude'          => '38.474253',
                'longitude'         => '27.075457',
                'status'            => 1,
                'consignment_close' => 1,
                'created_user_id'   => 1,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
