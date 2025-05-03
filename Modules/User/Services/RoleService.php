<?php

namespace Modules\User\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    public function getAllRoles(): Collection
    {
        return Role::all();
    }
}