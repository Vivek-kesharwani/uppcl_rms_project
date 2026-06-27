<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'hqadmin@uppcl.com'],
            [
                'name' => 'HQ Admin',
                'password' => bcrypt('password123'),
                'role' => 'HQ_ADMIN',
            ]
        );

        User::updateOrCreate(
            ['email' => 'discom@uppcl.com'],
            [
                'name' => 'DISCOM Admin',
                'password' => bcrypt('password123'),
                'role' => 'DISCOM_ADMIN',
            ]
        );

        User::updateOrCreate(
            ['email' => 'operator@uppcl.com'],
            [
                'name' => 'Operator',
                'password' => bcrypt('password123'),
                'role' => 'OPERATOR',
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@uppcl.com'],
            [
                'name' => 'Viewer',
                'password' => bcrypt('password123'),
                'role' => 'VIEWER',
            ]
        );
    }
}