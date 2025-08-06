<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_BRAND->value);
    }

    public function view(User $user, Brand $brand)
    {
        return $user->can(PermissionsEnum::VIEW_BRAND->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_BRAND->value);
    }

    public function update(User $user, Brand $brand)
    {
        return $user->can(PermissionsEnum::UPDATE_BRAND->value);
    }

    public function delete(User $user, Brand $brand)
    {
        return $user->can(PermissionsEnum::DELETE_BRAND->value);
    }

    public function restore(User $user, Brand $brand)
    {
        return $user->can(PermissionsEnum::RESTORE_BRAND->value);
    }

    public function forceDelete(User $user, Brand $brand)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_BRAND->value);
    }

    public function export(User $user)
    {
        return $user->can(PermissionsEnum::EXPORT_BRAND->value);
    }
}
