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
            'username' => 'manager',
            'first_name' => 'Manager',
            'last_name' => 'Manager',
            'email' => 'oguzhan.hayrandere@outlook.com',
            'is_active' => 1
        ]);
    }
}
