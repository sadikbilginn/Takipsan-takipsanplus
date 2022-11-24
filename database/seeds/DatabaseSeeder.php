<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LocalesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(MenuRoleSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(CustomPermissionSeeder::class);
        $this->call(TranslationTable::class);
        $this->call(ReadTypeTableSeeder::class);
        $this->call(ConsigneeTableSeeder::class);


        //$this->call(CompanySeeder::class);
        //$this->call(DeviceSeeder::class);
        //$this->call(ConsigneeTableSeeder::class);
        //$this->call(ConsignmentTableSeeder::class);
    }
}
