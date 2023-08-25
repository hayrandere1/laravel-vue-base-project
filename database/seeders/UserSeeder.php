<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(1)->create([
            'company_id' => 1,
            'is_active' => true,
            'username' => 'user',
            'first_name' => 'user',
            'last_name' => 'User',
            'email' => 'oguzhan.hayrandere@outlook.com',
            'type'=>'web_user'
        ]);
        $client = new ClientRepository();
        $client->create(1, 'user', '', 'users', false, true);

    }
}
