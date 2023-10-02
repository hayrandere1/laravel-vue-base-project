<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ManagerRoleGroup;
use App\Models\Package;
use App\Models\UserRoleGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $userRoleGroup = UserRoleGroup::whereNull('company_id')->first();
        $managerRoleGroup = ManagerRoleGroup::whereNull('company_id')->first();
        Package::create([
            'name' => 'Full Package',
            'manager_role_group_id' => $managerRoleGroup->id,
            'user_role_group_id' => $userRoleGroup->id,
            'is_active' => 1
        ]);
        Company::where('id','>',0)->update([
            'package_id' => 1
        ]);
    }
}
