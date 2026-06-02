<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            ['id'=> '1', 'name'=> 'user1', 'email'=> 'user1@example.com', 'email_verified_at'=> now(), 'password'=> bcrypt('password'), 'admin_status'=>false ],
            ['id' => '2', 'name' => 'user2', 'email' => 'user2@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'admin_status' => false ],
            ['id' => '3', 'name' => 'user3', 'email' => 'user3@example.com', 'email_verified_at' => now(), 'password' => bcrypt('password'), 'admin_status' => true ],
        ]);
    }
}
