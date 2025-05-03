<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = \Modules\User\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'), // Use a secure password hashing method
        ]);

        // Assign admin role to the user
        $admin->assignRole('admin');

        $customer = \Modules\User\Models\User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'), // Use a secure password hashing method
        ]);

        // Assign customer role to the user
        $customer->assignRole('customer');
    }

}
