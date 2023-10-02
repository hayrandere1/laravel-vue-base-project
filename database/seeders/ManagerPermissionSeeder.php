<?php

namespace Database\Seeders;

use App\Models\ManagerPermission;
use App\Models\ManagerRole;
use App\Models\ManagerRoleGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ManagerRole::all();
        $fullPackageRoleGroup = ManagerRoleGroup::whereNull('company_id')->first();
        foreach ($roles as $role) {
            ManagerPermission::create([
                'role_group_id' => 1,
                'role_id' => $role->id,
                'filter_type' => 'everyone'
            ]);
            ManagerPermission::create([
                'role_group_id' => $fullPackageRoleGroup->id,
                'role_id' => $role->id,
                'filter_type' => 'everyone'
            ]);
        }
    }
}
