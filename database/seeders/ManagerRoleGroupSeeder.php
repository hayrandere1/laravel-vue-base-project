<?php

namespace Database\Seeders;

use App\Models\ManagerRoleGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerRoleGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = ['Supervisor', 'Team Leader', 'Architect'];
        foreach ($groups as $group) {
            ManagerRoleGroup::factory(1)->create([
                'company_id' => 1,
                'name' => $group
            ]);
        }

        ManagerRoleGroup::factory(1)->create([
            'company_id' => null,
            'name' => 'Package 1 Manager Role Group'
        ]);
    }
}
