<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Notification\Services\NotificationService;

class DeleteOldNotifications extends Command
{
    protected $signature = 'notifications:clean {days=30}';
    protected $description = 'Delete notifications older than the specified number of days';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $days = $this->argument('days');
        $this->notificationService->deleteOldNotifications($days);
        $this->info("Deleted notifications older than {$days} days.");
    }
}
