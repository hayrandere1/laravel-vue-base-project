<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::factory(1)->create([
            'role_group_id' => 1,
            'username' => 'admin',
            'first_name' => 'Oğuzhan',
            'last_name' => 'Hayrandere',
            'email' => 'oguzhan.hayrandere@outlook.com',
            'is_active' => 1
        ]);
    }
}
