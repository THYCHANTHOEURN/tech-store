<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_ROLE->value);
    }

    public function view(User $user, Role $role)
    {
        return $user->can(PermissionsEnum::VIEW_ROLE->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_ROLE->value);
    }

    public function update(User $user, Role $role)
    {
        return $user->can(PermissionsEnum::UPDATE_ROLE->value);
    }

    public function delete(User $user, Role $role)
    {
        return $user->can(PermissionsEnum::DELETE_ROLE->value);
    }

    public function restore(User $user, Role $role)
    {
        return $user->can(PermissionsEnum::RESTORE_ROLE->value);
    }

    public function forceDelete(User $user, Role $role)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_ROLE->value);
    }
}
