<?php

namespace Database\Seeders;

use App\Models\UserPermission;
use App\Models\UserRole;
use App\Models\UserRoleGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = UserRole::all();
        $fullPackageRoleGroup = UserRoleGroup::whereNull('company_id')->first();
        foreach ($roles as $role) {
            UserPermission::create([
                'role_group_id' => 1,
                'role_id' => $role->id,
                'filter_type' => 'everyone'
            ]);
            UserPermission::create([
                'role_group_id' => $fullPackageRoleGroup->id,
                'role_id' => $role->id,
                'filter_type' => 'everyone'
            ]);
        }
    }
}
