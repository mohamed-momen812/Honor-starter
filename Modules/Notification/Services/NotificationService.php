<?php

namespace Modules\Notification\Services;

use Modules\Notification\Models\Notification;
use Modules\Notification\Repositories\NotificationRepositoryInterface;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    protected $notificationRepository;

    public function __construct(NotificationRepositoryInterface $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function getAllNotifications(): Collection
    {
        return $this->notificationRepository->getAll();
    }

    public function getPaginatedNotifications(int $perPage = 15): LengthAwarePaginator
    {
        $query = QueryBuilder::for(Notification::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('type'),
                AllowedFilter::scope('unread', 'whereUnread'),
            ])
            ->allowedSorts(['created_at', 'read_at'])
            ->allowedIncludes(['user']);

        if (!auth()->user()->hasPermissionTo('manage-notifications')) {
            $query->where('user_id', auth()->id());
        }

        return $query->paginate($perPage);
    }

    public function findNotificationById(int $id): ?Notification
    {
        return $this->notificationRepository->findById($id);
    }

    public function createNotification(array $data): Notification
    {
        return $this->notificationRepository->create($data);
    }

    public function markNotificationAsRead(int $id): Notification
    {
        return $this->notificationRepository->markAsRead($id);
    }

    public function deleteNotification(int $id): bool
    {
        return $this->notificationRepository->delete($id);
    }

    public function deleteOldNotifications(int $days = 30): void
    {
        Notification::where('created_at', '<', now()->subDays($days))->delete();
    }
}
