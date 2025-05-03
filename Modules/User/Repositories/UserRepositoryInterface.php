<?php

namespace Modules\User\Repositories;

use Modules\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function getAll(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
    public function delete(int $id): bool;
}
