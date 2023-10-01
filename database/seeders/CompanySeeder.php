<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::factory(1)->create([
            'name' => 'Main Company',
            'is_active' => true,
            'supervisor_id' => 1,
            'main_user_id' => 1,
        ]);
    }
}
