<?php

namespace Modules\Notification\Repositories;

use Modules\Notification\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getAll(): Collection
    {
        return Notification::with('user')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Notification::with('user')->paginate($perPage);
    }

    public function findById(int $id): ?Notification
    {
        return Notification::with('user')->find($id);
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function update(int $id, array $data): Notification
    {
        $notification = $this->findById($id);
        $notification->update($data);
        return $notification;
    }

    public function delete(int $id): bool
    {
        $notification = $this->findById($id);
        return $notification ? $notification->delete() : false;
    }

    public function markAsRead(int $id): Notification
    {
        $notification = $this->findById($id);
        $notification->update(['read_at' => now()]);
        return $notification;
    }
}