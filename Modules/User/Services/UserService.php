<?php

namespace Modules\User\Services;

use Modules\User\Models\User;
use Modules\User\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $credentials): ?string
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if ($user && password_verify($credentials['password'], $user->password)) {
            return $user->createToken('admin-token')->plainTextToken;
        }

        return null;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginate( $perPage);
    }

    public function findUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data, array $roles = []): User
    {
        $data['password'] = bcrypt($data['password']);
        $user = $this->userRepository->create($data);
        if (!empty($roles)) {
            $user->assignRole($roles);
        }
        return $user;
    }

    public function updateUser(int $id, array $data, array $roles = []): User
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $user = $this->userRepository->update($id, $data);
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
        return $user;
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}
