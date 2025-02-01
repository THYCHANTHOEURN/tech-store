<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        foreach (PermissionsEnum::cases() as $permission) {
            Permission::create(['name' => $permission->value]);
        }

        // Define roles and their permissions
        $roles = [
            RolesEnum::SUPER_ADMIN->value => PermissionsEnum::cases(),
            RolesEnum::ADMIN->value => [
                PermissionsEnum::VIEW_ANY_USER, PermissionsEnum::VIEW_USER, PermissionsEnum::CREATE_USER, PermissionsEnum::UPDATE_USER,
                PermissionsEnum::VIEW_ANY_PRODUCT, PermissionsEnum::VIEW_PRODUCT, PermissionsEnum::CREATE_PRODUCT, PermissionsEnum::UPDATE_PRODUCT,
                PermissionsEnum::VIEW_ANY_ORDER, PermissionsEnum::VIEW_ORDER, PermissionsEnum::UPDATE_ORDER,
                PermissionsEnum::VIEW_ANY_CATEGORY, PermissionsEnum::VIEW_CATEGORY,
                PermissionsEnum::VIEW_ANY_BRAND, PermissionsEnum::VIEW_BRAND,
                PermissionsEnum::VIEW_ANY_REVIEW, PermissionsEnum::VIEW_REVIEW,
                PermissionsEnum::VIEW_ANY_BANNER, PermissionsEnum::VIEW_BANNER,
                PermissionsEnum::VIEW_ANY_CART_ITEM, PermissionsEnum::VIEW_CART_ITEM,
            ],
            RolesEnum::MANAGER->value => [
                PermissionsEnum::VIEW_ANY_PRODUCT, PermissionsEnum::VIEW_PRODUCT, PermissionsEnum::UPDATE_PRODUCT,
                PermissionsEnum::VIEW_ANY_ORDER, PermissionsEnum::VIEW_ORDER, PermissionsEnum::UPDATE_ORDER,
                PermissionsEnum::VIEW_ANY_CATEGORY, PermissionsEnum::VIEW_CATEGORY,
                PermissionsEnum::VIEW_ANY_BRAND, PermissionsEnum::VIEW_BRAND,
                PermissionsEnum::VIEW_ANY_REVIEW, PermissionsEnum::VIEW_REVIEW,
            ],
            RolesEnum::CUSTOMER->value => [
                PermissionsEnum::VIEW_ANY_PRODUCT, PermissionsEnum::VIEW_PRODUCT,
                PermissionsEnum::CREATE_REVIEW, PermissionsEnum::UPDATE_REVIEW,
            ]
        ];

        // Create roles and assign permissions
        foreach ($roles as $role => $rolePermissions) {
            $role = Role::create(['name' => $role]);
            $role->givePermissionTo(array_map(fn($permission) => $permission->value, $rolePermissions));
        }
    }
}
