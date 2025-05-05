<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'manage-users',
            'view-users',
            'manage-roles',
            'view-roles',
            'manage-permissions',
            'view-permissions',
            'manage-categories',
            'view-categories',
            'manage-products',
            'view-products',
            'manage-orders',
            'view-orders',
            'manage-cart',
            'view-cart',
            'manage-checkout',
            'view-checkout',
            'manage-payments',
            'view-payments',
            'manage-notifications',
            'view-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo($permissions); // Admin has all permissions

        $customer = Role::create(['name' => 'customer']);
        $customer->givePermissionTo([
            'view-products',
            'view-categories',
            'view-orders',
            'view-notifications',
        ]);
    }
}