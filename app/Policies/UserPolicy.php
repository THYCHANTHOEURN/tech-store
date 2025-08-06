<?php

namespace App\Policies;

use App\Enums\PermissionsEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return $user->can(PermissionsEnum::VIEW_ANY_USER->value);
    }

    public function view(User $user, User $model)
    {
        return $user->can(PermissionsEnum::VIEW_USER->value);
    }

    public function create(User $user)
    {
        return $user->can(PermissionsEnum::CREATE_USER->value);
    }

    public function update(User $user, User $model)
    {
        return $user->can(PermissionsEnum::UPDATE_USER->value);
    }

    public function delete(User $user, User $model)
    {
        return $user->can(PermissionsEnum::DELETE_USER->value);
    }

    public function restore(User $user, User $model)
    {
        return $user->can(PermissionsEnum::RESTORE_USER->value);
    }

    public function forceDelete(User $user, User $model)
    {
        return $user->can(PermissionsEnum::FORCE_DELETE_USER->value);
    }

    public function export(User $user)
    {
        return $user->can(PermissionsEnum::EXPORT_USER->value);
    }
}
