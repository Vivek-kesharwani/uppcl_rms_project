<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'hqadmin@uppcl.com'],
            [
                'name' => 'HQ Admin',
                'password' => bcrypt('password123'),
                'role' => 'HQ_ADMIN',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'discom@uppcl.com'],
            [
                'name' => 'DISCOM Admin',
                'password' => bcrypt('password123'),
                'role' => 'DISCOM_ADMIN',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'agency@uppcl.com'],
            [
                'name' => 'Agency User',
                'password' => bcrypt('password123'),
                'role' => 'AGENCY',
            ]
        );
    }
}