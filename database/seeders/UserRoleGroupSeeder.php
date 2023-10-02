<?php

namespace Database\Seeders;

use App\Models\UserRoleGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = ['Customer', 'Guest', 'Premium'];
        foreach ($groups as $group) {
            UserRoleGroup::factory(1)->create([
                'company_id' => 1,
                'name' => $group
            ]);
        }
        UserRoleGroup::factory(1)->create([
            'company_id' => null,
            'name' => 'Package 1 User Role Group'
        ]);


    }
}
