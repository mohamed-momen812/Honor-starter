<?php

namespace Modules\User\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelIgnition\Recorders\QueryRecorder\Query;
use Spatie\QueryBuilder\QueryBuilder;

class RoleService
{
    public function getAllRoles(): Collection
    {
        return QueryBuilder::for(Role::class)
            ->allowedFilters(['name'])
            ->allowedSorts(['name'])
            ->allowedIncludes(['permissions'])
            ->get();
    }
}
