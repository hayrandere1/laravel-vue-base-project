<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = AdminRole::all();
        foreach ($roles as $role) {
            AdminPermission::create([
                'role_group_id' => 1,
                'role_id' => $role->id,
                'filter_type' => 'everyone'
            ]);
        }
    }
}
