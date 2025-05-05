<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Mockery\Matcher\Not;
use Modules\Cart\Database\Seeders\CartDatabaseSeeder;
use Modules\Notification\Database\Seeders\NotificationDatabaseSeeder;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;
use Modules\Order\Database\Seeders\OrderSeeder;
use Modules\Product\Database\Seeders\ProductDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserDatabaseSeeder::class,
            ProductDatabaseSeeder::class,
            CartDatabaseSeeder::class,
            OrderDatabaseSeeder::class,
            OrderSeeder::class,
            NotificationDatabaseSeeder::class,
        ]);
    }
}