<?php

namespace Modules\Notification\Repositories;

use Modules\Notification\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function getAll(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Notification;
    public function create(array $data): Notification;
    public function update(int $id, array $data): Notification;
    public function delete(int $id): bool;
    public function markAsRead(int $id): Notification;
}