<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class KeycloakUsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'keycloak_id' => '11111111-1111-1111-1111-111111111111',
                'name' => 'User One',
                'email' => 'user1@example.com',
                'password' => bcrypt('password1'),
            ],
            [
                'keycloak_id' => '22222222-2222-2222-2222-222222222222',
                'name' => 'User Two',
                'email' => 'user2@example.com',
                'password' => bcrypt('password2'),
            ],
            [
                'keycloak_id' => '33333333-3333-3333-3333-333333333333',
                'name' => 'User Three',
                'email' => 'user3@example.com',
                'password' => bcrypt('password3'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['keycloak_id' => $userData['keycloak_id']],
                $userData
            );
        }
    }
}
