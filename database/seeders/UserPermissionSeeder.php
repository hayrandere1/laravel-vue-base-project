<?php

namespace Database\Seeders;

use App\Models\UserPermission;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = UserRole::all();
        foreach ($roles as $role) {
            UserPermission::create([
                'role_group_id' => 1,
                'role_id' => $role->id,
                'filter_type' => 'everyone'
            ]);
        }
    }
}
