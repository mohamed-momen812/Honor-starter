<?php

namespace Modules\User\Repositories;

use Modules\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function getAll(): Collection
    {
        return User::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::exact('id'), // exact match
                AllowedFilter::partial('name'), // match any part of the name
                AllowedFilter::partial('email'), // match any part of the email
            ])
            ->allowedSorts(['name', 'email', 'created_at'])
            ->defaultSort('created_at')
            ->allowedIncludes(['roles', 'orders']) // Include related roles if needed (frontend ask for it)
            ->paginate($perPage)
            ->appends(request()->query()); // Append filters to pagination links
    }

    public function findById(int $id): ?User
    {
        return QueryBuilder::for(User::class)
            ->allowedIncludes(['roles', 'orders']) // Include related roles if needed (frontend ask for it)
            ->findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        return $user ? $user->delete() : false;
    }
}
