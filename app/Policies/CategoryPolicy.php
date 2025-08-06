<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_CATEGORY->value);
    }

    public function view(User $user, Category $category)
    {
        return $user->can(PermissionsEnum::VIEW_CATEGORY->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_CATEGORY->value);
    }

    public function update(User $user, Category $category)
    {
        return $user->can(PermissionsEnum::UPDATE_CATEGORY->value);
    }

    public function delete(User $user, Category $category)
    {
        return $user->can(PermissionsEnum::DELETE_CATEGORY->value);
    }

    public function restore(User $user, Category $category)
    {
        return $user->can(PermissionsEnum::RESTORE_CATEGORY->value);
    }

    public function forceDelete(User $user, Category $category)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_CATEGORY->value);
    }

    public function export(User $user)
    {
        return $user->can(PermissionsEnum::EXPORT_CATEGORY->value);
    }
}
