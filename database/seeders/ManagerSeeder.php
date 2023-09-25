<?php

namespace Database\Seeders;

use App\Models\Manager;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Manager::factory(1)->create([
            'company_id' => 1,
            'role_group_id' => 1,
            'username' => 'manager',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'oguzhan.hayrandere@outlook.com',
            'is_active' => 1
        ]);
    }
}
