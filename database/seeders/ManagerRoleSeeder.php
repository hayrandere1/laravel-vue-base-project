<?php

namespace Database\Seeders;

use App\Libraries\Permissions\PermissionList;
use App\Models\ManagerRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = PermissionList::getManagerRoleList();

        foreach ($roles as $key => $role) {
            $mainRole = ManagerRole::create([
                'controller' => '#',
                'action' => '#',
                'route_name' => $key,
                'parent' => null,
                'model' => null,
            ]);
            foreach ($role as $route => $child) {
                ManagerRole::create([
                    'controller' => $child['controller'],
                    'action' => $child['action'],
                    'route_name' => $route,
                    'parent' => $mainRole->id,
                    'model' => $child['model']
                ]);
            }
        }
    }
}
