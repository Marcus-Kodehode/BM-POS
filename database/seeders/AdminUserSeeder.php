<?php

/**
 * File: database/seeders/AdminUserSeeder.php
 * Purpose: Seeds the initial admin user for system access
 * Dependencies: User model
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bmpos.no'],
            [
                'name' => 'Admin',
                'email' => 'admin@bmpos.no',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'password_change_required' => false,
            ]
        );
        
        $this->command->info('Admin user created: admin@bmpos.no / password');
    }
}

/**
 * Summary: Creates or updates the admin user with known credentials for system access
 */
