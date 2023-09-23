<?php

namespace Database\Seeders;

use App\Libraries\Permissions\PermissionList;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = PermissionList::getUserRoleList();

        foreach ($roles as $key => $role) {
            $mainRole = UserRole::create([
                'controller' => '#',
                'action' => '#',
                'route_name' => $key,
                'parent' => null,
                'model' => null,
            ]);
            foreach ($role as $route => $child) {
                UserRole::create([
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
