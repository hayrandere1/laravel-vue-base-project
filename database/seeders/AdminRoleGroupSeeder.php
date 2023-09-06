<?php

namespace Database\Seeders;

use App\Models\AdminRoleGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminRoleGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = ['Developer', 'Accounting', 'Finance', 'Technical Support'];
        foreach ($groups as $group){
            AdminRoleGroup::factory(1)->create([
                'name' => $group
            ]);
        }
    }
}
