<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;

class UserDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UserSeeder::class);
    }
}