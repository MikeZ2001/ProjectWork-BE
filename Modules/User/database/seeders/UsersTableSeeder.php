<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo user
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
        
        // Create an admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        // Create an operator user
        User::create([
            'first_name' => 'Operator',
            'last_name' => 'User',
            'email' => 'operator@example.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
        ]);
    }
}
