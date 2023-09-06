<?php

namespace Database\Seeders;

use App\Libraries\Permissions\PermissionList;
use App\Models\AdminRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = PermissionList::getAdminRoleList();

        foreach ($roles as $key => $role) {
            $main_role = AdminRole::create([
                'controller' => '#',
                'action' => '#',
                'route_name' => $key,
                'parent' => null,
                'model' => null,
            ]);
            foreach ($role as $route => $child) {
                AdminRole::create([
                    'controller' => $child['controller'],
                    'action' => $child['action'],
                    'route_name' => $route,
                    'parent' => $main_role->id,
                    'model' => $child['model']
                ]);
            }
        }
    }
}
