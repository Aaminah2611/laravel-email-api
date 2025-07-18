<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'User One',
            'email' => 'User_one@example.com',
            'password' => Hash::make('password'),
        ]);

         User::create([
            'name' => 'User Two',
            'email' => 'User_two@example.com',
            'password' => Hash::make('password'),
        ]);

         User::create([
            'name' => 'User Three',
            'email' => 'User_three@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
