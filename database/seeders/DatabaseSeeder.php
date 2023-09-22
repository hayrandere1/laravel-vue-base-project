<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            AdminRoleGroupSeeder::class,
            AdminRoleSeeder::class,
            AdminSeeder::class,
            AdminPermissionSeeder::class,
            ManagerRoleGroupSeeder::class,
            ManagerRoleSeeder::class,
            ManagerSeeder::class,
            ManagerPermissionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
