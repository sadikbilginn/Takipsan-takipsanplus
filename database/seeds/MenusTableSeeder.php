<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->insert([
            [
                'parent_id'     => 0,
                'title'         => 'Üreticiler',
                'title_en'      => 'Manufacturers',
                'icon'          => 'far fa-building',
                'uri'           => 'company',
                'sort'          => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 0,
                'title'         => 'Siparişler',
                'title_en'      => 'Orders',
                'icon'          => 'fas fa-shopping-cart',
                'uri'           => 'order',
                'sort'          => 2,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 0,
                'title'         => 'Sevkiyatlar',
                'title_en'      => 'Consignments',
                'icon'          => 'fas fa-truck-loading',
                'uri'           => 'consignment',
                'sort'          => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            [
                'parent_id'     => 0,
                'title'         => 'Sevk Edilecek Firmalar',
                'title_en'      => 'Consignee',
                'icon'          => 'fas fa-truck-moving',
                'uri'           => 'consignee',
                'sort'          => 4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 0,
                'title'         => 'Raporlar',
                'title_en'      => 'Report',
                'icon'          => 'fas fa-chart-pie',
                'uri'           => 'reports',
                'sort'          => 5,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 0,
                'title'         => 'Kullanıcılar',
                'title_en'      => 'Users',
                'icon'          => 'fas fa-users',
                'uri'           => 'user',
                'sort'          => 6,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 0,
                'title'         => 'Sistem Ayarları',
                'title_en'      => 'System Settings',
                'icon'          => 'fas fa-cogs',
                'uri'           => '#',
                'sort'          => 7,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 7,
                'title'         => 'Çeviriler',
                'title_en'      => 'Translations',
                'icon'          => 'fas fa-language',
                'uri'           => 'translation',
                'sort'          => 4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 7,
                'title'         => 'Cihazlar',
                'title_en'      => 'Devices',
                'icon'          => 'fas fa-laptop',
                'uri'           => 'device',
                'sort'          => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 7,
                'title'         => 'Okuma Tipleri',
                'title_en'      => 'Reading Types',
                'icon'          => 'fab fa-audible',
                'uri'           => 'read-type',
                'sort'          => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 7,
                'title'         => 'İzinler',
                'title_en'      => 'Permissions',
                'icon'          => 'fas fa-lock-open',
                'uri'           => 'permission',
                'sort'          => 2,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 7,
                'title'         => 'Özel İzinler',
                'title_en'      => 'Custom Permissions',
                'icon'          => 'fas fa-lock-open',
                'uri'           => 'custompermission',
                'sort'          => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 7,
                'title'         => 'Roller',
                'title_en'      => 'Roller',
                'icon'          => 'fas fa-user-secret',
                'uri'           => 'role',
                'sort'          => 3,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'parent_id'     => 7,
                'title'         => 'Menüler',
                'title_en'      => 'Menus',
                'icon'          => 'fas fa-list',
                'uri'           => 'menu',
                'sort'          => 4,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
