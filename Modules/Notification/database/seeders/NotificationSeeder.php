<?php

namespace Modules\Notification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Notification\Models\Notification;
use Modules\User\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'order_status_updated',
            'data' => [
                'order_id' => 1,
                'status' => 'pending',
                'message' => 'Your order #1 has been updated to shipped.',
            ],
        ]);
    }
}
